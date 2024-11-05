<?php

class TelegraphText {
    public string $title, $text, $author, $published, $slug;
    const TXT_FILE_EXTENSION = '.txt';

    public function __construct(string $title, string $text, string $author)
    {
        $this->title = $title;
        $this->text = $text;
        $this->author = $author;
        $this->published = '15.08.2024 14:00';
        $this->slug = str_replace(' ', '-', $this->title);
    }

    public $infoArr = [];
    public function storeText(): string
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

    public function loadText(string $slug): ?array
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
    public number $id;
    public string $name;
    public $role;

    public abstract function getTextsToEdit();
}

function getNextEnding(string $oldFileName) {
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

$filesStorageObject = new FileStorage();
$telegraphTextObject = new TelegraphText('New title', 'New text', 'New author');
$telegraphTextObjectTwo = new TelegraphText('Hello, it is title2', 'Hello, it is text2', 'Hello, it is author2');
$telegraphTextObjectThree = new TelegraphText('Hello, it is title3', 'Hello, it is text3', 'Hello, it is author3');
var_dump( $filesStorageObject->read($filesStorageObject->create($telegraphTextObject)) );
$filesStorageObject->delete( $filesStorageObject->create($telegraphTextObjectTwo) );
var_dump( $filesStorageObject->list() );
