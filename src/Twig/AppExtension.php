<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_building_slug', [$this, 'getBuildingSlug']),
        ];
    }

    public function getBuildingSlug(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        $path = $request->getPathInfo();
        $matches = [];
        
        // Assuming your slug format is "/building/{slug}/..."
        if (preg_match('#/building/([^/]+)#', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
