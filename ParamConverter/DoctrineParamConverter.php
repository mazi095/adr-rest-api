<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Mazi\AdrRestApi\Exception\ApiException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as CustomConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DoctrineParamConverter.
 */
class DoctrineParamConverter implements ParamConverterInterface
{
    /**
     * @var CustomConverter
     */
    private $customConverter;

    /**
     * DoctrineParamConverter constructor.
     *
     * @param ManagerRegistry|null    $registry
     * @param ExpressionLanguage|null $expressionLanguage
     */
    public function __construct(ManagerRegistry $registry = null, ExpressionLanguage $expressionLanguage = null)
    {
        $this->customConverter = new CustomConverter($registry, $expressionLanguage);
    }

    /**
     * {@inheritdoc}
     * @throws \Mazi\AdrRestApi\Exception\ApiException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        try {
            return $this->customConverter->apply($request, $configuration);
        } catch (NotFoundHttpException $e) {
            throw new ApiException(
                sprintf('Ресурс %s не найден или был удален.', $configuration->getName()),
                null,
                404,
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $this->customConverter->supports($configuration);
    }
}
