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

$objectTelegraphText = new TelegraphText('New title', 'New text', 'New author');
$objectTelegraphText->editText('This is the second title', 'This is the second text');
$objectTelegraphText->storeText();
$objectTelegraphTextSeparateTitle = $objectTelegraphText->loadText($objectTelegraphText->storeText())['title'];
$objectTelegraphText->editText('Hello from new title!', 'Hello from new text!');
$objectTelegraphText->storeText();
$objectTelegraphTextSeparateText = $objectTelegraphText->loadText($objectTelegraphText->storeText())['text'];
