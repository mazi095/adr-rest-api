<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\Action;

use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class ActionLoader.
 */
class ActionLoader
{
    /**
     * @var array
     */
    private $actions = [];
    /**
     * @var LoaderResolverInterface
     */
    private $loaderResolver;

    public function __construct(LoaderResolverInterface $loaderResolver)
    {
        $this->loaderResolver = $loaderResolver;
    }

    /**
     * @param ActionInterface[] $action
     */
    public function addAction($action): void
    {
        $this->actions[] = $action;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return RouteCollection
     * @throws \Exception
     */
    public function loadRoutes(): RouteCollection
    {
        $collection = new RouteCollection();

        foreach ($this->actions as $action) {
            $collection->addCollection(
                $this->loaderResolver->resolve($action, 'annotation')
                    ->load($action, 'annotation')
            );
        }

        return $collection;
    }
}
