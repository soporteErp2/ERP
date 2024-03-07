<?php
include('../../../../configuracion/conectar.php');
include('../../../../configuracion/define_variables.php');

$id_empresa = $_SESSION['EMPRESA'];

switch ($opc) {
	case 'ventana_busqueda_terceros_certificar':
		ventana_busqueda_terceros_certificar($informe,$fecha_inicial,$fecha_final,$id_empresa,$mysql);
		break;
}

function ventana_busqueda_terceros_certificar($informe,$fecha_inicial,$fecha_final,$id_empresa,$mysql){
	if ($informe=='ICA') {
		$whereTipo = " tipo_retencion='ReteIca' ";
	}
	else{
		$whereTipo = " (tipo_retencion='ReteFuente' OR tipo_retencion='ReteIva') ";
	}

 	$sql = "SELECT cuenta FROM retenciones WHERE activo=1 AND id_empresa=$id_empresa AND modulo='compra' AND $whereTipo";
    $query = $mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)){
        $whereCuentas .= ($whereCuentas=='')? 'codigo_cuenta='.$row['cuenta'] : ' OR codigo_cuenta='.$row['cuenta'] ;
    }

	$sql="SELECT
                id_tercero,
                nit_tercero,
                tercero
            FROM asientos_colgaap
            WHERE
                activo=1
            AND id_empresa=$id_empresa
            AND ($whereCuentas)
            AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
            AND haber>0
            GROUP BY id_tercero ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $body .='<tr ondblclick="renderTerceroCertificado('.$row['id_tercero'].')">
                    <td id="div_TerceroCertificado_numero_identificacion_'.$row['id_tercero'].'">'.$row['nit_tercero'].'</td>
                    <td id="div_TerceroCertificado_nombre_'.$row['id_tercero'].'">'.$row['tercero'].'</td>
                </tr>';
    }

	echo '
			<style>

                .container{
                    width      : 100%;
                    height     : 95%;
                    text-align : center;
                    overflow-y : auto;
                }

                .table{
                    border-collapse : collapse;
                    width           : 90%;
                    cursor          : hand;
                }

                .table td{
                    font-family : Tahoma, Geneva, sans-serif;
                    padding     : 5px;
                    border      : 1px solid #999;
                }

                .thead td{
                    background-color : #999;
                    color            : #FFF;
                    font-size        : 15px;
                }

                .tbody td{
                    font-size        : 13px;
                    background-color : #FFF;
                }

                .tbody tr:hover >  td{
                    background-color : #D2D0D0;
                    color : #444;
                }

			</style>

			<div class="container">

                <table align="center" class="table">
                    <thead class="thead">
                        <tr>
                            <td>Documento</td>
                            <td>Tercero</td>
                        </tr>
                    <thead>
                    <tbody class="tbody">
                        '.$body.'
                    </tbody>
                </table>

			</div>

			';
}


?>