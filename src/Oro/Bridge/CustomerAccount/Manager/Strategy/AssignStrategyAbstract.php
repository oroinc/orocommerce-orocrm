<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

/**
 * Provides common functionality for customer-to-account assignment strategies.
 *
 * This base class handles the core operations needed when assigning commerce customers to CRM accounts,
 * including account building, customer management, and lifetime value calculations.
 * Subclasses should implement specific assignment logic based on different business rules.
 */
abstract class AssignStrategyAbstract implements AssignStrategyInterface
{
    /**
     * @var AccountBuilder
     */
    protected $builder;

    /**
     * @var AccountCustomerManager
     */
    protected $accountCustomerManager;

    /**
     * @var LifetimeProcessor
     */
    protected $lifetimeProcessor;

    public function __construct(
        AccountBuilder $builder,
        AccountCustomerManager $accountCustomerManager,
        LifetimeProcessor $lifetimeProcessor
    ) {
        $this->builder = $builder;
        $this->accountCustomerManager = $accountCustomerManager;
        $this->lifetimeProcessor = $lifetimeProcessor;
    }

    /**
     * Get root customer
     *
     * @param Customer $customer
     *
     * @return Customer
     */
    protected function getRootCustomer(Customer $customer)
    {
        if ($customer->getParent()) {
            $customer = $this->getRootCustomer($customer->getParent());
        }

        return $customer;
    }

    protected function recalculateLifeTimeValue(Customer $customer)
    {
        $newLifetimeValue = $this->lifetimeProcessor->calculateLifetimeValue($customer);
        $customer->setLifetime($newLifetimeValue);
    }
}
