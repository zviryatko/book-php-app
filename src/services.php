<?php

use League\Container\Container;

$container = new Container;

$container->add('twig', function () {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/view');
    $twig = new \Twig_Environment($loader, [
      'cache' => dirname(__DIR__) . '/cache',
      'debug' => true,
    ]);
    $twig->addGlobal('layout', 'layout.twig.html');

    return $twig;
});

$container->add('books', function () {
    $books_provider = file_get_contents(dirname(__DIR__) . '/data/book-list.json');
    return json_decode($books_provider);
});

$container->add('authors', function () {
    $authors_provider = file_get_contents('data/authors-list.json');
    return json_decode($authors_provider);
});

return $container;