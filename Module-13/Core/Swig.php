<?php

namespace Core;
use Entities\View;

class Swig extends View {
    function render($telegraphText): string
    {
        $swig = file_get_contents($this->templateName);

        foreach($this->variables as $key) {
            $swig = str_replace('{{ ' . $key . ' }}', $telegraphText->getAnyValue($key), $swig);
        };

        return $swig;
    }
};
