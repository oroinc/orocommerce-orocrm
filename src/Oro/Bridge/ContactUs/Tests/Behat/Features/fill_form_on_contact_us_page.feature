@regression
@fixture-OroContactUsBridgeBundle:CustomerUserFixture.yml
Feature: Contact us page fill form
  As a Customer User
  I want be able to contact Seller via Contact form on the website
  So that, we need to add additional page with Contact Us form in it.

  Scenario: Fill contact us form as unauthorized user
    Given I am on the homepage
    When I follow "Contact Us"
    Then Page title equals to "Contact Us"
    And fill form with:
      |First Name              |Test              |
      |Last Name               |Tester            |
      |Preferred contact method|Email             |
      |Email                   |qa@oroinc.com     |
      |Contact Reason          |Other             |
      |Comment                 |Test Comment      |
    And press "Submit"
    And I should see "Thank you for your Request!" flash message
    And I login as administrator
    And go to Activities/ Contact Requests
    And I should see Tester in grid with following data:
      | First Name  | Test         |
      | Last Name   | Tester       |
      | Step        | Open         |
      | Email       | qa@oroinc.com|
    And I click view "qa@oroinc.com" in grid
    And I should see "Test Comment"
    And I should see "Customer User N/A"
    And I click logout in user menu

  Scenario: Fill contact us form as authorized user
    Given I signed in as AmandaRCole@example.org on the store frontend
    When I follow "Contact Us"
    Then Page title equals to "Contact Us"
    And fill form with:
      |Preferred contact method|Email             |
      |Contact Reason          |Other             |
      |Comment                 |Testers Comment   |
    And press "Submit"
    And I should see "Thank you for your Request!" flash message
    And I press "Sign Out"
    And I login as administrator
    And go to Activities/ Contact Requests
    And I should see Amanda in grid with following data:
      | First Name  | Amanda                  |
      | Last Name   | Cole                    |
      | Email       | AmandaRCole@example.org |
    And I click view "AmandaRCole@example.org" in grid
    And I should see "Testers Comment"
    And I should see "Customer User Amanda Cole"
    And I click logout in user menu

  Scenario: Check validation messages
    Given I am on the homepage
    When I follow "Contact Us"
    Then Page title equals to "Contact Us"
    When I press "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Email      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    And fill form with:
      |Preferred contact method|Phone    |
    When I press "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Phone      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    And fill form with:
      |Preferred contact method|Both phone & email   |
    When I press "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Email      |This value should not be blank. |
      |Phone      |This value should not be blank. |
      |Comment    |This value should not be blank. |
