<?php
namespace movicon\db;
use \ArrayAccess;
use movicon\db\Db;
use movicon\db\DbConnection;
use movicon\db\DbRecord;
use movicon\db\DbTable;
use movicon\db\exception\DbException;

/**
 * Active record implementation.
 */
class DbActiveRecord extends DbRecord
{
    private $_tableName = "";
    private $_cols = [];

    /**
    * Constructor.
    *
    * @param DbConnection $db        Database connection
    * @param string       $tableName Table name
    * @param string       $id        Record ID (not required)
    */
    public function __construct($db, $tableName, $id = null)
    {
        $this->_tableName = $tableName;

        // gets the columns from the table
        $this->_cols = [];
        $sql = "show columns from " . Db::quote($this->_tableName);
        $cols = $db->queryAll($sql);
        foreach ($cols as $col) {
            $this->_cols[$col["Field"]] = "";
        }

        parent::__construct($db, $id);
    }

    /**
    * Magic 'get' method.
    *
    * @param string $colName Column name
    *
    * @return mixed
    */
    public function __get($colName)
    {
        $field = $this->_camelToSnakeCase($colName);

        return $field == "id" ? $this->id : $this->_cols[$field];
    }

    /**
    * Magic 'set' method.
    *
    * @param string $colName Column name
    * @param mixed  $value   Value
    *
    * @return void
    */
    public function __set($colName, $value)
    {
        $field = $this->_camelToSnakeCase($colName);

        if ($field == "id") {
            throw new DbException("The Primary Key is not writable");
        }

        $this->_cols[$field] = $value;
    }

    /**
    * {@inheritdoc}
    *
    * @return void
    */
    public function delete()
    {
        DbTable::delete($this->db, $this->_tableName, $this->id);
    }

    /**
    * {@inheritdoc}
    *
    * @return string Record ID
    */
    protected function select()
    {
        $colNames = array_keys($this->_cols);
        $colValues = DbTable::select(
            $this->db, $this->_tableName, $colNames, $this->id
        );

        $this->_cols = [];
        foreach ($colNames as $colName) {
            $this->_cols[$colName] = $colValues[$colName];
        }

        return $this->_cols["id"];
    }

    /**
    * {@inheritdoc}
    *
    * @return void
    */
    protected function update()
    {
        DbTable::update($this->db, $this->_tableName, $this->_cols, $this->id);
    }

    /**
    * {@inheritdoc}
    *
    * @return string Last inserted ID
    */
    protected function insert()
    {
        return DbTable::insert($this->db, $this->_tableName, $this->_cols);
    }

    /**
    * Converts a 'camelCase' string to a 'snake_case' string.
    *
    * Courtesy of:
    *    https://stackoverflow.com/a/23313687/1704895
    *
    * @param string $str A camelCase string
    *
    * @return string
    */
    private function _camelToSnakeCase($str)
    {
        $str = preg_replace('/([a-z])([A-Z])/', "\\1_\\2", $str);
        $str = strtolower($str);
        return $str;
    }
}
