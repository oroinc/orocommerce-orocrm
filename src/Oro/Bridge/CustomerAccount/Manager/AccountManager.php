<?php

namespace Oro\Bridge\CustomerAccount\Manager;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignStrategyInterface;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\CustomerBundle\Entity\Repository\AccountRepository;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;
use Psr\Log\LoggerInterface;

class AccountManager
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var AssignStrategyInterface[]
     */
    protected $strategies;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->logger = $logger;
    }

    public function addStrategy(AssignStrategyInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * Assign all customers to root customer account
     *
     * @param string $strategyType
     *
     * @return bool
     */
    public function assignAccounts($strategyType)
    {
        if (array_key_exists($strategyType, $this->strategies)) {
            $strategy = $this->strategies[$strategyType];
        } else {
            $this->logger->error(
                sprintf('Failed get strategy to create customer accounts. %s', $strategyType),
                ['strategyType' => $strategyType]
            );

            return false;
        }

        try {
            $entitiesIterator = $this->getRepository()->getBatchIterator();
            $objects = [];

            /** @var Customer $entity */
            foreach ($entitiesIterator as $entity) {
                $objects = array_merge($strategy->process($entity), $objects);

                if (count($objects) >= 100) {
                    $this->updateEntities($objects);
                    $objects = [];
                }
            }
            $this->updateEntities($objects);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Failed create customer accounts. %s', $e->getMessage()),
                ['exception' => $e]
            );

            return false;
        }

        return true;
    }

    /**
     * @return AccountRepository
     */
    protected function getRepository()
    {
        return $this->getManager()->getRepository('OroCustomerBundle:Customer');
    }

    /**
     * @return ObjectManager|null
     */
    protected function getManager()
    {
        return $this->registry->getManagerForClass('OroCustomerBundle:Customer');
    }

    /**
     * Clear account and customer entities
     */
    protected function clear()
    {
        $this->getManager()->clear(Customer::class);
        $this->getManager()->clear(Account::class);
        $this->getManager()->clear(CustomerAssociation::class);
    }

    protected function updateEntities($objects)
    {
        if ($objects) {
            array_map(function ($entity) {
                $this->getManager()->persist($entity);

                return true;
            }, $objects);
            $this->getManager()->flush($objects);
        }
        $this->clear();
    }
}
