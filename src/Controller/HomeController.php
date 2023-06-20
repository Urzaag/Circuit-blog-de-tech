<?php

namespace App\Controller;

use App\Model\ArticleManager;
use App\Model\ArticleSectionManager;

class HomeController extends AbstractController
{
    public function index(): string
    {
        $articleSectionMgr = new ArticleSectionManager();
        $articles = $articleSectionMgr->selectFirstNineArticleByDate();

        $articleManager = new ArticleManager();
        $pictureController = new PictureController();

        $pictures = [];
        $author = [];
        foreach ($articles as $article) {
            $pictures['id_article: ' . $article['id_article']] = $articleManager->getPictures($article['id_article']);
            $pictures[
                'id_article: ' . $article['id_article']] = $pictureController->organisePictures($pictures[
                    'id_article: ' . $article['id_article']]);
            $author['id_article: ' . $article['id_article']] = $articleManager->getAuthor($article['id_article']);
        }

        $navbarController = new NavbarController();
        $navbarController->modalLogin();

        return $this->twig->render('Home/index.html.twig', [
            'articles' => $articles,
            'pictures' => $pictures,
            'author' => $author]);
    }
}
