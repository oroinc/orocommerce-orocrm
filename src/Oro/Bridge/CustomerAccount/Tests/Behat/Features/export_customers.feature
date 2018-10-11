@regression
@ticket-BB-7843
@automatically-ticket-tagged
@fixture-OroCustomerAccountBridgeBundle:ExportCustomerFixture.yml
Feature: Export Customers
  In order to export list of customers
  As an Administrator
  I want to have the Export button on the Customers -> Customers page

  Scenario: Export Customers
    Given I login as administrator
    And I go to Customers/Customers
    When I click "Export"
    Then I should see "Export started successfully. You will receive email notification upon completion." flash message
    And Email should contains the following "Export performed successfully. 6 customers were exported. Download" text
    And Exported file for "Customers" contains at least the following columns:
      | Id  | Name                       |  Parent Id   |  Group Name         |Owner Id| Tax code   | Account Id  | Internal rating Id | Payment term Label |
      |  1  | Company A                  |              | All Customers       | 1      | Tax_code_1 | 1           | 2_of_5             |        net 30      |
      |  2  | Company A - East Division  |  1           | All Customers       | 1      | Tax_code_1 | 2           | 1_of_5             |        net 90      |
      |  3  | Company A - West Division  |  1           | All Customers       | 1      | Tax_code_1 | 3           | 1_of_5             |        net 60      |
      |  4  | Customer G                 |              | Wholesale Customers | 2      | Tax_code_3 | 4           | 3_of_5             |        net 60      |
      |  5  | Partner C                  |              | Partners            | 2      | Tax_code_3 | 5           | 4_of_5             |        net 30      |
      |  6  | Wholesaler B               |              | All Customers       | 2      | Tax_code_2 | 6           | 4_of_5             |        net 60      |
    And I click Logout in user menu
