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
