<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DTO;

interface FilterInterface
{
    /**
     * FilterInterface constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = []);
}
