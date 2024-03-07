<?php
	include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];

	$sql="SELECT entidad,concepto FROM entidades_contratos
			WHERE activo=1
			AND id_empresa=$id_empresa
			AND id_contrato=$id_contrato
			AND id_concepto=$id_concepto
			AND id_entidad=$id_entidad
			AND id_empleado=$id_empleado";
	$query=mysql_query($sql,$link);

	// $fecha_inicio=date("Y-m-d", strtotime ("-".$dias_liquidacion."days"));

	// CONSULTAR LA ENTIDAD ACTUAL
	$sql="SELECT numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_entidad";
	$query=mysql_query($sql,$link);
	$documento_entidad = mysql_result($query,0,'numero_identificacion');
	$entidad          = mysql_result($query,0,'nombre');

	// CONSULTAR EL CONCEPTO
	$sql="SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_concepto";
	$query=mysql_query($sql,$link);
	$descripcion_concepto = mysql_result($query,0,'descripcion');

	// CONSULTAR LA FECHA DE INICIO DEL PERIODO DE LA ENTIDAD
	$sql="SELECT MAX(fecha_final) AS fecha FROM empleados_contratos_entidades_traslados
			WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_concepto=$id_concepto";
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

	// $fecha_final = date('Y-m-d',strtotime (" $fecha_inicio +1 day"));

?>

<style>
	.titulos_ventana{
		color       : #15428B;
		font-weight : bold;
		font-size   : 13px;
		font-family : tahoma,arial,verdana,sans-serif;
		text-align  : center;
		margin-top  : 10px;
		float       : left;
		width       : 100%;
	}

	.contenedor_tablas_cuentas{
		float            : left;
		width            : 90%;
		background-color : #FFF;
		margin-top       : 10px;
		margin-left      : 20px;
		border           : 1px solid #D4D4D4;
	}

	.headDivs{
		float            : left;
		background-color : #F3F3F3;
		padding          : 5 0 5 3;
		font-size        : 11px;
		font-weight      : bold;
		border-right     : 1px solid #D4D4D4;
		border-bottom    : 1px solid #D4D4D4;
	}

	.filaDivs{
		float         : left;
		border-right  : 1px solid #D4D4D4;
		padding       :  5 0 5 3;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
	}

	.filaDivs input[type="text"] {
		border     : none;
		width      : 100%;
		height     : 24px;
		text-align : center;
	}

	.divIcono{
		float            : left;
		width            : 20px;
		height           : 16px;
		padding          : 3 0 4 5;
		background-color : #F3F3F3;
		overflow         : hidden;
	}

	.divIcono>img{
		cursor : pointer;
		width  : 16px;
		height : 16px;
	}

</style>


<div style="width:100%;">
	<div class="titulos_ventana">CONCEPTO</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:calc(100% - 184px - 7px);">CONCEPTO</div>
		<div class="headDivs" style="width:90px;">FECHA INICIAL</div>
		<div class="headDivs" style="width:90px;border-right:none;">FECHA FINAL</div>

		<div class="filaDivs" style="width:calc(100% - 184px - 7px);" title="<?php echo $descripcion_concepto; ?>"><?php echo $descripcion_concepto; ?></div>
		<div class="filaDivs" style="width:93px;padding:0px;"><input type="text" readonly id="fecha_inicial" value="<?php echo $fecha_inicio ?>"></div>
		<div class="filaDivs" style="width:93px;border-right:none;padding:0px;"><input type="text" id="fecha_final"></div>

	</div>

	<div class="titulos_ventana">ENTIDAD ACTUAL</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">DOCUMENTO</div>
		<div class="headDivs" style="width:calc(100% - 90px - 7px);border-right:none;">ENTIDAD</div>

		<div class="filaDivs" style="width:90px;" title="<?php echo $documento_entidad ?>">&nbsp;<?php echo $documento_entidad ?></div>
		<div class="filaDivs" style="width:calc(100% - 90px - 7px);border-right:none;" title="<?php echo $entidad ?>"><?php echo $entidad ?></div>

	</div>

	<div class="titulos_ventana">ENTIDAD A TRASLADAR</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">DOCUMENTO</div>
		<div class="headDivs" style="width:calc(100% - 90px - 7px);border-right:none;" >ENTIDAD</div>
		<div class="filaDivs" style="width:90px;" id="documento_tercero_traslado">&nbsp;</div>
		<div class="filaDivs" style="width:calc(100% - 90px - 33px);" id="tercero_traslado">&nbsp;</div>
		<input type="hidden" id="id_tercero_traslado">
		<div class="divIcono"  onclick="ventanaBuscarTercero()">
			<img src="images/buscar20.png" title="Buscar Tercero">
		</div>

	</div>

	<!-- <div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">NATURALEZA</div>
		<div class="headDivs" style="width:100px;">CUENTA</div>
		<div class="headDivs" style="width:calc(100% - 107px - 94px);border-right:none;">DESCRIPCION</div>

		<div class="filaDivs" style="width:90px;">DEBITO</div>
		<div class="filaDivs" id="cuenta_niif_debito" style="width:100px;">&nbsp;<?php echo $cuenta_niif_debito ?></div>
		<div class="filaDivs" id="descripcion_cuenta_niif_debito" style="width:calc(100% - 110px - 94px - 23px);">&nbsp;<?php echo $descripcion_cuenta_niif_debito ?></div>
		<div class="divIcono"  onclick="ventanaBuscarCuenta('niif','debito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>

		<div class="filaDivs" style="width:90px;border-top:1px solid #D4D4D4;">CREDITO</div>
		<div class="filaDivs" id="cuenta_niif_credito" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;<?php echo $cuenta_niif_credito ?></div>
		<div class="filaDivs" id="descripcion_cuenta_niif_credito" style="width:calc(100% - 110px - 94px - 23px);border-top:1px solid #D4D4D4;">&nbsp;<?php echo $descripcion_cuenta_niif_credito ?></div>
		<div class="divIcono"  style="border-top:1px solid #D4D4D4;" onclick="ventanaBuscarCuenta('niif','credito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>
	</div> -->

