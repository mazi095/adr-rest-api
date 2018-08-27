<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     type="object",
 *     description="Структура данных ошибки запроса.",
 *     @OA\Property(property="message", maxLength=1000, readOnly=true, type="string"),
 *     @OA\Property(property="user_message", maxLength=1000, readOnly=true, type="string"),
 *     required={
 *         "message",
 *         "user_message"
 *     }
 * )
 */
class Error
{
    /**
     * Текст ошибки.
     *
     * @var string
     */
    private $message;

    /**
     * Текст причины отказа в выполнении какой-либо операции для клиентов. Данное сообщение можно безопасно отдавать
     * в качестве ответа клиентам и не IT-специалистам. Операции, для которых не предусмотрено пользовательское сообщение
     * в случае ошибки возвращают: "Операция недопустима, пожалуйста, свяжитесь с техническим специалистом для выяснения причин.".
     *
     *
     * @var string
     */
    private $userMessage;

    /**
     * Данные исключения (возвращается только в debug-режиме).
     *
     * @var array|null
     */
    private $exception;

    /**
     * Error constructor.
     *
     * @param string $message
     * @param string $userMessage
     */
    public function __construct(string $message, string $userMessage)
    {
        $this->message     = $message;
        $this->userMessage = $userMessage;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * @return array|null
     */
    public function getException(): ?array
    {
        return $this->exception;
    }

    /**
     * @param array|null $exception
     *
     * @return Error
     */
    public function setException(?array $exception): self
    {
        $this->exception = $exception;

        return $this;
    }
}
