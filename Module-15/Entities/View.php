<?php

namespace Entities;
use Interfaces\IRender;

abstract class View implements IRender {
    protected string $templateName;
    protected array $variables;
    function __construct(string $templateName)
    {
        $this->templateName = $templateName;
    }

    public function addVariablesToTemplate(array $variables)
    {
        $this->variables = $variables;
    }
};
