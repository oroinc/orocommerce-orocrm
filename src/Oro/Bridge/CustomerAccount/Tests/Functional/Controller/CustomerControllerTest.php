<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;
use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadCustomer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    /** @var  Account */
    protected $account;

    /** @var  Customer */
    protected $customer;

    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->loadFixtures(
            [
                'Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadCustomer'
            ]
        );

        $manager = $this->client->getContainer()->get('doctrine')->getManagerForClass(
            'OroAccountBundle:Account'
        );

        $this->account = $manager->getRepository('OroAccountBundle:Account')->findOneBy(
            ['name' => LoadAccount::ACCOUNT_1]
        );

        $manager = $this->client->getContainer()->get('doctrine')->getManagerForClass(
            'OroCustomerBundle:Customer'
        );

        $this->customer = $manager->getRepository('OroCustomerBundle:Customer')->findOneBy(
            ['name' => LoadCustomer::CUSTOMER_1]
        );
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

        static::assertStringContainsString('simple_customer', $crawler->filter('div.account-customer-title')->html());

        $tabs = $crawler->filter('div.oro-tabs')->html();
        static::assertStringContainsString('Customer Users', $tabs);
        static::assertStringContainsString('Shopping Lists', $tabs);
        static::assertStringContainsString('Requests For Quote', $tabs);
        static::assertStringContainsString('Quotes', $tabs);
        static::assertStringContainsString('Orders', $tabs);
        static::assertStringContainsString('Opportunities', $tabs);
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
        static::assertStringContainsString('Customer Users', $tabs);
        static::assertStringContainsString('Shopping Lists', $tabs);
        static::assertStringContainsString('Requests For Quote', $tabs);
        static::assertStringContainsString('Orders', $tabs);
        static::assertStringContainsString('Quotes', $tabs);
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
