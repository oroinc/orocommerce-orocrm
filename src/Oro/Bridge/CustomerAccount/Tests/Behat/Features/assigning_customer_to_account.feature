@ticket-BB-11261
@ticket-BB-17389

Feature: Assigning Customer to Account
  In order to create Customer assigned to Account
  As an Administrator
  I want to see Customer in Commerce Channels in Account View

  Scenario: Account field should be optional when creating customer to allow for auto account creation
    Given I login as administrator
    And I go to Customers / Customers
    When I click "Create Customer"
    Then Account is not required field
    When I fill form with:
      | Owner | John Doe         |
      | Name  | Testing Customer |
    And I save and close form
    Then I should see "Customer has been saved" flash message
    When I go to Customers / Accounts
    And click view "Testing Customer" in grid
    Then I should see "Testing Customer"

  Scenario: Account field should be required when editing customer
    When I go to Customers / Customers
    And I click edit "Testing Customer" in grid
    Then "Customer Form" must contains values:
      | Account | Testing Customer |
    And Account is a required field
