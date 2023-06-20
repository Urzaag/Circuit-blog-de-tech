<?php

namespace App\Controller;

class ContactController extends AbstractController
{
    public function index(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        return $this->twig->render('Contact/index.html.twig');
    }
}
