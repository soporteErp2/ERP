<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    $id_empresa = $_SESSION['EMPRESA'];
    $id_usuario = $_SESSION['IDUSUARIO'];

    // CONSULTAR EL ID DEL EMPLEADO
    $sql="SELECT id,id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND documento='$documento_empleado'";
    $query=mysql_query($sql,$link);
    $id_empleado=mysql_result($query,0,'id');
    $id_sucursal_emp=mysql_result($query,0,'id_sucursal');

    // CONSULTAR EL ID DEL ULTIMO CONTRATO
    $sql="SELECT id FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado ORDER BY id ASC LIMIT 0,1";
    $query=mysql_query($sql,$link);
    $id_contrato=mysql_result($query,0,'id');

    $sql="SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_usuario";
    $query=mysql_query($sql,$link);
    $nombre_usuario=mysql_result($query,0,'nombre');

    $sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $selected=($id_sucursal_emp==$row['id'])? 'selected' : '' ;
        $select.='<option value="'.$row['id'].'" '.$selected.' >'.$row['nombre'].'</option>';
    }

    $sql="SELECT
            fecha_inicio,
            fecha_final,
            documento_empleado,
            nombre_empleado,
            id_usuario,
            documento_usuario,
            usuario,
            sucursal
        FROM empleados_sucursales_traslados
        WHERE activo=1
        AND id_empresa=$id_empresa
        AND id_empleado=$id_empleado";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $fecha_inicio = $row['fecha_inicio'];
        $fecha_final  = $row['fecha_final'];
        $usuario      = $row['usuario'];
        $sucursal     = $row['sucursal'];

        $bodyTable.='<tr>
                        <td class="filaDivs" >'.$fecha_inicio.'</td>
                        <td class="filaDivs" >'.$fecha_final.'</td>
                        <td class="filaDivs" title="'.$sucursal.'">'.$sucursal.'</td>
                        <td class="filaDivs" title="'.$usuario.'">'.$usuario.'</td>
                    </tr>';

    }

    // CONSULTAR LA FECHA DE INICIO DEL PERIODO DE LA ENTIDAD
    $sql="SELECT MAX(fecha_final) AS fecha FROM empleados_sucursales_traslados
            WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado";
    $query=mysql_query($sql,$link);
    $fecha_max = mysql_result($query,0,'fecha');

    if ($fecha_max <> '' || $fecha_max=='00-00-0000') {
     // $nuevafecha = date('Y-m-d', strtotime("$fechaFFase + 1 day"));
        $fecha_inicio = date('Y-m-d',strtotime (" $fecha_max +1 day"));
    }
    else{
        $sql="SELECT fecha_inicio_contrato FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
        $query=mysql_query($sql,$link);
        $fecha_inicio = mysql_result($query,0,'fecha_inicio_contrato');
    }

    if ($bodyTable=='') {
        $bodyTable='<tr><td colspan="4" style="font-style:italic;color:#999;font-weight:bold;font-size:12px; text-align:center;padding:10px;">No se han realizado traslados</td></tr>';
    }

?>

<style>
    .titulos_ventana{
        color         : #15428B;
        font-weight   : bold;
        font-size     : 13px;
        font-family   : tahoma,arial,verdana,sans-serif;
        text-align    : center;
        margin-top    : 15px;
        margin-bottom : 15px;
        float         : left;
        width         : 100%;
    }

    .table{
        border-collapse : collapse;
        width           : 482px;
        table-layout    : fixed;
        font-family     : Verdana,sans-serif,Tahoma;
        font-size       : 11px;
    }

    .table thead{
        background-color: #F3F3F3;
        font-weight: bold;
    }

    .table td{
        border: 1px solid #D4D4D4;
        padding: 4px;
    }

    .table tbody{
        background-color : #FFF;
        height           : 100px;
    }

    .table img {
        cursor : pointer;
    }


</style>

<div style="width:100%;border-top:1px solid #8DB2E3;">
    <div class="titulos_ventana">TRASLADAR EMPLEADO</div>

    <table class="table" align="center">
        <thead>
            <tr>
                <td colspan="2" style="width: 190px;">PERIODO</td>
                <td>SUCURSAL</td>
                <td>USUARIO</td>
                <td style="width:16px;border-left:none;overflow:hidden;" id="tdLoading"></td>
            </tr>
        </thead>
            <tbody>
                <td data-value="<?php echo $fecha_inicio ?>" id="fecha_inicio"><?php echo $fecha_inicio ?></td>
                <td><input type="text" id="fecha_final" readonly value=""></td>
                <td >
                    <select id="id_nueva_sucursal" style="width:120px;">
                        <?php echo $select; ?>
                    </select>
                </td>
                <td ><?php echo $nombre_usuario; ?></td>
                <td><img src="images/traslado.png" title="Trasladar" onclick="trasladar_empleado_sucursal()"></td>
            </tbody>
    </table>

    <div class="titulos_ventana">TRASLADOS DEL EMPLEADO</div>
    <table align="center" cellspacing="0" class="table" style="width:482;margin-top:10px;">

        <thead>
            <tr>
                <td colspan="2" style="width: 190px;">PERIODO</td>
                <td>SUCURSAL</td>
                <td>USUARIO</td>
            </tr>
        </thead>

        <tbody id="tbody">
            <?php echo $bodyTable; ?>

        </tbody>

    </table>

</div>

<script>

    new Ext.form.DateField({
        emptyText  : 'Insert a date...',    //PLACEHOLDER
        fieldLabel : 'Date from today',     //SI TIENE LABEL
        format     : 'Y-m-d',               //FORMATO
        width      : 90,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_final',
        editable   : false,                 //EDITABLE
        value      : new Date(),             //VALOR POR DEFECTO
        listeners  : { select: function() {   } }
    });

    function trasladar_empleado_sucursal() {

        var fecha_inicio      = document.getElementById('fecha_inicio').dataset.value
        ,   fecha_final       = document.getElementById('fecha_final').value
        ,   id_nueva_sucursal = document.getElementById('id_nueva_sucursal').value

        MyLoading2('on');
        Ext.get('tdLoading').load({
           url     : 'bd/bd.php',
           scripts : true,
           nocache : true,
           params  :
           {
                op                : 'trasladar_empleado_sucursal',
                id_empleado       : '<?php echo $id_empleado; ?>',
                id_nueva_sucursal : id_nueva_sucursal,
                fecha_inicio      : fecha_inicio,
                fecha_final       : fecha_final,
           }
       });

   }

</script>