<?php

namespace Oro\Bridge\CustomerAccount\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SalesBundle\Entity\Customer as SalesCustomer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SalesBundle\Entity\Repository\CustomerRepository as SalesCustomerRepository;

class CustomerAccountImportExportHelper
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var string
     */
    private $salesCustomerClass;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param string $salesCustomerClass
     */
    public function __construct(DoctrineHelper $doctrineHelper, $salesCustomerClass)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->salesCustomerClass = $salesCustomerClass;
    }

    /**
     * @param Customer[] $customers
     * @return Account[]
     */
    public function loadCustomerAccounts(array $customers)
    {
        $field = AccountCustomerManager::getCustomerTargetField(Customer::class);
        $result = [];

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $salesAccount = $this->getSalesCustomerRepository()->getCustomerByTarget($customer->getId(), $field);

            if ($salesAccount) {
                $result[$customer->getId()] = $salesAccount->getAccount();
            }
        }

        return $result;
    }

    /**
     * @param int $id
     * @return null|Account
     */
    public function fetchAccount($id)
    {
        return $this->getAccountRepository()->find($id);
    }

    public function assignAccount(Customer $customer, Account $account)
    {
        $field = AccountCustomerManager::getCustomerTargetField(Customer::class);

        if ($customer->getId()) {
            $salesCustomer = $this->getSalesCustomerRepository()->getCustomerByTarget($customer->getId(), $field);

            //No need to change current Account for customer
            if ($salesCustomer && $salesCustomer->getAccount()->getId() === $account->getId()) {
                return;
            }

            //Customer has different account that he has now. We need to change it
            if ($salesCustomer) {
                $salesCustomer->setTarget($account, $customer);

                return;
            }
        }

        //There is a new Customer. We need to assign Account to her/him
        $this->assignAccountToNewCustomer($customer, $account);
    }

    /**
     * @return SalesCustomer
     */
    private function createNewSalesCustomer()
    {
        return new $this->salesCustomerClass();
    }

    /**
     * @return SalesCustomerRepository
     */
    private function getSalesCustomerRepository()
    {
        return $this->doctrineHelper->getEntityRepository(SalesCustomer::class);
    }

    /**
     * @return EntityRepository
     */
    private function getAccountRepository()
    {
        return $this->doctrineHelper->getEntityRepository(Account::class);
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->doctrineHelper->getEntityManagerForClass(Customer::class);
    }

    private function assignAccountToNewCustomer(Customer $customer, Account $account)
    {
        $salesCustomer = $this->createNewSalesCustomer()
            ->setTarget($account, $customer);

        $this->getEntityManager()->persist($salesCustomer);
    }
}
