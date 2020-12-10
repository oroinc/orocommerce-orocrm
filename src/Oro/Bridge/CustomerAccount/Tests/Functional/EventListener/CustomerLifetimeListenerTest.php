<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\Lifetime\OrderPaymentTransactionAndStatus;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolationPerTest
 */
class CustomerLifetimeListenerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient();

        $this->loadFixtures([
            LoadOrders::class,
            OrderPaymentTransactionAndStatus::class,
        ]);
    }

    public function testChangeSubtotal()
    {
        /** @var Order $order */
        $order = $this->getReference('my_order');
        $order->setSubtotal(500);
        $customer = $order->getCustomer();
        $em = $this->getEntityManager();
        self::assertEquals(1500.0, $customer->getLifetime());

        $em->persist($order);
        $em->flush();

        self::assertEquals(500.0, $customer->getLifetime());

        $em->remove($order);
        $em->flush($order);

        self::assertEquals(0, $customer->getLifetime());
    }

    public function testCreatePaymentStatusFull()
    {
        $order = $this->getReference('simple_order');

        $paymentStatus = new PaymentStatus();
        $paymentStatus->setEntityClass(ClassUtils::getClass($order));
        $paymentStatus->setEntityIdentifier($order->getId());
        $paymentStatus->setPaymentStatus(PaymentStatusProvider::FULL);
        $customer = $order->getCustomer();

        $em = $this->getEntityManager();
        $em->persist($paymentStatus);
        $em->flush();

        self::assertEquals(789, $customer->getLifetime());
    }

    public function testCreatePaymentStatusNotFull()
    {
        $order = $this->getReference('simple_order');

        $paymentStatus = new PaymentStatus();
        $paymentStatus->setEntityClass(ClassUtils::getClass($order));
        $paymentStatus->setEntityIdentifier($order->getId());
        $paymentStatus->setPaymentStatus(PaymentStatusProvider::PENDING);
        $customer = $order->getCustomer();

        $em = $this->getEntityManager();
        $em->persist($paymentStatus);
        $em->flush();

        self::assertEquals(null, $customer->getLifetime());
    }

    public function testDeleteOrderWithoutCustomer()
    {
        /** @var Order $order */
        $order = $this->getReference('simple_order');
        $order->setCustomer(null);
        $em = $this->getEntityManager();
        $em->flush($order);
        $em->remove($order);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
