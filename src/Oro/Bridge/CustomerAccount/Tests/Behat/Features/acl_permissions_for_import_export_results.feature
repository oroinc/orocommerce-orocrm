@regression
@ticket-BB-27951
@fixture-OroCustomerAccountBridgeBundle:ImportCustomerFixture.yml

Feature: ACL permissions for import export results
  In order to protect import and export results
  As an Administrator
  I want the results of other users to be available only to users who are permitted to view them

  Scenario: Feature background
    Given sessions active:
      | Admin | first_session  |
      | User  | second_session |

  Scenario: Prepare the Customers import file
    Given I proceed as the Admin
    And I login as administrator
    When I go to Customers/ Customers
    And I download "Customers" Data Template file
    Then I see Name column
    And I fill template with data:
      | Id | Name         | Parent Id | Group Name    | Tax code           | Account Id | Internal rating Id | Payment term Label | Owner ID |
      |    | Wholesaler B |           | All Customers | Tax_code_undefined | 6          | 4_of_5             | net 60             | 3        |

  Scenario: Error log of another user import requires the view permission on import export results
    When I import file
    Then Email should contains the following "Result of importing file" text

    When I proceed as the User
    And I login as "userWithAssign1" user
    And I follow "Error log" link from the email
    Then I should see "403. Forbidden You don't have permission to access this page."
    And I am on dashboard

    When I proceed as the Admin
    And I go to System/User Management/Roles
    And I filter Label as is equal to "Sales Rep"
    And I click edit "Sales Rep" in grid
    And select following permissions:
      | Import/Export result | View:Global |
    And I save form
    Then I should see "Role saved" flash message

    When I proceed as the User
    Then take the "Error log" link from email and download the file from this link

  Scenario: Export file of another user requires the view permission on the exported entity
    Given I proceed as the Admin
    And I go to Customers/ Accounts
    When I click "Export"
    Then I should see "Export started successfully. You will receive email notification upon completion." flash message
    And Email should contains the following "Export performed successfully" text

    When I proceed as the User
    Then take the "Download" link from email and download the file from this link

    When I proceed as the Admin
    And I go to System/User Management/Roles
    And I filter Label as is equal to "Sales Rep"
    And I click edit "Sales Rep" in grid
    And select following permissions:
      | Account | View:None |
    And I save form
    Then I should see "Role saved" flash message

    When I proceed as the User
    And I follow "Download" link from the email
    Then I should see "403. Forbidden You don't have permission to access this page."
    And I am on dashboard

    When I proceed as the Admin
    And select following permissions:
      | Account              | View:Global |
      | Import/Export result | View:User   |
    And I save form
    Then I should see "Role saved" flash message

  Scenario: Own grid export file is available to its owner with the view permission limited to own records
    Given I proceed as the User
    And I reload the page
    And I go to Customers/ Accounts
    When I click "Export Grid"
    And I click "CSV"
    Then I should see "Export started successfully. You will receive email notification upon completion." flash message
    And Email should contains the following "Grid export performed successfully. Download" text
    And take the "Download" link from email and download the file from this link
