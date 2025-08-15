<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Tests\Functional\Api\DataFixtures\LoadCustomerData;

/**
 * @dbIsolationPerTest
 */
class CustomerTest extends RestJsonApiTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([LoadCustomerData::class]);
        $this->getReferenceRepository()->setReference('default_customer', $this->getDefaultCustomer());
    }

    public function testCreateCustomerWithEachStrategy(): void
    {
        $manager = self::getContainer()->get('oro_sales.manager.account_customer');
        $response = $this->post(['entity' => 'customers'], 'create_customer.yml');

        $customer = $this->getEntityManager()->find(Customer::class, $this->getResourceId($response));
        $association = $manager->getAccountCustomerByTarget($customer);

        self::assertEquals($customer->getName(), $association->getAccount()->getName());
    }

    public function testCreateCustomerWithRootStrategy(): void
    {
        $manager = self::getContainer()->get('oro_sales.manager.account_customer');

        $configManager = self::getConfigManager();
        $initialSettings = $configManager->get('oro_customer_account_bridge.customer_account_settings');
        $configManager->set('oro_customer_account_bridge.customer_account_settings', 'root');
        $configManager->flush();
        try {
            $response = $this->post(['entity' => 'customers'], 'create_customer.yml');

            $customer = $this->getEntityManager()->find(Customer::class, $this->getResourceId($response));
            $association = $manager->getAccountCustomerByTarget($customer);

            self::assertEquals($customer->getParent()->getName(), $association->getAccount()->getName());
        } finally {
            $configManager->set('oro_customer_account_bridge.customer_account_settings', $initialSettings);
            $configManager->flush();
        }
    }

    public function testGetListShouldNotReturnLifetimeAttribute(): void
    {
        $customerId = $this->getReference('customer.1')->getId();

        $response = $this->cget(
            ['entity' => 'customers'],
            ['filter[id]' => (string)$customerId]
        );

        $this->assertResponseContains(
            ['data' => [['type' => 'customers', 'id' => (string)$customerId]]],
            $response
        );
        $content = self::jsonToArray($response->getContent());
        self::assertArrayNotHasKey('lifetime', $content['data'][0]['attributes']);
    }

    public function testGetShouldNotReturnLifetimeAttribute(): void
    {
        $customerId = $this->getReference('customer.1')->getId();

        $response = $this->get(['entity' => 'customers', 'id' => (string)$customerId]);

        $this->assertResponseContains(
            ['data' => ['type' => 'customers', 'id' => (string)$customerId]],
            $response
        );
        $content = self::jsonToArray($response->getContent());
        self::assertArrayNotHasKey('lifetime', $content['data']['attributes']);
    }

    public function testGetParentSubresourceShouldNotReturnLifetimeAttribute(): void
    {
        /** @var Customer $customer */
        $customer = $this->getReference('customer.1');
        $customerId = $customer->getId();
        $parentId = $customer->getParent()->getId();

        $response = $this->getSubresource(
            ['entity' => 'customers', 'id' => (string)$customerId, 'association' => 'parent']
        );

        $this->assertResponseContains(
            ['data' => ['type' => 'customers', 'id' => (string)$parentId]],
            $response
        );
        $content = self::jsonToArray($response->getContent());
        self::assertArrayNotHasKey('lifetime', $content['data']['attributes']);
    }

    public function testGetChildrenSubresourceShouldNotReturnLifetimeAttribute(): void
    {
        /** @var Customer $customer */
        $customer = $this->getReference('customer.1');
        $customerId = $customer->getId();
        $parentId = $customer->getParent()->getId();

        $response = $this->getSubresource(
            ['entity' => 'customers', 'id' => (string)$parentId, 'association' => 'children']
        );

        $this->assertResponseContains(
            ['data' => [['type' => 'customers', 'id' => (string)$customerId]]],
            $response
        );
        $content = self::jsonToArray($response->getContent());
        self::assertArrayNotHasKey('lifetime', $content['data'][0]['attributes']);
    }

    public function testUpdateShouldNotAcceptLifetimeAttribute(): void
    {
        $customerId = $this->getReference('customer.1')->getId();

        $response = $this->patch(
            ['entity' => 'customers', 'id' => (string)$customerId],
            [
                'data' => [
                    'type'       => 'customers',
                    'id'         => (string)$customerId,
                    'attributes' => [
                        'lifetime' => 1
                    ]
                ]
            ],
            [],
            false
        );

        $this->assertResponseValidationError(
            [
                'title'  => 'extra fields constraint',
                'detail' => 'This form should not contain extra fields: "lifetime".'
            ],
            $response
        );
    }

    private function getDefaultCustomer(): Customer
    {
        return $this->getEntityManager()
            ->getRepository(Customer::class)
            ->findOneByName('CustomerUser CustomerUser');
    }
}
