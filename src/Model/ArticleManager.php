<?php

namespace App\Model;

use PDO;

class ArticleManager extends AbstractManager
{
    public const TABLE = 'bt_article';

    public function getPictures(int $id): array|false
    {
        $statement = $this->pdo->prepare(
            "SELECT link, " . $this->getForeignKeyNameFromTable(static::TABLE) . "
                    FROM bt_picture p
                        INNER JOIN " . static::TABLE . " ba
                        ON p." . $this->getForeignKeyNameFromTable(static::TABLE) . "
                        = ba." . $this->getPrimaryKeyNameFromTable() . "
                        WHERE " . $this->getForeignKeyNameFromTable(static::TABLE) . " = :id"
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $article): int
    {
        $query = "INSERT INTO " . self::TABLE .
            "(user_id, title, description_article, body_article)
            VALUES (:user_id, :title, :description, :body)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $article['title'], PDO::PARAM_STR);
        $statement->bindValue(':description', $article['description'], PDO::PARAM_STR);
        $statement->bindValue(':body', $article['content'], PDO::PARAM_STR);
        $statement->execute();

        return (int)$this->pdo->lastInsertId();
    }

    public function getAuthor(int $id): array|false
    {
        $statement = $this->pdo->prepare(
            "SELECT user_name
                    FROM bt_user u
                        INNER JOIN " . static::TABLE . " ba
                        ON u." . $this->getPrimaryKeyNameFromTable('bt_user') . "
                        = ba." . $this->getForeignKeyNameFromTable('bt_user') . "
                        WHERE ba." . $this->getPrimaryKeyNameFromTable('bt_article') . " = :id"
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function update(array $article): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `title` = :title,
        `description_article` = :description, `body_article` = :content WHERE id_article=:id");

        $statement->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $article['title']);
        $statement->bindValue(':description', $article['description']);
        $statement->bindValue(':content', $article['content']);

        return $statement->execute();
    }

    public function getPreviousArticle($id): array
    {
        $id = $_GET['id'];

        $statement = $this->pdo->prepare(
            "SELECT id_article, title FROM bt_article WHERE id_article < :id ORDER BY id_article DESC LIMIT 1"
        );
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getNextArticle($id): array
    {
        $id = $_GET['id'];

        $statement = $this->pdo->prepare(
            "SELECT id_article, title FROM bt_article WHERE id_article > :id ORDER BY id_article LIMIT 1"
        );
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPreviousPhoto($id): array
    {
        $id = $_GET['id'];

        $statement = $this->pdo->prepare(
            "SELECT link FROM bt_picture WHERE article_id < :id AND is_main = 1 ORDER BY article_id DESC LIMIT 1"
        );
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNextPhoto($id): array
    {
        $id = $_GET['id'];

        $statement = $this->pdo->prepare(
            "SELECT link FROM bt_picture WHERE article_id > :id AND is_main = 1 ORDER BY article_id LIMIT 1"
        );
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePhotos($id)
    {
        $id = $_GET['id'];
        $statement = $this->pdo->prepare("DELETE FROM bt_picture WHERE article_id = :id");
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();
    }

    public function deleteComments($id)
    {
        $id = $_GET['id'];
        $statement = $this->pdo->prepare("DELETE FROM bt_comment WHERE article_id = :id");
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();
    }

    public function deleteArticle($id)
    {
        $id = $_GET['id'];
        $statement = $this->pdo->prepare("DELETE FROM bt_article WHERE id_article = :id");
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();
    }

    public function selectAllArticles()
    {
        $statement = $this->pdo->prepare("SELECT * FROM bt_article");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
