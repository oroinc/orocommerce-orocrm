<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\Lifetime;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;

class OrderPaymentTransactionAndStatus extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @var array
     */
    protected $orders = [
        LoadOrders::MY_ORDER => [
            'paymentStatus' => [
                'referenceName' => 'my_order_payment_status',
                'value' => PaymentStatusProvider::FULL
            ],
            'paymentTransaction' => [
                'referenceName' => 'my_order_payment_transaction',
                'successful' => true,
                'amount' => 1000,
                'currency' => 'USD',
                'action' => 'purchase',
                'paymentMethod' => 'purchase'
            ]
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadOrders::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->orders as $orderReferenceName => $config) {
            $this->createPaymentTransaction($manager, $orderReferenceName, $config['paymentTransaction']);
            $this->createPaymentStatus($manager, $orderReferenceName, $config['paymentStatus']);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string $orderReferenceName
     * @param array $paymentTransactionData
     *
     * @return PaymentTransaction
     */
    protected function createPaymentTransaction(
        ObjectManager $manager,
        $orderReferenceName,
        array $paymentTransactionData
    ) {
        $order = $this->getReference($orderReferenceName);

        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setEntityClass(ClassUtils::getClass($order));
        $paymentTransaction->setEntityIdentifier($order->getId());
        $paymentTransaction->setSuccessful($paymentTransactionData['successful']);
        $paymentTransaction->setAmount($paymentTransactionData['amount']);
        $paymentTransaction->setCurrency($paymentTransactionData['currency']);
        $paymentTransaction->setAction($paymentTransactionData['action']);
        $paymentTransaction->setPaymentMethod($paymentTransactionData['paymentMethod']);

        $manager->persist($paymentTransaction);
        $this->addReference($paymentTransactionData['referenceName'], $paymentTransaction);

        return $paymentTransaction;
    }

    /**
     * @param ObjectManager $manager
     * @param string $orderReferenceName
     * @param array $paymentStatusData
     *
     * @return PaymentStatus
     */
    protected function createPaymentStatus(ObjectManager $manager, $orderReferenceName, array $paymentStatusData)
    {
        $order = $this->getReference($orderReferenceName);

        $paymentStatus = new PaymentStatus();
        $paymentStatus->setEntityClass(ClassUtils::getClass($order));
        $paymentStatus->setEntityIdentifier($order->getId());
        $paymentStatus->setPaymentStatus($paymentStatusData['value']);

        $manager->persist($paymentStatus);
        $this->addReference($paymentStatusData['referenceName'], $paymentStatus);

        return $paymentStatus;
    }
}
