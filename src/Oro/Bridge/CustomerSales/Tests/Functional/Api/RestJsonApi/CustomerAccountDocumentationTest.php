<?php

namespace Oro\Bridge\CustomerSales\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Request\ApiAction;
use Oro\Bundle\ApiBundle\Tests\Functional\DocumentationTestTrait;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;

/**
 * @group regression
 */
class CustomerAccountDocumentationTest extends RestJsonApiTestCase
{
    use DocumentationTestTrait;

    /** @var string used in DocumentationTestTrait */
    private const VIEW = 'rest_json_api';

    private static bool $isDocumentationCacheWarmedUp = false;

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::$isDocumentationCacheWarmedUp) {
            $this->warmUpDocumentationCache();
            self::$isDocumentationCacheWarmedUp = true;
        }
    }

    public function testCustomerAccount(): void
    {
        $docs = $this->getEntityDocsForAction('customers', ApiAction::GET);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The account associated with the customer record.</p>',
            $resourceData['response']['account']['description']
        );
    }

    public function testCustomerAccountForCreate(): void
    {
        $docs = $this->getEntityDocsForAction('customers', ApiAction::CREATE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The account associated with the customer record.</p>'
            . '<p><strong>If not specified, a new account will be created.</strong></p>',
            $resourceData['parameters']['account']['description']
        );
    }

    public function testCustomerAccountForUpdate(): void
    {
        $docs = $this->getEntityDocsForAction('customers', ApiAction::UPDATE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The account associated with the customer record.</p>'
            . '<p><strong>The required field.</strong></p>',
            $resourceData['parameters']['account']['description']
        );
    }

    public function testAccountCustomers(): void
    {
        $docs = $this->getEntityDocsForAction('accounts', ApiAction::GET);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The customers associated with the account record.</p>',
            $resourceData['response']['customers']['description']
        );
    }

    public function testAccountCustomersForCreate(): void
    {
        $docs = $this->getEntityDocsForAction('accounts', ApiAction::CREATE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The customers associated with the account record.</p>'
            . '<p><strong>The read-only field. A passed value will be ignored.</strong></p>',
            $resourceData['parameters']['customers']['description']
        );
    }

    public function testAccountCustomersForUpdate(): void
    {
        $docs = $this->getEntityDocsForAction('accounts', ApiAction::UPDATE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The customers associated with the account record.</p>'
            . '<p><strong>The read-only field. A passed value will be ignored.</strong></p>',
            $resourceData['parameters']['customers']['description']
        );
    }

    public function testCustomerAccountGetSubresource(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('customers', 'account', ApiAction::GET_SUBRESOURCE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get account', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the account record associated with a specific customer record.</p>',
            $resourceData['documentation']
        );
    }

    public function testCustomerAccountGetRelationship(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('customers', 'account', ApiAction::GET_RELATIONSHIP);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get "account" relationship', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the ID of the account associated with a specific customer record.</p>',
            $resourceData['documentation']
        );
    }

    public function testAccountCustomersGetSubresource(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('accounts', 'customers', ApiAction::GET_SUBRESOURCE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get customers', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the records of the customers associated with a specific account record.</p>',
            $resourceData['documentation']
        );
    }

    public function testAccountCustomersGetRelationship(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('accounts', 'customers', ApiAction::GET_RELATIONSHIP);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get "customers" relationship', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the IDs of the customers associated with a specific account record.</p>',
            $resourceData['documentation']
        );
    }
}
