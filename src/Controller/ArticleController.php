<?php

namespace App\Controller;

use App\Model\ArticleManager;

class ArticleController extends AbstractController
{
    public function show(int $id): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        if (!empty($_SESSION)) {
            $userId = $_SESSION['user_id'];
        } else {
            $userId = "";
        }

        $articleManager = new ArticleManager();
        $pictureController = new PictureController();
        $article = $articleManager->selectOneById($id);

        $bodyArticleSplit = $this->splitArticleText($article['body_article']);

        $pictures = $articleManager->getPictures($id);
        $pictures = $pictureController->organisePictures($pictures);

        $author = $articleManager->getAuthor($id);

        $commentController = new CommentController();
        $commentController->addNewComment();

        $commentController = new CommentController();
        $showComments = $commentController->selectCommentsWithUsernames();

        $articleManager = new ArticleManager();
        $previousId = $articleManager->getPreviousArticle($id);

        $articleManager = new ArticleManager();
        $nextId = $articleManager->getNextArticle($id);

        $articleManager = new ArticleManager();
        $linkPreviousPhoto = $articleManager->getPreviousPhoto($id);

        $articleManager = new ArticleManager();
        $linkNextPhoto = $articleManager->getNextPhoto($id);


        return $this->twig->render('Article/show.html.twig', [
            'article' => $article,
            'bodyArticleSplit' => $bodyArticleSplit,
            'pictures' => $pictures,
            'author' => $author,
            'showComments' => $showComments,
            'previousId' => $previousId,
            'nextId' => $nextId,
            'linkPreviousPhoto' => $linkPreviousPhoto,
            'linkNextPhoto' => $linkNextPhoto,
            'userId' => $userId,

            ]);
    }

    public function splitArticleText(string $bodyArticle): array
    {
        return str_split($bodyArticle, (strlen($bodyArticle) - strlen($bodyArticle) % 3) / 3);
    }

    public function add(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['articleAddSubmit'])) {
            // clean $_POST data
            $article = array_map('trim', $_POST);

            $articleManager = new ArticleManager();
            $id = $articleManager->insert($article);

            $pictureController = new PictureController();
            $pictureController->add($id);
            header('Location:/user/show');
        }
        return $this->twig->render('Article/add.html.twig');
    }


    public function edit(int $id): string
    {
        $articleManager = new ArticleManager();
        $article = $articleManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $article = array_map('trim', $_POST);
            $articleManager->update($article);
            header('Location:/user/show');
        }
        return $this->twig->render('Article/edit.html.twig', ['article' => $article]);
    }

    public function delete($id)
    {
        $articleManager = new ArticleManager();
        $articleManager->deletePhotos($id);

        $articleManager = new ArticleManager();
        $articleManager->deleteComments($id);

        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);

        header('Location: /user/show');
    }
}
