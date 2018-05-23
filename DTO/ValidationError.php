<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\DTO;

use Swagger\Annotations as OAS;

/**
 * @OAS\Schema(
 *     type="object",
 *     description="Структура данных ошибки валидации данных.",
 *     allOf={
 *         @OAS\Schema(
 *             ref="#/components/schemas/Error"
 *         ),
 *         @OAS\Schema(
 *             type="object",
 *             @OAS\Property(
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
