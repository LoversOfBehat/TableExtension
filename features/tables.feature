Feature: Inspecting HTML tables
  In order to verify that the functionality is correctly implemented
  As a developer for the TableContext library
  I need to check that the step definitions work as expected

  Scenario: Check all tables
    Given I am on the homepage
    Then I should see a table
    And I should see 4 tables
    And I should see a table with 2 columns
    And I should see a table with 3 columns
    And I should see a table with 4 columns
    And I should see a table with 5 columns
    But I should not see a table with 1 column
    And I should not see a table with 6 columns

    Given I am on "no-tables-here.html"
    Then I should not see a table
    And I should see 0 tables
    And I should not see a table with 1 column
    And I should not see a table with 2 columns
    And I should not see a table with 3 columns
    And I should not see a table with 4 columns
    And I should not see a table with 5 columns
