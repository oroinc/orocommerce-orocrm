<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\EventListener;

use Doctrine\ORM\EntityManager;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\AccountBundle\Entity\Account;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class CustomerCreateListenerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateCustomerWithoutAccount()
    {
        $em = $this->getEntityManager();
        $customer = new Customer();
        $customer->setName('Test Customer');

        $this->assertNull($customer->getAccount());

        $em->persist($customer);
        $em->flush();

        $this->assertEquals('Test Customer', $customer->getAccount()->getName());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateCustomerWithAccount()
    {
        $em = $this->getEntityManager();
        $customer = new Customer();
        $customer->setName('Test Customer');
        $account = new Account();
        $account->setName('Test Account');
        $customer->setAccount($account);

        $em->persist($account);
        $em->persist($customer);
        $em->flush();

        $this->assertEquals('Test Account', $customer->getAccount()->getName());
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
