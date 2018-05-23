<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationException.
 */
class ValidationException extends ApiException
{
    /**
     * @var array
     */
    private $errors;

    /**
     * ValidationException constructor.
     *
     * @param string|null     $message
     * @param array           $errors
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = null, array $errors = [], \Throwable $previous = null)
    {
        if (null === $message) {
            $message = 'Ошибка валидации переданных данных.';
        }

        parent::__construct($message, null, 400, $previous);

        $this->errors = $errors;
    }

    /**
     * @param string $message
     *
     * @return ValidationException
     */
    public static function fromMessage(string $message): self
    {
        return new self($message);
    }

    /**
     * @param array $errors
     *
     * @return ValidationException
     */
    public static function fromArray(array $errors): self
    {
        return new self(null, $errors);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return self
     */
    public static function fromViolationsList(ConstraintViolationListInterface $violations): self
    {
        $errors = [];
        foreach ($violations as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return new self(null, $errors);
    }

    /**
     * @param string $property
     * @param string $message
     *
     * @return self
     */
    public function addError(string $property, string $message): self
    {
        $this->errors[$property] = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
