<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Tests\EventSubscriber;

use ConnectHolland\CookieConsentBundle\Cookie\CookieChecker;
use ConnectHolland\CookieConsentBundle\DOM\DOMBuilder;
use ConnectHolland\CookieConsentBundle\DOM\DOMParser;
use ConnectHolland\CookieConsentBundle\EventSubscriber\AppendCookieConsentSubcriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AppendCookieConsentSubscriberTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $cookieChecker;

    /**
     * @var MockObject
     */
    private $domBuilder;

    /**
     * @var MockObject
     */
    private $domParser;

    /**
     * @var AppendCookieConsentSubscriber
     */
    private $appendCookieConsentSubscriber;

    public function setUp()
    {
        $this->cookieChecker                 = $this->createMock(CookieChecker::class);
        $this->domBuilder                    = $this->createMock(DOMBuilder::class);
        $this->domParser                     = $this->createMock(DOMParser::class);
        $this->appendCookieConsentSubscriber = new AppendCookieConsentSubcriber($this->cookieChecker, $this->domBuilder, $this->domParser, ['app_cookies'], ['/cookies']);
    }

    public function testGetSubscribedEvents(): void
    {
        $expectedEvents = [
            KernelEvents::RESPONSE => ['onResponse', 0],
        ];

        $this->assertSame($expectedEvents, $this->appendCookieConsentSubscriber->getSubscribedEvents());
    }

    public function testOnResponse(): void
    {
        $response = new Response();
        $request  = new Request();

        $event = $this->createMock(FilterResponseEvent::class);
        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $event
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(false);

        $this->domBuilder
            ->expects($this->once())
            ->method('buildCookieConsentDom');

        $this->domParser
            ->expects($this->once())
            ->method('appendToBody')
            ->willReturn('<body><div>Cookie consent</div></body>');

        $this->appendCookieConsentSubscriber->onResponse($event);

        $this->assertSame('<body><div>Cookie consent</div></body>', $response->getContent());
    }

    public function testOnResponseWithExcludedRoute(): void
    {
        $response = new Response();
        $request  = $this->createMock(Request::class);

        $event = $this->createMock(FilterResponseEvent::class);
        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(false);

        $request
            ->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn('app_cookies');

        $this->appendCookieConsentSubscriber->onResponse($event);

        $this->assertSame('', $response->getContent());
    }

    public function testOnResponseWithExcludedPath(): void
    {
        $response = new Response();
        $request  = $this->createMock(Request::class);

        $event = $this->createMock(FilterResponseEvent::class);
        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(false);

        $request
            ->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn('');

        $request
            ->expects($this->once())
            ->method('getRequestUri')
            ->willReturn('/cookies');

        $this->appendCookieConsentSubscriber->onResponse($event);

        $this->assertSame('', $response->getContent());
    }

    public function testOnResponseWithCookieConsentAlreadySaved(): void
    {
        $response = new Response();

        $event = $this->createMock(FilterResponseEvent::class);
        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);

        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(true);

        $this->appendCookieConsentSubscriber->onResponse($event);

        $this->assertSame('', $response->getContent());
    }

    public function testOnResponseWithSubrequest(): void
    {
        $response = new Response();

        $event = $this->createMock(FilterResponseEvent::class);
        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(false);

        $this->appendCookieConsentSubscriber->onResponse($event);

        $this->assertSame('', $response->getContent());
    }
}
