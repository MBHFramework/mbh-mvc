<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 COD-Project
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

namespace Mbh\Connection;

/**
 * created by Ulises Jeremias Cornejo Fandos
 */
class StdConnection extends \PDO
{
    private static $instance;

    /**
     * Starts the connection instance, if it has already been declared before, does not duplicate it and saves memory.
     *
     * @param array $database
     *
     * @return connection instance
     */
    public static function start($database = [], $new_instance = false)
    {
        if (!self::$instance instanceof self or $new_instance) {
            self::$instance = new self($database);
        }
        return self::$instance;
    }

    /**
     * Starts database connection
     *
     * @param array $database
     *
     * @return connection instance
     */
    public static function create($database = [])
    {
        try {
            $driver = isset($database['driver']) ? ucwords($database['driver']) : "";
            $className = __NAMESPACE__ . "\\Engines\\" . $driver;
            if (class_exists($className)) {
                return new $className($database);
            } else {
                throw new \RuntimeException("Unidentified connection engine $className.");
            }
        } catch (\PDOException $e) {
            throw new \RuntimeException('Problem connecting to the database: ' . $e->getMessage());
        }
    }

    /**
     * Returns an associative array of all the results thrown by a query
     *
     * @param object \PDOStatement $query, return value of the query
     *
     * @return associative array
     */
    public function fetchArray($query)
    {
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the number of rows found after a SELECT
     *
     * @param object \PDOStatement $query, return value of the query
     *
     * @return number of rows found
     */
    public function rows($query)
    {
        return $query->rowCount();
    }

    /**
     * Heals a value to be later entered into a query
     *
     * @param string/int/float
     *
     * @return int/float/string
     */
    public function escape($e)
    {
        if (!isset($e)) {
            return '';
        }
        if (is_numeric($e) and PHP_INT_MIN <= $e and $e <= PHP_INT_MAX) {
            if (explode('.', $e)[0] != $e) {
                return (float) $e;
            }
            return (int) $e;
        }
        return (string) trim(str_replace(['\\',"\x00",'\n','\r',"'",'"',"\x1a"], ['\\\\','\\0','\\n','\\r',"\'",'\"','\\Z'], $e));
    }

    /**
     * Performs a query, and if it is in debug mode it analyzes which query was executed
     *
     * @param SQL string, recieve a SQL query to execute
     *
     * @return object \PDOStatement
     */
    public function query($q)
    {
        try {
            $_SESSION['___QUERY_DEBUG___'][] = (string) $q;
            return parent::query($q);
        } catch (\Exception $e) {
            $message = 'Error in query: <b>' . $q . '<b/><br /><br />' . $e->getMessage();
        }
    }

    /**
     * Clears a series of items securely from a table in the database
     *
     * @param string $table: Table to which you want to remove an element
     * @param string $where: Deletion condition that defines who are those elements
     * @param string $limit: By default it is limited to deleting a single element that matches the $ where
     *
     * @return object \PDOStatement
     */
    public function delete($table, $where, $limit = 'LIMIT 1')
    {
        return $this->query("DELETE FROM $table WHERE $where $limit;");
    }

    /**
     * Insert a series of elements into a table in the database
     *
     * @param string $table: Table to which elements are to be inserted
     * @param array $e: Associative arrangement of elements, with the 'field_en_la_tabla' => 'value_to_insertar_en_ese_campo',
     *                  all elements of the array $ e, will be healed by the method without having to do it manually when creating the array...
     *
     * @return object \PDOStatement
     */
    public function insert($table, $e)
    {
        if (sizeof($e) == 0) {
            trigger_error('array passed in Connection::insert(...) is empty.', E_ERROR);
        }
        $query = "INSERT INTO $table (";
        $values = '';
        foreach ($e as $campo => $v) {
            $query .= $campo . ',';
            $values .= '\'' . $this->escape($v) . '\',';
        }
        $query[strlen($query) - 1] = ')';
        $values[strlen($values) - 1] = ')';
        $query .= ' VALUES (' . $values . ';';
        self::$id = parent::lastInsertId($table);
        return $this->query($query);
    }

    /**
     * Updates elements of a table in the database according to a condition
     *
     * @param string $table: table to update
     * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
     *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
     * @param string $where: Condition indicating who will be modified
     * @param string $limite: Limit modified elements, by default modifies them all
     *
     * @return object \PDOStatement
     */
    public function update($table, $e, $where, $limit = '')
    {
        if (sizeof($e) == 0) {
            trigger_error('El arreglo pasado por $this->db->update(...) está vacío.', E_ERROR);
        }
        $query = "UPDATE $table SET ";
        foreach ($e as $campo => $valor) {
            $query .= $campo . '=\'' . $this->escape($valor) . '\',';
        }
        $query[strlen($query) - 1] = ' ';
        $query .= "WHERE $where $limit;";
        return $this->query($query);
    }

    /**
     * Selects and lists in an associative / numeric array the results of a search in the database
     *
     * @param string $e: Elements to Select Comma Separated
     * @param string $table: Table from which you want to extract the elements $e
     * @param string $where: Condition that indicates who are the ones that are extracted, if not placed extracts all
     * @param string $limite: Limit of items to bring, by default brings ALL those that match $where
     *
     * @return False if you do not find any results, array associative / numeric if you get at least one
     */
    public function select($e, $table, $where = '1 = 1', $limit = "")
    {
        $sql = $this->query("SELECT $e FROM $table WHERE $where $limit;");
        $result = $sql->fetchAll();
        $sql->closeCursor();
        return $sql;
    }

    /**
     * Alert to avoid cloning
     *
     * @throws \RuntimeException
     * @return void
     */
    public function __clone()
    {
        throw new \RuntimeException('You are trying to clone the Connection', E_ERROR);
    }
}
