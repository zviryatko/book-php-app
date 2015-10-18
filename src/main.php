<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Container\Container;
use League\Route\RouteCollection;
use League\Route\Http\Exception\NotFoundException;

// Register our classes with dependencies.
$container = new Container;

$container->singleton('twig', function () {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/View');
    $twig = new \Twig_Environment($loader, [
      'cache' => dirname(__DIR__) . '/cache',
      'debug' => true,
    ]);
    $twig->addGlobal('layout', 'layout.twig.html');

    return $twig;
});

$container->singleton('books', function () {
    $books_provider = file_get_contents(dirname(__DIR__) . '/data/book-list.json');
    return json_decode($books_provider);
});

$container->singleton('authors', function () {
    $authors_provider = file_get_contents('data/authors-list.json');
    return json_decode($authors_provider);
});

$container->add('BookPhpApp\Controller\Book')->withArguments(['twig', 'books']);
$container->add('BookPhpApp\Controller\Author')->withArguments(['twig', 'authors']);

// Register routes.
$router = new RouteCollection($container);

$router->addRoute('GET', '/', 'BookPhpApp\Controller\Book::indexAction');
$router->addRoute('GET', '/book/{id:number}', 'BookPhpApp\Controller\Book::bookAction');
$router->addRoute('GET', '/category/{id:number}', 'BookPhpApp\Controller\Book::categoryAction');
$router->addRoute('GET', '/authors', 'BookPhpApp\Controller\Author::indexAction');
$router->addRoute('GET', '/author/{id:number}', 'BookPhpApp\Controller\Author::authorAction');

$dispatcher = $router->getDispatcher();
$request = Request::createFromGlobals();

try {
    $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
    $response->send();
} catch (NotFoundException $exception) {
    $response = new Response;
    $content = $container->get('twig')->render('404.twig.html');
    $response->setContent($content);
    $response->send();
}