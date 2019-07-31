<?php

namespace LobbyCore\utils;


class MySQLManager
{

    /** @var resource $db */
    private $db;

    public function init() : void{
        $this->db = new \mysqli("127.0.0.1", "root", "oveCka18", "netsword");
        if($this->db->connect_error){
            error_log("Failed to connect to MySQL!");
        }
    }

    /**
     * @return resource
     */
    public function getMysqli(){
        return $this->db;
    }

    /**
     * @param string $table
     * @return array|null
     */
    public function fetchArray(string $table) : ?array{
        $query = "SELECT * FROM " . $table;
        $result = $this->db->query($query);
        if($result instanceof \mysqli_result){
            $array = $result->fetch_assoc();
            $result->free();
            return $array;
        }
        return null;
    }

    /**
     * @param string $table
     * @param string $key
     * @param string $value
     * @return array|null
     */
    public function fetchArrayByKey(string $table, string $key, string $value) : ?array{
        $query = "SELECT * FROM " . $table . " WHERE " . $key . "='" . $value . "'";
        $result = $this->db->query($query);
        if($result instanceof \mysqli_result){
            $array = $result->fetch_assoc();
            $result->free();
            return $array;
        }
        return null;
    }
}