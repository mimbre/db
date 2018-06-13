<?php
namespace movicon\db;

/**
 * This interface represents a database connection.
 */
interface DbConnection
{
  /**
   * Executes an SQL statement.
   *
   * This function executes an SQL statement and returns the number of
   * affected rows.
   *
   * Example:
   *
   *    $count = $db->exec("delete from mytable where section = 'mysection'");
   *    echo "Number of affected rows: $count";
   *
   * @param string  $sql       SQL statement
   * @param mixed[] $arguments List of strings (not required)
   *
   * @return int
   */
  function exec($sql, $arguments = []);

  /**
   * Executes a SQL statement.
   *
   * This function executes a SQL statement (select, show, describe, etc...)
   * and returns a datasource.
   *
   * Examples:
   *
   *    $db = new DbConnection($dbname, $username, $password);
   *
   *    // retrieves a single row
   *    $row = $db->query("select count(*) from table");
   *    echo $row[0];
   *
   *    // retrieves multiple rows
   *    $rows = $db->query(
   *      "select id, name from mytable where section = ?", ["my-section"]
   *    );
   *    foreach ($rows as $row) {
   *      echo "$row[id]: $row[name]\n";
   *    }
   *
   *    // uses an array as arguments
   *    $rows = $db->query(
   *      "select id, name from mytable where col1 = ? and col2 = ?",
   *      [101, 102]
   *    );
   *    echo "Number of rows" . count($rows);
   *
   * @param string  $sql       SQL statement
   * @param mixed[] $arguments Arguments
   *
   * @return DbSource
   */
  function query($sql, $arguments = []);

  /**
   * Escapes and quotes a value.
   *
   * For example:
   * ```php
   * $rows = $db->query("select * from mytable where id = " . $db->quote($id));
   * ```
   *
   * In any case, is preferable to write the previous code as follows:
   * ```php
   * $rows = $db->query("select * from mytable where id = ?" . $id);
   * ```
   *
   * @param string|null $value Value
   *
   * @return string
   */
  function quote($value);

  /**
   * Executes a SQL statement and returns all rows.
   *
   * This function executes a SQL statement (select, show, describe, etc...)
   * and returns an associative array. I recommend to use DbConnector::query()
   * instead.
   *
   * @param string  $sql       SQL statement
   * @param mixed[] $arguments List of arguments (not required)
   *
   * @return array
   */
  function fetchRows($sql, $arguments = []);

  /**
   * Closes the database connection.
   *
   * @return void
   */
  function close();
}
