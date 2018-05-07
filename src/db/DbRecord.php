<?php
namespace mimbre\db;
use mimbre\db\DbConnection;

abstract class DbRecord
{
  protected $db;
  protected $id;
  private $_isSaved;

  /**
   * Constructor.
   *
   * @param DbConnection $db Databse connection
   * @param string       $id Record ID
   */
  public function __construct($db, $id = null)
  {
    $this->db = $db;
    $this->id = $id;
    $this->_isSaved = ($id !== null);

    if ($this->_isSaved) {
      $this->select();
    }
  }

  /**
   * Gets the record ID.
   *
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Was the record found?
   *
   * @return boolean
   */
  public function isFound()
  {
    return !Text::isEmpty($this->id);
  }

  /**
   * Saves this record.
   *
   * @return void
   */
  public function save()
  {
    if ($this->_isSaved) {
      $this->update($this->id);
    } else {
      $this->id = $this->insert();
      $this->_isSaved = true;
    }
  }

  /**
   * Deletes a record from a table.
   *
   * @return void
   */
  abstract public function delete();

  /**
   * Selects a record from a table.
   *
   * @return void
   */
  abstract protected function select();

  /**
   * Updates a record from a table.
   *
   * @return void
   */
  abstract protected function update();

  /**
   * Inserts a record into a table.
   *
   * @return string Last inserted ID
   */
  abstract protected function insert();
}