<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi;

use Mazi\AdrRestApi\DependencyInjection\MaziAdrRestApiExtension;
use MiddleOffice\DependencyInjection\ActionsRouteAnnotationsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MaziAdrRestApiBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MaziAdrRestApiExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ActionsRouteAnnotationsPass());
    }
}
