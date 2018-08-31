<?php

declare(strict_types=1);

namespace Mazi\AdrRestApi\EventSubscriber;

use Mazi\AdrRestApi\DTO\Error;
use Mazi\AdrRestApi\DTO\ValidationError;
use Mazi\AdrRestApi\Exception\ExceptionInterface;
use JMS\Serializer\SerializerInterface;
use Mazi\AdrRestApi\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param SerializerInterface   $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param LoggerInterface       $logger
     */
    public function __construct(
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger
    ) {
        $this->serializer   = $serializer;
        $this->urlGenerator = $urlGenerator;
        $this->logger       = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW      => 'onKernelView',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * Сериализация возвращаемых контроллером значений в JSON.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);

        $controllerResult = $event->getControllerResult();

        $json = $this->serializer->serialize($controllerResult, 'json');
        $response->setContent($json);

        $event->setResponse($response);
    }

    /**
     * Перевод всех исключений в структуру объекта Error и, в зависимости от окружения, добавление дебаг информации.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $message     = $exception->getMessage();
        $userMessage = ExceptionInterface::DEFAULT_USER_MESSAGE;
        $statusCode  = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpExceptionInterface || $exception instanceof ExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        if ($exception instanceof ExceptionInterface) {
            $userMessage = $exception->getUserMessage();
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $message = 'Нет прав на выполнение данной операции.';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $message = 'Некорректный метод запроса, убедитесь что данная операция поддерживает указанный HTTP метод. Необходимо повторить запрос с корректным методом.';
        }

        if ($exception instanceof ValidationException) {
            $error = new ValidationError($message, $userMessage, $exception->getErrors());
        } else {
            $error = new Error($message, $userMessage);
        }

        if ($statusCode >= 500) {
            $this->logger->critical(
                $exception->getMessage(),
                ['exception' => $exception, 'status_code' => $statusCode]
            );
        } else {
            $this->logger->error(
                $exception->getMessage(),
                ['exception' => $exception, 'status_code' => $statusCode]
            );
        }

        $json     = $this->serializer->serialize($error, 'json');
        $response = new JsonResponse(
            $json,
            $statusCode,
            [
                'Content-Type' => 'application/json',
            ],
            true
        );

        $event->setResponse($response);
    }
}
