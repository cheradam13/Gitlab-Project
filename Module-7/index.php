<?php

$searchRoot = 'test-search';
$searchName = 'test.txt';
$searchResult = [];

function deepSearchFile(string $searchRoot, string $searchName, array &$searchResult)
{
    $searchRootElements = scandir($searchRoot);
    array_shift($searchRootElements);
    array_shift($searchRootElements);

    for($i = 0; $i < count($searchRootElements); $i++) {
        if(is_dir($searchRoot . '\\' . $searchRootElements[$i])) {
            deepSearchFile($searchRoot . '\\' . $searchRootElements[$i], $searchName, $searchResult);
        } else {
            if($searchRootElements[$i] === $searchName) {
                array_push($searchResult, $searchRoot . '\\' . $searchRootElements[$i]);
            };
        };
    };
};
deepSearchFile($searchRoot, $searchName, $searchResult);

$searchResultFiltered = array_filter($searchResult, 'filesize');
if(count($searchResultFiltered) > 0) {
    var_dump($searchResultFiltered);
} else {
    echo 'Ничего не найдено :(';
}
