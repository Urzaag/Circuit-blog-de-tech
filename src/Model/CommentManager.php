<?php

namespace App\Model;

use PDO;

class CommentManager extends AbstractManager
{
    public const TABLE = 'bt_comment';
    public function insertComment(): bool
    {

        $userId = $_SESSION['user_id'];

        $content = $_POST['comment'];
        $articleId = $_GET['id'];

        // Prepare SQL statement
        $stmt = $this->pdo->prepare(
            "INSERT INTO " . self::TABLE . "
            (article_id, user_id, content_comment, comment_created_at)
            VALUES (?, ?, ?, NOW())"
        );
        $stmt->bindParam(1, $articleId, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);
        $stmt->bindParam(3, $content, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function selectCommentsWithUsernames(): array
    {
        $statement = $this->pdo->prepare(
            "SELECT c.*, u.user_name
         FROM bt_comment c
         INNER JOIN bt_user u ON c.user_id = u.id_user
         WHERE c.article_id = :article_id"
        );
        $statement->execute(['article_id' => $_GET['id']]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateComment($newComment): bool
    {
        $commentId = $_GET['id'];
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `content_comment` =
         :comment WHERE id_comment = :comment_id");
        $statement->bindValue('comment', $newComment['content_comment'], PDO::PARAM_STR);
        $statement->bindValue('comment_id', $commentId, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function deleteComment(): bool
    {
        $commentId = $_GET['id'];
            $statement = $this->pdo->prepare("
            DELETE FROM " . self::TABLE . " WHERE id_comment = :id");
            $statement->bindValue('id', $commentId);

            return $statement->execute();
    }
    public function getAllCommentsByUserId(): array
    {
        $userId = $_SESSION['user_id'];

        $statement = $this->pdo->prepare("
        SELECT c.*, a.*
        FROM bt_comment c
        JOIN bt_article a ON c.article_id = a.id_article
        WHERE c.user_id = :id");
        $statement->bindValue(':id', $userId);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
