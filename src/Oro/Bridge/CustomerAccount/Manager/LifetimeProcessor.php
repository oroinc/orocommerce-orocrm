<?php

namespace Oro\Bridge\CustomerAccount\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;

use Oro\Bundle\CurrencyBundle\Query\CurrencyQueryBuilderTransformerInterface;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

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

    /**
     * @param ManagerRegistry $registry
     * @param CurrencyQueryBuilderTransformerInterface $qbTransformer
     */
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
            ->where(
                $qb->expr()->eq('o.account', ':account')
            )
            ->setParameter('account', $customer->getId());

        return (float)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->registry->getManager();
    }
}
