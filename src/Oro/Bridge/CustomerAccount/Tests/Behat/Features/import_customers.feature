@ticket-BAP-19273
@regression
@fixture-OroCustomerAccountBridgeBundle:ImportCustomerFixture.yml
Feature: Import Customers
  In order to add multiple customers at once
  As an Administrator
  I want to be able to import customers from a CSV file using a provided template

  Scenario: Data Template for Customers
    Given I login as administrator
    And go to Customers/ Customers
    And there is no records in grid
    When I download "Customers" Data Template file
    Then I see Name column
    And I see Parent Id column
    And I see Group Name column
    And I see Tax code column
    And I see Account Id column
    And I see Internal rating Id column
    And I see Payment term Label column
    And I see Owner Id column

  Scenario: Import new Customers
    Given I fill template with data:
      | Id | Name                      | Parent Name | Group Name          | Tax code   | Account Id | Internal rating Id | Payment term Label | Owner ID |
      |    | Company A                 |             | All Customers       | Tax_code_1 | 1          | 2_of_5             | net 30             |          |
      |    | Company A - East Division | Company A   | All Customers       | Tax_code_1 | 2          | 1_of_5             | net 90             |          |
      |    | Company A - West Division | Company A   | All Customers       | Tax_code_1 | 3          | 1_of_5             | net 60             |          |
      |    | Customer G                |             | Wholesale Customers | Tax_code_3 | 4          | 3_of_5             | net 60             | 2        |
      |    | Partner C                 |             | Partners            | Tax_code_3 | 5          | 4_of_5             | net 30             | 1        |
      |    | Wholesaler B              |             | All Customers       | Tax_code_2 | 6          | 4_of_5             | net 60             | 3        |
    When I import file
    And reload the page
    Then Email should contains the following "Errors: 1 processed: 5, read: 6, added: 5, updated: 0, replaced: 0" text
    When I follow "Error log" link from the email
    Then I should see "Error in row #6. You have no access to set given owner"
    When I login as administrator
    And go to Customers/ Customers
    Then I should see following grid:
      | Name                      | Group               | Parent Customer | Internal rating | Payment term | Tax code   | Account                   |
      | Company A                 | All Customers       |                 | 2_of_5          | net 30       | Tax_code_1 | Company A                 |
      | Company A - East Division | All Customers       | Company A       | 1_of_5          | net 90       | Tax_code_1 | Company A - East Division |
      | Company A - West Division | All Customers       | Company A       | 1_of_5          | net 60       | Tax_code_1 | Company A - West Division |
      | Customer G                | Wholesale Customers |                 | 3_of_5          | net 60       | Tax_code_3 | Customer G                |
      | Partner C                 | Partners            |                 | 4_of_5          | net 30       | Tax_code_3 | Partner C                 |
    And number of records should be 5
    When I click view "Partner C" in grid
    Then I should see "Owner: John Doe"
    When I go to Customers/ Customers
    And I click view "Customer G" in grid
    Then I should see "Owner: Viggo Mortensen"

  Scenario: Update Customers
    Given I go to Customers/ Customers
    And I fill template with data:
      | Id   | Name                      | Parent Id | Group Name          | Tax code   | Account Id | Internal rating Id | Payment term Label |
      | test | Company A - 1 new         |           | All Customers       | Tax_code_1 | 1          | 2_of_5             | net 30             |
      | 4    | Company A - East Division | 1         | All Customers       | Tax_code_1 | 2          | 1_of_5             | net 90             |
      | 5    | Company A - West Division | 10        | Partners            | Tax_code_1 | 3          | 1_of_5             | net 60             |
      | 2    | Customer G                |           | Wholesale Customers | Tax_code_3 | 4          | 3_of_5             | net 90             |
      | 3    | Partner C                 |           | Partners            | Tax_code_3 | 5          | 1_of_5             | net 30             |
    And I import file
    And reload the page
    Then Email should contains the following "Errors: 2 processed: 3, read: 5, added: 0, updated: 0, replaced: 3" text
    When I follow "Error log" link from the email
    Then I should see "Error in row #1. Parent customer with ID \"10\" was not found"
    Then I should see "Error in row #1. Id: This value should contain only valid integer."
    When I login as administrator
    And go to Customers/ Customers
    And I should see following grid:
      | Name                      | Group               | Parent Customer | Internal rating | Payment term | Tax code   | Account                   |
      | Company A                 | All Customers       |                 | 2_of_5          | net 30       | Tax_code_1 | Company A                 |
      | Company A - East Division | All Customers       | Company A       | 1_of_5          | net 90       | Tax_code_1 | Company A - East Division |
      | Company A - West Division | All Customers       | Company A       | 1_of_5          | net 60       | Tax_code_1 | Company A - West Division |
      | Customer G                | Wholesale Customers |                 | 3_of_5          | net 90       | Tax_code_3 | Customer G                |
      | Partner C                 | Partners            |                 | 1_of_5          | net 30       | Tax_code_3 | Partner C                 |
    And number of records should be 5

  Scenario: Export - Import Customers
    Given I go to Customers/ Customers
    When I click "Export"
    Then I should see "Export started successfully. You will receive email notification upon completion." flash message
    When I import exported file
    Then I should see "Import started successfully. You will receive an email notification upon completion." flash message
    When I reload the page
    Then Email should contains the following "Errors: 0 processed: 5, read: 5, added: 0, updated: 0, replaced: 5" text
    And I should see following grid:
      | Name                      | Group               | Parent Customer | Internal rating | Payment term | Tax code   | Account                   |
      | Company A                 | All Customers       |                 | 2_of_5          | net 30       | Tax_code_1 | Company A                 |
      | Company A - East Division | All Customers       | Company A       | 1_of_5          | net 90       | Tax_code_1 | Company A - East Division |
      | Company A - West Division | All Customers       | Company A       | 1_of_5          | net 60       | Tax_code_1 | Company A - West Division |
      | Customer G                | Wholesale Customers |                 | 3_of_5          | net 90       | Tax_code_3 | Customer G                |
      | Partner C                 | Partners            |                 | 1_of_5          | net 30       | Tax_code_3 | Partner C                 |
    And number of records should be 5

  Scenario: Import Customers with circular reference
    Given go to Customers/ Customers
    When I fill template with data:
      | Id | Name                       | Parent Id | Group Name    | Tax code   | Account Id | Internal rating Id | Payment term Label |
      | 1  | Company A - 1 circular     | 6         | All Customers | Tax_code_1 | 1          | 2_of_5             | net 30             |
      |    | XX - Customer w/o circular | 1         | All Customers | Tax_code_1 | 1          | 2_of_5             | net 30             |
    And I import file
    And reload the page
    Then Email should contains the following "Errors: 1 processed: 1, read: 2, added: 1, updated: 0, replaced: 0" text
    And I should see following grid:
      | Name                       | Group               | Parent Customer | Internal rating | Payment term | Tax code   | Account                   |
      | Company A                  | All Customers       |                 | 2_of_5          | net 30       | Tax_code_1 | Company A                 |
      | Company A - East Division  | All Customers       | Company A       | 1_of_5          | net 90       | Tax_code_1 | Company A - East Division |
      | Company A - West Division  | All Customers       | Company A       | 1_of_5          | net 60       | Tax_code_1 | Company A - West Division |
      | Customer G                 | Wholesale Customers |                 | 3_of_5          | net 90       | Tax_code_3 | Customer G                |
      | Partner C                  | Partners            |                 | 1_of_5          | net 30       | Tax_code_3 | Partner C                 |
      | XX - Customer w/o circular | All Customers       | Company A       | 2_of_5          | net 30       | Tax_code_1 | Company A                 |
    And number of records should be 6

  Scenario: Import Customers by user without "Create" permissions
    Given user has following permissions
      | Assign | Customer       | Global |
      | Create | Customer       | None   |
      | Delete | Customer       | Global |
      | Edit   | Customer       | Global |
      | Edit   | User           | Global |
      | Assign | User           | Global |
      | Edit   | Customer Group | Global |
      | Edit   | Payment Term   | Global |
    And user has following entity permissions enabled
      | Import Entity Records |
    When I login to dashboard as "userWithoutAssign1" user
    And go to Customers/ Customers
    And check all records in grid
    And click Delete mass action
    And confirm deletion
    And reload the page
    Then there is no records in grid
    When I fill template with data:
      | Id | Name    | Parent Id | Group Name    | Tax code   | Account | Internal rating Id | Payment term Label | Owner Id |
      | 7  | NewUser |           | All Customers | Tax_code_2 | NewUser | 4_of_5             | net 60             | 1        |
    And I import file
    And reload the page
    Then Email should contains the following "Errors: 1 processed: 0, read: 1, added: 0, updated: 0, replaced: 0" text
    And there is no records in grid
    And I click Logout in user menu

  Scenario: Import Customers by user with "Create" permissions but not admin
    Given user has following permissions
      | Create | Customer | Global |
    When I login to dashboard as "userWithAssign1" user
    And go to Customers/ Customers
    And I fill template with data:
      | Id | Name    | Parent Name | Group Name    | Tax code   | Account Id | Internal rating Id | Payment term Label | Owner Id |
      | 7  | NewUser |             | All Customers | Tax_code_2 | 7          | 4_of_5             | net 60             | 1        |
    And I import file
    And reload the page
    Then Email should contains the following "Errors: 0 processed: 1, read: 1, added: 1, updated: 0, replaced: 0" text
    And I should see following grid:
      | Name    | Group         | Parent Customer | Internal rating | Payment term | Tax code   | Account |
      | NewUser | All Customers |                 | 4_of_5          | net 60       | Tax_code_2 | NewUser |
    And number of records should be 1
    When click view "NewUser" in grid
    Then should see "Owner: John Doe"

  Scenario: Import Customers by user without "Assign" permissions
    Given user has following permissions
      | Assign | Customer | None |
    When I login to dashboard as "userWithAssign1" user
    And go to Customers/ Customers
    And I fill template with data:
      | Id | Name    | Parent Name | Group Name    | Tax code   | Account Id | Internal rating Id | Payment term Label | Owner Id |
      | 7  | NewUser |             | All Customers | Tax_code_2 | 7          | 4_of_5             | net 60             | 2        |
    And I import file
    And reload the page
    Then Email should contains the following "Errors: 1 processed: 0, read: 1, added: 0, updated: 0, replaced: 0" text
    When I follow "Error log" link from the email
    Then I should see "Error in row #1. You have no access to set given owner"
    When I login to dashboard as "userWithAssign1" user
    And go to Customers/ Customers
    And I should see following grid:
      | Name    | Group         | Parent Customer | Internal rating | Payment term | Tax code   | Account |
      | NewUser | All Customers |                 | 4_of_5          | net 60       | Tax_code_2 | NewUser |
    And number of records should be 1
    When click view "NewUser" in grid
    Then should see "Owner: John Doe"

  Scenario: Import Customers with only specific columns
    Given I go to Customers/ Customers
    When I fill template with data:
      | Id | Name       |
      | 7  | NewUserXXX |
    And I import file
    And reload the page
    Then Email should contains the following "Errors: 0 processed: 1, read: 1, added: 0, updated: 0, replaced: 1" text
    And I should see following grid:
      | Name       | Group         | Parent Customer | Internal rating | Payment term | Tax code   | Account |
      | NewUserXXX | All Customers |                 | 4_of_5          | net 60       | Tax_code_2 | NewUser |
    And number of records should be 1
