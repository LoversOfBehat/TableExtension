Feature: Inspecting HTML tables
  In order to verify that the functionality is correctly implemented
  As a developer for the TableExtension library
  I need to check that the step definitions work as expected

  Scenario: Check if any table on the page matches certain characteristics
    Given I am on the homepage
    Then I should see a table
    And I should see 5 tables

    And I should see a table with 2 columns
    And I should see a table with 3 columns
    And I should see a table with 4 columns
    And I should see a table with 5 columns
    And I should see a table with 6 columns

    And I should see a table with 3 rows
    And I should see a table with 4 rows
    And I should see a table with 5 rows
    And I should see a table with 9 rows

    But I should not see a table with 1 column
    And I should not see a table with 7 columns

    And I should not see a table with 1 row
    And I should not see a table with 2 rows

    Given I am on "no-tables-here.html"
    Then I should not see a table
    And I should not see the simple table
    And I should see 0 tables
    And I should not see a table with 1 column
    And I should not see a table with 2 columns
    And I should not see a table with 1 row
    And I should not see a table with 2 rows

  Scenario: Check if specific tables are displayed correctly
    Given I am on the homepage

    # The first table is a simple affair with 3 columns, a header and 2 rows.
    Then I should see the simple table
    And the simple table should have 3 columns
    And the simple table should have 3 rows
    And the simple table should contain:
      | Header 1    | Header 2    | Header 3    |
      | Row 1 Col 1 | Row 1 Col 2 | Row 1 Col 3 |
      | Row 2 Col 1 | Row 2 Col 2 | Row 2 Col 3 |
    # Check that we can verify subsets of the table, regardless of the order in which they appear.
    And the simple table should contain:
      | Header 1    | Header 3    | Header 2    |
      | Row 1 Col 1 | Row 1 Col 3 | Row 1 Col 2 |
    And the simple table should contain:
      | Header 1    | Header 3    |
      | Row 2 Col 1 | Row 2 Col 3 |
    And the simple table should contain:
      | Header 1    |
      | Row 2 Col 1 |
      | Row 1 Col 1 |

    # The second table has 2 columns, of which the first is a vertical header.
    And I should see the Algarve table
    And the Algarve table should have 2 columns
    And the Algarve table should have 5 rows
    And the Algarve table should contain:
      | Country       | Portugal   |
      | Capital       | Faro       |
      | Highest point | Fóia       |
      | Lowest point  | Sea level  |
      | Area          | 4996.80km² |

    # The third table has a horizontal as well as a vertical header.
    And I should see the "Population data" table
    And the "Population data" table should have 4 columns
    And the "Population data" table should have 9 rows
    And the "Population data" table should contain:
      | Country   | Population | Surface area | Population density   |
      |           | millions   | square km    | people per square km |
      | Albania   | 2.9        | 28800        | 105                  |
      | Andorra   | 0.1        | 500          | 164                  |
      | Algeria   | 40.6       | 2381700      | 17                   |
      | Angola    | 28.8       | 1246700      | 23                   |
      | Argentina | 43.8       | 2780400      | 16                   |
      | Bahamas   | 0.4        | 13900        | 39                   |
      | World     | 7442.1     | 134325100    | 57                   |
    # Check that we can verify non-consecutive columns by specifying the headers.
    And the "Population data" table should contain the following columns:
      | Country   | Population density |
      | Albania   | 105                |
      | Andorra   | 164                |
      | Algeria   | 17                 |
      | Angola    | 23                 |
      | Argentina | 16                 |
      | Bahamas   | 39                 |
      | World     | 57                 |
    # Check that we can verify non-consecutive rows by specifying the headers.
    And the "Population data" table should contain the following rows:
      | Andorra   | 0.1  | 500     | 164 |
      | Argentina | 43.8 | 2780400 | 16  |
      | Bahamas   | 0.4  | 13900   | 39  |

    # The fourth table has a rowspan on the first element.
    And I should see the Employees table
    And the Employees table should have 5 columns
    And the Employees table should have 4 rows
    And the Employees table should contain:
      | Name            | Department |                | Contact information |              |
      |                 | Office     | Position       | E-mail address      | Phone number |
      | Lelisa Ericsson | Healthcare | Nurse          | lelisa@example.com  | 555-1234567  |
      | Genista Sumner  | Science    | Anthropologist | genista@example.com | 555-987654   |
    # Check that we can verify non-consecutive columns of which not all headers are in the same row.
    And the Employees table should contain the following columns:
      | Name            | Office     | Phone number |
      | Lelisa Ericsson | Healthcare | 555-1234567  |
      | Genista Sumner  | Science    | 555-987654   |

    # The fifth table combines rowspans and colspans.
    And I should see the "Mad spanner" table
    And the "Mad spanner" table should have 6 columns
    And the "Mad spanner" table should have 5 rows
    And the "Mad spanner" table should contain:
      | 1A |    | 1C | 1D | 1E | 1F |
      |    |    | 2C |    |    | 2F |
      |    |    | 3C |    |    |    |
      | 4A | 4B |    | 4D |    |    |
      | 5A |    | 5C |    |    |    |
