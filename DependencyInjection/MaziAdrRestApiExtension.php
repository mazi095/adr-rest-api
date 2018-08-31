<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DependencyInjection;

use Mazi\AdrRestApi\Action\ActionInterface;
use Mazi\AdrRestApi\EventSubscriber\ApiResponseSubscriber;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Mazi\AdrRestApi\Action\ActionLoader;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

class MaziAdrRestApiExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        if ($config['logger'] === "default"){
            $logger = NullLogger::class;
        }else {
            $logger =  $config['logger'];
        }
        $container->register('api_logger', $logger);

        if ($config['subscribers']['api_response_subscriber']) {
            $container->register('Mazi\AdrRestApi\EventSubscriber\ApiResponseSubscriber', ApiResponseSubscriber::class)
                ->addArgument(new Reference('jms_serializer'))
                ->addArgument(new Reference(UrlGeneratorInterface::class))
                ->addArgument(new Reference('api_logger'))
                ->addTag('kernel.event_subscriber');
        }

        $container->register(ActionLoader::class, ActionLoader::class)
            ->addArgument(LoaderResolverInterface::class)
            ->addTag('routing.loader');

        $container->registerForAutoconfiguration(ActionInterface::class)
            ->addTag('controller.service_arguments')
            ->addTag('route.annotation');
    }
}
