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
}
