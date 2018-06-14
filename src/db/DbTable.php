<?php
namespace movicon\db;
use movicon\db\Db;
use movicon\db\DbConnection;

/**
 * This is a 'helper class' that contains auxiliary functions to easily operate
 * with database tables.
 */
class DbTable
{
    /**
    * Selects a record from a table.
    *
    * For example:
    *
    *   list(
    *     $username,
    *     $password
    *   ) = DbTable::select($db, "user", ["username", "password"], 1);
    *
    * @param DbConnector $db        Database connection
    * @param string      $tableName Table name
    * @param string[]    $colNames  Column names
    * @param string      $id        Record ID
    *
    * @return mixed[]
    */
    public static function select($db, $tableName, $colNames, $id)
    {
        $cols = array_map(
            function ($colName) {
                return Db::quote($colName);
            },
            $colNames
        );

        $sql = "select " . implode($cols, ", ") .
          " from " . Db::quote($tableName) . " where id = ?";
        return $db->query($sql, [$id]);
    }

    /**
    * Updates a table record. For example:
    *
    *    DbTable::update(
    *      $db, "user", ["username" => "aaa", "password" => "bbb"], 1
    *    );
    *
    * @param DbConnector $db        Database connection
    * @param string      $tableName Table name
    * @param array[]     $cols      Pairs of column names and values
    * @param string      $id        Record ID
    *
    * @return void
    */
    public static function update($db, $tableName, $cols, $id)
    {
        $columns = [];
        foreach ($cols as $name => $value) {
            array_push($columns, Db::quote($name) . " = " . $db->quote($value));
        }

        $sql = "update " . Db::quote($tableName) .
          " set " . implode($columns, ",") . " where id = ?";
        $db->exec($sql, [$id]);
    }

    /**
    * Inserts a table record. For example:
    *
    *    $id = DbTable::insert(
    *      $db, "user", ["username" => "aaa", "password" => "bbb"]
    *    );
    *
    * @param DbConnector $db        Database connection
    * @param string      $tableName Table name
    * @param array[]     $cols      Pairs of column names and values
    *
    * @return int Last inserted ID.
    */
    public static function insert($db, $tableName, $cols)
    {
        $colNames = [];
        $colValues = [];
        foreach ($cols as $name => $value) {
            array_push($colNames, Db::quote($name));
            array_push($colValues, $db->quote($value));
        }

        $sql = "insert into " . Db::quote($tableName) .
          "( " . implode($colNames, ", ") . ")" .
          " values(" . implode($colValues, ", ") . ")";
        $db->exec($sql);

        $row = $db->query("select last_insert_id()");
        return $row[0];
    }

    /**
    * Deletes a table record. For example:
    *
    * @param DbConnector $db        Database connection
    * @param string      $tableName Table name
    * @param string      $id        Record ID
    *
    * @return void
    */
    public static function delete($db, $tableName, $id)
    {
        $sql = "delete from " . Db::quote($tableName) . " where id = ?";
        $db->exec($sql, [$id]);
    }
}
