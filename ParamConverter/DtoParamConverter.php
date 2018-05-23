<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\ParamConverter;

use JMS\Serializer\SerializerInterface;
use Mazi\AdrRestApi\DTO\DtoInterface;
use Mazi\AdrRestApi\DTO\ExtendableDtoInterface;
use Mazi\AdrRestApi\DTO\SelfConstructingDtoInterface;
use Mazi\AdrRestApi\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DtoParamConverter implements ParamConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * DtoParamConverter constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface  $validator
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator  = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Mazi\AdrRestApi\Exception\ValidationException
     * @throws BadRequestHttpException                     Если запрос не содержит JSON данных
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$request->getContent()) {
            throw new BadRequestHttpException('JSON данные не переданы');
        }

        $object     = $this->deserialize($configuration->getClass(), $request);
        $violations = $this->validator->validate($object, null, $this->getValidationGroups($request));
        if ($violations->count()) {
            throw ValidationException::fromViolationsList($violations);
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        $class = $configuration->getClass();

        return $class && is_subclass_of($class, DtoInterface::class);
    }

    /**
     * @param string  $class
     * @param Request $request
     *
     * @return DtoInterface
     *
     * @throws BadRequestHttpException Если передан невалидный JSON объект
     * @throws ValidationException
     */
    private function deserialize(string $class, Request $request): DtoInterface
    {
        $data = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException('Передан некорректный JSON-объект');
        }

        if (is_subclass_of($class, ExtendableDtoInterface::class)) {
            /** @var ExtendableDtoInterface $class */
            $field = $class::getDiscriminatorFieldName();
            if (empty($data[$field])) {
                throw ValidationException::fromArray([$field => 'Поле обязательно для заполнения']);
            }

            if (!$class::getClass($data[$field])) {
                throw ValidationException::fromArray(
                    [
                        $field => sprintf(
                            'Неверное значение параметра: %s. Доступные варианты: %s',
                            $data[$field],
                            implode(', ', $class::getDiscriminatorValueList())
                        ),
                    ]
                );
            }

            $class = $class::getClass($data[$field]);
        }

        if (is_subclass_of($class, SelfConstructingDtoInterface::class)) {
            /** @var DtoInterface $object */
            $object = new $class($data);
        } else {
            /** @var DtoInterface $object */
            $object = $this->serializer->deserialize($request->getContent(), $class, 'json');
        }

        return $object;
    }

    /**
     * Возвращает список групп валидации для переданного запроса.
     *
     * @param Request $request
     *
     * @return string[]
     */
    private function getValidationGroups(Request $request): array
    {
        $actionPath = explode('\\', $request->get('_controller'));

        return ['Default', array_pop($actionPath)];
    }
}
