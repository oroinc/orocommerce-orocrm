@fixture-ExportCustomerFixture.yml @dn
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
      | Id  | Name                       |  Parent Name |  Group Name         | Tax code   | Payment term Label   | Account                    |Internal rating Id|
      |  1  | Company A                  |              | All Customers       | Tax_code_1 |        net 30        | Company A                  |2_of_5            |
      |  2  | Company A - East Division  |  Company A   | All Customers       | Tax_code_1 |        net 90        | Company A - East Division  |1_of_5            |
      |  3  | Company A - West Division  |  Company A   | All Customers       | Tax_code_1 |        net 60        | Company A - West Division  |1_of_5            |
      |  4  | Wholesaler B               |              | All Customers       | Tax_code_2 |        net 60        | Wholesaler B               |4_of_5            |
      |  5  | Partner C                  |              | Partners            | Tax_code_3 |        net 30        | Partner C                  |4_of_5            |
      |  6  | Customer G                 |              | Wholesale Customers | Tax_code_3 |        net 60        | Customer G                 |3_of_5            |
    And I click Logout in user menu
