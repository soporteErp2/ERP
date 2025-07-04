<?php
/////////////////////////////////////////////////////////////
if ($this->LaOpcion == 'GuardaBD') {

    $cadena = $this->VarPostSql;
    $cadena = substr($cadena, 0, strrpos($cadena, '{.}'));
    $MyVariables = explode('{.}', $cadena);
    $contenido = array();

    $SQLCampos = "";
    $SQLValores = "";

    for ($i = 0; $i < count($MyVariables); $i++) {
        $contenido[$i] = explode('{:}', $MyVariables[$i]);
        $campo = $contenido[$i][0];
        $valor = $contenido[$i][1];

        // Validaciones personalizadas por campo
        for ($v = 0; $v < $this->CuantosValidations; $v++) {
            if ($this->ValidacionesCampos[$v] == $campo) {
                $tipo = $this->Validaciones[$v];
                switch ($tipo) {
                    case 'trim':
                        $valor = trim($valor);
                        break;
                    
                    case 'blankspaces':
                        $valor = str_replace(' ', '', $valor);
                        break;
                    
                    case 'nombres':
                        $valor = limpiarTexto($valor);
                        break;
                }
            }
        }

        $SQLCampos .= $campo;
        $SQLValores .= "'" . $valor . "'";

        if ($i < (count($MyVariables) - 1)) {
            $SQLCampos .= ',';
            $SQLValores .= ',';
        }
    }

    if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM ".$this->TableName2." LIKE 'UserIdLog' ")) == 1) {
        $SQLCampos .= ",UserIdLog";
        $SQLValores .= ",'" . $_SESSION["IDUSUARIO"] . "'";
    }

    $SQL = "INSERT INTO ".$this->TableName2."(".$SQLCampos.") VALUES(".$SQLValores.")";

    $connectid = mysql_query($SQL, $this->Link);

    if ($connectid) {
        $id = mysql_insert_id($this->Link);
        echo 'true{.}'.$id.'{.}';
        mylog('GRILLA('.$this->GrillaName.') -> '.$SQL, 4, $this->Link);
    } else {
        echo 'false{.}';
        echo mysql_error().'{.}';
        echo $SQL;
    }
}

/////////////////////////////////////////////////////////////
if ($this->LaOpcion == 'ActualizaBD') {

    $cadena = $this->VarPostSql;
    $cadena = substr($cadena, 0, strrpos($cadena, '{.}'));
    $MyVariables = explode('{.}', $cadena);
    $contenido = array();

    $SQL = "UPDATE ".$this->TableName2." SET ";

    for ($i = 0; $i < count($MyVariables); $i++) {
        $contenido[$i] = explode('{:}', $MyVariables[$i]);
        $campo = $contenido[$i][0];
        $valor = $contenido[$i][1];

        // Validaciones personalizadas por campo
        for ($v = 0; $v < $this->CuantosValidations; $v++) {
            if ($this->ValidacionesCampos[$v] == $campo) {
                $tipo = $this->Validaciones[$v];
                switch ($tipo) {
                    case 'trim':
                        $valor = trim($valor);
                        break;
                    
                    case 'blankspaces':
                        $valor = str_replace(' ', '', $valor);
                        break;
                    
                    case 'nombres':
                        $valor = limpiarTexto($valor);
                        break;
                }
            }
        }

        // Sanitiza comillas
        $valor = str_replace("'", "`", $valor);
        $valor = str_replace('"', "``", $valor);

        $SQL .= $campo . " = '" . $valor . "'";

        if ($i < (count($MyVariables) - 1)) {
            $SQL .= ", ";
        }
    }

    if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM ".$this->TableName2." LIKE 'UserIdLog' ")) == 1) {
        $SQL .= ", UserIdLog = '".$_SESSION["IDUSUARIO"]."'";
    }

    $SQL .= " WHERE id=".$this->VariableInUpDe;
    $connectid = mysql_query($SQL, $this->Link);

    if ($connectid) {
        echo 'true{.}'.$this->VariableInUpDe.'{.}'.$this->LastUpdate;
        mylog('GRILLA('.$this->GrillaName.') -> '.$SQL, 4, $this->Link);
    } else {
        echo 'false{.}';
        echo mysql_error().'{.}';
        echo $SQL;
    }
}

/////////////////////////////////////////////////////////////
if ($this->LaOpcion == 'EliminaBD') {

    if ($this->VSqlBtnEliminar != 'false') {
        $queryValidateBtn = mysql_query($this->VSqlBtnEliminar, $this->Link);
        while ($row = mysql_fetch_array($queryValidateBtn)) {
            echo '{.}trueSQL{.}';
            return;
        }
    }

    if ($this->VComporEliminar == 'true') {
        $SQL = "UPDATE ".$this->TableName2." SET activo = 0 WHERE id='".$this->VariableInUpDe."'";
    } else {
        $SQL = "DELETE FROM ".$this->TableName2." WHERE id='".$this->VariableInUpDe."'";
    }

    $connectid = mysql_query($SQL, $this->Link);

    if ($connectid) {
        echo '{.}true{.}'.$this->VariableInUpDe.'{.}';
        mylog('GRILLA('.$this->GrillaName.') -> '.$SQL, 4, $this->Link);
    } else {
        echo '{.}false{.}';
        echo mysql_error().'{.}';
        echo $SQL;
    }
}

/**
 * Limpia un texto eliminando acentos, ñ, signos, números, saltos de línea
 * y lo convierte todo a mayúsculas planas sin símbolos ni tildes.
 *
 * @param string $valor Texto a limpiar
 * @return string Texto limpio y en mayúsculas
 */
function limpiarTexto($valor) {
    // Asegurar que el texto esté en UTF-8 antes de manipularlo
    $valor = mb_convert_encoding(
        $valor,
        'UTF-8',
        mb_detect_encoding($valor, 'UTF-8, ISO-8859-1, ISO-8859-15', true)
    );

    // Reemplazar vocales acentuadas y ñ por su versión sin tilde ni diacríticos
    $buscar  = array(
        'á','é','í','ó','ú','ä','ë','ï','ö','ü','à','è','ì','ò','ù','ñ',
        'Á','É','Í','Ó','Ú','Ä','Ë','Ï','Ö','Ü','À','È','Ì','Ò','Ù','Ñ'
    );
    $reempl = array(
        'a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','n',
        'A','E','I','O','U','A','E','I','O','U','A','E','I','O','U','N'
    );
    $valor = str_replace($buscar, $reempl, $valor);

    // Eliminar signos de puntuación y caracteres especiales
    $valor = str_replace(
        array(
            ',', ';', ':', '!', '?', '¿', '¡', '"', "'", '“', '”',
            '(', ')', '[', ']', '{', '}', '/', '\\', '-', '_', '°', '@', '#', '$',
            '%', '*', '+', '=', '<', '>', 'º'
        ),
        '',
        $valor
    );

    // Reemplazar saltos de línea, retorno de carro y tabulaciones por espacio
    $valor = str_replace(array("\r", "\n", "\t"), ' ', $valor);

    // Convertir todo el texto a mayúsculas 
    $valor = strtoupper($valor);

    // Unificar múltiples espacios consecutivos en uno solo
    $valor = preg_replace('/\s+/', ' ', $valor);

    // Eliminar espacios al principio y al final
    $valor = trim($valor);

    return $valor;
}

?>
