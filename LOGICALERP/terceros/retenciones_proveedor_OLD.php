<?php  
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$idProveedor   = $id;
	$idEmpresa     = $_SESSION['EMPRESA'];
	$acumImpuestos = '';
	$sqlImpuesto   = "	SELECT id,retencion,valor
						FROM retenciones
						WHERE  activo=1
							AND id IN (SELECT id_retencion FROM terceros_retenciones WHERE activo=1 AND id_proveedor=$idProveedor AND id_empresa='$idEmpresa')
							AND id_empresa='$idEmpresa'								
						ORDER BY retencion ASC";

	$queryImpuesto  = mysql_query($sqlImpuesto,$link);
   //cuenta la cantidad de retenciones asignados a ese equipo
    $cont=0;
    //en este primer while se muestran select con el retencion ya agregado
    while($row = mysql_fetch_array($queryImpuesto)){ 
    	//se incrementa en 1 el valor del contador para saber cuantos retenciones  hay agregados
    	$cont++;	    	
		$acumImpuestos         .='<div id="divRetencion'.$cont.'" style="margin:5px 10px; float:left; width:100%;" >
									<div id="btnMenos'.$cont.'" onclick="eliminarSelectRetencion('.$cont.','.$row['id'].')" style="float:left; width:20px;">
										<img src="../../temas/clasico/images/formularios/delete.png" />
									</div>
									<div id="contenedorActualizaSelect'.$cont.'" style="float:left; width: calc(100% - 20px);">
										<input type="text" readonly="readonly" value="'.$row['retencion'].' - '.$row['valor'].'" style="border:none; height:20px; box-shadow : 2px 2px 2px #666; width: calc(100% - 15px);">
    								</div>
								</div>';

    }

    $cont++;
	$acumImpuestos      .= '<div id="divRetencion'.$cont.'" style="margin:5px 10px; float:left; width:100%;" >
								<div id="btnMas'.$cont.'" style="float:left; width:20px;">
									<img src="../../temas/clasico/images/formularios/add3.png" />
								</div>
								<div id="btnMenos'.$cont.'" style="float:left; width:20px; display:none;">
									<img src="../../temas/clasico/images/formularios/delete.png" />
								</div>
								<div id="contenedorActualizaSelect'.$cont.'" style="float:left; width: calc(100% - 20px);">
									<select id="retencion_'.$cont.'"  onchange="guardar_retenciones_articulo(this.value,this.id)" style="width: calc(100% - 15px); height:10px;" class="myfield">
										<option value="0">Seleccione...</option>>';
	
	$cont++;
	$sqlImpuesto   = "	SELECT id,retencion,valor
						FROM retenciones
						WHERE  activo=1
							AND id	NOT IN (SELECT id_retencion FROM terceros_retenciones WHERE activo=1 AND id_proveedor=$idProveedor AND id_empresa='$idEmpresa')
							AND id_empresa='$idEmpresa'							
						ORDER BY retencion ASC";

	$queryImpuesto  = mysql_query($sqlImpuesto,$link);
    while($row = mysql_fetch_array($queryImpuesto)){ $acumImpuestos.='<option value="'.$row['id'].'">'.$row['retencion'].' - '.$row['valor'].'</option>'; }

    $acumImpuestos      .= 			'</select>
								</div>
							</div>
							<div id="divRetencion'.$cont.'" style="margin:5px 10px; float:left; width:100%; overflow:hidden;" ></div>
							<script>
								contSelectRetenciones     ='.$cont.';
								backContSelectRetenciones ='.$cont.'-1;
							</script>';	


?>
<div style="width:85%; float:left; margin:10px 0 0 10px;">
	<div id="cargar"></div>
	<div id="contenedorSelectRetenciones" style="float:left; width:100%; overflow:hidden;"><?php echo $acumImpuestos; ?></div>
</div>

<script>

	function eliminarSelectRetencion(contId,idRetencion){
		Ext.get('cargar').load({
			url     : 'bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op          : 'eliminarRetencionItem',
				idItem      : '<?php echo $id; ?>',
				idRetencion : idRetencion
			}
		});

		document.getElementById('divRetencion'+contId).innerHTML="";
		var contenidoSelect=document.getElementById('divRetencion'+contId);
		contenidoSelect.parentNode.removeChild(contenidoSelect);
	}

	function guardar_retenciones_articulo(valor,campo){

		Ext.get('divRetencion'+contSelectRetenciones).load({
			url		: 'bd/bd.php',
			scripts	: true,
			nocache	: true,
			params	:
			{
				op          : 'guardar_retenciones_articulo',
				idRetencion : valor,
				idItem      : '<?php echo $id; ?>',
				campo       : campo
			}
		});
	}

	
</script>