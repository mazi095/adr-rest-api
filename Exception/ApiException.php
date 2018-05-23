<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\Exception;

use Throwable;

class ApiException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $userMessage;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * MiddleOfficeException constructor.
     *
     * @param string         $message
     * @param string|null    $userMessage
     * @param int            $statusCode
     * @param Throwable|null $previous
     */
    public function __construct(string $message, string $userMessage = null, int $statusCode = 500, Throwable $previous = null)
    {
        if (null === $userMessage) {
            $userMessage = self::DEFAULT_USER_MESSAGE;
        }

        parent::__construct($message, 0, $previous);

        $this->userMessage = $userMessage;
        $this->statusCode  = $statusCode;
    }

    /**
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Создает исключение для отсутствующего REST ресурса.
     *
     * @return ApiException
     */
    public static function resourceNotFound(): self
    {
        return new self('Ресурс по указанному адресу не найден или был удален.', null, 404);
    }
}
