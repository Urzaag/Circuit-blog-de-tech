<?php

namespace App\Controller;

use App\Model\CommentManager;

class CommentController extends AbstractController
{
    public function addNewComment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['SendComment'])) {
            $comment = new CommentManager();
            $comment->insertComment();
        }
    }

    public function selectCommentsWithUsernames(): array
    {
        $commentManager = new CommentManager();
        return $commentManager->selectCommentsWithUsernames();
    }

    public function editComment($id): ?string
    {
            $commentManager = new CommentManager();
            $comment = $commentManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newComment = array_map('trim', $_POST);

            $commentManager = new CommentManager();
            $commentManager->updateComment($newComment);

            header('Location: /user/show');
        }
            return $this->twig->render('Article/Form-edit-comment.html.twig', ['comment' => $comment]);
    }

    public function deleteComment($id)
    {
        $commentManager = new CommentManager();
        $commentManager->selectOneById($id);

        $commentManager = new CommentManager();
        $commentManager->deleteComment();

        header('Location: /user/show');
    }
}
