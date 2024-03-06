<?php

namespace App\Url\Parts;

trait Model
{
    private bool $isNew = false;
    private ?int $id;

    public function isNew()
    {
        return $this->isNew === true;
    }

    public function setNew()
    {
        $this->isNew = true;
    }

    public function setId(string|false $id)
    {
        $this->id = (int)$id;
    }

    public function getId()
    {
        return $this->id;
    }
}
