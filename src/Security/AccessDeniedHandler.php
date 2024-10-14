<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;


class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        
        // Пренасочване към логин страницата
       // return new Response('Достъпът е отказан! Нямате права за тази страница.', 403);
        return new RedirectResponse($this->router->generate('app_login'));
    }
}