<?php
header("Content-Type: text/html; charset=utf-8");
class Form
{
    public $Name = "";
    public $Table = "";
    public $FNameWindow = "";
    public $FCloseWindow = true;
    public $FAuto = true;
    public $FTitle = "";
    public $FTbar = true;
    public $columNumber = true;
    public $FormMaterial = false;
    public $FBodyAncho = "auto";
    public $FDivAncho = "auto";
    public $FDivMaxAncho = false;
    public $FDivAlto = 30;
    public $FLabelAncho = "auto";
    public $FFieldAncho = "auto";
    public $FAlto = 300;
    public $FAncho = 300;
    public $Vwidth = "";
    public $VminWidth = "";
    public $VmaxWidth = "";
    public $Vheight = "";
    public $VminHeight = "";
    public $VmaxHeight = "";
    public $VscrollY = "true";
    public $VscrollX = "false";
    public $FPermisoInsert = true;
    public $FPermisoUpdate = true;
    public $FPermisoDelete = true;
    public $FDeleteBd = false;
    public $FLastInsert = "";
    public $FLastUpdate = "";
    public $FLastDelete = "";
    public $FVentanaTitle = "FORMULARIO";
    protected $ApiSql;
    protected $OpcionClass = "";
    protected $IndexClass = "";
    protected $JsonFormValue = "";
    protected $VarPost = "";
    private $ArrayForm = [];
    private $ContForm = 1;
    private $ArraySqlField = [];
    private $FormIni = [];
    public function __construct($apiSql, $varPos, $type = "form")
    {
        $this->ApiSql = $apiSql;
        if (isset($varPos["opcionClass"])) {
            $this->OpcionClass = $this->ValidateVar($varPos["opcionClass"]);
        }
        if (isset($varPos["jsonFormValue"])) {
            $this->JsonFormValue = $varPos["jsonFormValue"];
        }
        if (isset($varPos["indexClass"])) {
            $this->IndexClass = $this->ValidateVar($varPos["indexClass"]);
        }
        if (isset($varPos["activeOrder"])) {
            unset($varPos["activeOrder"]);
        }
        foreach ($varPos as $index => $value) {
            $this->VarPost .= $index . ': "' . $value . '",';
        }
        $this->VarPost =
            $this->VarPost != ""
                ? "{" . substr($this->VarPost, 0, -1) . "}"
                : "{}";
        if ($type == "form") {
            if ($this->OpcionClass == "" && $this->IndexClass > 0) {
                $this->OpcionClass = "vUpdate";
            } elseif ($this->OpcionClass == "") {
                $this->OpcionClass = "vInsert";
            }
        }
    }
    public function AddTextField(
        $label,
        $field,
        $width = "160",
        $required = "false",
        $hidden = "false",
        $value = ""
    ) {
        $hidden =
            $hidden === "true" ||
            $hidden === true ||
            $hidden === "hidden" ||
            $hidden === "hiden"
                ? "true"
                : "false";
        $required =
            $required === "true" ||
            $required === true ||
            $required === "required"
                ? "true"
                : "false";
        $this->ArrayForm[$field] = [
            "type" => "TextField",
            "label" => $label,
            "width" => $width,
            "required" => $required,
            "validate" => "",
            "hidden" => $hidden,
            "value" => $value,
        ];
    }
    public function AddComboBox(
        $label,
        $field,
        $width,
        $required = "false",
        $bd = "true",
        $config = "",
        $where = ""
    ) {
        $campodb = "";
        $textdb = "";
        $required =
            $required === "true" ||
            $required === true ||
            $required === "required"
                ? "true"
                : "false";
        if (gettype($field) == "string") {
            $campodb = $field;
        } else {
            $campodb = $field["id"];
            $textdb = $field["text"];
        }
        $this->ArrayForm[$campodb] = [
            "type" => "ComboBox",
            "textdb" => $textdb,
            "label" => $label,
            "width" => $width,
            "required" => $required,
            "validate" => "",
            "bd" => $bd,
            "config" => $config,
            "where" => $where,
        ];
    }
    public function AddTextArea(
        $label,
        $field,
        $width,
        $height,
        $required = "false"
    ) {
        $required =
            $required === "true" ||
            $required === true ||
            $required === "required"
                ? "true"
                : "false";
        $this->ArrayForm[$field] = [
            "type" => "TextArea",
            "label" => $label,
            "width" => $width,
            "height" => $height,
            "required" => $required,
            "validate" => "",
        ];
    }
    public function AddSeparator($text, $icon = "")
    {
        $this->ArrayForm["campo_" . $this->ContForm] = [
            "type" => "Separador",
            "text" => $text,
            "icon" => $icon,
        ];
        $this->ContForm++;
    }
    public function AddValidation($field, $type, $opc = "")
    {
        if ($type != "unique") {
            $this->ArrayForm[$field]["validate"] = $type;
        } else {
            $this->ArrayForm[$field]["validateUnique"] = $opc;
        }
    }
    public function AddSqlField($field, $value)
    {
        if (
            $this->OpcionClass != "fUpdate" &&
            $this->OpcionClass != "fInsert"
        ) {
            return;
        }
        $this->ArraySqlField[$field] = $value;
    }
    public function IniClass()
    {
        if ($this->FNameWindow == "") {
            $this->FNameWindow = $this->Name;
        }
        if (
            $this->OpcionClass == "fUpdate" ||
            $this->OpcionClass == "fInsert"
        ) {
            $this->SetFormData();
            echo '{"estado":"true","type":"form","name":"' .
                $this->Name .
                '","fCloseWindow":"' .
                $this->FCloseWindow .
                '","columNumber":"' .
                $this->columNumber .
                '"}';
            return;
        } elseif ($this->OpcionClass == "fDelete") {
            $this->DeleteFormData("form");
            return;
        }
        $rowSql = [];
        if ($this->OpcionClass == "vUpdate") {
            $rowSql = $this->GetFormData();
        }
        foreach ($this->ArrayForm as $field => $arrayField) {
            if (isset($rowSql[$field])) {
                $this->ArrayForm[$field]["value"] = isset($rowSql[$field])
                    ? utf8_encode($rowSql[$field])
                    : "";
            } else {
                $this->ArrayForm[$field]["value"] = isset(
                    $this->ArrayForm[$field]["value"]
                )
                    ? utf8_encode($this->ArrayForm[$field]["value"])
                    : "";
            }
            if ($arrayField["type"] == "ComboBox") {
                $this->ArrayForm[$field]["option"] =
                    $arrayField["bd"] == "false"
                        ? $this->OptionText($arrayField["config"])
                        : $this->OptionDb(
                            $arrayField["config"],
                            $arrayField["where"]
                        );
            }
        }
        $this->FormIni["url"] = $_SERVER["SCRIPT_NAME"];
        $this->FormIni["name"] = $this->Name;
        $this->FormIni["field"] = $this->ArrayField("createForm");
        $this->FormIni["fTbar"] = $this->FTbar;
        $this->FormIni["indexClass"] = $this->IndexClass;
        $this->FormIni["opcionClass"] = $this->OpcionClass;
        $this->FormIni["fNameWindow"] = $this->FNameWindow;
        $this->FormIni["fPermisoInsert"] = $this->FPermisoInsert;
        $this->FormIni["fPermisoUpdate"] = $this->FPermisoUpdate;
        $this->FormIni["fPermisoDelete"] = $this->FPermisoDelete;
        $this->FormIni["columNumber"] = $this->columNumber;
        $this->FormIni["FLastInsert"] = $this->FLastInsert;
        $this->FormIni["FLastUpdate"] = $this->FLastUpdate;
        $this->FormIni["FLastDelete"] = $this->FLastDelete;
        $this->FormIni["size"]["fBodyAncho"] = $this->FBodyAncho;
        $this->FormIni["size"]["fDivAncho"] = $this->FDivAncho;
        $this->FormIni["size"]["fDivMaxAncho"] = $this->FDivMaxAncho;
        $this->FormIni["size"]["fDivAlto"] = $this->FDivAlto;
        $this->FormIni["size"]["fLabelAncho"] = $this->FLabelAncho;
        $this->FormIni["size"]["fFieldAncho"] = $this->FFieldAncho;
        $this->FormIni["size"]["FormMaterial"] = $this->FormMaterial;
        echo '<div id="parent_form_' .
            $this->Name .
            '" style="height:100%;" data-role="win-body"></div>
                <script>$W.Form.ini(' .
            json_encode($this->FormIni) .
            "," .
            $this->VarPost .
            ");</script>";
    }
    protected function ValidateVar($value)
    {
        $value = str_replace(
            ["\r\n", "\r", "\n", "\\r", "\\n", "\\r\\n"],
            "<br />",
            $value
        );
        $value = str_replace(["--", '\'', "#", '"'], "", $value);
        $value = $this->ApiSql->real_escape_string($value);
        return utf8_decode($value);
    }
    protected function DeleteFormData($type)
    {
        if ($this->FPermisoDelete != true) {
            return;
        } elseif ($this->FDeleteBd === false) {
            $sql =
                "UPDATE " .
                $this->Table .
                " SET activo=0 WHERE id=" .
                $this->IndexClass;
        } else {
            $sql =
                "DELETE FROM " .
                $this->Table .
                " WHERE id=" .
                $this->IndexClass;
        }
        $query = $this->ApiSql->query($sql);
        if (!$query) {
            echo json_encode(["estado" => "false"]);
            return;
        }
        echo json_encode([
            "estado" => "true",
            "type" => $type,
            "fNameWindow" => $this->FNameWindow,
        ]);
    }
    protected function SetFormData()
    {
        if (
            ($this->OpcionClass == "fUpdate" &&
                $this->FPermisoUpdate != true) ||
            ($this->OpcionClass == "fInsert" && $this->FPermisoInsert != true)
        ) {
            return;
        }
        $query = true;
        $updateSql = "";
        $arraySave = [];
        $objForm = json_decode($this->JsonFormValue, true);
        foreach ($this->ArrayField("fieldDb") as $field) {
            $value = isset($objForm[$field])
                ? $this->ValidateVar($objForm[$field])
                : "";
            if (isset($this->ArrayForm[$field]["validateUnique"])) {
                if (!$this->ValidateUnique($field, $value)) {
                    echo '{"estado":"false","msj":"El campo <b>' .
                        $this->ArrayForm[$field]["label"] .
                        '</b> con valor <b>\"' .
                        $value .
                        '\"</b> ya ha sido registrado!."}';
                    exit();
                }
            }
            if (isset($this->ArrayForm[$field]["required"])) {
                if (
                    $this->ArrayForm[$field]["required"] == "tue" &&
                    $value == ""
                ) {
                    echo '{"estado":"false","msj":"Aviso,\nEl campo ' .
                        $field .
                        ' es obligatorio."}';
                    exit();
                }
            }
            $arraySave[$field] = $value;
            $updateSql .= $field . '=\'' . $value . '\',';
        }
        foreach ($this->ArraySqlField as $field => $value) {
            $arraySave[$field] = $value;
            $updateSql .= $field . '=\'' . $value . '\',';
        }
        if ($updateSql == "") {
        } elseif ($this->OpcionClass == "fInsert") {
            $sql = "INSERT INTO " . $this->Table;
            $sql .= " (`" . implode("`, `", array_keys($arraySave)) . "`)";
            $sql .= " VALUES ('" . implode("', '", $arraySave) . "')";
            $query = $this->ApiSql->query($sql);
        } else {
            $updateSql = substr($updateSql, 0, -1);
            $sql =
                "UPDATE " .
                $this->Table .
                " SET " .
                $updateSql .
                " WHERE id=" .
                $this->IndexClass;
            $query = $this->ApiSql->query($sql);
        }
        if (!$query) {
            echo $sql .
                '{"estado":"false","msj":"Aviso,\nNo se ejecuto el query."}';
            exit();
        }
        if ($this->OpcionClass == "fInsert") {
            $this->IndexClass = $this->ApiSql->insert_id();
        }
    }
    private function OptionText($config)
    {
        $array = [];
        if (gettype($config) == "string") {
            $campos = explode(",", $config);
            for ($i = 0, $j = count($campos); $i < $j; $i++) {
                list($index, $value) = explode(":", $campos[$i]);
                $array[] = ["index" => $index, "value" => $value];
            }
        } else {
            foreach ($config as $index => $value) {
                $array[] = ["index" => $index, "value" => $value];
            }
        }
        return $array;
    }
    private function OptionDb($config, $where)
    {
        $array = [];
        $myWhere = "";
        list($table, $fieldId, $fieldText, $activo) = explode(",", $config);
        if ($activo == "true") {
            $myWhere .= "AND activo = 1 ";
        }
        if ($where != "") {
            $myWhere .= "AND $where";
        }
        $sql = "SELECT * FROM $table WHERE id>0 $myWhere";
        $query = $this->ApiSql->query($sql);
        while ($row = $this->ApiSql->fetch_array($query)) {
            $array[] = [
                "index" => $row[$fieldId],
                "value" => utf8_encode($row[$fieldText]),
            ];
        }
        return $array;
    }
    private function GetFormData()
    {
        $campos = implode(",", $this->ArrayField("fieldDb"));
        $sql =
            "SELECT " .
            $campos .
            " FROM " .
            $this->Table .
            ' WHERE id="' .
            $this->IndexClass .
            '" LIMIT 0,1';
        $query = $this->ApiSql->query($sql);
        return $this->ApiSql->fetch_array($query);
    }
    private function ArrayField($option)
    {
        $response = [];
        if ($option == "fieldDb") {
            foreach ($this->ArrayForm as $field => $arrayField) {
                if ($arrayField["type"] == "Separador") {
                    continue;
                } elseif (
                    $arrayField["type"] == "ComboBox" &&
                    $arrayField["textdb"] != ""
                ) {
                    $response[] = $arrayField["textdb"];
                }
                $response[] = $field;
            }
        } elseif ($option == "createForm") {
            foreach ($this->ArrayForm as $field => $arrayField) {
                unset($arrayField["bd"]);
                unset($arrayField["where"]);
                unset($arrayField["config"]);
                $response[$field] = $arrayField;
            }
        }
        return $response;
    }
    private function ValidateUnique($field, $value)
    {
        $addWhere =
            $this->ArrayForm[$field]["validateUnique"] != ""
                ? "AND " . $this->ArrayForm[$field]["validateUnique"]
                : "";
        $whereIndex =
            $this->OpcionClass == "fUpdate"
                ? "AND id<>" . $this->IndexClass
                : "";
        $sqlValidate =
            "SELECT COUNT(id) AS cont FROM " .
            $this->Table .
            " WHERE activo=1 AND " .
            $field .
            '="' .
            $value .
            '" ' .
            $addWhere .
            " " .
            $whereIndex;
        $queryValidate = $this->ApiSql->query($sqlValidate);
        $cont = $this->ApiSql->result($queryValidate, 0, "cont");
        return $cont > 0 ? false : true;
    }
}
