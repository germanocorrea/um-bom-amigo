<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 04/10/16
 * Time: 10:54
 */

namespace Model;


abstract class Model
{

    protected $table;
    protected $pdo;
    protected $data;

    public function __construct()
    {
        if (!$this->table) $this->table = $this->discoverTableName();

        $connection = \Config\DataBase::$driver . ':';
        $connection .= 'host=' . \Config\DataBase::$host . ';';
        $connection .= 'dbname=' . \Config\DataBase::$database . ';';
        $connection .= 'charset=' . \Config\DataBase::$charset;

        $options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];

        try
        {
            $this->pdo = new \PDO($connection, \Config\DataBase::$username, \Config\DataBase::$password);
        }
        catch (\PDOException $e)
        {
            die('Connection: ' . $e->getMessage());
        }
    }

    protected function discoverTableName()
    {
        $class = explode('\\', get_class($this));

        return strtolower(end($class)) . 's';
    }

    public function setTableName($table_name)
    {
        return $this->table = $table_name;
    }

    public function search($what, $options = false)
    {
        switch ($what)
        {
            case 'one':
                return $this->searchOne($options);
                break;
            case 'all':
                return $this->searchAll($options);
                break;
            case 'count':
                return $this->count($options);
                break;
        }

        throw new \Exception('Invalid search mode!');
    }

    private function searchOne($options)
    {
        $everything = $this->searchAll($options);

        if (!count($everything)) return false;

        $returnObject = clone $this;
        $returnObject->data = $everything[0];
        return $returnObject;
    }

    private function searchAll($options)
    {
        $sql = $this->mountSQL($options, 'select');

        return $this->executeSQL($sql, $options);
    }

    private function count($options)
    {
        $sql = $this->mountSQL($options, 'count');

        $count = $this->executeSQL($sql, $options);

        if (!count($count)) return false;

        return (int) $count[0]['COUNT(*)'];
    }

    private function executeSQL($sql, $options)
    {
        $data = $this->pdo->prepare($sql);

        $bind = [];

        if (isset($options['fields'])) $bind = array_values($options['fields']);

        if (isset($options['conditions'])) $bind = array_merge($bind, array_values($options['conditions']));

        if (!count($bind)) $bind = null;

        try
        {
            $data->execute($bind);
        }
        catch (\PDOException $e)
        {
            throw new \Exception('Database: ' . $e->getMessage());
            return false;
        }

        $error = $data->errorInfo();

        if ($error[1]) {
            throw new \Exception('Database: ' . $error[2]);
            return false;
        }

        return $data->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function mountSQL($options = false, $type = 'select')
    {
        $sqlOptions = '';

        if ($options && count($options)) {
            if (isset($options['conditions'])) $sqlOptions .= ' WHERE ' . implode(' AND ', array_keys($options['conditions']));
        }

        switch ($type) {
            case 'select':
            default:
                $sql = 'SELECT * FROM ' . $this->table;
                break;
            case 'count':
                $sql = 'SELECT COUNT(*) FROM ' . $this->table;
                break;
            case 'insert':
            case 'update':
                if (!isset($options['fields']) || !($set = $this->mountSet($options['fields']))) {
                    throw new \Exception('No field to insert.');
                    return false;
                }
                $sql = ($type == 'insert') ? 'INSERT INTO ' : 'UPDATE ';
                $sql .= $this->table . $set;
                break;
            case 'delete':
                $sql = 'DELETE FROM ' . $this->table;
                break;
        }

        return $sql . ' ' . $sqlOptions;
    }

    private function mountSet($fields)
    {
        if (!is_array($fields) || !count($fields)) return false;

        $fields = array_keys($fields);
        $set = ' SET ' . implode(' = ?, ', $fields) . ' = ? ';

        return $set;
    }

    public function record($multi = false)
    {
        if (!$this->data && !$multi) return false;

        if ($multi && !isset($multi[0])) return false;

        if (!$multi) $multi = [0 => $this->data];

        foreach ($multi as $data)
        {
            $options = ['fields' => $data];

            if (isset($data['id']))
            {
                $options['conditions'] = [
                    'id = ?' => $data['id']
                ];

                $sql = $this->mountSQL($options, 'update');
            }
            else
            {
                $sql = $this->mountSQL($options, 'insert');
            }

            $this->executeSQL($sql, $options);
        }

        return true;
    }

    public function delete()
    {
        if (!isset($this->data['id'])) return false;

        return $this->deleteById($this->data['id']);
    }

    public function deleteById($id)
    {
        if (is_numeric($id))
        {
            $options = [
                'conditions' => [
                    'id = ?' => $id,
                ]
            ];

            $sql = $this->mountSQL($options, 'delete');

            $this->executeSQL($sql, $options);

            return true;
        }

        return false;
    }

    public function deleteByUniqueKey($key, $column)
    {
        if (is_string($key))
        {
            $options = [
                'conditions' => [
                    $column . ' = ?' => $key,
                ]
            ];

            $sql = $this->mountSQL($options, 'delete');

            if ($this->executeSQL($sql, $options)) return true;
        }
    }

    public function get($var)
    {
        return isset($this->data[$var]) ? $this->data[$var] : null;
    }

    public function set($var, $valor)
    {
        $this->data[$var] = $valor;
        return true;
    }
}
