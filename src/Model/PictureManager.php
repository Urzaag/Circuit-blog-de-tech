<?php

namespace App\Model;

use PDO;

class PictureManager extends AbstractManager
{
    public const TABLE = 'bt_picture';

    public function insert(array $files, int $id): void
    {
        foreach ($files as $position => $file) {
            if ($position === 0) {
                $isMain = 1;
            } else {
                $isMain = 0;
            }
            $query = "INSERT INTO " . self::TABLE .
                "(article_id, link, is_main)
            VALUES (:article_id, :link, :is_main)";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':article_id', $id, PDO::PARAM_INT);
            $statement->bindValue(':link', $file, PDO::PARAM_STR);
            $statement->bindValue(':is_main', $isMain, PDO::PARAM_BOOL);
            $statement->execute();
        }
    }
}
