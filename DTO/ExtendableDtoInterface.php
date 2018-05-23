<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DTO;

/**
 * Interface ExtendableDtoInterface.
 */
interface ExtendableDtoInterface extends DtoInterface
{
    /**
     * @return string
     */
    public static function getDiscriminatorFieldName(): string;

    /**
     * @param string $discriminator
     *
     * @return null|string
     */
    public static function getClass(string $discriminator): ?string;

    /**
     * @return array
     */
    public static function getDiscriminatorValueList(): array;
}
