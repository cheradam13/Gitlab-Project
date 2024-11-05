<?php

namespace Core;
use Entities\View;

class Com extends View {
    function render($telegraphText): string
    {
        $com = file_get_contents($this->templateName);

        foreach($this->variables as $key) {
            $com = str_replace('<!---' . $key . '---!>', $telegraphText->getAnyValue($key), $com);
        };
        
        return $com;
    }
};
