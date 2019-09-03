<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Tests\Controller;

use ConnectHolland\CookieConsentBundle\Controller\CookieConsentController;
use ConnectHolland\CookieConsentBundle\Cookie\CookieChecker;
use ConnectHolland\CookieConsentBundle\Form\CookieConsentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class CookieConsentControllerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $templating;

    /**
     * @var MockObject
     */
    private $formFactory;

    /**
     * @var MockObject
     */
    private $cookieChecker;

    /**
     * @var MockObject
     */
    private $translator;

    /**
     * @var CookieConsentController
     */
    private $cookieConsentController;

    public function setUp()
    {
        $this->templating              = $this->createMock(\Twig_Environment::class);
        $this->formFactory             = $this->createMock(FormFactoryInterface::class);
        $this->cookieChecker           = $this->createMock(CookieChecker::class);
        $this->translator              = $this->createMock(TranslatorInterface::class);
        $this->cookieConsentController = new CookieConsentController($this->templating, $this->formFactory, $this->cookieChecker, 'dark', $this->translator);
    }

    public function testShow()
    {
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CookieConsentType::class)
            ->willReturn($this->createMock(FormInterface::class));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->willReturn('test');

        $response = $this->cookieConsentController->show(new Request());

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testShowIfCookieConsentNotSet()
    {
        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(false);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CookieConsentType::class)
            ->willReturn($this->createMock(FormInterface::class));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->willReturn('test');

        $response = $this->cookieConsentController->showIfCookieConsentNotSet(new Request());

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testShowIfCookieConsentNotSetWithLocale()
    {
        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(false);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CookieConsentType::class)
            ->willReturn($this->createMock(FormInterface::class));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->willReturn('test');

        $request = $this->createMock(Request::class);
        $locale  = 'en';

        $request
            ->expects($this->once())
            ->method('get')
            ->with('locale')
            ->willReturn($locale);

        $this->translator
            ->expects($this->once())
            ->method('setLocale')
            ->with($locale);

        $request
            ->expects($this->once())
            ->method('setLocale')
            ->with($locale);

        $response = $this->cookieConsentController->showIfCookieConsentNotSet($request);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testShowIfCookieConsentNotSetWithCookieConsentSet()
    {
        $this->cookieChecker
            ->expects($this->once())
            ->method('isCookieConsentSavedByUser')
            ->willReturn(true);

        $this->formFactory
            ->expects($this->never())
            ->method('create')
            ->with(CookieConsentType::class);

        $this->templating
            ->expects($this->never())
            ->method('render');

        $response = $this->cookieConsentController->showIfCookieConsentNotSet(new Request());

        $this->assertInstanceOf(Response::class, $response);
    }
}
