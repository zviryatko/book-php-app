<?php
/**
 * @file
 *
 */

namespace BookPhpApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Route\Http\Exception\NotFoundException;

class Book
{
    protected $twig;
    protected $books;

    public function __construct(\Twig_Environment $twig, $books)
    {
        $this->twig = $twig;
        $this->books = $books;
    }

    public function indexAction(Request $request, Response $response)
    {
        $content = $this->twig->render('home.twig.html', [
          'books' => $this->books,
        ]);

        $response->setContent($content);

        return $response;
    }

    public function bookAction(Request $request, Response $response, array $args)
    {
        $books = array_filter($this->books, function ($book) use ($args) {
            return $book->id === $args['id'];
        });

        if (empty($books)) {
            throw new NotFoundException;
        }

        $content = $this->twig->render('book.twig.html', ['book' => reset($books)]);

        $response->setContent($content);

        return $response;
    }

    public function categoryAction(Request $request, Response $response, array $args)
    {
        $books = array_filter($this->books, function ($book) use ($args) {
            return $book->categoryID === $args['id'];
        });

        if (empty($books)) {
            throw new NotFoundException;
        }

        $category = '';
        $firstBook = reset($books);
        if ($firstBook) {
            $category = $firstBook->category;
        }

        $content = $this->twig->render('category.twig.html', [
          'books' => $books,
          'category' => $category,
        ]);

        $response->setContent($content);

        return $response;
    }
}