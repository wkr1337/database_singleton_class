<?php
class DB {
    private static $_instance = null; 
    private $_pdo, $_query, $_error = false, $_results, $_count = 0, $_lastInsertId = '';

    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host=127.0.0.1;dbname=test2','root','');
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    //  this function makes it a singleton class.
    public static function get_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

/**
 * query function prevents sql injection.
 *
 * @param String $sql
 * @param array $params
 * @return void
 */
    public function query($sql, $params = []) {
        $this->_error = false;
        // prepare sql statement
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
                $this->lastInsertId = $this->_pdo->lastInsertId();

            } else {
                $this->_error = true;
            }

        }

        return $this;
    }
    
    /**
     * This function inserts the given array into the given table and executes it.
     * $table = table to insert into
     * 
     * $fields = an associative array of the table colums and values like:
     * 
     * 'column name' => 'column value'
     * 
     * @var $table = table to insert into
     * @var $fields is an associative array of the table colums and values
     */

    public function insert($table, $fields = []) {
        $fieldString = "";
        $valueString = "";
        $values = [];
        foreach($fields as $field => $value) {
            $fieldString .= $field . ", ";
            $valueString .= '?,';
            $values[] = $value;
        }
        $valueString = rtrim($valueString, ',');
        $fieldString = rtrim($fieldString, ', ');
        $sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";

        if($this->query($sql, $values)) {
            return true;
        }
        return false;
    }
/**
 * Updates the given table and set it's fields where id = the given id.
 * Protects against sql injection
 *  
 * @param string $table
 * @param integer $id
 * @param array $fields
 * @return void
 */
    public function update($table, $id, $fields = []) {
        $fieldString = '';
        $values = [];
        foreach($fields as $field => $value) {
            $fieldString .= ' ' . $field . ' = ?,';
            $values[] = $value;
        }
        $fieldString = trim($fieldString); // remove the white space in front of the string
        $fieldString = rtrim($fieldString, ',');
        $sql = "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";
        
        if($this->query($sql, $values)) {
            return true;
        }

        return false;

    }

    public function delete($table, $id) {

        $sql = "DELETE FROM {$table} WHERE id = {$id}";
        if (!$this->query($sql)->error()) {
            return true;
        }
        return false;

    }
   
    private function _read($table, $params = []) {
        $conditionString = '';
        $bind = [];
        $order = '';
        $limit = '';
        // conditions
        if (isset($params['conditions'])) {
            if(is_array($params['conditions'])) {
                foreach($params['conditions'] as $condition) {
                    $conditionString .= ' ' . $condition . ' AND';
                }
                $conditionString = trim($conditionString);
                $conditionString = rtrim($conditionString, ' AND');
            } else {
                $conditionString = $params['conditions'];
            }

            if ($conditionString != '') {
                $conditionString = ' WHERE ' . $conditionString;
            }
        }
        // bind
        if (array_key_exists('bind', $params)) {
            $bind = $params['bind'];
        }
        // order
        if (array_key_exists('order', $params)) {
            $order = ' ORDER BY ' . $params['order'];
        }
        // limit
        if (array_key_exists('limit', $params)) {
            $limit = ' LIMIT '. $params['limit'];
        }
        $sql = "SELECT * FROM {$table}{$conditionString}{$order}{$limit}";
        var_dump($sql);die();
        if ($this->query($sql, $bind)) {
            if (sizeof($this->_results) == 0) return false;
            return true;
        }
        return false;
    }

    public function find($table, $params = []) {
        if($this->_read($table, $params)) {
            return $this->_results;
        }
        return false;

    }
    public function findFirst($table, $params = []) {
        if ($this->_read($table, $params)) {
            return $this->_results[0];
        }
        return false;
    }

    public function results() {
        return $this->_results;
    }

    public function count() {
        return $this->_count;
    }
    public function error() {
        return $this->_error;
    }
    public function lastID() {
        return $this->_lastInsertId;
    }


}