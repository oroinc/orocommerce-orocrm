<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\BatchBundle\ORM\Query\BufferedIdentityQueryResultIterator;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

class CreateAccountEntities extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $field = AccountCustomerManager::getCustomerTargetField(Customer::class);

        $qb = $manager->getRepository(Customer::class)
            ->createQueryBuilder('c')
            ->leftJoin(CustomerAssociation::class, 'ca', 'WITH', sprintf('ca.%s = c', $field))
            ->where('ca.id IS NULL');

        $iterator = new BufferedIdentityQueryResultIterator($qb);
        $iterator->setBufferSize(500);
        $objects = [];
        foreach ($iterator as $entity) {
            $customerAssociation = $this->createCustomerAssociation($entity);
            $manager->persist($customerAssociation);
            $objects[] = $customerAssociation;
            if (count($objects) >= 100) {
                $manager->flush($objects);
                $this->clear($manager);
                $objects = [];
            }
        }
        if ($objects) {
            $manager->flush($objects);
        }
        $this->clear($manager);
    }

    /**
     * @param Customer $entity
     *
     * @return CustomerAssociation
     */
    protected function createCustomerAssociation($entity)
    {
        $account = new Account();
        $account->setName($entity->getName());
        $account->setOrganization($entity->getOrganization());
        $account->setOwner($entity->getOwner());

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account, $entity);

        return $customerAssociation;
    }

    /**
     * @param ObjectManager $manager
     */
    protected function clear($manager)
    {
        $manager->clear(CustomerAssociation::class);
        $manager->clear(Account::class);
    }
}
