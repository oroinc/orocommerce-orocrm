@regression
@smoke
@fixture-OroContactUsBridgeBundle:CustomerUserFixture.yml
Feature: Contact Requests Activities
  In order to check Activity entity on admin panel
  As a Admin
  I want to start end to end test

  Scenario: Create a contact request
    Given login as administrator
    And go to Activities/ Contact Requests
    And click "Create Contact Request"
    And fill form with:
      | First name    | Joshua        |
      | Last name     | Field         |
      | Email         | test@test.com |
      | Comment       | testComment   |
      | Customer User | Amanda Cole   |
    And save and close form
    And should see "Contact request has been saved successfully" flash message
    And click "Log call"
    And fill "Log Call Form" with:
      | Subject             | Call for contact request       |
      | Additional comments | Offered                        |
      | Call date & time    | <DateTime:2016-12-31 08:00:00> |
      | Phone number        | 0503508888                     |
      | Direction           | Incoming                       |
      | Duration            | 40s                            |
    And click "Log call"
    Then should see "Call saved" flash message
    And should see "Call for contact request - Offered"
    And should see "Call logged by John Doe"
    And go to Activities/ Calls
    And click view "Call for contact request" in grid
    And should see "Context Joshua Field"

  Scenario: Manage Contact Requests
    Given go to Activities/ Contact Requests
    And click view "Joshua" in grid
    When click "Convert to Opportunity"
    And click "Submit"
    Then should see "Converted to Opportunity"
    And go to Sales/ Opportunities
    And I should see following grid:
      | Opportunity name | Status | Owner    |
      | Joshua Field     | Open   | John Doe |
    And go to Activities/ Contact Requests
    And click "Create Contact Request"
    And fill form with:
      | First name | Bob           |
      | Last name  | Cornelius     |
      | Email      | rest@test.com |
      | Comment    | restComment   |
    And save and close form
    When click "Convert to Lead"
    And click "Submit"
    Then should see "Converted to Lead"
    And go to Sales/ Leads
    And I should see following grid:
      | Lead name     | Status | First name | Last name | Email         | Owner    |
      | Bob Cornelius | New    | Bob        | Cornelius | rest@test.com | John Doe |
    And go to Activities/ Contact Requests
    And click "Create Contact Request"
    And fill form with:
      | First name | Janet          |
      | Last name  | Graham         |
      | Email      | trest@test.com |
      | Comment    | trestComment   |
    And save and close form
    When click "Resolve"
    And type "test test" in "Feedback"
    And click "Submit"
    Then should see "Resolved"
    When click "Delete"
    And click "Yes, Delete"
    Then I should see "Contact Request deleted" flash message
    And go to Activities/ Contact Requests
    And I should see following grid:
      | First name | Last name | Email         | Step                     |
      | Bob        | Cornelius | rest@test.com | Converted to Lead        |
      | Joshua     | Field     | test@test.com | Converted to Opportunity |
