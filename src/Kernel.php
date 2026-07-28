<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Application kernel for the OrangeHRM Demo application.
 *
 * The Leave Balance Alert capability is deployed inside the existing
 * application boundary as a modular monolith extension.
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
