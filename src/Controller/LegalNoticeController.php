<?php

namespace App\Controller;

class LegalNoticeController extends AbstractController
{
    public function legalNotice(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        return $this->twig->render('Legal/bt-legal-notice.html.twig');
    }

    public function privacyPolicy(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        return $this->twig->render('Legal/bt-privacy-policy.html.Twig');
    }

    public function rgpd(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        return $this->twig->render('Legal/bt-RGPD.html.twig');
    }

    public function cgu(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        return $this->twig->render('Legal/bt-CGU.html.twig');
    }
}
