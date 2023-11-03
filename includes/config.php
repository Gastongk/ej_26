<?php

define('DB_HOST', 'mysql:host=localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'user');

class ConexionDB{
    private static $instancia = null;
    private $conexion;

    private function __construct(){
        $dsn = DB_HOST .';dbname=' . DB_NAME; 
        $username = DB_USER;
        $password = DB_PASSWORD;
        try {
            $this->conexion = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            throw new Exception("Error al conectarse: " . $e->getMessage());
        }
    }

    public static function getInstancia(){
        if (self::$instancia === null) {
            self::$instancia = new ConexionDB();
        }
        return self::$instancia;
    }

    public function getConexion(){
        return $this->conexion;
    }

    public function ejecutarConsulta($sql, $parametros = array()) {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($parametros);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error al ejecutar la consulta: " . $e->getMessage());
        }
    }

    public function obtenerResultados($sql, $parametros = array()) {
        $stmt = $this->ejecutarConsulta($sql, $parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

