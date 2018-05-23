<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\Exception;

use Throwable;

interface ExceptionInterface extends Throwable
{
    public const DEFAULT_USER_MESSAGE = 'Операция недопустима, пожалуйста, свяжитесь с техническим специалистом для выяснения причин.';

    /**
     * Возвращает сообщения для клиентов, которое можно показывать не IT-специалистам.
     *
     * @return string
     */
    public function getUserMessage(): string;

    /**
     * Возвращает HTTP статус код.
     *
     * @return int
     */
    public function getStatusCode(): int;
}
