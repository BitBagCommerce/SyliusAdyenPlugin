<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Mock;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\SuccessfulResponseProcessor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class RequestMother
{
    public const TEST_LOCALE = 'pl_PL';

    public const WHERE_YOUR_HOME_IS = '127.0.0.1';

    public static function createWithSession(): Request
    {
        $session = new Session(new MockArraySessionStorage());
        $request = Request::create('/');
        $request->setSession($session);

        return $request;
    }

    public static function createWithSessionForDefinedOrderId(): Request
    {
        $result = self::createWithSession();
        $result->getSession()->set(SuccessfulResponseProcessor::ORDER_ID_KEY, 42);

        return $result;
    }

    public static function createWithSessionForSpecifiedQueryToken(): Request
    {
        $result = self::createWithSession();
        $result->query->set(SuccessfulResponseProcessor::TOKEN_VALUE_KEY, 'Szczebrzeszyn');

        return $result;
    }

    public static function createWithLocaleSet(): Request
    {
        $result = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => self::WHERE_YOUR_HOME_IS]);
        $result->setLocale(self::TEST_LOCALE);

        return $result;
    }
}
