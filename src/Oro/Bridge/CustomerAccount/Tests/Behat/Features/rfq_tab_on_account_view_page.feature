@ticket-BB-27836
@fixture-OroCustomerAccountBridgeBundle:AccountCustomerTabs.yml

Feature: RFQ tab on Account view page

  Scenario: Both tabs are displayed with the default state of the features
    When I login as administrator
    And I go to Customers/ Accounts
    And I click "View" on row "Tabs Account" in grid
    Then I should see "Requests For Quote" in the "Account Customer Tabs" element
    And I should see "Opportunities" in the "Account Customer Tabs" element

  Scenario: Requests For Quote tab is hidden once the RFQ feature is disabled
    Given I disable configuration options:
      | oro_rfp.feature_enabled |
    When I reload the page
    Then I should not see "Requests For Quote" in the "Account Customer Tabs" element
    And I should see "Opportunities" in the "Account Customer Tabs" element
    And I should see "Shopping Lists" in the "Account Customer Tabs" element

  Scenario: Opportunities tab is hidden once the Opportunities feature is disabled
    Given I disable configuration options:
      | oro_sales.opportunity_feature_enabled |
    When I reload the page
    Then I should not see "Opportunities" in the "Account Customer Tabs" element
    And I should see "Shopping Lists" in the "Account Customer Tabs" element
