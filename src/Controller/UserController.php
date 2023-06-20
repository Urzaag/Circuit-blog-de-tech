<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\CommentManager;

class UserController extends AbstractController
{
    public function login(): array
    {
        $errors = [];
        $userLogin = $this->sanitizeData($_POST, self::FIELDS_LOGIN);
        $errors = $this->validateData($userLogin, self::FIELDS_LOGIN);
        $userManager = new UserManager();
        $user = $userManager->selectOneByUsername($userLogin['username']);
        if (!$user) {
            $errors['username']['fatal']['user!Exists'] = "Cet utilisateur n'existe pas";
        }
        if (empty($errors)) {
            $password = $userLogin['password'];
            $username = $user['user_name'];
            $hashPassword = $user['user_password'];
            if (password_verify($password, $hashPassword)) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $username;
            } else {
                $errors['password']['fatal']['password!Match'] = "Mot de passe ou pseudo incorrect";
            }
        }
        return $errors;
    }

    public function register(): array
    {
        $errors = [];
        $userRegister = $this->sanitizeData($_POST, self::FIELDS_REGISTER);
        $errors = $this->validateData($userRegister, self::FIELDS_REGISTER);

        if (empty($errors)) {
            $userManager = new UserManager();
            $id = $userManager->insert($userRegister);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $userRegister['new-username'];
        }
        return $errors;
    }

    public function disconnect(): void
    {
        session_unset();
        session_destroy();
    }

    public function show(): string
    {
        $navbarController = new NavbarController();
        $navbarController->modalLogin();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
        } else {
            $id = $_SESSION['user_id'];

            $userManager = new UserManager();
            $user = $userManager->selectOneById($id);

            $userManager = new UserManager();
            $articles = $userManager->getUserArticlesWithPhotos($id);

            $commentManager = new CommentManager();
            $comments = $commentManager->getAllCommentsByUserId();

            return $this->twig->render('User/index.html.twig', [
                'id' => $id,
                'user' => $user,
                'articles' => $articles,
                'comments' => $comments,
            ]);
        }
        return $this->twig->render('User/index.html.twig');
    }

    public function edit(): string
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
        } else {
            $userManager = new UserManager();

            $id = $_SESSION['user_id'];

            $user = $userManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editUserSubmit'])) {
                array_pop($_POST);
                $userEdit = $_POST;
                $userEdit = $this->sanitizeData($userEdit, self::FIELDS_EDIT);

                $userEdit['id'] = $_SESSION['user_id'];

                $userManager = new UserManager();
                $id = $userManager->update($userEdit);
                header('Location: /user/show');
            }

            $this->delete();


            return $this->twig->render('User/edit.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->twig->render('User/edit.html.twig');
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUserSubmit'])) {
            $id = trim($_SESSION['user_id']);
            $userManager = new UserManager();
            $userManager->delete((int)$id);

            header('Location:/');
        }
    }

    public function index(): string
    {
        $id = $_SESSION['user_id'];

        $userManager = new userManager();
        $userManager->selectOneById($id);

        $userManager = new UserManager();
        $articles = $userManager->getUserArticlesWithPhotos($id);

        $commentManager = new CommentManager();
        $comments = $commentManager->getAllCommentsByUserId();

        return $this->twig->render('User/index.html.twig', [
            'id' => $id,
            'articles' => $articles,
            'comments' => $comments,
        ]);
    }
}
