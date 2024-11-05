<?php

class TelegraphText {
    private string $title, $text, $author, $published, $slug;
    const TXT_FILE_EXTENSION = '.txt';

    public function setAuthor(string $newAuthor)
    {
        if(strlen($newAuthor) < 120) {
            $this->author = $newAuthor;
        }
    }
    public function getAuthor(): string
    {
        return $this->author;
    }
    public function setSlug(string $newSlug)
    {
        $isNewSlugValid = true;
        foreach(str_split($newSlug) as $item) {
            if($item !== '-' && $item !== '_' && !preg_match("#[a-z]+#i", $item) && !preg_match('#^[0-9]+$#', $item)) {$isNewSlugValid = false;}
        }
        if($isNewSlugValid) {$this->slug = $newSlug;}
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function setPublished(string $newPublished)
    {
        $dateObject = new DateTime();
        if($newPublished <= $dateObject->format('Y-m-d')) {
            $this->published = $newPublished;
        }
    }
    public function getPublished(): string
    {
        return $this->published;
    }

    public function __set(string $name, $value)
    {
        switch($name) {
            case 'author':
                $this->setAuthor($value);
                break;
            case 'published':
                $this->setPublished($value);
                break;
            case 'slug':
                $this->setSlug($value);
                break;
            case 'text':
                $this->storeText();
                break;
        }
    }
    public function __get(string $name)
    {
        switch($name) {
            case 'author':
                return $this->getAuthor();
                break;
            case 'published':
                return $this->getPublished();
                break;
            case 'slug':
                return $this->getSlug();
                break;
            case 'text':
                return $this->loadText($this->slug);
                break;
        }
    }

    public function setAnyValue($field, $newValue)
    {
        $this->$field = $newValue;
    }
    public function getAnyValue($field)
    {
        return $this->$field;
    }

    public function __construct(string $title, string $text, string $author)
    {
        $this->title = $title;
        $this->text = $text;
        $this->author = $author;
        $this->published = '15.08.2024 14:00';
        $this->slug = str_replace(' ', '-', $this->title);
    }

    public $infoArr = [];
    private function storeText(): string
    {
        $this->infoArr = [
            'title' => $this->title,
            'text' => $this->text,
            'author' => $this->author,
            'published' => $this->published,
        ];

        file_put_contents($this->slug . self::TXT_FILE_EXTENSION, serialize($this->infoArr));

        return $this->slug;
    }

    private function loadText(string $slug): ?array
    {
        if(filesize($slug . self::TXT_FILE_EXTENSION) !== 0) {
            $infoArrUnserialized = unserialize(file_get_contents($slug . self::TXT_FILE_EXTENSION));
            return $infoArrUnserialized;
        }
    }

    public function editText(string $title, string $text)
    {
        $this->title = $title;
        $this->text = $text;
        $this->slug = str_replace(' ', '-', $this->title);
        $this->storeText();
    }
}

abstract class Storage {
    public abstract function create($object): string;
    public abstract function read(string $slug);
    public abstract function update(string $slug, $newObject);
    public abstract function delete(string $slug);
    public abstract function list();
}

abstract class User {
    protected number $id;
    protected string $name;
    protected $role;

    public abstract function getTextsToEdit();
}

function getNextEnding(string $oldFileName): string
{
    $allFiles = scandir(__DIR__);
    array_shift($allFiles);
    array_shift($allFiles);
    array_filter($allFiles, 'is_file');

    $areThereCopiesWithHyphen = false;
    $copiesWithHyphenLastNumbersArr = [];
    foreach($allFiles as $item) {
        if(str_contains($item, $oldFileName . '-')) {
            $areThereCopiesWithHyphen = true;
            array_push($copiesWithHyphenLastNumbersArr, substr(str_replace('.txt', '', $item), strlen($oldFileName . '-')));
        }
    }
    
    if($areThereCopiesWithHyphen === false) {
        return $oldFileName . '-1';
    } else {
        return $oldFileName . '-' . max($copiesWithHyphenLastNumbersArr) + 1;
    }
};

class FileStorage extends Storage {
    public function create($object): string
    {
        $fileName = $object->slug . '_' . str_replace('/', '_',  date('d/m/y'));
        if(file_exists($fileName . '.txt')) {
            file_put_contents( getNextEnding($fileName) . '.txt', serialize($object) );
        } else {
            file_put_contents($fileName . '.txt', serialize($object));
        }

        return $fileName;
    }

    public function read($slug)
    {
        if(file_exists($slug . '.txt')) {
            return unserialize(file_get_contents($slug . '.txt'));
        }
    }
    
    public function update($slug, $newObject)
    {
        if(file_exists($slug . '.txt')) {
            $oldUnserializedObject = unserialize(file_get_contents($slug . '.txt'));
            file_put_contents($slug . '.txt', serialize($newObject));
        }
    }

    public function delete($slug)
    {
        if(file_exists($slug . '.txt')) {
            unlink($slug . '.txt');
        }
    }

    public function list()
    {
        $resultArr = [];
        $folderContents = scandir(__DIR__);
        array_shift($folderContents);
        array_shift($folderContents);
        unset($folderContents[array_search('index.php', $folderContents)]);

        foreach($folderContents as $value) {
            array_push($resultArr, unserialize(file_get_contents($value)));
        }

        return $resultArr;
    }
}

interface IRender {
    function render($telegraphText): string;
};

abstract class View implements IRender {
    protected string $templateName;
    protected array $variables;
    function __construct(string $templateName)
    {
        $this->templateName = $templateName;
    }

    public function addVariablesToTemplate(array $variables)
    {
        $this->variables = $variables;
    }
};

class Swig extends View {
    function render($telegraphText): string
    {
        $swig = file_get_contents($this->templateName);

        foreach($this->variables as $key) {
            $swig = str_replace('{{ ' . $key . ' }}', $telegraphText->getAnyValue($key), $swig);
        };

        return $swig;
    }
};
class Spl extends View {
    function render($telegraphText): string
    {
        $spl = file_get_contents($this->templateName);

        foreach($this->variables as $key) {
            $spl = str_replace('$$' . $key . '$$', $telegraphText->getAnyValue($key), $spl);
        };
        
        return $spl;
    }
};
class Com extends View {
    function render($telegraphText): string
    {
        $com = file_get_contents($this->templateName);

        foreach($this->variables as $key) {
            $com = str_replace('<!---' . $key . '---!>', $telegraphText->getAnyValue($key), $com);
        };
        
        return $com;
    }
};

$telegraphTextObject = new TelegraphText('New title', 'New text', 'New author');
$telegraphTextObject->editText('Title 2', 'Text 2');

$swigObject = new Swig('templates/telegraph_text.swig');
$swigObject->addVariablesToTemplate(['slug', 'text']);
$splObject = new Spl('templates/telegraph_text.spl');
$splObject->addVariablesToTemplate(['slug', 'title', 'text']);
$comObject = new Com('templates/telegraph_text.com');
$comObject->addVariablesToTemplate(['slug', 'title', 'text']);

$templateEngines = [$swigObject, $splObject, $comObject];
if($swigObject instanceof IRender) echo $swigObject->render($telegraphTextObject);
if($splObject instanceof IRender) echo $splObject->render($telegraphTextObject);
if($comObject instanceof IRender) echo $comObject->render($telegraphTextObject);
