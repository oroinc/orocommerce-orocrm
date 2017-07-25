@skip
@fixture-OroContactUsBridgeBundle:CustomerUserFixture.yml
Feature: Contact us widget
  As an Administrator
  I want be able to use "Contact Us" widget for insertion into CMS pages
  So that, we need to implement Contact Us widget, which should be easily inserted into content in any CMS page via Landing pages functionality.

  Scenario: Enable widget for about page
    Given I login as administrator
    And go to Marketing/ Landing Pages
    And I click edit "About" in grid
    And I fill in "CMS Page Content" with "{{widget('contact_us_form')}}"
    When I save and close form
    Then I should see "Page has been saved" flash message
    And I click logout in user menu

  Scenario:Fill contact us form as unauthorized user
    Given I go to "/about"
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
      | Website     | Default      |
    And I click view "qa@oroinc.com" in grid
    And I should see "Test Comment"
    And I click logout in user menu

  Scenario:Fill contact us form as authorized user
    Given I signed in as AmandaRCole@example.org on the store frontend
    And go to "/about"
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
      | Website     | Default                 |
    And I click view "AmandaRCole@example.org" in grid
    And I should see "Testers Comment"
    And I click logout in user menu

  Scenario:Check validation messages
    Given I go to "/about"
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
