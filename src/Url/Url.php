<?php

namespace App\Url;

use App\Url\Parts\CreatedAt;
use App\Url\Parts\Model;

class Url
{
    use CreatedAt;
    use Model;

    private string $name;
    private string $errMsg;

    public function __construct(mixed $params)
    {
        if (is_string($params)) {
            $params = [
                'name' => $params
            ];
        }
        $this->id = $params['id'] ?? null;
        $this->name = $params['name'] ?? '';
        $this->setCreatedAt($params['created_at'] ?? null);
    }

    public function isValid()
    {
        $url = $this->name;
        $schemes = ['http://', 'https://'];
        foreach ($schemes as $checkScheme) {
            if (strpos($url, $checkScheme) !== false) {
                return filter_var($url, FILTER_VALIDATE_URL);
            }
        }
        return false;
    }

    public function getError()
    {
        if (!$this->isValid()) {
            if (!$this->name) {
                $this->errMsg = 'URL не должен быть пустым ';
            } else {
                $this->errMsg = 'Некорректный URL';
            }
        }
        return $this->errMsg;
    }

    public function getName()
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
        ];
    }
}
