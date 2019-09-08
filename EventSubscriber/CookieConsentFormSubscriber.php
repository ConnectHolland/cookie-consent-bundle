<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\EventSubscriber;

use ConnectHolland\CookieConsentBundle\Cookie\CookieHandler;
use ConnectHolland\CookieConsentBundle\Cookie\CookieLogger;
use ConnectHolland\CookieConsentBundle\Enum\CookieNameEnum;
use ConnectHolland\CookieConsentBundle\Form\CookieConsentType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieConsentFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CookieLogger
     */
    private $cookieLogger;

    /**
     * @var bool
     */
    private $useLogger;

    private $cookiePrefix;
//
//    private $cookieKeyName;
//
//    private $cookieConsentName;

    public function __construct(FormFactoryInterface $formFactory, CookieLogger $cookieLogger, bool $useLogger, CookieNameEnum $cookieNameEnum)  //, string $cookieKeyName, string $cookieConsentName
    {
        $this->formFactory  = $formFactory;
        $this->cookieLogger = $cookieLogger;
        $this->useLogger    = $useLogger;
        $this->cookiePrefix = $cookieNameEnum;

    }

    public static function getSubscribedEvents(): array
    {
        return [
           KernelEvents::RESPONSE => ['onResponse'],
        ];
    }

    /**
     * Checks if form has been submitted and saves users preferences in cookies by calling the CookieHandler.
     */
    public function onResponse(FilterResponseEvent $event): void
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        $form = $this->createCookieConsentForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleFormSubmit($form->getData(), $request, $response);
        }
    }

    /**
     * Handle form submit.
     */
    protected function handleFormSubmit(array $categories, Request $request, Response $response): void
    {
        $cookieConsentKey = $this->getCookieConsentKey($request);

        $cookieHandler = new CookieHandler($response,  $this->cookiePrefix);
        $cookieHandler->save($categories, $cookieConsentKey);

        if ($this->useLogger) {
            $this->cookieLogger->log($categories, $cookieConsentKey);
        }
    }

    /**
     *  Return existing key from cookies or create new one.
     */
    protected function getCookieConsentKey(Request $request): string
    {
        return $request->cookies->get(CookieNameEnum::COOKIE_CONSENT_KEY_NAME) ?? uniqid();
    }

    /**
     * Create cookie consent form.
     */
    protected function createCookieConsentForm(): FormInterface
    {
        return $this->formFactory->create(CookieConsentType::class);
    }
}
