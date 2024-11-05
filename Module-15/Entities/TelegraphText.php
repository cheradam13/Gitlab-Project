<?php

namespace Entities;
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
