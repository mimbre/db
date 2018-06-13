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
   *    // deletes a single record
   *    $count = $db->exec(
   *        "delete from mytable where section = ?", ['mysection']
   *    );
   *    
   *    echo "Number of affected rows: $count";
   *
   * @param string  $sql       SQL statement
   * @param mixed[] $arguments List of strings (not required)
   *
   * @return int
   */
  function exec($sql, $arguments = []);

  /**
   * Selects a single record.
   *
   * Returns NULL if no records were found.
   *
   * Example:
   *
   *    // selects a single row
   *    $row = $db->query(
   *        "select id, title from my_table where id = ?", [101]
   *    );
   *
   *    // prints the record if it was found
   *    if ($row !== null) {
   *        echo "Row ID: $row[id], title: $row[title]\n";
   *    }
   *
   * @param string  $sql       SQL statement
   * @param mixed[] $arguments List of arguments
   *
   * @return array|null
   */
  function query($sql, $arguments = []);

  /**
   * Selects a list of records.
   *
   * Example:
   *
   *    $db = new DbConnection($dbname, $username, $password);
   *
   *    $rows = $db->query("select id, title from my_table");
   *    foreach ($rows as $row) {
   *        echo "Row ID: $row[id], title: $row[title]\n";
   *    }
   *
   * @param string  $sql       SQL statement
   * @param mixed[] $arguments List of arguments
   *
   * @return array            [description]
   */
  function queryAll($sql, $arguments = []);

  /**
   * Escapes and quotes a value.
   *
   * For example:
   *
   *    $rows = $db->query(
   *        "select * from mytable where id = " . $db->quote($id)
   *    );
   *
   *    // in any case, is preferable to write the previous code as follows:
   *    $rows = $db->query("select * from mytable where id = ?" . $id);
   *
   * @param string|null $value Value
   *
   * @return string
   */
  function quote($value);

  /**
   * Closes the database connection.
   *
   * @return void
   */
  function close();
}
