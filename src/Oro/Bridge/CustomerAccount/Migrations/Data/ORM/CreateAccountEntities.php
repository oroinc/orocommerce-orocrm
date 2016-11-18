<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\BatchBundle\ORM\Query\BufferedQueryResultIterator;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

class CreateAccountEntities extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $qb = $manager->getRepository(Customer::class)
            ->createQueryBuilder('c')
            ->where('c.account IS NULL');

        $iterator = new BufferedQueryResultIterator($qb);
        $iterator->setBufferSize(500);
        $objects = [];
        foreach ($iterator as $entity) {
            if (!$entity->getAccount()) {
                $account = $this->getAccount($entity);
                $manager->persist($account);
                $entity->setAccount($account);
                $manager->persist($entity);
                $objects[] = $account;
                $objects[] = $entity;
                if (count($objects) >= 100) {
                    $manager->flush($objects);
                    $this->clear($manager);
                    $objects = [];
                }
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
     * @return Account
     */
    protected function getAccount($entity)
    {
        $account = new Account();
        $account->setName($entity->getName());
        $account->setOrganization($entity->getOrganization());
        $account->setOwner($entity->getOwner());

        return $account;
    }

    /**
     * @param ObjectManager $manager
     */
    protected function clear($manager)
    {
        $manager->clear(Customer::class);
        $manager->clear(Account::class);
    }
}
