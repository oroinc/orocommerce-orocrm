<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Workflow;

use Symfony\Component\DomCrawler\Form;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture;

/**
 * @dbIsolationPerTest
 */
class QuoteWorkflowCreateTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader());
        $this->client->useHashNavigation(true);

        $this->loadFixtures([
            CreateDefaultAccountFixture::class,
            OpportunityQuotesListenerFixture::class
        ]);

        /** @var WorkflowManager $workflowManager */
        $workflowManager = $this->client->getContainer()->get('oro_workflow.manager');
        $workflowManager->activateWorkflow('quote_flow');
    }

    public function testWorkflowWithOpportunityTrueCondition()
    {
        /** @var Opportunity $opportunity */
        $opportunity = $this->getReference('opportunity');
        /** @var Customer $customer */
        $customer = $this->getReference('customer');

        $this->client->request(
            'GET',
            $this->getUrl('oro_workflow_api_rest_workflow_start', [
                'workflowName' => 'quote_flow',
                'transitionName' => 'quote_create',
                'entityId' => $opportunity->getId()
            ])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $crawler = $this->client->request(
            'GET',
            $result['workflowItem']['result']['redirectUrl'],
            [],
            [],
            $this->generateBasicAuthHeader()
        );
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();

        $this->assertEquals($opportunity->getId(), $form->get('oro_sale_quote[opportunity]')->getValue());
        $this->assertEquals($customer->getId(), $form->get('oro_sale_quote[customer]')->getValue());
    }
}
