# Database connection

A library to connect to databases. Specifically this library contains a class to connect to a MySQL database.

## Install

This library uses the [Composer package manager](https://getcomposer.org/). Simply execute the following command from a terminal:

```bash
composer require movicon\movicon-db
```

After that, you are ready to use the library. Just include the following PHP code at the beggining of the scripts:
```php
require_once "/path/to/vendor/autoload.php";
use movicon\db\mysql\MySqlConnection;

// connects to a database
$db = new MySqlConnection("my_db", "root", "password");
```

## query, queryAll and exec

Use the `query` method to access single records. This method returns `NULL` if no records were found. For example:

```php
// selects a single row and prints it
$row = $db->query("select id, title from my_table where id = ?", 101);
if ($row !== null) {
  echo "Record ID: $row[id], title: $row[title]\n";
} else {
  echo "Record not found!";
}
```

Use the `queryAll` method to fetch a list of records from the database. This method returns an empty array if no records were found. For example:

```php
// selects a list of records and prints it
$rows = $db->queryAll("select id, title from my_table");
if (count($rows) > 0) {
  foreach ($rows as $row) {
    echo "Row ID: $row[id], Title: $row[title]\n";
  }
} else {
  echo "No records found!";
}
```

Use the `exec` method to execute SQL statements. This method returns the number of 'affected rows'. For example:

```php
$numRows = $db->exec(
  "update my_table set title = ?, description = ? where id = ?",
  ['New title', 'New description', 101]
);
echo "Number of rows affected: $numRows";
```

## ActiveRecord

The `ActiveRecord` class implements the [active record pattern](https://en.wikipedia.org/wiki/Active_record_pattern). Thus, we can insert, edit or delete records as if it were instances of objects. For example, let's say that our table has the following structure:

```text
create table person (
    id int not null auto_increment,
    first_name varchar(200) not null,
    last_name varchar(200),
    age int
    primary key(id)
)
```

In the following example we'll insert, edit and finally delete a record. Note that we use the `camelCase` naming convention to access the table fields. For example, we use `firstName` instead of `first_name`.

```php
require_once "path/to/vendor/autoload.php";
use movicon\db\DbActiveRecord;
use movicon\db\mysql\MySqlConnection;

// connects to a MySQL database
$db = new MySqlConnection("your-database", "root", "your-password");

// inserts a record
$row = new DbActiveRecord($db, "person");
$row->firstName = 'John';
$row->lastName = 'Smith';
$row->age = 33;
$row->save();

// updates the previous record
$row = new DbActiveRecord($db, "person", $row->getId());
$row->firstName = 'Agent';
$row->lastName = 'Smith';
$row->save();

// deletes the previous record
$row = new DbActiveRecord($db, "person", $row->id);
$row->delete();
```
