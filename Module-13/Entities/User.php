<?php

namespace Entities;
abstract class User {
    protected $id;
    protected string $name;
    protected $role;

    public abstract function getTextsToEdit();
}
