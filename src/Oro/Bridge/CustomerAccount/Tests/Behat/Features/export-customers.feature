@ticket-BB-7843
@automatically-ticket-tagged
@fixture-ExportCustomerFixture.yml
Feature: Export Customers
  In order to export list of customers
  As an Administrator
  I want to have the Export button on the Customers -> Customers page

  Scenario: Export Customers
    Given I login as administrator
    And I go to Customers/Customers
    When I press "Export"
    Then I should see "Export started successfully. You will receive email notification upon completion." flash message
    And Email should contains the following "Export performed successfully. 6 customers were exported. Download" text
    And Exported file for "Customers" contains the following data:
      | Id  | Name                       |  Parent Name |  Group Name         | Tax code   | Account                    | Internal rating Id | Payment term Label |
      |  1  | Company A                  |              | All Customers       | Tax_code_1 | Company A                  | 2_of_5             |        net 30      |
      |  2  | Company A - East Division  |  Company A   | All Customers       | Tax_code_1 | Company A - East Division  | 1_of_5             |        net 90      |
      |  3  | Company A - West Division  |  Company A   | All Customers       | Tax_code_1 | Company A - West Division  | 1_of_5             |        net 60      |
      |  4  | Wholesaler B               |              | All Customers       | Tax_code_2 | Wholesaler B               | 4_of_5             |        net 60      |
      |  5  | Partner C                  |              | Partners            | Tax_code_3 | Partner C                  | 4_of_5             |        net 30      |
      |  6  | Customer G                 |              | Wholesale Customers | Tax_code_3 | Customer G                 | 3_of_5             |        net 60      |
    And I click Logout in user menu
