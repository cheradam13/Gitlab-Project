<?php

$textStorage = [];

function add(array &$arr, string $title, string $text): void
{
    array_push($arr, ['title' => $title, 'text' => $text]);
};
add($textStorage, 'This is title 1', 'This is description 1');
add($textStorage, 'This is title 2', 'This is description 2');

var_dump($textStorage);

function remove(array &$arr, int $index): bool
{
    if (isset($arr[$index])) {
        unset($arr[$index]);
        return true;
    };
    return false;
};
remove($textStorage, 0);
remove($textStorage, 5);

var_dump($textStorage);

function edit(int $index, string $title, string $newText, array &$arr): bool
{
    if (isset($arr[$index])) {
        $arr[$index][$title] = $newText;
        return true;
    };
    return false;
};
edit(1, 'title', 'New Title', $textStorage);

var_dump(edit(10, 'title', 'New Title', $textStorage));
var_dump($textStorage);
