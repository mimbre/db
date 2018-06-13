<?php
namespace movicon\db;
use \ArrayAccess;
use \ArrayObject;
use \Countable;
use \IteratorAggregate;

/**
 * A database source.
 */
class DbSource implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * List of rows.
     * @var array of array
     */
    private $_rows = [];

    /**
     * Constructor.
     *
     * @param Db      $db        Database connection
     * @param string  $sql       SQL statement (select, show, describe...)
     * @param mixed[] $arguments List of arguments (not required)
     */
    public function __construct($db, $sql, $arguments = [])
    {
        $this->_db = $db;
        $this->_rows = $this->_db->fetchRows($sql, $arguments);
    }

    /**
    * Gets the iterator.
    *
    * @return ArrayObject
    */
    public function getIterator()
    {
        return new ArrayObject($this->_rows);
    }

    /**
     * Does the column exist?
     *
     * @param string $columnName Column name
     *
     * @return boolean
     */
    public function offsetExists($columnName)
    {
        $row = current($this->_rows);
        return array_key_exists($columnName, $row);
    }

    /**
     * Gets the column value.
     *
     * @param string $columnName Column name
     *
     * @return string|null
     */
    public function offsetGet($columnName)
    {
        $row = current($this->_rows);
        return $row !== false? $row[$columnName] : null;
    }

    /**
     * Sets the column value.
     *
     * @param string $columnName Column name
     * @param mixed  $value      Value
     *
     * @return void
     */
    public function offsetSet($columnName, $value)
    {
        $this->_rows[key($this->_rows)][$columnName] = "$value";
    }

    /**
     * Removes a column.
     *
     * @param string $columnName Column name
     *
     * @return void
     */
    public function offsetUnset($columnName)
    {
        unset($this->_rows[key($this->_rows)][$columnName]);
    }

     /**
      * Gets the number of rows.
      *
      * @return integer
      */
    public function count()
    {
        return count($this->_rows);
    }
}
