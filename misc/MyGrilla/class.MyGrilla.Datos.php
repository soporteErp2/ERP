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
                if ($tipo == 'trim') {
                    $valor = trim($valor);
                } elseif ($tipo == 'blankspaces') {
                    $valor = str_replace(' ', '', $valor);
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
                if ($tipo == 'trim') {
                    $valor = trim($valor);
                } elseif ($tipo == 'blankspaces') {
                    $valor = str_replace(' ', '', $valor);
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
?>
