<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa=$_SESSION['EMPRESA'];

    $sql="SELECT
                EV.fecha_terminacion_anterior,
                EV.fecha_terminacion_nueva,
                EV.fecha_modificacion,
                CONCAT(E.nombre1,' ',E.apellido1) AS usuario
            FROM empleados_contratos_modificacion_vencimientos AS EV
            INNER JOIN empleados AS E ON (E.id = EV.id_usuario)
            WHERE EV.activo=1
            AND EV.id_empresa=$id_empresa            
            AND EV.id_contrato=$id_contrato
            ";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $bodyTable.='<tr>
                        <td class="filaDivs" style="width:80px;">'.$row['fecha_terminacion_anterior'].'</td>
                        <td class="filaDivs" style="width:80px;">'.$row['fecha_terminacion_nueva'].'</td>
                        <td class="filaDivs" style="width:70px;">'.$row['fecha_modificacion'].'</td>
                        <td class="filaDivs" style="width:120px;">'.$row['usuario'].'</td>
                    </tr>';
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

    .contenedor_tablas_cuentas{
        float            : left;
        width            : 90%;
        height           : 85%;
        background-color : #FFF;
        margin-top       : 10px;
        margin-left      : 20px;
    }

    .headDivs{
        float            : left;
        background-color : #F3F3F3;
        padding          : 6px;
        font-size        : 11px;
        font-weight      : bold;
    }

    .content-tabla{
        height   : 75%;
        width    : 100%;
        overflow : auto;
    }

    .table{
        border-collapse : collapse;
        border: 1px solid #D4D4D4;
        /*width           : 350px;*/
        /*table-layout    : fixed;*/

    }

    .table td{
        border-top: 1px solid #D4D4D4;
    }


    .table tbody{
        background-color : #FFF;
        /*height           : 100px;*/
    }

    .filaDivs{
        font-size     : 11px;
        float         : left;
        padding       :  5px;
        overflow      : hidden;
        white-space   : nowrap;
        text-overflow : ellipsis;
    }

</style>

<div style="width:100%;border-top:1px solid #8DB2E3;">
    <div class="titulos_ventana">ACTUALIZACION DE LOS VENCIMIENTOS DEL CONTRATO</div>

    <div class="content-tabla">
        <table align="center" cellspacing="0" class="table" >

            <thead>
                <tr>
                    <td class="headDivs" style="width:80px;border-top:none;">Vencimiento Anterior</td>
                    <td class="headDivs" style="width:80px;border-top:none;">Vencimiento Nuevo</td>
                    <td class="headDivs" style="width:70px;border-top:none;">Fecha</td>
                    <td class="headDivs" style="width:120px;border-top:none;">Usuario</td>
                </tr>
            </thead>

            <tbody>
                <?php echo $bodyTable; ?>
            </tbody>

        </table>
    </div>

</div>
