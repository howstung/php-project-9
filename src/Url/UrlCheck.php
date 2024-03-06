<?php

namespace App\Url;

use App\Url\Parts\CreatedAt;
use App\Url\Parts\Model;

class UrlCheck
{
    use CreatedAt;
    use Model;

    private int $url_id;
    private int $status_code;
    private string $h1;
    private string $title;
    private string $description;

    private const LENGTH_VARCHAR = 255;

    public function __construct(array $params)
    {
        $this->url_id = $params['url_id'];
        $this->status_code = $params['status_code'];
        $this->h1 = $params['h1'];
        $this->title = $params['title'];
        $this->description = $params['description'];
        $this->setCreatedAt($params['created_at'] ?? null);
    }


    public function getStatusCode()
    {
        return $this->status_code;
    }

    private function makeVarchar(string $string)
    {
        return mb_strlen($string) > $this::LENGTH_VARCHAR ? mb_substr($string, 0, $this::LENGTH_VARCHAR - 1) : $string;
    }

    public function getH1()
    {
        return $this->makeVarchar($this->h1);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getUrlId()
    {
        return $this->url_id;
    }
}
