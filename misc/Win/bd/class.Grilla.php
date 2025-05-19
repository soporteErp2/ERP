<?php
require_once "class.Form.php";
error_reporting(E_ALL);
ini_set("display_errors", "1");
class Grilla extends Form
{
    public $Toolbar = false;
    public $FieldToolbar = "";
    public $FieldOrder = "";
    public $SqlLimit = "0,50";
    public $SqlGroup = "";
    public $SqlOrder = "";
    public $SqlWhere = "";
    public $SqlDebug = false;
    public $SqlJoin = "";
    public $EventDelete = true;
    public $EventInsert = true;
    public $EventUpdate = true;
    public $TextBtnNuevo = "Nuevo";
    public $Tbar = true;
    public $TbarHeight = 64;
    private $AdvancedToolbar = "disable";
    private $ValueToolbar = "";
    private $ActiveOrder = "";
    private $Pagina = 1;
    private $MaxPage = 1;
    private $ContCol = 1;
    private $ArrayFieldDb = [];
    private $ArrayCol = [];
    private $GrillaIni = [];
    private $ArrayFilter = [];
    private $ArrayCtxMenu = [];
    private $FilterData = [];
    function __construct($apiSql, $varPos)
    {
        $this->ArrayFieldDb[] = "T1.id";
        $this->ApiSql = $apiSql;
        if (isset($varPos["contador"])) {
        }
        if (isset($varPos["pagina"])) {
            $this->Pagina = parent::ValidateVar($varPos["pagina"]);
        }
        if (isset($varPos["opcionClass"])) {
            $this->OpcionClass = parent::ValidateVar($varPos["opcionClass"]);
        }
        if (isset($varPos["advancedToolbar"])) {
            $this->AdvancedToolbar = parent::ValidateVar(
                $varPos["advancedToolbar"]
            );
        }
        if (isset($varPos["valueToolbar"])) {
            $this->ValueToolbar = parent::ValidateVar($varPos["valueToolbar"]);
        }
        if (isset($varPos["activeOrder"])) {
            $this->ActiveOrder = $varPos["activeOrder"];
        }
        if (isset($varPos["FilterData"])) {
            $this->FilterData = $varPos["FilterData"];
        }
        parent::__construct($apiSql, $varPos, "grid");
    }
    private function os()
    {
        $os = "desktop";
        if (stripos($_SERVER["HTTP_USER_AGENT"], "iPhone")) {
            $os = "ios";
        } elseif (stripos($_SERVER["HTTP_USER_AGENT"], "iPad")) {
            $os = "ios";
        } elseif (stripos($_SERVER["HTTP_USER_AGENT"], "Android")) {
            $os = "android";
        }
        return $os;
    }
    public function AddCol($title, $field, $width)
    {
        if ($this->os() == "desktop") {
            $nameCol = $this->NameFielQuery($field);
            $this->ArrayCol[$nameCol] = [
                "type" => "col",
                "title" => $title,
                "width" => $width,
                "movil" => false,
            ];
            $this->ArrayFieldDb[] = $field;
        }
    }
    public function AddColMovil($title, $field, $width)
    {
        if ($this->os() == "android" || $this->os() == "ios") {
            $nameCol = $this->NameFielQuery($field);
            $this->ArrayCol[$nameCol] = [
                "type" => "col",
                "title" => $title,
                "width" => $width,
                "movil" => true,
            ];
            $this->ArrayFieldDb[] = $field;
        }
    }
    public function AddColHtml($title, $html, $width)
    {
        if ($this->os() == "desktop") {
            $arrayVar = $this->ReplaceVar($html);
            $this->ArrayCol["campo_" . $this->ContCol] = [
                "type" => "html",
                "title" => $title,
                "html" => $html,
                "width" => $width,
                "arrayVar" => $arrayVar,
                "movil" => false,
            ];
            $this->ContCol++;
        }
    }
    public function AddColHtmlMovil($title, $html, $width)
    {
        if ($this->os() == "android" || $this->os() == "ios") {
            $arrayVar = $this->ReplaceVar($html);
            $this->ArrayCol["campo_" . $this->ContCol] = [
                "type" => "html",
                "title" => $title,
                "html" => $html,
                "width" => $width,
                "arrayVar" => $arrayVar,
                "movil" => true,
            ];
            $this->ContCol++;
        }
    }
    public function AddColStyle($field, $styleCss, $validate = "")
    {
        if ($this->os() == "desktop") {
            $nameCol = $this->NameFielQuery($field);
            $this->ArrayCol[$nameCol]["style"] = $styleCss;
        }
    }
    public function AddColFunction(
        $campoMysql,
        $name,
        $option1 = "",
        $option2 = ""
    ) {
        $nameCol = $this->NameFielQuery($campoMysql);
        $this->ArrayCol[$nameCol]["function"] = [
            "name" => $name,
            "option1" => $option1,
            "option2" => $option2,
        ];
    }
    public function AddFilter($title, $id, $value)
    {
        $this->ArrayFilter[] = [
            "title" => $title,
            "id" => $id,
            "value" => $value,
        ];
    }
    public function AddCtxMenu($txt, $handler, $icon)
    {
        $arrayVar = $this->ReplaceVar($handler);
        $this->ArrayCtxMenu[] = [
            "text" => $txt,
            "cls" => $icon,
            "handler" => $handler,
            "arrayVar" => $arrayVar,
        ];
    }
    public function SqlJoin($join)
    {
        $this->SqlJoin .= $join;
    }
    public function AddFilterAside($title, $idCampo)
    {
        $arrayDat = $this->ReplaceDat(1, $idCampo, "", "", "", "");
        $this->ArrayFilter[] = [
            "title" => $title,
            "idCampo" => $idCampo,
            "labels" => $arrayDat,
        ];
    }
    public function AddFilterAsideJoin(
        $title,
        $idCampo,
        $tabla,
        $idJoin,
        $nombreJoin
    ) {
        $arrayDat = $this->ReplaceDat(
            2,
            $idCampo,
            $tabla,
            $idJoin,
            $nombreJoin
        );
        $this->ArrayFilter[] = [
            "title" => $title,
            "idCampo" => $idCampo,
            "labels" => $arrayDat,
        ];
    }
    private function ReplaceDat($tipo, $idCampo, $tabla, $idJoin, $nombreJoin)
    {
        $arrayLabels = [];
        if ($tipo == 1) {
            $SQL1 =
                "SELECT " .
                $idCampo .
                ",count(" .
                $idCampo .
                ") as cuantos FROM " .
                $this->Table .
                " GROUP BY " .
                $idCampo;
            $result1 = $this->ApiSql->query($SQL1);
            while ($row1 = $this->ApiSql->fetch_array($result1)) {
                $arrayLabels[] = [
                    "label" => utf8_encode($row1[$idCampo]),
                    "cantidad" => $row1["cuantos"],
                    "valor" => utf8_encode($row1[$idCampo]),
                ];
            }
        }
        if ($tipo == 2) {
            $whereSql =
                $this->SqlWhere != "" ? " WHERE " . $this->SqlWhere : "";
            $SQL1 =
                "SELECT " .
                $idCampo .
                ",count(T1.id) as cuantos FROM " .
                $this->Table .
                " AS T1 " .
                $this->SqlJoin .
                " " .
                $whereSql .
                " GROUP BY " .
                $idCampo;
            $result1 = $this->ApiSql->query($SQL1);
            while ($row1 = $this->ApiSql->fetch_array($result1)) {
                $SQL2 =
                    "SELECT " .
                    $nombreJoin .
                    " FROM " .
                    $tabla .
                    " WHERE " .
                    $idJoin .
                    ' = \'' .
                    $row1[$idCampo] .
                    '\'';
                $label = $this->ApiSql->result(
                    $this->ApiSql->query($SQL2),
                    0,
                    $nombreJoin
                );
                $arrayLabels[] = [
                    "label" => utf8_encode($label),
                    "cantidad" => $row1["cuantos"],
                    "valor" => utf8_encode($row1[$idCampo]),
                ];
            }
        }
        return $arrayLabels;
    }
    public function IniClass()
    {
        $this->FNameWindow = "Win_grilla_form_" . $this->Name;
        if (
            $this->OpcionClass == "vUpdate" ||
            $this->OpcionClass == "vInsert"
        ) {
            parent::IniClass();
            return;
        } elseif (
            $this->OpcionClass == "updateRow" ||
            $this->OpcionClass == "insertRow"
        ) {
            $this->SelectRowSql();
            return;
        } elseif (
            $this->OpcionClass == "fUpdate" ||
            $this->OpcionClass == "fInsert"
        ) {
            parent::SetFormData();
            $this->SelectRowSql();
            return;
        } elseif ($this->OpcionClass == "fDelete") {
            parent::DeleteFormData("grilla");
            return;
        }
        $like = $this->AdvancedToolbar == "enable" ? "" : "%";
        $acumWhere = "";
        $whereSql = $this->SqlWhere != "" ? " WHERE " . $this->SqlWhere : "";
        if ($this->ValueToolbar != "") {
            $arrayToolbar = explode(",", $this->FieldToolbar);
            foreach ($arrayToolbar as $field) {
                $acumWhere .=
                    $field .
                    ' LIKE "' .
                    $like .
                    $this->ValueToolbar .
                    $like .
                    '" OR ';
            }
            $acumWhere =
                $whereSql != ""
                    ? " AND ( " . substr($acumWhere, 0, -3) . " )"
                    : " WHERE ( " . substr($acumWhere, 0, -3) . " )";
        }
        $sqlGroup = $this->SqlGroup != "" ? " GROUP BY " . $this->SqlGroup : "";
        $sqlOrder = $this->SqlOrder != "" ? " ORDER BY " . $this->SqlOrder : "";
        $sqlQueryCont =
            "SELECT T1.id FROM " .
            $this->Table .
            " AS T1 " .
            $this->SqlJoin .
            " " .
            $whereSql .
            " " .
            $acumWhere .
            " " .
            $sqlGroup;
        $queryLimit = $this->ApiSql->query($sqlQueryCont);
        $contData = $this->ApiSql->num_rows($queryLimit);
        $this->CreatePaginacion($this->SqlLimit, $contData);
        $arrayField = array_unique($this->ArrayFieldDb);
        $campos = implode(",", $arrayField);
        $jsonOrder = [];
        if ($this->ActiveOrder != "") {
            $jsonOrder = json_decode($this->ActiveOrder, true);
            if (isset($jsonOrder["field"]) && isset($jsonOrder["state"])) {
                $state = $jsonOrder["state"] == "upOn" ? "ASC" : "DESC";
                $sqlOrder = " ORDER BY " . $jsonOrder["field"] . " " . $state;
            }
        }
        if ($this->FilterData) {
            $arrayFilterData = json_decode(
                str_replace("'", "\"", $this->FilterData),
                true
            );
            for ($i = 0; $i < count($arrayFilterData); $i++) {
                if ($whereSql != "") {
                    $whereSql .= " AND ";
                }
                $whereSql .=
                    $arrayFilterData[$i]["campo"] .
                    " = '" .
                    $arrayFilterData[$i]["valor"] .
                    "'";
            }
        }
        $this->SqlQueryData =
            "SELECT " .
            $campos .
            " FROM " .
            $this->Table .
            " AS T1 " .
            $this->SqlJoin .
            " " .
            $whereSql .
            " " .
            $acumWhere .
            " " .
            $sqlGroup .
            " " .
            $sqlOrder .
            " LIMIT " .
            $this->SqlLimit;
        $queryData = $this->ApiSql->query($this->SqlQueryData);
        if ($this->SqlDebug == true) {
            echo $this->SqlQueryData;
        }
        if ($this->Vwidth == "") {
            $this->Vwidth = $this->FAncho;
        }
        if ($this->Vheight == "") {
            $this->Vheight = $this->FAlto;
        }
        $this->GrillaIni["pagina"] = $this->Pagina;
        $this->GrillaIni["maxPage"] = $this->MaxPage;
        $this->GrillaIni["idApply"] = "parent_grilla_" . $this->Name;
        $this->GrillaIni["opcionClass"] = $this->OpcionClass;
        $this->GrillaIni["filterAside"] = $this->ArrayFilter;
        $this->GrillaIni["name"] = utf8_encode($this->Name);
        $this->GrillaIni["url"] = $_SERVER["SCRIPT_NAME"];
        $this->GrillaIni["tbar"] = $this->Tbar;
        $this->GrillaIni["tbarHeight"] = $this->TbarHeight;
        $this->GrillaIni["toolbar"] = $this->Toolbar;
        $this->GrillaIni["valueToolbar"] = utf8_encode($this->ValueToolbar);
        $this->GrillaIni["advancedToolbar"] = $this->AdvancedToolbar;
        $this->GrillaIni["fPermisoInsert"] = $this->FPermisoInsert;
        $this->GrillaIni["textBtnNuevo"] = $this->TextBtnNuevo;
        $this->GrillaIni["columNumber"] = $this->columNumber;
        $this->GrillaIni["eventDelete"] = $this->EventDelete;
        $this->GrillaIni["eventInsert"] = $this->EventInsert;
        $this->GrillaIni["eventUpdate"] = $this->EventUpdate;
        $this->GrillaIni["fNameWindow"] = $this->FNameWindow;
        $this->GrillaIni["fTitle"] = utf8_encode($this->FTitle);
        $this->GrillaIni["Vwidth"] = $this->Vwidth;
        $this->GrillaIni["VmaxWidth"] = $this->VmaxWidth;
        $this->GrillaIni["VminWidth"] = $this->VminWidth;
        $this->GrillaIni["Vheight"] = $this->Vheight;
        $this->GrillaIni["VminHeight"] = $this->VminHeight;
        $this->GrillaIni["VmaxHeight"] = $this->VmaxHeight;
        $this->GrillaIni["VmaxHeight"] = $this->VmaxHeight;
        $this->GrillaIni["VscrollY"] = $this->VscrollY;
        $this->GrillaIni["VscrollX"] = $this->VscrollY;
        $this->GrillaIni["titleItems"] = $this->AddTitleFilaGrilla($jsonOrder);
        while ($row = $this->ApiSql->fetch_array($queryData)) {
            $sqlData = $this->AddBodyFilaGrilla($row);
            $this->GrillaIni["rows"][] = [
                "idRow" => $row["id"],
                "cols" => $sqlData["cols"],
                "ctxmenu" => json_encode($sqlData["ctxmenu"]),
            ];
        }
        if ($this->OpcionClass == "") {
            echo '<div id="parent_grilla_' .
                $this->Name .
                '" class="parent_grilla" style="height:100%;" data-role="win-body"></div>';
        }
        echo '<script>$W.Grilla.ini(' .
            json_encode($this->GrillaIni) .
            "," .
            $this->VarPost .
            ");</script>";
    }
    private function Codigo($codigo, $length = 4, $str = "0")
    {
        return str_pad($codigo, $length, $str, STR_PAD_LEFT);
    }
    private function Moneda($valor, $simbolo = "", $decimales = 0)
    {
        if ($simbolo == "") {
            $sql =
                "SELECT simbolo,decimales FROM configuracion_moneda WHERE predeterminado = 'true'";
            $query = $this->ApiSql->query($sql);
            $simbolo = $this->ApiSql->result($query, 0, "simbolo");
            $decimales = $this->ApiSql->result($query, 0, "decimales");
        }
        return $simbolo . " " . number_format($valor, $decimales);
    }
    private function Fecha($fecha)
    {
        if ($fecha == "") {
            return "";
        }
        list($date, $time) = explode(" ", $fecha);
        list($year, $month, $day) = explode("-", $date);
        $ww = date("w", mktime(0, 0, 0, date($month), date($day), date($year)));
        $dias = [
            "Domingo",
            "Lunes",
            "Martes",
            "Miercoles",
            "Jueves",
            "Viernes",
            "Sabado",
        ];
        $meses = [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        ];
        $resultado =
            $dias[$ww] . " " . $day . " " . $meses[$month - 1] . " de " . $year;
        if ($time != "") {
            $resultado .= " - " . $time;
        }
        return $resultado;
    }
    private function ReplaceVar($html)
    {
        preg_match_all("/\[[^\]]*\]/", $html, $array);
        $result = str_replace(["[", "]"], "", $array[0]);
        $this->ArrayFieldDb = array_merge($this->ArrayFieldDb, $result);
        return $result;
    }
    private function CreatePaginacion($limit, $total)
    {
        $limi = explode(",", $limit);
        $pag = ceil($total / $limi[1]);
        if ($pag > 1) {
            $this->MaxPage = $pag;
            $newInicio = $limi[1] * ($this->Pagina - 1);
            $this->SqlLimit = $newInicio . "," . $limi[1];
        }
    }
    private function AddTitleFilaGrilla($jsonOrder)
    {
        $arrayCol = [];
        $arrayOrder = [];
        if ($this->FieldOrder != "") {
            $arrayCampos = explode(",", $this->FieldOrder);
            foreach ($arrayCampos as $field) {
                $field = $this->NameFielQuery($field);
                $arrayOrder[$field] = true;
            }
        }
        foreach ($this->ArrayCol as $indice => $field) {
            $order = isset($arrayOrder[$indice]) ? "true" : "false";
            $state = "false";
            if (isset($jsonOrder["field"])) {
                if ($indice == $jsonOrder["field"]) {
                    $state = $jsonOrder["state"];
                }
            }
            $arrayCol[] = [
                "field" => $indice,
                "width" => $field["width"],
                "title" => $field["title"],
                "order" => $order,
                "state" => $state,
            ];
        }
        return $arrayCol;
    }
    private function AddBodyFilaGrilla($row)
    {
        $arrayCol = [];
        foreach ($this->ArrayCol as $indice => $field) {
            $style = "";
            if ($field["type"] == "col") {
                $value = utf8_encode($row[$indice]);
                if (isset($field["function"])) {
                    switch ($field["function"]["name"]) {
                        case "fecha":
                            $value = $this->Fecha($value);
                            break;
                        case "codigo":
                            $value = $this->Codigo(
                                $value,
                                $field["function"]["option1"]
                            );
                            break;
                        case "moneda":
                            $value = $this->Moneda(
                                $value,
                                $field["function"]["option1"],
                                $field["function"]["option2"]
                            );
                            break;
                    }
                }
                if (isset($field["style"])) {
                    $style = $field["style"];
                }
            } else {
                $arrayHtml = $field["arrayVar"];
                for ($i = 0, $j = count($arrayHtml); $i < $j; $i++) {
                    $campoSql = $this->NameFielQuery($arrayHtml[$i]);
                    $field["html"] = str_replace(
                        "[" . $arrayHtml[$i] . "]",
                        utf8_encode($row[$campoSql]),
                        $field["html"]
                    );
                }
                $value = $field["html"];
            }
            $arrayCol[$indice] = [
                "html" => $value,
                "id" => $row["id"],
                "width" => $field["width"],
                "style" => $style,
                "type" => $field["type"],
                "movil" => $field["movil"],
            ];
        }
        $ctxMenu = [];
        foreach ($this->ArrayCtxMenu as $optionMenu) {
            $arrayHtml = $optionMenu["arrayVar"];
            for ($i = 0, $j = count($arrayHtml); $i < $j; $i++) {
                $campoSql = $this->NameFielQuery($arrayHtml[$i]);
                $optionMenu["handler"] = str_replace(
                    "[" . $arrayHtml[$i] . "]",
                    utf8_encode($row[$campoSql]),
                    $optionMenu["handler"]
                );
            }
            $ctxMenu[] = $optionMenu;
        }
        return ["cols" => $arrayCol, "ctxmenu" => $ctxMenu];
    }
    private function SelectRowSql()
    {
        $arrayField = array_unique($this->ArrayFieldDb);
        $campos = implode(",", $arrayField);
        $sqlData =
            "SELECT T1.id," .
            $campos .
            " FROM " .
            $this->Table .
            " AS T1 " .
            $this->SqlJoin .
            " WHERE T1.id=" .
            $this->IndexClass .
            " LIMIT 0,1";
        $queryData = $this->ApiSql->query($sqlData);
        $dataSql = $this->ApiSql->fetch_assoc($queryData);
        if (!$queryData) {
            echo $sqlData;
        }
        $sqlData = $this->AddBodyFilaGrilla($dataSql);
        $arrayFila[] = [
            "idRow" => $this->IndexClass,
            "cols" => $sqlData["cols"],
            "ctxmenu" => $sqlData["ctxmenu"],
        ];
        $arrayResponse = [
            "estado" => "true",
            "type" => "grilla",
            "idRow" => $this->IndexClass,
            "eventUpdate" => $this->EventUpdate,
            "fNameWindow" => $this->FNameWindow,
            "width" => $this->Vwidth,
            "minWidth" => $this->VminWidth,
            "maxWidth" => $this->VmaxWidth,
            "height" => $this->Vheight,
            "minHeight" => $this->VminHeight,
            "maxHeight" => $this->VmaxHeight,
            "scrollX" => $this->VscrollX,
            "scrollY" => $this->VscrollY,
            "fTitle" => utf8_encode($this->FTitle),
            "rows" => $arrayFila,
            "fCloseWindow" => $this->FCloseWindow,
            "columNumber" => $this->columNumber,
        ];
        echo json_encode($arrayResponse);
    }
    private function NameFielQuery($field)
    {
        $field = str_replace(" ", "", $field);
        if (strpos($field, "AS")) {
            list($text, $field) = explode("AS", $field);
        } elseif (strpos($field, ".")) {
            list($text, $field) = explode(".", $field);
        }
        return $field;
    }
} ?>
