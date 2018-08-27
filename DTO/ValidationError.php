<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     type="object",
 *     description="Структура данных ошибки валидации данных.",
 *     allOf={
 *         @OA\Schema(
 *             ref="#/components/schemas/Error"
 *         ),
 *         @OA\Schema(
 *             type="object",
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 nullable=true,
 *                 description="Список полей с некорректными значениями и причина ошибки",
 *                 additionalProperties={"type": "string"},
 *                 readOnly=true
 *             )
 *         )
 *     }
 * )
 */
class ValidationError extends Error
{
    /**
     * @var array
     */
    private $errors;

    /**
     * ValidationError constructor.
     *
     * @param array|null $errors
     */
    public function __construct(string $message, string $userMessage, array $errors)
    {
        parent::__construct($message, $userMessage);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
