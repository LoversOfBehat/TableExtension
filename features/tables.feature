Feature: Inspecting HTML tables
  In order to verify that the functionality is correctly implemented
  As a developer for the TableContext library
  I need to check that the step definitions work as expected

  Scenario: Check if any table on the page matches certain characteristics
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

  Scenario: Check if specific tables are displayed correctly
    Given I am on the homepage

    # The first table is a simple affair with 3 columns, a header and 2 rows.
    Then I should see the simple table

    # The second table has 2 columns, of which the first is a vertical header.
    And I should see the Algarve table

    # The third table has a horizontal as well as a vertical header.
    And I should see the "Population data" table

    # The fourth table has a colspan as well as a rowspan on the first element.
    And I should see the Employees table
