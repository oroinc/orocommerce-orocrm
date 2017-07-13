<?php

namespace Oro\Bridge\CustomerAccount\Tests\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;

use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\OroMainContext;

class CustomerAccountContext extends OroFeatureContext
{
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
}
