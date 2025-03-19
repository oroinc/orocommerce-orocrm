<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\Lifetime\OrderPaymentTransactionAndStatus;
use Oro\Bundle\CurrencyBundle\Entity\MultiCurrency;
use Oro\Bundle\CustomerBundle\Entity\Audit;
use Oro\Bundle\DataAuditBundle\Entity\AuditField;
use Oro\Bundle\MessageQueueBundle\Test\Functional\MessageQueueExtension;
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
    use MessageQueueExtension;

    #[\Override]
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
        $order->setSubtotalObject(MultiCurrency::create('500', 'USD'));
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

    public function testThatListenerNotProduceNewDataAuditRecordsInDatabase()
    {
        $this->getOptionalListenerManager()->enableListener(
            'oro_dataaudit.listener.send_changed_entities_to_message_queue'
        );

        $manager = self::getDataFixturesExecutorEntityManager();

        $orderReference = $this->getReference(LoadOrders::ORDER_1);

        $paymentStatus = new PaymentStatus();
        $paymentStatus->setEntityClass(Order::class);
        $paymentStatus->setEntityIdentifier($orderReference->getId());
        $paymentStatus->setPaymentStatus(PaymentStatusProvider::FULL);

        $manager->persist($paymentStatus);
        $manager->flush();

        self::consumeAllMessages();

        $this->assertEmpty($manager->getRepository(AuditField::class)->findAll());
        $this->assertEmpty($manager->getRepository(Audit::class)->findAll());

        $this->getOptionalListenerManager()->disableListener(
            'oro_dataaudit.listener.send_changed_entities_to_message_queue'
        );
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
