<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    // public function getFunctions(): array
    // {
    //     return [
    //         new TwigFunction('slugify', [AppExtensionRuntime::class, 'slugify']),
    //     ];
    // }

    public function slugify($string = '')
    {



        $string = mb_strtolower(preg_replace('/[^A-Za-z0-9 ]+/', '', $string), 'UTF-8');
        $string = mb_strtolower(preg_replace("/ +/", "-", trim($string)));

        return $string;
    }
}
