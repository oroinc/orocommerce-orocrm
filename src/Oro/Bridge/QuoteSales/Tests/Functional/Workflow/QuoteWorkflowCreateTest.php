<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Workflow;

use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;
use Symfony\Component\DomCrawler\Form;

/**
 * @dbIsolationPerTest
 */
class QuoteWorkflowCreateTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
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

        $this->ensureSessionIsAvailable();
    }

    public function testWorkflowWithOpportunityTrueCondition()
    {
        /** @var Opportunity $opportunity */
        $opportunity = $this->getReference('opportunity');
        /** @var Customer $customer */
        $customer = $this->getReference('customer');

        $this->ajaxRequest(
            'POST',
            $this->getUrl('oro_api_workflow_start', [
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
