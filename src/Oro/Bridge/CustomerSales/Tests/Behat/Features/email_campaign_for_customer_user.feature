@ticket-BB-17470
@fixture-OroCustomerSalesBridgeBundle:EmailCampaignForCustomerUserFixture.yml

Feature: Email campaign for Customer User
  In order to manage email campaign
  As an administrator
  I want to be able to sent email campaign for Customer Users by Contact Information field

  Scenario: Send email campaign for Customer User with chosen Contact Information field
    Given I login as administrator
    When I go to Marketing / Email Campaigns
    And I click "Create Email Campaign"
    And fill form with:
      | Name           | Test email campaign |
      | Marketing List | ML                  |
    Then should see the following options for "Template" select:
      | test_template |
    When I fill form with:
      | Template | test_template |
    And I save and close form
    Then I should see "Email campaign saved" flash message
    And I should see following grid:
      | Contact Information     |
      | AmandaRCole@example.org |
    When I click "Send"
    Then I should see "Email campaign was sent" flash message
    And I should not see "There was an error performing the requested operation" flash message
    And email with Subject "Test Subject" containing the following was sent:
      | To   | AmandaRCole@example.org |
      | Body | Test Content            |
