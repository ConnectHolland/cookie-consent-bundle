<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Tests\EventSubscriber;

use ConnectHolland\CookieConsentBundle\Cookie\CookieHandler;
use ConnectHolland\CookieConsentBundle\Cookie\CookieLogger;
use ConnectHolland\CookieConsentBundle\Enum\CategoryEnum;
use ConnectHolland\CookieConsentBundle\Enum\CookieNameEnum;
use ConnectHolland\CookieConsentBundle\EventSubscriber\CookieConsentFormSubscriber;
use ConnectHolland\CookieConsentBundle\EventSubscriber\CookieConsentReviewSubscriber;
use ConnectHolland\CookieConsentBundle\Form\CookieConsentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieConsentReviewSubscriberTest extends TestCase
{
    public $cookieConsentKey;

    public function setUp(): void
    {
        $response = new Response();
        $this->cookieConsentKey = uniqid();
        $cookieHandler = new CookieHandler($response);
        $categories = [
            CategoryEnum::CATEGORY_ANALYTICS => 'false',
            CategoryEnum::CATEGORY_MARKETING => 'true',
            CategoryEnum::CATEGORY_TRACKING => 'false',
            CategoryEnum::CATEGORY_SOCIAL_MEDIA => 'false',
        ];
        $cookieHandler->save($categories, $this->cookieConsentKey);
    }

    public function testGetSubscribedEvents(): void
    {
        $expectedEvents = [
           KernelEvents::RESPONSE => ['onResponse'],
        ];

        $cookieConsentReviewSubscriber = new CookieConsentReviewSubscriber();
        $this->assertSame($expectedEvents, $cookieConsentReviewSubscriber->getSubscribedEvents());
    }

    public function testOnResponse(): void
    {
        $request  = new Request();
        $response = new Response();

        $request->query->set('_cookie_consent_review', '1');
        $request->attributes->set('_route', 'main');

        $event = $this->getResponseEvent($request, $response);

        $cookieConsentReviewSubscriber = new CookieConsentReviewSubscriber();
        $cookieConsentReviewSubscriber->onResponse($event);

        $this->assertFalse($request->query->has('_cookie_consent_review'));
        $this->assertFalse($request->cookies->has(CookieNameEnum::COOKIE_CONSENT_NAME));
    }

    public function testOnResponseWithUnfoundResponseEvent(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No ResponseEvent class found');

        $cookieConsentReviewSubscriber = new CookieConsentReviewSubscriber();
        $cookieConsentReviewSubscriber->onResponse($this->createMock(KernelEvent::class));
    }

    /**
     * @return ResponseEvent|FilterResponseEvent
     */
    private function getResponseEvent(Request $request, Response $response)
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        if (class_exists(ResponseEvent::class)) {
            return new ResponseEvent($kernel, $request, 200, $response);
        } else {
            // Support for Symfony 3.4 & < 4.3
            return new FilterResponseEvent($kernel, $request, 200, $response);
        }
    }
}
