<?php

declare(strict_types=1);

namespace ConnectHolland\CookieConsentBundle\EventSubscriber;

use ConnectHolland\CookieConsentBundle\Enum\CookieNameEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieConsentReviewSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse'],
        ];
    }

    /**
     * Checks if form has been submitted and saves users preferences in cookies by calling the CookieHandler.
     */
    public function onResponse(KernelEvent $event): void
    {
        if ($event instanceof FilterResponseEvent === false && $event instanceof ResponseEvent === false) {
            throw new \RuntimeException('No ResponseEvent class found');
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route', 'doesnt_matter');

        if (strpos($route, 'ch_cookie_consent') === false) {
            if ($request->query->get('_cookie_consent_review') === '1') {
                $request->query->remove('_cookie_consent_review');
                $response = $event->getResponse();
                $response->headers->clearCookie(CookieNameEnum::COOKIE_CONSENT_NAME);
                $event->setResponse($response);
            }
        }
    }

}
