<?php

namespace Entities;
abstract class Storage {
    public abstract function create($object): string;
    public abstract function read(string $slug);
    public abstract function update(string $slug, $newObject);
    public abstract function delete(string $slug);
    public abstract function list();
}
