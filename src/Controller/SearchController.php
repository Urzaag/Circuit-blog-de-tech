<?php

namespace App\Controller;

class SearchController extends AbstractController
{
    public function index(): string
    {
        return $this->twig->render('Search/index.html.twig', ['items' => $items]);
    }
}
