<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Cookie;

use ConnectHolland\CookieConsentBundle\Enum\CookieNameEnum;
use DateInterval;
use DateTime;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CookieHandler
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @var bool
     */
    private $httpOnly;

    /**
     * @var bool
     */
    private $raw;

    public function __construct(Response $response, bool $httpOnly = true, bool $raw = true)
    {
        $this->response  = $response;
        $this->raw       = $raw;
        $this->httpOnly  = $httpOnly;
    }

    /**
     * Save chosen cookie categories in cookies.
     */
    public function save(array $categories, string $key): void
    {
        $this->saveCookie(CookieNameEnum::COOKIE_CONSENT_NAME, date('r'));
        $this->saveCookie(CookieNameEnum::COOKIE_CONSENT_KEY_NAME, $key);

        foreach ($categories as $category => $permitted) {
            $this->saveCookie(CookieNameEnum::getCookieCategoryName($category), $permitted);
        }
    }

    /**
     * Add cookie to response headers.
     */
    protected function saveCookie(string $name, string $value): void
    {
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1Y'));
        $this->response->headers->setCookie(
            new Cookie($name, $value, $expirationDate, '/', null, null, $this->httpOnly, $this->raw)
        );
    }
}
