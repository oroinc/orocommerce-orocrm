<?php

namespace Oro\Bridge\CustomerAccount\Tests\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;

use Oro\Bundle\ImportExportBundle\Tests\Behat\Context\ImportExportContext;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\MessageQueueIsolatorAwareInterface;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\MessageQueueIsolatorInterface;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\OroMainContext;

class CustomerAccountContext extends OroFeatureContext implements MessageQueueIsolatorAwareInterface
{
    /**
     * @var MessageQueueIsolatorInterface
     */
    private $messageQueueIsolator;

    /**
     * @var ImportExportContext
     */
    private $importExportContext;

    /**
     * @var OroMainContext
     */
    private $mainContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->importExportContext = $environment->getContext(ImportExportContext::class);
        $this->mainContext = $environment->getContext(OroMainContext::class);
    }

    /**
     * @Given /^Email should contains the following "([^"]*)" text$/
     * @param string $text
     */
    public function emailShouldContainsTheFollowingText($text)
    {
        //todo: to be implemented in scope of CRM-7599. Consulted with Serhii Polishchuk
    }

    /**
     * @When /^I import customers file$/
     */
    public function iImportCustomersFile()
    {
        $this->importExportContext->iImportFile();
        // need to wait for so long because of postpone processing of customers rows. See PostponeRowsHandler consts
        $this->messageQueueIsolator->waitWhileProcessingMessages(600);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageQueueIsolator(MessageQueueIsolatorInterface $messageQueueIsolator)
    {
        $this->messageQueueIsolator = $messageQueueIsolator;
    }
}
