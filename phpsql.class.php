<?php
/**!
 * Coder By GamerboyTR (https://github.com/gamerboytr)
 * A Php Library For MySql
 * Version 1.0
 * License Under MIT
 */
namespace GamerboyTR;
use mysqli;

class PhpSql {
    private $database = null;
    private $databaseUser = "root";
    private $databasePassword = "";
    private $databaseHost = "localhost";

    function __construct($host = null, $username = null, $password = null) {
        $this->databaseHost = $host ? $host : $this->databaseHost;
        $this->databaseUser = $username ? $username : $this->databaseUser;
        $this->databasePassword = $password ? $password : $this->databasePassword;
        if(!$this->getStatus()['success']) {
            die("<br>Mysqli Error : ".$this->getStatus()['error']);
        };
    }
    /**
     * Connect Mysqli
     * @return object Mysql Variable
     */
    public function connect() {
        try {
            @$mysqli = new mysqli($this->databaseHost, $this->databaseUser, $this->databasePassword, $this->database);
            if($mysqli->connect_errno) die("<br>Mysqli Connect Error : ".$mysqli->connect_error);
            return $mysqli;
        } catch (\Throwable $th) {
            die("<br>Mysqli Connect Error : $th");
        }
    }
    /**
     * Get Mysqli Status
     * @return array Status
     */
    public function getStatus() {
        try {
            @$mysqli = new mysqli($this->databaseHost, $this->databaseUser, $this->databasePassword);
            if($mysqli->connect_errno) {
                return [
                    "success" => 0,
                    "errorType" => "Mysqli Error",
                    "errorCode" => $mysqli->connect_errno,
                    "error" => $mysqli->connect_error,
                ];
            }else {
                return ["success" => 1];
            }
        } catch (\Throwable $th) {
            return [
                "success" => 0,
                "errorType" => "Mysqli Error",
                "error" => $th,
            ];
        }
    }
    /**
     * Set Mysqli Config
     * @param string $host Mysqli Host
     * @param string $username Mysqli Username
     * @param string $password Mysqli Password
     * @return array Mysqli Status
     */
    public function setMysqli($host = "", $username = "", $password = "") {
        $this->databaseHost = $host ? $host : $this->databaseHost;
        $this->databaseUser = $username ? $username : $this->databaseUser;
        $this->databasePassword = $password ? $password : $this->databasePassword;
        if(!$this->getStatus()['success']) {
            die("<br>Mysqli Error : ".$this->getStatus()['error']);
        };
    }
    /**
     * Returning Mysqli Details
     * @return array Details
     */
    public function getMysqliDetails() {
        return [
            "database" => $this->database,
            "user" => $this->databaseUser,
            "password" => $this->databasePassword,
            "host" => $this->databaseHost,
            "active" => $this->getStatus(),
        ];
    }
    /**
     * Set Mysqli Database
     * @param string $database Database Name
     * @return boolean Connected
     */
    public function setDatabase($database) {
        $this->database = $database;
        if($this->getStatus()['success']) {
            return true;
        }else {
            return $this->getStatus();
        }
    }
    /**
     * Return Mysqli Query
     * @param string $query Mysqli Query
     * @param boolean $fetch Fetch Query
     * @param boolean $showErrors Show Query Errors
     * @return mixed Data From Table
     */
    public function query($query, $fetch = true) {
        if(!$this->database) die("<br>Please Select a Database First !");
        try {
            @$mysqli = new mysqli($this->databaseHost, $this->databaseUser, $this->databasePassword, $this->database);
            $myQuery = $mysqli->query($query);
            if(!$myQuery) die("<br>A Mysqli Query Error Occurred : ".$mysqli->error);
            if($fetch) return $myQuery->fetch_assoc();
            else return $myQuery;
        } catch (\Throwable $th) {
            die("<br>Mysqli Error : $th");
        }
    }
    /**
     * Create Table For Database
     * @param string $name Table Name
     * @param array $props Table Props
     * @return array
     */
    public function createTable($name, $props) {
        if(!is_array($props)) die("<br>Props Must Be Array In Array");
        $str = "CREATE TABLE $name (";
        foreach($props as $key => $val) {
            if(empty($val['name']) || empty($val['type'])) die("<br>Name And Type Required");
            $val = array_merge([
                "length" => 11,
                "AI" => false,
                "unique" => null,
                "isnull" => false,
                "comment" => null
            ],$val);
            if(!is_array($val)) die("<br>Props Must Be Array In Array");
            $str .= "`".$val['name']."` ".$val['type']."(".$val['length'].") ".(!$val['isnull'] ? 'NOT NULL ' : '').($val['AI'] ? 'AUTO_INCREMENT' : '').($val['comment'] ? 'COMMENT "'.$val['comment'].'"' : '')." ".$val['unique'].",";
        }
        $str = trim(mb_substr(preg_replace("/\s\s/", " ", trim($str)), 0, -1));
        $str .= ")";
        $mysqli = $this->connect();
        $mysqli->select_db($this->database);
        $query = $mysqli->query($str);
        if($query) return ["success" => true];
        else return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
            "tryingCode" => $str,
        ];
    }
    /**
     * Select Data From Table
     * @param string $column Select Column (* = Select All)
     * @param string $extra Extra Selector (e.g. WHERE, ORDER BY)
     * @param string $table Selecting Data Table
     * @param string $fetch Fetch Query
     * @return mixed
     */
    public function select($column, $table, $extra = null, $fetch = true) {
        @$mysqli = new mysqli($this->databaseHost, $this->databaseUser, $this->databasePassword, $this->database);
        $query = $mysqli->query("SELECT $column FROM $table".($extra ? ' '.$extra : ''));
        if($mysqli->errno || !$query) die("<br>Error Fetching Column : ".$mysqli->error);
        if($fetch) return $query->fetch_assoc();
        else return $query;
    }
}