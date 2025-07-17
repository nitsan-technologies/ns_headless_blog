<?php
declare(strict_types=1);

namespace NITSAN\NsHeadlessBlog\Configuration\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator, ContainerBuilder $container) {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->set('NITSAN\\NsHeadlessBlog\\Controller\\BlogController')
        ->public()
        ->tag('backend.controller');
}; 