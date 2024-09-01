<?php
namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 * @method User getUser()
 */
abstract class BaseController extends AbstractController
{
    protected function getUser(): User
    {
        return parent::getUser();
    }

    protected function ensureUserIsAuthenticated()
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }
        
        return null; // Връща null, ако потребителят е логнат
    }

}