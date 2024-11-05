<?php

require_once 'autoload.php';
use Entities\TelegraphText;
use Entities\Storage;
use Entities\FileStorage;
use Entities\User;
use Entities\View;
use Interfaces\IRender;
use Core\Com;
use Core\Spl;
use Core\Swig;

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
