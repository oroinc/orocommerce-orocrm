<?php

namespace Oro\Bridge\QuoteSales\Tests\Behat\Context;

use Oro\Bridge\QuoteSales\Tests\Behat\Element\EntityStatus;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;
use Oro\Bundle\TestFrameworkBundle\Behat\Element\OroPageObjectAware;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\PageObjectDictionary;

class FeatureContext extends OroFeatureContext implements OroPageObjectAware
{
    use PageObjectDictionary;

    /**
     * Asserts status badge on entity view page
     *
     * Examples: Then I should see "Closed Lost" gray status
     *           Then I should see "Open" green status
     *
     * @Then /^I should see "(?P<status>[^"]+)" (?P<color>(green|gray)) status$/
     */
    public function iShouldSeeColoredStatus($status, $color)
    {
        /** @var EntityStatus $element */
        $element = $this->createElement('Entity Status');

        self::assertEquals($status, $element->getText());
        self::assertEquals($color, $element->getColor());
    }
}
