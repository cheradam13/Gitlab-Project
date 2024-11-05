<?php

spl_autoload_register(function($classname) {
    $classname = str_replace('\\', DIRECTORY_SEPARATOR, $classname);
    $fileName = __DIR__ . DIRECTORY_SEPARATOR . $classname . '.php';
    if(file_exists($fileName)) {
        require_once $fileName;
    }
});

include_once 'vendor/autoload.php';
