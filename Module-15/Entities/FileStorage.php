<?php

namespace Entities;
use Entities\Storage;
require_once 'getNextEndingFunc.php';

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
