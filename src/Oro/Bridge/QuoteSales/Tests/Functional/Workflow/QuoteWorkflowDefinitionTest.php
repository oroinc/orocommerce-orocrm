<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Workflow;

use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

/**
 * @dbIsolationPerTest
 */
class QuoteWorkflowDefinitionTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient(
            ['debug' => false],
            $this->generateBasicAuthHeader()
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

        $btn = $crawler->filter('[id^="transition-quote_flow-quote_create"]');

        self::assertStringContainsString(
            'Create Quote',
            $btn->text(),
            'Transition on opportunity that satisfies all the conditions is unavailable'
        );
    }

    public function testWorkflowWithOpportunityFalseCondition()
    {
        $opportunity = $this->getReference('opportunity_won_b2b');

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_sales_opportunity_view', ['id' => $opportunity->getId()])
        );

        $btn = $crawler->filter('[id^="transition-quote_flow-quote_create"]');

        $this->assertEquals(
            0,
            $btn->count(),
            'Transition on opportunity that not satisfies all the conditions is available'
        );

        $opportunity = $this->getReference('opportunity_won');

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_sales_opportunity_view', ['id' => $opportunity->getId()])
        );

        $btn = $crawler->filter('[id^="transition-quote_flow-quote_create"]');

        $this->assertEquals(
            0,
            $btn->count(),
            'Transition on opportunity that not satisfies all the conditions is available'
        );
    }
}
