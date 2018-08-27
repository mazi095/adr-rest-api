<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DependencyInjection;

use Mazi\AdrRestApi\Action\ActionLoader;
use Mazi\AdrRestApi\Action\ActionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ActionsRouteAnnotationsPass.
 */
class ActionsRouteAnnotationsPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ActionLoader::class)) {
            return;
        }

        $definition = $container->findDefinition(ActionLoader::class);

        $actions = $container->findTaggedServiceIds('route.annotation');
        foreach ($actions as  $id => $tags) {
            $definition->addMethodCall('addAction', [$id]);
        }
    }
}
