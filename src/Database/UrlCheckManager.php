<?php

namespace App\Database;

use App\Url\UrlCheck;

class UrlCheckManager extends DBManager
{
    public function save(UrlCheck $urlCheck)
    {
        $columns = [
            'url_id',
            'status_code',
            'h1',
            'title',
            'description',
            'created_at'
        ];

        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", array_map(fn($a) => (":$a"), $columns));

        $sql = "INSERT INTO url_checks ({$columnsStr}) VALUES ({$valuesStr})";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue('url_id', $urlCheck->getUrlId());
        $stmt->bindValue('status_code', $urlCheck->getStatusCode());
        $stmt->bindValue('h1', $urlCheck->getH1());
        $stmt->bindValue('title', $urlCheck->getTitle());
        $stmt->bindValue('description', $urlCheck->getDescription());
        $stmt->bindValue('created_at', $urlCheck->getCreatedAt());
        $stmt->execute();
    }
}
