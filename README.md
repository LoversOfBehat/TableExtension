TableExtension
==============

This library provides step definitions for checking HTML5 tables in
Behat scenarios.

Installation
------------

```
$ composer require lovers-of-behat/table-extension
```

Configuration
-------------

Add the extension and context to your test suite in `behat.yml`:

```
suites:
  default:
    contexts:
      - LoversOfBehat\TableExtension\Context\TableContext:
  extensions:
    LoversOfBehat\TableExtension:
      table_map:
        'Users': 'page.users .main-content table'
        'Country codes': 'table#country-codes'
```

### Options

* `tableMap`: Maps human readable table names to the CSS selectors that
  identify the tables in the web page. This allows you to use the human
  readable names in your Behat scenarios.

Usage
-----

Given this example table:

<table id="employees">
  <tr>
    <th rowspan="2">Name</th>
    <th colspan="2">Department</th>
    <th colspan="2">Contact information</th>
  </tr>
  <tr>
    <th>Office</th>
    <th>Position</th>
    <th>E-mail address</th>
    <th>Phone number</th>
  </tr>
  <tr>
    <td><strong>Lelisa Ericsson</strong></td>
    <td>Healthcare</td>
    <td>Nurse</td>
    <td>lelisa@example.com</td>
    <td>555-1234567</td>
  </tr>
  <tr>
    <td><strong>Genista Sumner</strong></td>
    <td>Science</td>
    <td>Anthropologist</td>
    <td>genista@example.com</td>
    <td>555-987654</td>
  </tr>
</table>

And we have added the table to the `table_map` in `behat.yml`:

```
suites:
  extensions:
    LoversOfBehat\TableExtension:
      table_map:
        'Employees': '#employees'
```

Then we can use steps such as these to check the table:

```
# Check that the table is present on the page.
Then I should see the Employees table

# Check basic properties.
And the Employees table should have 5 columns

# Check the contents of the table. Cells that contain colspans and
# rowspans can be left empty.
And the Employees table should contain:
  | Name            | Department |                | Contact information |              |
  |                 | Office     | Position       | E-mail address      | Phone number |
  | Lelisa Ericsson | Healthcare | Nurse          | lelisa@example.com  | 555-1234567  |
  | Genista Sumner  | Science    | Anthropologist | genista@example.com | 555-987654   |

# The same step definition can be used to check partial data, as long as
# it is in a consecutive block of cells:
And the Employees table should contain:
  | Lelisa Ericsson | Healthcare | Nurse          |
  | Genista Sumner  | Science    | Anthropologist |

# Check non-consecutive columns by identifying them with the header
# text. This works even though the headers are in different rows in the
# original table.
And the Employees table should contain the following columns:
  | Name            | Office     | Phone number |
  | Lelisa Ericsson | Healthcare | 555-1234567  |
  | Genista Sumner  | Science    | 555-987654   |
```

For a more complete example, see
[tables.feature](features/tables.feature). Or check
[TableContext.php](src/Context/TableContext.php)
itself for the full list of available steps.

Development
-----------

Running tests locally:

```
$ git clone git@github.com:LoversOfBehat/TableExtension.git table-extension
$ cd table-extension
$ composer install
$ php -S localhost:8000 -t fixtures &
$ ./vendor/bin/behat
```

Credits
-------

Development of this extension has been sponsored by the Directorate-General for
Informatics (DIGIT) of the European Commission, as part of the OpenEuropa
initiative.
