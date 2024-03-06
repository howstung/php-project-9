<?php

namespace App\Database;

use App\Url\Url;

class UrlManager extends DBManager
{
    public function save(Url $Url)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE name = ?");
        $stmt->execute([$Url->getName()]);
        $resultCheck = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($resultCheck) === 0) {
            $stmt = $this->pdo->prepare("INSERT INTO urls (name, created_at) VALUES (?, ?)");
            $stmt->execute([$Url->getName(), $Url->getCreatedAt()]);

            $Url->setId($this->pdo->lastInsertId());
            $Url->setNew();
        } else {
            $Url->setId($resultCheck[0]['id']);
        }
    }

    public function getUrlById(int $id): ?Url
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE id = ?;");
        $stmt->execute([$id]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) !== 0 ? new Url($result[0]) : null;
    }

    public function getAllUrls()
    {
        //$stmt = $this->pdo->prepare("SELECT * FROM urls ORDER BY id DESC;");
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM urls
            LEFT JOIN 
                (SELECT url_id,
                     MAX(created_at) AS updated_at
                FROM url_checks
                GROUP BY  url_id) AS last_update
                ON urls.id = last_update.url_id
            LEFT JOIN 
                (SELECT url_id, status_code, h1, title, description, created_at
                FROM url_checks) AS checks
                ON urls.id = checks.url_id
                    AND last_update.updated_at = checks.created_at ORDER BY urls.id DESC;
		");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getChecksByUrl(Url $Url)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM url_checks WHERE url_id = ? ORDER BY created_at DESC;");
        $stmt->execute([$Url->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
