<?php

namespace App\Url\Parts;

use Carbon\Carbon;

trait CreatedAt
{
    private Carbon $created_at;

    public function setCreatedAt(?string $created_at)
    {
        //$this->created_at = date("Y-m-d H:i:s");
        $this->created_at = !is_null($created_at) ? new Carbon($created_at) : Carbon::now();
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
