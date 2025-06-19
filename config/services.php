<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // controllers
    $services->load('App\\Controller\\', '../src/Controller')
        ->tag('controller.service_arguments');

    // repositories
    $services->load('App\\Repository\\', '../src/Repository')
        ->tag('doctrine.repository_service');

    // form types
    $services->load('App\\Form\\', '../src/Form')
        ->tag('form.type');
};
