@ticket-CRM-8748
@fixture-OroCustomerSalesBridgeBundle:CustomerReportFixture.yml

Feature: Commerce Customer report with Opportunity and Lead relations
  In order to manage reports
  As an Administrator
  I need to be able to use Opportunity and Lead relations in report for Customer entity

  Scenario: Created report with Opportunity and Lead relations
    Given I login as administrator
    And I go to Reports & Segments/ Manage Custom Reports
    And I click "Create Report"
    When I fill form with:
      | Name        | Customer Report |
      | Entity      | Customer        |
      | Report Type | Table           |
    And I add the following columns:
      | Name                          |
      | Opportunity->Opportunity name |
      | Lead->Lead name               |
    And I save and close form
    Then I should see "Report saved" flash message
    And there are 10 records in grid
    And I should see following grid:
      | Name        | Opportunity name | Lead name |
      | Customer 1  | Opportunity 1    |           |
      | Customer 2  | Opportunity 2    |           |
      | Customer 3  | Opportunity 3    |           |
      | Customer 4  | Opportunity 4    |           |
      | Customer 5  | Opportunity 5    |           |
      | Customer 6  |                  | Lead 6    |
      | Customer 7  |                  | Lead 7    |
      | Customer 8  |                  | Lead 8    |
      | Customer 9  |                  | Lead 9    |
      | Customer 10 |                  | Lead 10   |
