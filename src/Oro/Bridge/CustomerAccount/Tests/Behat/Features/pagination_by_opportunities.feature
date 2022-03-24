@fix-BAP-16833
@fixture-OroCustomerAccountBridgeBundle:opportunities.yml
Feature: Pagination by opportunities
  In order to use pagination
  As an administrator
  I want to be able to use pagination by Opportunities

  Scenario: Opportunities navigation (mobile version)
    Given I set window size to 375x640
    And I login as administrator
    And I go to Sales/Opportunities
    And I should see "Testing opportunity3" in grid
    And I should see "Testing opportunity1" in grid
    And I click View "Testing opportunity3" in grid
    And I should see "Testing opportunity3" in the "Opportunity Title" element
    When I click "Next opportunity icon"
    Then I should see "Testing opportunity1" in the "Opportunity Title" element
