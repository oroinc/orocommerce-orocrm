<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Workflow;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture;

/**
 * @dbIsolationPerTest
 */
class QuoteWorkflowDefinitionTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->initClient(
            ['debug' => false],
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );
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
        $opportunity = $this->getReference('opportunity');

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_sales_opportunity_view', ['id' => $opportunity->getId()])
        );

        $btn = $crawler->filter('#transition-quote_flow-quote_create');

        $this->assertContains(
            'Create Quote',
            $btn->text(),
            "Transition on opportunity that satisfies all the conditions is unavailable"
        );
    }

    public function testWorkflowWithOpportunityFalseCondition()
    {
        $opportunity = $this->getReference('opportunity_won_b2b');

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_sales_opportunity_view', ['id' => $opportunity->getId()])
        );

        $btn = $crawler->filter('#transition-quote_flow-quote_creating');

        $this->assertEquals(
            0,
            $btn->count(),
            "Transition on opportunity that not satisfies all the conditions is available"
        );

        $opportunity = $this->getReference('opportunity_won');

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_sales_opportunity_view', ['id' => $opportunity->getId()])
        );

        $btn = $crawler->filter('#transition-quote_flow-quote_creating');

        $this->assertEquals(
            0,
            $btn->count(),
            "Transition on opportunity that not satisfies all the conditions is available"
        );
    }
}
