<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Tests\Form;

use ConnectHolland\CookieConsentBundle\Cookie\CookieChecker;
use ConnectHolland\CookieConsentBundle\Form\CookieConsentType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class CookieConsentTypeTest extends TypeTestCase
{
    /**
     * @var MockObject
     */
    private $cookieChecker;

    public function setUp(): void
    {
        $this->cookieChecker = $this->createMock(CookieChecker::class);

        parent::setUp();
    }

    public function testSubmitAcceptOnlySelected(): void
    {
        $formData = [
            'analytics' => 'true',
            'tracking' => 'true',
            'marketing' => 'false',
        ];

        $form = $this->factory->create(CookieConsentType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($formData, $form->getData());
    }

    public function testSubmitAcceptAll(): void
    {
        $formData = [
            'analytics' => 'false',
            'tracking' => 'false',
            'marketing' => 'false',
            'use_all_cookies' => true,
        ];

        $form = $this->factory->create(CookieConsentType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame(
            [
                'analytics' => 'true',
                'tracking' => 'true',
                'marketing' => 'true',
            ],
            $form->getData()
        );
    }

    public function testSubmitAcceptOnlyFunctional(): void
    {
        $formData = [
            'analytics' => 'false',
            'tracking' => 'false',
            'marketing' => 'false',
            'use_only_functional_cookies' => true,
        ];

        $form = $this->factory->create(CookieConsentType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame(
            [
                'analytics' => 'false',
                'tracking' => 'false',
                'marketing' => 'false',
            ],
            $form->getData()
        );
    }


    protected function getExtensions(): array
    {
        $type = new CookieConsentType($this->cookieChecker, ['analytics', 'tracking', 'marketing']);

        return [
            new PreloadedExtension([$type], []),
        ];
    }
}
