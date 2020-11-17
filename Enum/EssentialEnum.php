<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Enum;

class EssentialEnum
{
    const COOKIE_CONSENT   = 'cookie_consent';
    const PHPSESSID        = 'phpsessid';
    const SF_REDIRECT      = 'sf_redirect';

    /**
     * @var array
     */
    private static $essentials = [
        self::COOKIE_CONSENT,
        self::PHPSESSID,
        self::SF_REDIRECT,
    ];

    /**
     * Get all essential cookies.
     */
    public static function getAvailableEssentialCookies(): array
    {
        return self::$essentials;
    }
}
