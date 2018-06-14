<?php
namespace movicon\db;

abstract class DbConnection
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
    *        "delete from mytable where section = ?",
    *        'mysection'
    *    );
    *
    *    echo "Number of affected rows: $count";
    *
    * @param string        $sql       SQL statement
    * @param mixed|mixed[] $arguments A single or a list of arguments
    *
    * @return int
    */
    abstract public function exec($sql, $arguments = []);

    /**
    * Selects a single record.
    *
    * Returns NULL if no records were found.
    *
    * Example:
    *
    *    // selects a single row
    *    $row = $db->query(
    *        "select id, title from my_table where id = ?", 101
    *    );
    *
    *    // prints the record if it was found
    *    if ($row !== null) {
    *        echo "Row ID: $row[id], title: $row[title]\n";
    *    }
    *
    * @param string        $sql       SQL statement
    * @param mixed|mixed[] $arguments A single or a list of arguments
    *
    * @return mixed[]|null
    */
    abstract public function query($sql, $arguments = []);

    /**
    * Selects a list of records.
    *
    * Example:
    *
    *    $rows = $db->query("select id, title from my_table");
    *    foreach ($rows as $row) {
    *        echo "Row ID: $row[id], title: $row[title]\n";
    *    }
    *
    * @param string        $sql       SQL statement
    * @param mixed|mixed[] $arguments A single or a list of arguments
    *
    * @return array            [description]
    */
    abstract public function queryAll($sql, $arguments = []);

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
    abstract public function quote($value);

    /**
    * Closes the database connection.
    *
    * @return void
    */
    abstract public function close();

    /**
    * Replaces arguments in an SQL statement.
    *
    * For example:
    *
    *    // the following command returns:
    *    //     select * from my_table where id = '100'
    *    $db->replaceArgs("select * from my_table where id = ?", 100);
    *
    * @param string        $sql       SQL statement
    * @param mixed|mixed[] $arguments A single or a list of arguments
    *
    * @return string
    */
    protected function replaceArgs($sql, $arguments)
    {
        // fixes arguments
        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }

        // searches string segments (startPos, endPos)
        $stringSegments = [];
        $matches = [];
        $searchArgs = preg_match_all(
            '/(["\'`])((?:\\\\\1|.)*?)\1/', $sql, $matches, PREG_OFFSET_CAPTURE
        );
        if ($searchArgs) {
            foreach ($matches[2] as $match) {
                $startPos = $match[1];
                $endPos = $startPos + strlen($match[0]);
                array_push($stringSegments, [$startPos, $endPos]);
            }
        }

        // searches arguments position
        $argsPos = [];
        preg_match_all('/\?/', $sql, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $match) {
            array_push($argsPos, $match[1]);
        }

        // replaces arguments
        $matchCount = 0;
        $argCount = 0;
        return preg_replace_callback(
            '/\?/',
            function ($matches) use (
                &$argCount, &$matchCount, $arguments, $argsPos, $stringSegments
            ) {
                $ret = $matches[0];

                if ($argCount < count($arguments)) {
                    // is the current match inside a quoted string?
                    $argPos = $argsPos[$matchCount];
                    $isInsideQuotedString = false;
                    foreach ($stringSegments as $segment) {
                        if ($argPos >= $segment[0] &&  $argPos < $segment[1]) {
                            $isInsideQuotedString = true;
                            break;
                        }
                    }

                    if (!$isInsideQuotedString) {
                        $ret = $this->quote($arguments[$argCount++]);
                    }
                }

                $matchCount++;
                return $ret;
            },
            $sql
        );
    }
}
