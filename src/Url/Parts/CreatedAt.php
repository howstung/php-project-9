<?php

namespace App\Url\Parts;

use Carbon\Carbon;

trait CreatedAt
{
    private $created_at;

    public function setCreatedAt($created_at)
    {
        //$this->created_at = date("Y-m-d H:i:s");
        $this->created_at = isset($created_at) ? new Carbon($created_at) : Carbon::now();
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
