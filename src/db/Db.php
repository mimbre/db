<?php
namespace movicon\db;

/**
 * A database helper.
 */
class Db
{
    // TODO: rename quoteId by quote
    /**
     * Quotes an identifier.
     *
     * @param string $identifier Identifier
     *
     * @return string
     */
    public static function quoteId($identifier)
    {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }
}
