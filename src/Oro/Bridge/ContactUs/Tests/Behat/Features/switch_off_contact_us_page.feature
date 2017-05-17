Feature: Switch off contact us page
  As an Administrator
  I want be able to enable/disable ability of contacting Seller via Contact form on the website
  So that, we need to add on/off switcher (checkbox) for page with Contact Us form on the System level.

  Scenario: Disable contact us page on System level
    Given I login as administrator
    And I go to System/ Configuration
    And I click "Commerce" on configuration sidebar
    And I click "Customer" on configuration sidebar
    And I click "Contact Requests" on configuration sidebar
    # Fix it after BAP-14749
    And I uncheck "contact_requests[oro_contact_us_bridge___enable_contact_request][value]"
    And I click "Save settings"
    When I am on the homepage
    Then I should not see "Contact Us"
