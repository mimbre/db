<?php
namespace movicon\db\mysql;
use \Mysqli;
use movicon\db\Db;
use movicon\db\DbConnection;
use movicon\db\exception\DbException;

/**
 * A MySQL connection.
 */
class MySqlConnection extends DbConnection
{
    /**
     * Database connection.
     *
     * @var Mysqli
     */
    private $_conn;

    /**
     * Constructor.
     *
     * @param string $dbname   Database name
     * @param string $username User name (not required)
     * @param string $password Password (not required)
     * @param string $server   Server machine (default is 'localhost')
     * @param string $charset  Character set (default is 'utf8')
     */
    public function __construct(
        $dbname,
        $username = "",
        $password = "",
        $server = "localhost",
        $charset = "utf8"
    ) {
        $this->_conn = @mysqli_connect($server, $username, $password);
        if ($this->_conn === false) {
            throw new DbException("Failed to connect to the database");
        }

        @mysqli_select_db($this->_conn, $dbname);
        if ($this->_conn->errno > 0) {
            throw new DbException(
                "{$this->_conn->error} (Error no. {$this->_conn->errno})"
            );
        }

        $this->_conn->set_charset($charset);
    }

    /**
     * {@inheritdoc}
     *
     * @param string  $sql       SQL statement
     * @param mixed[] $arguments List of strings (not required)
     *
     * @return int
     */
    public function exec($sql, $arguments = [])
    {
        $result = $this->_exec($sql, $arguments);
        return $this->_conn->affected_rows;
    }

    /**
     * {@inheritdoc}
     *
     * @param string  $sql        SQL statement
     * @param mixed[] $arguments  Arguments
     * @param int     $resultType Result type
     *
     * @return mixed[]
     */
    public function query($sql, $arguments = [], $resultType = MYSQLI_ASSOC)
    {
        $ret = null;

        $rows = $this->queryAll($sql, $arguments, $resultType);
        if (count($rows) > 0) {
            $ret = $rows[0];
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     *
     * @param string  $sql        SQL statement
     * @param mixed[] $arguments  Arguments (not required)
     * @param int     $resultType Result type
     *
     * @return array
     */
    public function queryAll($sql, $arguments = [], $resultType = MYSQLI_ASSOC)
    {
        $ret = array();
        $result = $this->_exec($sql, $arguments);

        // fetches all rows
        while ($row = $result->fetch_array($resultType)) {
            array_push($ret, $row);
        }
        $result->close();

        return $ret;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function close()
    {
        $this->_conn->close();
    }

    /**
     * Escapes and quotes a value.
     *
     * @param string|null $value Value
     *
     * @return string
     */
    public function quote($value)
    {
        return is_null($value)
          ? "null"
          : "'" . mysqli_real_escape_string($this->_conn, $value) . "'";
    }

    /**
     * Executes an SQL statement.
     *
     * @param string  $sql       SQL statement
     * @param mixed[] $arguments List of arguments (not required)
     *
     * @return Mysqli_result
     */
    private function _exec($sql, $arguments = array())
    {
        $sql = $this->replaceArgs($sql, $arguments);

        // executes the statement
        $result = $this->_conn->query($sql);
        if ($this->_conn->errno > 0) {
            throw new DbException(
                "Failed to execute the statement: " .
                "({$this->_conn->errno}) {$this->_conn->error}"
            );
        }

        return $result;
    }
}
