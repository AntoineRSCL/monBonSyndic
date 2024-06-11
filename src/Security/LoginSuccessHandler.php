<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $security;

    public function __construct(RouterInterface $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        // Assurez-vous que le champ `building` existe sur votre entité `Person`
        $buildingSlug = $user->getBuilding() ? $user->getBuilding()->getSlug() : null;

        if ($buildingSlug) {
            $response = new RedirectResponse($this->router->generate('building_index', ['slug' => $buildingSlug]));
        } else {
            $response = new RedirectResponse($this->router->generate('default_target_path'));
        }

        return $response;
    }
}
