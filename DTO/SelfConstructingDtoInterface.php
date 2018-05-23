<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DTO;

interface SelfConstructingDtoInterface extends DtoInterface
{
    /**
     * SelfConstructingDtoInterface constructor.
     *
     * @param array $data Декодированный JSON из запроса в виде массива
     */
    public function __construct(array $data = []);
}
