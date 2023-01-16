@ticket-BAP-14250
@feature-BB-21879
@automatically-ticket-tagged
@fixture-OroCustomerAccountBridgeBundle:opportunity_from_related.yml
Feature: Create Opportunity from Customers entity view
  In order to ease opportunity management
  as a Sales Rep
  I should have a possibility to create Opportunity from related entity views

  Scenario: Sales Rep creates Opportunity for Customers
    Given I login as "Johnconnor8" user
    When I go to Customers/Customers
    And click View CommSkyNet in grid
    And I click "More actions"
    And click "Create Opportunity"
    And I fill in "Opportunity name" with "Fourth Invasion"
    And I save and close form
    Then I should see Fourth Invasion with:
      | Opportunity Name | Fourth Invasion|
      | Status           | Open           |
      | Account          | SkyNet         |
      | Probability      | 0%             |
