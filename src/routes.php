<?php

session_start();

return [
    '' => ['HomeController', 'index',],

    'items' => ['ItemController', 'index',],
    'items/edit' => ['ItemController', 'edit', ['id']],
    'items/show' => ['ItemController', 'show', ['id']],
    'items/add' => ['ItemController', 'add',],
    'items/delete' => ['ItemController', 'delete',],
    'article/show' => ['ArticleController', 'show', ['id']],
    'article/add' => ['ArticleController', 'add',],
    'user/show' => ['UserController', 'index', ['id']],
    'user/delete' => ['UserController', 'delete'],
    'user/edit' => ['UserController', 'edit'],
    'article/edit' => ['ArticleController', 'edit',['id']],
    'article/delete' => ['ArticleController', 'delete', ['id']],
    'comment/edit' => ['CommentController', 'editComment', ['id']],
    'comment/delete' => ['CommentController', 'deleteComment', ['id']],
    'contact' => ['ContactController', 'index',],
    'mentions-legales' => ['LegalNoticeController', 'legalNotice'],
    'politique-de-confidentialite' => ['LegalNoticeController', 'privacyPolicy'],
    'CGU' => ['LegalNoticeController', 'cgu'],
    'RGPD' => ['LegalNoticeController', 'rgpd'],
];
