<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;
use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadCustomer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    private Account $account;
    private Customer $customer;

    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->loadFixtures([LoadCustomer::class]);

        $this->account = self::getContainer()->get('doctrine')
            ->getRepository(Account::class)
            ->findOneBy(['name' => LoadAccount::ACCOUNT_1]);

        $this->customer = self::getContainer()->get('doctrine')
            ->getRepository(Customer::class)
            ->findOneBy(['name' => LoadCustomer::CUSTOMER_1]);
    }

    public function testAccountCustomersInfoAction()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_widget_customers_info',
                [
                    'accountId' => $this->account->getId(),
                    'channelId' => $this->customer->getDataChannel()->getId()
                ]
            )
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        self::assertStringContainsString('simple_customer', $crawler->filter('div.account-customer-title')->html());

        $tabs = $crawler->filter('div.oro-tabs')->html();
        self::assertStringContainsString('Customer Users', $tabs);
        self::assertStringContainsString('Shopping Lists', $tabs);
        self::assertStringContainsString('Requests For Quote', $tabs);
        self::assertStringContainsString('Quotes', $tabs);
        self::assertStringContainsString('Orders', $tabs);
        self::assertStringContainsString('Opportunities', $tabs);
    }

    public function testCustomerInfoAction()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_customer_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $tabs = $crawler->filter('div.oro-tabs')->html();
        self::assertStringContainsString('Customer Users', $tabs);
        self::assertStringContainsString('Shopping Lists', $tabs);
        self::assertStringContainsString('Requests For Quote', $tabs);
        self::assertStringContainsString('Orders', $tabs);
        self::assertStringContainsString('Quotes', $tabs);
    }

    public function testCustomerUserAction()
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_customer_user_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testShoppingListsAction()
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_shopping_lists_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testRfqAction()
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_rfq_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testOrdersAction()
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_orders_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testOpportunitiesAction()
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_opportunities_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testQuotesAction()
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_account_customer_widget_quotes_info',
                [
                    'id' => $this->customer->getId()
                ]
            ),
            ['_widgetContainer' => 'widget']
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }
}
