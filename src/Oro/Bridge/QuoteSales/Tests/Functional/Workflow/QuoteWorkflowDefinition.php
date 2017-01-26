<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Workflow;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @dbIsolationPerTest
 */
class QuoteWorkflowDefinition extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(
            ['debug' => false],
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );
        $this->client->useHashNavigation(true);

        $this->loadFixtures([
            'Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture',
            'Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture'
        ]);

        /** Activate workflow */
        $this->client->request(
            'GET',
            $this->getUrl('oro_workflow_definition_view', ['name' => 'quote_flow'])
        );

        $href = $this->getLinkFromAttribute(
            $this->client->getCrawler(),
            '#quote_flow-workflow-activate-btn',
            'href'
        );

        $this->client->followRedirects(true);
        $this->client->request(
            'GET',
            $href
        );
    }

    public function testAvailableQuoteFlow()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_workflow_definition_index')
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains(
            'Quote flow',
            $crawler->html(),
            "Quote flow isn't available. Please check System -> Workflows"
        );
    }

    public function testEnableDisableQuoteFlow()
    {
        $result = $this->client->getResponse();

        $this->assertButtonClickSuccess(
            $result,
            "Workflow activated",
            "Error when Quote flow activated, please check Quote flow"
        );

        $this->client->request(
            'GET',
            $this->getUrl('oro_workflow_definition_view', ['name' => 'quote_flow'])
        );

        $href = $this->getLinkFromAttribute(
            $this->client->getCrawler(),
            '#quote_flow-workflow-deactivate-btn',
            'href'
        );

        $this->client->followRedirects(true);
        $this->client->request(
            'GET',
            $href
        );

        $result = $this->client->getResponse();

        $this->assertButtonClickSuccess(
            $result,
            "Workflow deactivated",
            "Error when Quote flow deactivated, please check Quote flow"
        );
    }

    public function testWorkflowWithOpportunityTrueCondition()
    {

        $opportunity = $this->getReference('opportunity');

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_sales_opportunity_view', ['id' => $opportunity->getId()])
        );

        $btn = $crawler->filter('#transition-quote_flow-quote_creating');

        $this->assertContains(
            'Create Quote',
            $btn->text(),
            "Transition on opportunity that satisfies all the conditions is unavailable"
        );

        $href = $this->getLinkFromAttribute($crawler, '#transition-quote_flow-quote_creating', 'data-transition-url');

        $this->client->request(
            'GET',
            $href
        );

        $result = $this->client->getResponse();

        /** @TODO updated this test after finish CRM-7444 */
        $this->assertResponseStatusCodeEquals($result, 200);
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

    /**
     * Get link from selector attribute
     * $selector - element, element Id or element class
     * $attribute - element attribute like "href
     *
     * @param Crawler   $crawler
     * @param string    $selector
     * @param string    $attribute
     *
     * @return string
     */
    protected function getLinkFromAttribute(Crawler $crawler, $selector, $attribute)
    {
        $element = $crawler->filter($selector);

        $attributeData = $element->extract([$attribute]);

        return array_shift($attributeData);
    }
}
