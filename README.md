# Database connection

## Install

This library uses the [Composer package manager](https://getcomposer.org/). Simply execute the following command from a terminal:

```bash
composer require movicon\movicon-db
```

## Examples

This library uses the [Active Record](https://en.wikipedia.org/wiki/Active_record_pattern) design pattern to insert, update or delete records.

```php
require_once "path/to/vendor/autoload.php";
use movicon\db\DbActiveRecord;
use movicon\db\mysql\MySqlConnection;

// connects to a MySQL database
$db = new MySqlConnection("test", "root", "your password");

// inserts a record
$row = new DbActiveRecord($db, "my-table");
$row->title = 'A title';
$row->save();

// updates the previous record
$row = new DbActiveRecord($db, "items", $row->getId());
$row->title = 'Another title';
$row->save();

// deletes the previous record
$row = new DbActiveRecord($db, "items, $row->getId());
$row->delete();
```
