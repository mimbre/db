# Database interface.

A database interface.

## Install

This library uses the [Composer package manager](https://getcomposer.org/). Simply execute the following command from a terminal:

```bash
composer require mimbre\db
```
## Examples

```php
require_once "path/to/vendor/autoload.php";
use mimbre\db\DbActiveRecord;
use mimbre\db\mysql\MySqlConnection;

// connects to a MySQL database
$db = new MySqlConnection("test", "root", "chum11145");

// inserts a record
$row = new DbActiveRecord($db, "my-table");
$row->title = 'A title';
$row->save();

// updates the previous record
$row = new DbActiveRecord($db, "items", "2");
$row->title = 'Another title';
$row->save();
```

## Developer notes

```bash
# verifies that the source code conforms the current coding standards
phpcs --standard=phpcs.xml src
```
