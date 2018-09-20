@ticket-BB-11261
@fixture-OroCustomerAccountBridgeBundle:AssigningCustomerToAccountFixture.yml
Feature: Assigning Customer to Account
  In order to create Customer assigned to Account
  As an Administrator
  I want to see Customer in Commerce Channels in Account View

  Scenario: Export Customers
    Given I login as administrator
    And I go to Customers / Customers
    When I click "Create Customer"
    And I fill form with:
      | Name    | Testing Customer |
      | Account | Account A        |
    And I save and close form
    Then I should see "Customer has been saved" flash message
    And I go to Customers / Accounts
    And click view "Account 1" in grid
    And I should see "Testing Customer"
