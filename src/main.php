<?php

namespace zviryatko\BookApp;

use FastRoute\BadRouteException;
use League\Container\Container;

$container = new Container;

$container->add('twig', function () {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/view');
    return new \Twig_Environment($loader, [
      'cache' => dirname(__DIR__) . '/cache',
      'debug' => true,
    ]);
});

$twig = $container->get('twig');
$twig->addGlobal('layout', 'layout.twig.html');

$container->add('books', function () {
    $books_provider = file_get_contents(dirname(__DIR__) . '/data/book-list.json');
    return json_decode($books_provider);
});

$container->add('authors', function () {
    $authors_provider = file_get_contents('data/authors-list.json');
    return json_decode($authors_provider);
});

/**
 * @var \FastRoute\Dispatcher $dispatcher
 */
$dispatcher = \FastRoute\simpleDispatcher(
  function (\FastRoute\RouteCollector $r) use ($twig, $container) {
      $r->addRoute('GET', '/', function () use ($twig, $container) {
          return $twig->render('home.twig.html', [
            'books' => $container->get('books'),
          ]);
      });
      $r->addRoute('GET', '/author-list', function () use ($twig, $container) {
          return $twig->render('author-list.twig.html', [
            'author' => $container->get('authors'),
          ]);
      });
      $r->addRoute('GET', '/book/{id:\d+}', function ($id) use ($twig, $container) {
          $books = $container->get('books');
          foreach ($books as $book) {
              if ($book->id === $id) {
                  return $twig->render('book.twig.html', array('book' => $book));
              }
          }

          return [\FastRoute\Dispatcher::NOT_FOUND];
      });
      $r->addRoute('GET', '/author/{id:\d+}', function ($id) use ($twig, $container) {
          $authors = $container->get('authors');
          foreach ($authors as $author) {
              if ($author->id === $id) {
                  return $twig->render('author.twig.html', array('author' => $author));
              }
          }

          return [\FastRoute\Dispatcher::NOT_FOUND];
      });
      $r->addRoute('GET', '/category/{id:\d+}', function ($id) use ($twig, $container) {
          $books = array_filter($container->get('books'), function ($book) use ($id) {
              return $book->categoryID === $id;
          });
          return $twig->render('category.twig.html', ['books' => $books]);
      });
  }
);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        echo $twig->render('404.twig.html');
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo $twig->render('405.twig.html',
          ['allowedMethods' => $allowedMethods]);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo call_user_func_array($handler, $vars);
        break;
}

