<?php
class Orm_Controller 
{
    private $mysqli = null;
    private $resource = null;
    private $ServidorDb;
    private $UsuarioDb;
    private $PasswordDb;
    private $NameDb;

    /**
     * Constructor de la clase.
     * Inicializa los datos de conexión y establece la conexión con la base de datos.
     * 
     * @param string $ServidorDb  Servidor de la base de datos
     * @param string $UsuarioDb   Usuario de la base de datos
     * @param string $PasswordDb  Contraseña del usuario
     * @param string $NameDb      Nombre de la base de datos
     */
    public function __construct($ServidorDb, $UsuarioDb, $PasswordDb, $NameDb) {
        $this->ServidorDb = $ServidorDb;
        $this->UsuarioDb = $UsuarioDb;
        $this->PasswordDb = $PasswordDb;
        $this->NameDb = $NameDb;
        $this->connect();
    }

    /**
     * Establece una conexión con la base de datos.
     * 
     * @return array ['success' => bool, 'data' => null|string (mensaje de error si falla)]
     */
    public function connect() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->mysqli = new mysqli($this->ServidorDb, $this->UsuarioDb, $this->PasswordDb, $this->NameDb);

        if ($this->mysqli->connect_error) {
            return ['success' => false, 'data' => "Error conectando al servidor: " . $this->mysqli->connect_error];
        }

        $this->mysqli->set_charset("utf8mb4");
        return ['success' => true, 'data' => null];
    }

    /**
     * Ejecuta una consulta SQL.
     * 
     * @param string $sql Consulta SQL a ejecutar
     * @return array ['success' => bool, 'data' => mysqli_result|null (resultado de la consulta o mensaje de error)]
     */
    public function query($sql) {   
        if (!$this->mysqli) {
            return ['success' => false, 'data' => "Error: No hay conexión a la base de datos."];
        }

        $this->resource = $this->mysqli->query($sql);

        if (!$this->resource) {
            return ['success' => false, 'data' => "Error en la consulta: " . $this->mysqli->error];
        }

        return ['success' => true, 'data' => $this->resource];
    }

    /**
     * Obtiene todos los resultados de una consulta en un array asociativo.
     * 
     * @param string $sql Consulta SQL a ejecutar
     * @return array ['success' => bool, 'data' => array|null (datos obtenidos o mensaje de error)]
     */
    public function fetchAll($sql) {
        $queryResult = $this->query($sql);
        if (!$queryResult['success']) {
            return $queryResult;
        }

        $result = [];
        while ($row = mysqli_fetch_assoc($this->resource)) {
            $result[] = $row;
        }
        mysqli_free_result($this->resource);

        return ['success' => true, 'data' => $result];
    }

    /**
     * Obtiene un solo registro de una consulta SQL.
     * 
     * @param string $sql Consulta SQL a ejecutar
     * @return array ['success' => bool, 'data' => array|null (registro obtenido o mensaje de error)]
     */
    public function fetchOne($sql) {
        $queryResult = $this->query($sql);
        if (!$queryResult['success']) {
            return $queryResult;
        }

        $row = mysqli_fetch_assoc($this->resource);
        mysqli_free_result($this->resource);

        return ['success' => true, 'data' => $row ?: null];
    }

    /**
     * Obtiene los resultados de una consulta y los organiza en un array indexado por un campo específico.
     * 
     * @param string $sql Consulta SQL a ejecutar
     * @param string $indice Nombre del campo que se usará como índice
     * @return array ['success' => bool, 'data' => array|null (datos indexados o mensaje de error)]
     */
    public function fetchIndexed($sql, $indice) {
        if ($indice === null) {
            return ['success' => false, 'data' => "Debe especificar un campo como índice para 'fetchIndexed'."];
        }

        $queryResult = $this->query($sql);
        if (!$queryResult['success']) {
            return $queryResult;
        }

        $result = [];
        while ($row = mysqli_fetch_assoc($this->resource)) {
            if (!isset($row[$indice])) {
                return ['success' => false, 'data' => "El campo '$indice' no existe en los resultados."];
            }
            $result[$row[$indice]] = $row;
        }
        mysqli_free_result($this->resource);

        return ['success' => true, 'data' => $result];
    }

    /**
     * Obtiene un único valor de la primera fila y primera columna de una consulta SQL.
     * 
     * @param string $sql Consulta SQL a ejecutar
     * @return array ['success' => bool, 'data' => mixed|null (valor encontrado o mensaje de error)]
     */
    public function fetchValue($sql) {
        $queryResult = $this->fetchOne($sql);

        if (!$queryResult['success'] || empty($queryResult['data'])) {
            return ['success' => false, 'data' => "No se encontró el valor"];
        }

        $value = reset($queryResult['data']);
        return ['success' => true, 'data' => $value];
    }
    /**
     * Devuelve el objeto mysql
     */
    public function cerrarConexion() {
        if ($this->mysqli !== null) {
            mysqli_close($this->mysqli);
            $this->mysqli = null;
        }
    }
    /**
     * Devuelve el objeto de conexion.
     */
    public function getConnection() {
        return $this->mysqli;
    }

    /**
     * Devuelve la base de datos a la que esta conectado.
     */
    public function getNameDb() {
        return $this->NameDb;
    }
}