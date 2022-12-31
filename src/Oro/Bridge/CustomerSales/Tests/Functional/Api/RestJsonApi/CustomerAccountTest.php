<?php

namespace Oro\Bridge\CustomerSales\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

/**
 * @dbIsolationPerTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class CustomerAccountTest extends RestJsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures(['@OroCustomerSalesBridgeBundle/Tests/Functional/Api/DataFixtures/customers.yml']);
    }

    private function getCustomerIds(int $accountId): array
    {
        $rows = $this->getEntityManager()->createQueryBuilder()
            ->from(CustomerAssociation::class, 'ca')
            ->select('c.id')
            ->innerJoin('ca.' . AccountCustomerManager::getCustomerTargetField(Customer::class), 'c')
            ->innerJoin('ca.account', 'a')
            ->where('a.id = :accountId')
            ->setParameter('accountId', $accountId)
            ->orderBy('c.id')
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'id');
    }

    public function testGetAccount(): void
    {
        $response = $this->get(
            ['entity' => 'accounts', 'id' => '<toString(@account1->id)>']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'          => 'accounts',
                    'id'            => '<toString(@account1->id)>',
                    'relationships' => [
                        'customers' => [
                            'data' => [
                                ['type' => 'customers', 'id' => '<toString(@customer1->id)>'],
                                ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                            ]
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetAccountWithIncludedCustomers(): void
    {
        $response = $this->get(
            ['entity' => 'accounts', 'id' => '<toString(@account1->id)>'],
            ['include' => 'customers', 'fields[accounts]' => 'customers']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    'type'          => 'accounts',
                    'id'            => '<toString(@account1->id)>',
                    'relationships' => [
                        'customers' => [
                            'data' => [
                                ['type' => 'customers', 'id' => '<toString(@customer1->id)>'],
                                ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'          => 'customers',
                        'id'            => '<toString(@customer1->id)>',
                        'relationships' => [
                            'account' => [
                                'data' => ['type' => 'accounts', 'id' => '<toString(@account1->id)>']
                            ]
                        ]
                    ],
                    [
                        'type'          => 'customers',
                        'id'            => '<toString(@customer2->id)>',
                        'relationships' => [
                            'account' => [
                                'data' => ['type' => 'accounts', 'id' => '<toString(@account1->id)>']
                            ]
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetAccountWithIncludedCustomersWithOnlyAccountAssociation(): void
    {
        $response = $this->get(
            ['entity' => 'accounts', 'id' => '<toString(@account1->id)>'],
            ['include' => 'customers', 'fields[accounts]' => 'customers', 'fields[customers]' => 'account']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    'type'          => 'accounts',
                    'id'            => '<toString(@account1->id)>',
                    'relationships' => [
                        'customers' => [
                            'data' => [
                                ['type' => 'customers', 'id' => '<toString(@customer1->id)>'],
                                ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'          => 'customers',
                        'id'            => '<toString(@customer1->id)>',
                        'relationships' => [
                            'account' => [
                                'data' => ['type' => 'accounts', 'id' => '<toString(@account1->id)>']
                            ]
                        ]
                    ],
                    [
                        'type'          => 'customers',
                        'id'            => '<toString(@customer2->id)>',
                        'relationships' => [
                            'account' => [
                                'data' => ['type' => 'accounts', 'id' => '<toString(@account1->id)>']
                            ]
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetCustomer(): void
    {
        $response = $this->get(
            ['entity' => 'customers', 'id' => '<toString(@customer1->id)>']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'          => 'customers',
                    'id'            => '<toString(@customer1->id)>',
                    'relationships' => [
                        'account' => [
                            'data' => ['type' => 'accounts', 'id' => '<toString(@account1->id)>']
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetSubresourceForAccountCustomers(): void
    {
        $response = $this->getSubresource([
            'entity'      => 'accounts',
            'id'          => '<toString(@account1->id)>',
            'association' => 'customers'
        ]);
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'customers',
                        'id'         => '<toString(@customer1->id)>',
                        'attributes' => [
                            'name' => '<toString(@customer1->name)>'
                        ]
                    ],
                    [
                        'type'       => 'customers',
                        'id'         => '<toString(@customer2->id)>',
                        'attributes' => [
                            'name' => '<toString(@customer2->name)>'
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForAccountCustomers(): void
    {
        $response = $this->getRelationship([
            'entity'      => 'accounts',
            'id'          => '<toString(@account1->id)>',
            'association' => 'customers'
        ]);
        $this->assertResponseContains(
            [
                'data' => [
                    ['type' => 'customers', 'id' => '<toString(@customer1->id)>'],
                    ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                ]
            ],
            $response
        );
    }

    public function testGetSubresourceForCustomerAccount(): void
    {
        $response = $this->getSubresource([
            'entity'      => 'customers',
            'id'          => '<toString(@customer1->id)>',
            'association' => 'account'
        ]);
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'accounts',
                    'id'         => '<toString(@account1->id)>',
                    'attributes' => [
                        'name' => '<toString(@account1->name)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForCustomerAccount(): void
    {
        $response = $this->getRelationship([
            'entity'      => 'customers',
            'id'          => '<toString(@customer1->id)>',
            'association' => 'account'
        ]);
        $this->assertResponseContains(
            [
                'data' => ['type' => 'accounts', 'id' => '<toString(@account1->id)>']
            ],
            $response
        );
    }

    public function testTryToUpdateAccountCustomers(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $customer3Id = $this->getReference('customer3')->getId();
        $response = $this->patch(
            ['entity' => 'accounts', 'id' => (string)$account1Id, 'association' => 'customers'],
            [
                'data' => [
                    'type'          => 'accounts',
                    'id'            => (string)$account1Id,
                    'relationships' => [
                        'customers' => [
                            'data' => [
                                ['type' => 'customers', 'id' => (string)$customer3Id]
                            ]
                        ]
                    ]
                ]
            ]
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'          => 'accounts',
                    'id'            => (string)$account1Id,
                    'relationships' => [
                        'customers' => [
                            'data' => [
                                ['type' => 'customers', 'id' => (string)$customer1Id],
                                ['type' => 'customers', 'id' => (string)$customer2Id]
                            ]
                        ]
                    ]
                ]
            ],
            $response
        );
        self::assertEquals([$customer1Id, $customer2Id], $this->getCustomerIds($account1Id));
    }

    public function testTryToUpdateRelationshipForAccountCustomers(): void
    {
        $response = $this->patchRelationship(
            ['entity' => 'accounts', 'id' => '<toString(@account2->id)>', 'association' => 'customers'],
            [
                'data' => [
                    ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                ]
            ],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToAddRelationshipForAccountCustomers(): void
    {
        $response = $this->postRelationship(
            ['entity' => 'accounts', 'id' => '<toString(@account2->id)>', 'association' => 'customers'],
            [
                'data' => [
                    ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                ]
            ],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDeleteRelationshipForAccountCustomers(): void
    {
        $response = $this->deleteRelationship(
            ['entity' => 'accounts', 'id' => '<toString(@account1->id)>', 'association' => 'customers'],
            [
                'data' => [
                    ['type' => 'customers', 'id' => '<toString(@customer2->id)>']
                ]
            ],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testUpdateCustomerAccount(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $customer3Id = $this->getReference('customer3')->getId();
        $data = [
            'data' => [
                'type'          => 'customers',
                'id'            => (string)$customer3Id,
                'relationships' => [
                    'account' => [
                        'data' => ['type' => 'accounts', 'id' => (string)$account1Id]
                    ]
                ]
            ]
        ];
        $response = $this->patch(
            ['entity' => 'customers', 'id' => (string)$customer3Id],
            $data
        );
        $this->assertResponseContains($data, $response);
        self::assertEquals([$customer1Id, $customer2Id, $customer3Id], $this->getCustomerIds($account1Id));
    }

    public function testUpdateCustomerAccountMoveToAnotherAccount(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $account2Id = $this->getReference('account2')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $data = [
            'data' => [
                'type'          => 'customers',
                'id'            => (string)$customer2Id,
                'relationships' => [
                    'account' => [
                        'data' => ['type' => 'accounts', 'id' => (string)$account2Id]
                    ]
                ]
            ]
        ];
        $response = $this->patch(
            ['entity' => 'customers', 'id' => (string)$customer2Id],
            $data
        );
        $this->assertResponseContains($data, $response);
        self::assertEquals([$customer1Id], $this->getCustomerIds($account1Id));
        self::assertEquals([$customer2Id], $this->getCustomerIds($account2Id));
    }

    public function testTryToUpdateCustomerAccountToNull(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $customer3Id = $this->getReference('customer3')->getId();
        $response = $this->patch(
            ['entity' => 'customers', 'id' => (string)$customer3Id],
            [
                'data' => [
                    'type'          => 'customers',
                    'id'            => (string)$customer3Id,
                    'relationships' => [
                        'account' => [
                            'data' => null
                        ]
                    ]
                ]
            ],
            [],
            false
        );
        $this->assertResponseValidationError(
            [
                'title'  => 'not blank constraint',
                'detail' => 'This value should not be blank.',
                'source' => ['pointer' => '/data/relationships/account/data']
            ],
            $response
        );
        self::assertEquals([$customer1Id, $customer2Id], $this->getCustomerIds($account1Id));
    }

    public function testUpdateRelationshipForCustomerAccount(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $customer3Id = $this->getReference('customer3')->getId();
        $this->patchRelationship(
            ['entity' => 'customers', 'id' => (string)$customer3Id, 'association' => 'account'],
            [
                'data' => ['type' => 'accounts', 'id' => (string)$account1Id]
            ]
        );
        self::assertEquals([$customer1Id, $customer2Id, $customer3Id], $this->getCustomerIds($account1Id));
    }

    public function testUpdateRelationshipForCustomerAccountMoveToAnotherAccount(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $account2Id = $this->getReference('account2')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $this->patchRelationship(
            ['entity' => 'customers', 'id' => (string)$customer2Id, 'association' => 'account'],
            [
                'data' => ['type' => 'accounts', 'id' => (string)$account2Id]
            ]
        );
        self::assertEquals([$customer1Id], $this->getCustomerIds($account1Id));
        self::assertEquals([$customer2Id], $this->getCustomerIds($account2Id));
    }

    public function testTryToUpdateRelationshipForCustomerAccountToNull(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $response = $this->patchRelationship(
            ['entity' => 'customers', 'id' => (string)$customer1Id, 'association' => 'account'],
            [
                'data' => null
            ],
            [],
            false
        );
        $this->assertResponseValidationError(
            [
                'title'  => 'not blank constraint',
                'detail' => 'This value should not be blank.'
            ],
            $response
        );
        self::assertEquals([$customer1Id, $customer2Id], $this->getCustomerIds($account1Id));
    }

    public function testCreateCustomerWithAccount(): void
    {
        $account1Id = $this->getReference('account1')->getId();
        $customer1Id = $this->getReference('customer1')->getId();
        $customer2Id = $this->getReference('customer2')->getId();
        $data = [
            'data' => [
                'type'          => 'customers',
                'attributes'    => [
                    'name' => 'New Customer'
                ],
                'relationships' => [
                    'account' => [
                        'data' => ['type' => 'accounts', 'id' => (string)$account1Id]
                    ]
                ]
            ]
        ];
        $response = $this->post(['entity' => 'customers'], $data);
        $newCustomerId = (int)$this->getResourceId($response);
        $expectedContent = $data;
        $expectedContent['data']['id'] = (string)$newCustomerId;
        $this->assertResponseContains($expectedContent, $response);
        self::assertEquals([$customer1Id, $customer2Id, $newCustomerId], $this->getCustomerIds($account1Id));
    }

    public function testCreateCustomerWithoutAccount(): void
    {
        $data = [
            'data' => [
                'type'       => 'customers',
                'attributes' => [
                    'name' => 'New Customer'
                ]
            ]
        ];
        $response = $this->post(['entity' => 'customers'], $data);
        $newCustomerId = (int)$this->getResourceId($response);
        $expectedContent = $data;
        $expectedContent['data']['id'] = (string)$newCustomerId;
        $expectedContent['data']['relationships']['account']['data']['type'] = 'accounts';
        $this->assertResponseContains($expectedContent, $response);
        $content = self::jsonToArray($response->getContent());
        $newAccountId = (int)$content['data']['relationships']['account']['data']['id'];
        self::assertEquals([$newCustomerId], $this->getCustomerIds($newAccountId));
    }
}
