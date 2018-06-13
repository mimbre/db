<?php
namespace movicon\db;

/**
 * A database helper.
 */
class Db
{
    /**
     * Quotes an identifier.
     *
     * For example:
     *
     *      $tableName = "my_table";
     *      $sql = "select * from " . Db::quote($tableName);
     *
     *      // the following command prints:
     *      //     select * from `my_table`
     *      echo $sql;
     *
     * @param string $identifier Identifier
     *
     * @return string
     */
    public static function quote($identifier)
    {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }
}
