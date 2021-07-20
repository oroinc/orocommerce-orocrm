<?php

namespace Oro\Bridge\CustomerAccount\Manager;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CurrencyBundle\Query\CurrencyQueryBuilderTransformerInterface;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;

class LifetimeProcessor
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var CurrencyQueryBuilderTransformerInterface
     */
    protected $qbTransformer;

    public function __construct(ManagerRegistry $registry, CurrencyQueryBuilderTransformerInterface $qbTransformer)
    {
        $this->registry = $registry;
        $this->qbTransformer = $qbTransformer;
    }

    /**
     * @param Customer $customer
     *
     * @return float
     */
    public function calculateLifetimeValue(Customer $customer)
    {
        $qb = $this->getEntityManager()->getRepository('OroOrderBundle:Order')
            ->createQueryBuilder('o');
        $subtotalValueQuery = $this->qbTransformer->getTransformSelectQuery('subtotal', $qb);
        $qb->select(sprintf('SUM(%s)', $subtotalValueQuery))
            ->leftJoin(
                'Oro\Bundle\PaymentBundle\Entity\PaymentStatus',
                'payment_status',
                Join::WITH,
                'payment_status.entityIdentifier = o.id '
                . "AND payment_status.entityClass = 'Oro\\Bundle\\OrderBundle\\Entity\\Order'"
            )
            ->where(
                $qb->expr()->eq('o.customer', ':customer')
            )
            ->andWhere('payment_status.paymentStatus = :paymentStatus')
            ->setParameter('customer', $customer->getId())
            ->setParameter('paymentStatus', PaymentStatusProvider::FULL);

        return (float)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->registry->getManager();
    }
}
