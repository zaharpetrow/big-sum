<?php

class Cacher
{

    private $tableName = 'file';
    private $db;

    public function __construct()
    {
        $this->db = $this->getDB();
    }

    public function get_last_num()
    {
        $sql = "SELECT MAX(id) as id FROM {$this->tableName}";
        $sth = $this->db->query($sql);
        return $sth->fetch()['id'];
    }

    public function setCache(array $data)
    {
        if (!isset($data['num']) &&
                !isset($data['triangular']) &&
                !isset($data['divided'])) {
            throw new Exception('Не верный формат');
        }
        $this_num = $data['num'];
        $last_num = $this->get_last_num();

        if ($this_num > $last_num) {
            $sql = "INSERT INTO triangular_with_divided "
                    . "(number, triangular, divided) "
                    . "VALUES (?,?,?)";
            $sth = $this->db->prepare($sql);
            $sth->execute([
                $data['num'],
                $data['triangular'],
                $data['divided'],
            ]);
        }
    }

    public function get_first_num_with_count_divided($count_divided)
    {
        $allRows = $this->get_all_rows();
        foreach ($allRows as $row) {
            $array_divided = json_decode($row['divided']);

            if (count($array_divided) >= $count_divided) {
                return $row;
            }
        }
        return false;
    }

    public function max_divided()
    {
        $max_divided   = 0;
        $result_string = '';

        $allRows = $this->get_all_rows();

        foreach ($allRows as $row) {
            $array_divided = json_decode($row['divided']);

            if (count($array_divided) > $max_divided) {
                $max_divided   = count($array_divided);
                $result_string = $row;
            }
        }
        return ['max_divided' => $max_divided, 'result_string' => $result_string];
    }

    private function getDB(): PDO
    {
        $host    = 'localhost';
        $db      = 'test_db_2';
        $user    = 'root';
        $pass    = '';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => FALSE
        ];

        return (new PDO($dsn, $user, $pass, $opt));
    }

    private function get_all_rows()
    {
        $sql    = "SELECT * FROM {$this->tableName}";
        $result = $this->db->query($sql);
        return $result->fetchAll();
    }

}
