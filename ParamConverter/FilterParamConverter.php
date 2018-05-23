<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\ParamConverter;

use Mazi\AdrRestApi\Exception\ValidationException;
use Mazi\AdrRestApi\DTO\FilterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilterParamConverter implements ParamConverterInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * FilterParamConverter constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ValidationException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $filterClass = $configuration->getClass();
        $filter      = new $filterClass($request->query->all());

        $violations = $this->validator->validate($filter);
        if ($violations->count()) {
            throw ValidationException::fromViolationsList($violations);
        }

        $request->attributes->set($configuration->getName(), $filter);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        $class = $configuration->getClass();

        return $class && is_subclass_of($class, FilterInterface::class);
    }
}
