@regression
@fixture-OroContactUsBridgeBundle:CustomerUserFixture.yml
@fixture-OroLocaleBundle:LocalizationFixture.yml
Feature: Contact us page fill form
  As a Customer User
  I want be able to contact Seller via Contact form on the website
  So that, we need to add additional page with Contact Us form in it.

  Scenario: Create two session
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |

  Scenario: Add localization for contact reason
    And I proceed as the Admin
    And I login as administrator
    And I enable the existing localizations
    And go to System/ Contact Reasons
    And I should see Want to know more about the product in grid with following data:
      | Label | Want to know more about the product |
    And I click edit "Want to know more about the product" in grid
    And I click on "Contact Reason Form Label Fallbacks"
    And fill "Contact Reason Form" with:
      | Label Second Use Default | false                                                |
      | Label Second             | Want to know more about the product (Localization 1) |
    And I save and close form
    And I should see "Contact reason has been saved successfully" flash message

  Scenario: Fill contact us form as unauthorized user
    Given I proceed as the Buyer
    And I am on the homepage
    When I follow "Contact Us"
    Then Page title equals to "Contact Us"
    And fill form with:
      |First Name              |Test              |
      |Last Name               |Tester            |
      |Preferred contact method|Email             |
      |Email                   |qa@oroinc.com     |
      |Contact Reason          |Other             |
      |Comment                 |Test Comment      |
    And click "Submit"
    And I should see "Thank you for your Request!" flash message

  Scenario: Fill contact us form with localized contact reason
    Given I click "Localization Switcher"
    And I select "Localization 1" localization
    And fill form with:
      | First Name               | Branda                                               |
      | Last Name                | Smith                                                |
      | Preferred contact method | Email                                                |
      | Email                    | branda@oroinc.com                                    |
      | Contact Reason           | Want to know more about the product (Localization 1) |
      | Comment                  | Test Comment                                         |
    And click "Submit"
    And I should see "Thank you for your Request!" flash message
    And I press "Localization Switcher"
    And I select "English" localization

  Scenario: Check contact requests
    And I proceed as the Admin
    And go to Activities/ Contact Requests
    And I should see Tester in grid with following data:
      | First Name  | Test         |
      | Last Name   | Tester       |
      | Step        | Open         |
      | Email       | qa@oroinc.com|
    And I should see Smith in grid with following data:
      | First Name     | Branda                              |
      | Email          | branda@oroinc.com                   |
      | Contact Reason | Want to know more about the product |
    And I click view "qa@oroinc.com" in grid
    And I should see "Test Comment"
    And I should see "Customer User N/A"

  Scenario: Fill contact us form as authorized user
    Given I proceed as the Buyer
    And I signed in as AmandaRCole@example.org on the store frontend
    When I follow "Contact Us"
    Then Page title equals to "Contact Us"
    And fill form with:
      |Preferred contact method|Email             |
      |Contact Reason          |Other             |
      |Comment                 |Testers Comment   |
    And click "Submit"
    And I should see "Thank you for your Request!" flash message
    And I click "Sign Out"
    And I proceed as the Admin
    And go to Activities/ Contact Requests
    And I should see Amanda in grid with following data:
      | First Name  | Amanda                  |
      | Last Name   | Cole                    |
      | Email       | AmandaRCole@example.org |
    And I click view "AmandaRCole@example.org" in grid
    And I should see "Testers Comment"
    And I should see "Customer User Amanda Cole"

  Scenario: Check validation messages
    Given I proceed as the Buyer
    And I am on the homepage
    When I follow "Contact Us"
    Then Page title equals to "Contact Us"
    When I click "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Email      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    And fill form with:
      |Preferred contact method|Phone    |
    When I click "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Phone      |This value should not be blank. |
      |Comment    |This value should not be blank. |
    And fill form with:
      |Preferred contact method|Both phone & email   |
    When I click "Submit"
    Then I should see validation errors:
      |First name |This value should not be blank. |
      |Last name  |This value should not be blank. |
      |Email      |This value should not be blank. |
      |Phone      |This value should not be blank. |
      |Comment    |This value should not be blank. |
