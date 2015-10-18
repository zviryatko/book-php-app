<?php
/**
 * @file
 *
 */

namespace BookPhpApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Route\Http\Exception\NotFoundException;

class Author
{
    protected $twig;
    protected $authors;

    public function __construct(\Twig_Environment $twig, $authors)
    {
        $this->twig = $twig;
        $this->authors = $authors;
    }

    public function indexAction(Request $request, Response $response)
    {
        $content = $this->twig->render('authors.twig.html', [
          'authors' => $this->authors,
        ]);

        $response->setContent($content);

        return $response;
    }

    public function authorAction(Request $request, Response $response, array $args)
    {
        $authors = array_filter($this->authors, function ($book) use ($args) {
            return $book->id === $args['id'];
        });

        if (empty($authors)) {
            throw new NotFoundException;
        }

        $content = $this->twig->render('author.twig.html', ['author' => reset($authors)]);

        $response->setContent($content);

        return $response;
    }
}