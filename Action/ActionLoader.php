<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\Action;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class ActionLoader.
 */
class ActionLoader extends Loader
{

    private $isLoaded = false;


    /**
     * @var array
     */
    private $actions = [];


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


    public function load($resource, $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "api_actions" loader twice');
        }

        $collection = new RouteCollection();

        foreach ($this->actions as $action) {
            $collection->addCollection(
                $this->resolver->resolve($action, 'annotation')
                    ->load($action, 'annotation')
            );
        }

        $this->isLoaded = true;

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'api_actions' === $type;
    }


}
