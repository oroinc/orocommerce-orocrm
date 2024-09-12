<?php

namespace Oro\Bridge\CustomerAccount\Manager;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\CurrencyBundle\Query\CurrencyQueryBuilderTransformerInterface;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;

/**
 * Calculates lifetime value for a customer.
 */
class LifetimeProcessor
{
    private ManagerRegistry $doctrine;
    private CurrencyQueryBuilderTransformerInterface $qbTransformer;

    public function __construct(ManagerRegistry $doctrine, CurrencyQueryBuilderTransformerInterface $qbTransformer)
    {
        $this->doctrine = $doctrine;
        $this->qbTransformer = $qbTransformer;
    }

    public function calculateLifetimeValue(Customer $customer): float
    {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getManagerForClass(Order::class)->createQueryBuilder();
        $qb->from(Order::class, 'o')
            ->select(sprintf('SUM(%s)', $this->qbTransformer->getTransformSelectQuery('subtotal', $qb)))
            ->leftJoin(
                PaymentStatus::class,
                'payment_status',
                Join::WITH,
                'payment_status.entityIdentifier = o.id AND payment_status.entityClass = :entityClass'
            )
            ->where('o.customer = :customer AND payment_status.paymentStatus = :paymentStatus')
            ->setParameter('entityClass', Order::class)
            ->setParameter('customer', $customer->getId())
            ->setParameter('paymentStatus', PaymentStatusProvider::FULL);

        return (float)$qb->getQuery()->getSingleScalarResult();
    }
}
