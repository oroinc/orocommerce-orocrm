@regression
@behat-test-env
@ticket-BB-24484

Feature: Check Contact Us captcha protection

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |

  Scenario: Enable CAPTCHA protection
    Given I proceed as the Admin
    And I login as administrator
    When I go to System / Configuration
    And I follow "System Configuration/Integrations/CAPTCHA Settings" on configuration sidebar

    And uncheck "Use default" for "Enable CAPTCHA protection" field
    And I check "Enable CAPTCHA protection"

    And uncheck "Use default" for "CAPTCHA service" field
    And I fill in "CAPTCHA service" with "Dummy"

    And uncheck "Use default" for "Protect Forms" field
    And I check "Contact Us Form"

    And I submit form
    Then I should see "Configuration saved" flash message

  Scenario: Check CAPTCHA protection for Customer User Reset Password Form
    Given I proceed as the Buyer

    When I am on the homepage
    And I click "Contact Us" in hamburger menu
    Then Page title equals to "Contact Us"
    And I should see "Captcha"

    When fill form with:
      | First Name               | Test          |
      | Last Name                | Tester        |
      | Preferred contact method | Email         |
      | Email                    | qa@oroinc.com |
      | Comment                  | Test Comment  |
      | Captcha                  | invalid       |
    And I click "Submit"
    Then I should see "The form cannot be sent because you did not passed the anti-bot validation. If you are a human, please contact us."

    When fill form with:
      | First Name               | Test          |
      | Last Name                | Tester        |
      | Preferred contact method | Email         |
      | Email                    | qa@oroinc.com |
      | Comment                  | Test Comment  |
      | Captcha                  | valid         |
    And I click "Submit"
    Then I should see "Thank you for your Request!" flash message
