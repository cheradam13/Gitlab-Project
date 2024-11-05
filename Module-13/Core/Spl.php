<?php

namespace Core;
use Entities\View;

class Spl extends View {
    function render($telegraphText): string
    {
        $spl = file_get_contents($this->templateName);

        foreach($this->variables as $key) {
            $spl = str_replace('$$' . $key . '$$', $telegraphText->getAnyValue($key), $spl);
        };
        
        return $spl;
    }
};
