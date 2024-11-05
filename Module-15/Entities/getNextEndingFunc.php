<?php

function getNextEnding(string $oldFileName): string
{
    $allFiles = scandir('./');
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

?>