<?php

namespace App\Url\Parts;

trait Model
{
    private $isNew = false;
    private $id;

    public function isNew()
    {
        return $this->isNew === true;
    }

    public function setNew()
    {
        $this->isNew = true;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
