@ticket-BB-22937
@regression

Feature: Creating new customers with different strategies
  Check whether separate accounts are created for each commercial customer or one account for the root commercial
  customer.

  Scenario: Create root customer with 'each' strategy
    Given I login as administrator
    And go to Customers / Customers
    When I click "Create Customer"
    And fill "Customer Form" with:
      | Name | Root Customer 1 |
    And save and close form
    Then should see "Customer has been saved" flash message

  Scenario: Import root customer with 'each' strategy
    Given I go to Customers/ Customers
    And download "Customers" Data Template file
    When I fill template with data:
      | Name            |
      | Root Customer 2 |
    And import file
    And reload the page
    Then Email should contains the following "Errors: 0 processed: 1, read: 1, added: 1, updated: 0, replaced: 0" text
    And should see following grid:
      | Name            | Parent Customer | Account         |
      | Root Customer 1 |                 | Root Customer 1 |
      | Root Customer 2 |                 | Root Customer 2 |
    And number of records should be 2

  Scenario: Create child customer with 'each' strategy
    Given I go to Customers / Customers
    When I click "Create Customer"
    And fill "Customer Form" with:
      | Name            | Child Customer 1 |
      | Parent Customer | Root Customer 1  |
    And save and close form
    Then should see "Customer has been saved" flash message

  Scenario: Import child customer with 'each' strategy
    Given I go to Customers/ Customers
    When I fill template with data:
      | Name             | Parent Name     |
      | Child Customer 2 | Root Customer 2 |
    And import file
    And reload the page
    Then Email should contains the following "Errors: 0 processed: 1, read: 1, added: 1, updated: 0, replaced: 0" text
    And should see following grid:
      | Name             | Parent Customer | Account          |
      | Child Customer 1 | Root Customer 1 | Child Customer 1 |
      | Child Customer 2 | Root Customer 2 | Child Customer 2 |
      | Root Customer 1  |                 | Root Customer 1  |
      | Root Customer 2  |                 | Root Customer 2  |
    And number of records should be 4

  Scenario: Change configuration
    Given I go to System/ Configuration
    And follow "System Configuration/Integration/CRM and Commerce" on configuration sidebar
    When I fill "Account and Customer Relation Configuration Form" with:
      | Creation New Account Default | false                           |
      | Creation New Account         | Only for root Commerce Customer |
    And save form
    Then I should see "Configuration saved" flash message

  Scenario: Create child customer with 'root' strategy
    Given I go to Customers / Customers
    When I click "Create Customer"
    And fill "Customer Form" with:
      | Name            | Child Customer 3 |
      | Parent Customer | Root Customer 1  |
    And save and close form
    Then should see "Customer has been saved" flash message

  Scenario: Import child customer with 'each' strategy
    Given I go to Customers/ Customers
    And download "Customers" Data Template file
    When I fill template with data:
      | Name             | Parent Name     |
      | Child Customer 4 | Root Customer 2 |
    And import file
    And reload the page
    Then Email should contains the following "Errors: 0 processed: 1, read: 1, added: 1, updated: 0, replaced: 0" text
    And should see following grid:
      | Name             | Parent Customer | Account         |
      | Child Customer 1 | Root Customer 1 | Root Customer 1 |
      | Child Customer 2 | Root Customer 2 | Root Customer 2 |
      | Child Customer 3 | Root Customer 1 | Root Customer 1 |
      | Child Customer 4 | Root Customer 2 | Root Customer 2 |
      | Root Customer 1  |                 | Root Customer 1 |
      | Root Customer 2  |                 | Root Customer 2 |
    And number of records should be 6
