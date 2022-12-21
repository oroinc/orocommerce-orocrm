@ticket-BB-20600
@fixture-OroContactUsBridgeBundle:CustomerUserFixture.yml
Feature: Contact us widget
  As an Administrator
  I want be able to use "Contact Us" widget for insertion into CMS pages
  So that, we need to implement Contact Us widget, which should be easily inserted into content in any CMS page via Landing pages functionality.

  Scenario: Feature background
    Given sessions active:
      | Admin | first_session  |
      | Guest | second_session |
      | Buyer | system_session |

  Scenario: Enable widget for about page
    Given I proceed as the Admin
    When I login as administrator
    And go to Marketing/ Landing Pages
    And I click "edit" on row "About" in grid
    And I fill in WYSIWYG "CMS Page Content" with "{{widget('contact_us_form')}}"
    And I save and close form
    Then I should see "Page has been saved" flash message

  Scenario: Fill contact us form as unauthorized user
    Given I proceed as the Guest
    And I am on the homepage
    When I click "About"
    And fill form with:
      |First Name              |Test              |
      |Last Name               |Tester            |
      |Preferred contact method|Email             |
      |Email                   |qa@oroinc.com     |
      |Contact Reason          |Other             |
      |Comment                 |Test Comment      |
    And click "Submit"
    Then I should see "Thank you for your Request!" flash message
    When I proceed as the Admin
    And go to Activities/ Contact Requests
    Then I should see Tester in grid with following data:
      | First Name     | Test         |
      | Last Name      | Tester       |
      | Step           | Open         |
      | Email          | qa@oroinc.com|
      | Website        | Default      |
      | Contact Reason | Other        |
      | Step           | Open         |
      | Phone          |              |
      | Customer User  |              |
    When I click "view" on row "qa@oroinc.com" in grid
    Then I should see "Customer User N/A"
    And I should see "Comment Test Comment"

  Scenario: Fill contact us form as authorized user
    Given I proceed as the Buyer
    When I signed in as AmandaRCole@example.org on the store frontend
    And I click "About"
    And fill form with:
      |Preferred contact method|Email             |
      |Contact Reason          |Other             |
      |Comment                 |Testers Comment   |
    And click "Submit"
    Then I should see "Thank you for your Request!" flash message
    When I proceed as the Admin
    And go to Activities/ Contact Requests
    Then I should see Amanda in grid with following data:
      | First Name  | Amanda                  |
      | Last Name   | Cole                    |
      | Email       | AmandaRCole@example.org |
      | Website     | Default                 |
    When I click view "AmandaRCole@example.org" in grid
    Then I should see "Testers Comment"

  Scenario: Check validation messages
    Given I proceed as the Guest
    And I am on the homepage
    When I click "About"
    And I click "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Email      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    When fill form with:
      |Preferred contact method| Phone |
    And I click "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Phone      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    When fill form with:
      |Preferred contact method|Both phone & email   |
    And I click "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Email      |This value should not be blank. |
      |Phone      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    When fill form with:
      |First Name              |Test                                                                                                                                                                                                                                                                                                                                                                           |
      |Last Name               |Tester                                                                                                                                                                                                                                                                                                                                                                         |
      |Email                   |123412312321321321@fdsdfdsfdssfsdfdsfsdfsdfsdfsdfdsfsfsdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd.com                                                                                                                                                                                                            |
      |Phone                   |Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. |
      |Contact Reason          |Other                                                                                                                                                                                                                                                                                                                                                                          |
      |Comment                 |Test Comment                                                                                                                                                                                                                                                                                                                                                                   |
    And click "Submit"
    Then I should see validation errors:
      |Email      | This value is too long. It should have 100 characters or less. |
      |Phone      | This value is too long. It should have 100 characters or less. |
