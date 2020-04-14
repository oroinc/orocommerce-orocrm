<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Tests\Functional\Api\DataFixtures\LoadCustomerData;

class CustomerTest extends RestJsonApiTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([LoadCustomerData::class]);
    }

    public function testGetListShouldNotReturnLifetimeAttribute()
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

    public function testGetShouldNotReturnLifetimeAttribute()
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

    public function testGetParentSubresourceShouldNotReturnLifetimeAttribute()
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

    public function testGetChildrenSubresourceShouldNotReturnLifetimeAttribute()
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

    public function testUpdateShouldNotAcceptLifetimeAttribute()
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
}