</div>


<script>

	new Ext.form.DateField({
	    emptyText  : 'Seleccione...',    //PLACEHOLDER
	    fieldLabel : 'Date from today',     //SI TIENE LABEL
	    format     : 'Y-m-d',               //FORMATO
	    width      : 92,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_final',
	    value      : new Date(),
	    editable   : false,                 //EDITABLE
	    listeners  : { select: function() {   } }
	});

	//VENTANA PARA BUSCAR LA ENTIDAD
    function ventanaBuscarTercero(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_ventana_buscar_entidad = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_ventana_buscar_entidad',
            title       : 'Terceros',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFuncion : 'responseVentanaBuscarTercero(id)',
                    nombre_grilla : 'entidades',
                }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    items   :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'left',
                            handler     : function(){ Win_Ventana_ventana_buscar_entidad.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function responseVentanaBuscarTercero(id) {
        var documento = document.getElementById('div_entidades_numero_identificacion_'+id).innerHTML;
        var nombre = document.getElementById('div_entidades_nombre_'+id).innerHTML;

        document.getElementById('id_tercero_traslado').value=id;
        document.getElementById('documento_tercero_traslado').innerHTML=documento;
        document.getElementById('tercero_traslado').innerHTML=nombre;
        Win_Ventana_ventana_buscar_entidad.close();
    }

	function generar_traslado() {
		// VALIDAR QUE SE HAYA SELECCIONADO EL TERCERO
		var id_tercero_traslado = document.getElementById('id_tercero_traslado').value;
		var fecha_inicial       = document.getElementById('fecha_inicial').value;
		var fecha_final         = document.getElementById('fecha_final').value;

		if (id_tercero_traslado==0 || id_tercero_traslado=='') { alert("Debe seleccionar la entidad a trasladar!"); return; }

		MyLoading2('on');

		Ext.Ajax.request({
		    url     : 'contratos/bd/bd.php',
		    params  :
		    {
				opc                 : 'generar_traslado',
				id_contrato         : '<?php echo $id_contrato; ?>',
				id_concepto         : '<?php echo $id_concepto; ?>',
				id_empleado         : '<?php echo $id_empleado; ?>',
				cont                : '<?php echo $cont; ?>',
				id_tercero_traslado : id_tercero_traslado,
				id_tercero_old      : '<?php echo $id_entidad; ?>',
				fecha_inicial       : fecha_inicial,
				fecha_final         : fecha_final,
		    },
		    success :function (result, request){
		    			console.log(result.responseText);
		    			var resul = result.responseText.split("{.}")[0];
		                if(resul == 'true'){
							Win_Ventana_trasladar_concepto.close();
							document.getElementById("entidad_<?php echo $cont; ?>").innerHTML=result.responseText.split("{.}")[2];
							document.getElementById('traslate_<?php echo $cont; ?>').setAttribute('onclick','ventana_trasladar_concepto(<?php echo $id_concepto; ?>,'+result.responseText.split("{.}")[1]+',<?php echo $cont; ?>)');
							// var atribute=document.getElementById('traslate_<?php echo $cont; ?>').getAttribute('onclick');
							// atribute = atribute.replace("Microsoft", "W3Schools")
							// atribute=atribute.split('(')[1].split(')')[0].split(',')[1]
							// atribute=atribute.split(')')[0]
		                }
		                else{ alert('Error\nNo se logro generar el traslado, intentelo de nuevo si el problema continua comuniquese con el administrador del sistema'); }

		                MyLoading2('off');
		            },
		    failure : function(){ console.log("fail"); MyLoading2('off');}
		});

	}

</script>