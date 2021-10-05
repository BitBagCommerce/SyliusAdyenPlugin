<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Processor\PaymentResponseProcessor;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\Processor;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

abstract class AbstractProcessorTest extends KernelTestCase
{
    protected const DEFAULT_ROUTE_LOCALE = 'en_US';

    /** @var Processor */
    protected $processor;

    abstract public static function provideForTestAccepts(): array;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public static function getRouter(ContainerInterface $container): Router
    {
        $router = $container->get('router');
        $requestContext = new RequestContext();
        $requestContext->setParameter('_locale', self::DEFAULT_ROUTE_LOCALE);

        $router->setContext($requestContext);

        return $router;
    }

    /**
     * @dataProvider provideForTestAccepts
     */
    public function testAccepts(string $code, bool $accepts): void
    {
        $payment = $this->getPayment($code);
        $this->assertEquals(
            $accepts,
            $this->processor->accepts(Request::create('/'), $payment)
        );
    }

    protected function createRequestWithSession(): Request
    {
        $session = new Session(new MockArraySessionStorage());
        $request = Request::create('/');
        $request->setSession($session);

        return $request;
    }

    protected function getPayment(?string $resultCode = null, ?string $orderToken = null): PaymentInterface
    {
        $details = [];
        if ($resultCode !== null) {
            $details['resultCode'] = $resultCode;
        }

        $result = $this->createMock(PaymentInterface::class);
        $result
            ->method('getDetails')
            ->willReturn($details)
        ;

        if ($orderToken !== null) {
            $order = $this->createMock(OrderInterface::class);
            $order
                ->method('getTokenValue')
                ->willReturn($orderToken)
            ;
            $result
                ->method('getOrder')
                ->willReturn($order)
            ;
        }

        return $result;
    }
}
