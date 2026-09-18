<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\Lifetime\OrderPaymentTransactionAndStatus;
use Oro\Bundle\CurrencyBundle\Entity\MultiCurrency;
use Oro\Bundle\DataAuditBundle\Test\Functional\AuditRecordsExtension;
use Oro\Bundle\MessageQueueBundle\Test\Functional\MessageQueueExtension;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\PaymentStatus\PaymentStatuses;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolationPerTest
 */
class CustomerLifetimeListenerTest extends WebTestCase
{
    use AuditRecordsExtension;
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
        $paymentStatus->setPaymentStatus(PaymentStatuses::PAID_IN_FULL);
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
        $paymentStatus->setPaymentStatus(PaymentStatuses::PENDING);
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
        /** @var EntityManager $manager */
        $manager = self::getDataFixturesExecutorEntityManager();

        $this->emptyMessageQueue();

        $lastAuditId = $this->getLastAuditId();
        $lastAuditFieldId = $this->getLastAuditFieldId();

        $this->getOptionalListenerManager()->enableListener(
            'oro_dataaudit.listener.send_changed_entities_to_message_queue'
        );

        try {
            $orderReference = $this->getReference(LoadOrders::ORDER_1);

            $paymentStatus = new PaymentStatus();
            $paymentStatus->setEntityClass(Order::class);
            $paymentStatus->setEntityIdentifier($orderReference->getId());
            $paymentStatus->setPaymentStatus(PaymentStatuses::PAID_IN_FULL);

            $manager->persist($paymentStatus);
            $manager->flush();

            self::consumeAllMessages();
        } finally {
            // A failed assertion must not leave the listener enabled for the next tests.
            $this->getOptionalListenerManager()->disableListener(
                'oro_dataaudit.listener.send_changed_entities_to_message_queue'
            );
        }

        self::assertSame(
            [],
            $this->getAuditFieldsCreatedAfter($lastAuditFieldId),
            'Updating the lifetime value must not add audit field records.'
        );
        self::assertSame(
            [],
            $this->getAuditsCreatedAfter($lastAuditId),
            'Updating the lifetime value must not add audit records.'
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
