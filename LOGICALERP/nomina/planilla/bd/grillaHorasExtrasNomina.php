<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	$rowStructure["hora_extra"]=[
									[
										"name"=>"fecha_inicio",
										"label"=>"Fecha inicio",
										"comment"=>"",
										"type"=>"date",
										"value" => "",
									],
									[
										"name"=>"hora_inicio",
										"label"=>"Hora Inicio",
										"comment"=>"",
										"type"=>"time",
										"value" => "",
									],
									[
										"name"=>"fecha_fin",
										"label"=>"Fecha fin",
										"comment"=>"",
										"type"=>"date",
										"value" => "",
									],
									[
										"name"=>"hora_fin",
										"label"=>"Hora fin",
										"comment"=>"",
										"type"=>"time",
										"value" => "",
									],
									[
										"name"=>"valor",
										"label"=>"Valor",
										"comment"=>"",
										"type"=>"int",
										"value" => "",
									],
									[
										"name"=>"porcentaje",
										"label"=>"Porcentaje",
										"comment"=>"Porcentaje al cual corresponde el calculo de 1 hora extra deacuerdo al tipo (extra,diurna,nocturna,etc)",
										"type"=>"double",
										"value" => "",
									]
								];

	$rowStructure["cesantias"]=[
									[
										"name"=>"porcentaje",
										"label"=>"Porcentaje",
										"comment"=>"Porcentaje que corresponde al Interes de Cesantia de Ley",
										"type"=>"double",
										"value" => "",
									],
									[
										"name"=>"pago_intereses",
										"label"=>"Pago intereses",
										"comment"=>"Pago de los Intereses de Cesantia otorgada por Ley Valor Pagado por Intereses de Cesantias",
										"type"=>"double",
										"value" => "",
									]
								];

	$rowStructure["incapacidad"]=[
									[
										"name"=>"fecha_inicio",
										"label"=>"Fecha inicio",
										"comment"=>"",
										"type"=>"date",
										"value" => "",
									],
									[
										"name"=>"fecha_fin",
										"label"=>"Fecha fin",
										"comment"=>"",
										"type"=>"date",
										"value" => "",
									],
									[
										"name"=>"tipo",
										"label"=>"Tipo",
										"comment"=>"",
										"type"=>"select",
										"value" => ["1"=>"Comun","2"=>"profesional","3"=>"Laboral"],
									],
								];
	$rowStructure["licencia"]=[
									[
										"name"=>"fecha_inicio",
										"label"=>"Fecha inicio",
										"comment"=>"",
										"type"=>"date",
										"value" => "",
									],
									[
										"name"=>"fecha_fin",
										"label"=>"Fecha fin",
										"comment"=>"",
										"type"=>"date",
										"value" => "",
									]
								];

	$rowStructure["fondo_solidaridad_pensional"]=[
									[
										"name"=>"porcentaje",
										"label"=>"Porcentaje",
										"comment"=>"Debe corresponder al porcentaje de deducción de fondo de seguridad pensional que paga el trabajador",
										"type"=>"double",
										"value" => "",
									],
									[
										"name"=>"deduccion",
										"label"=>"Deduccion",
										"comment"=>"Todo trabajador que devengue un sueldo que sea igual o superior a 4 salarios mininos, debe aportar un 1% al Fondo de solidaridad pensional",
										"type"=>"double",
										"value" => "",
									],
									[
										"name"=>"porcentaje_fondo_subsistencia",
										"label"=>"Porcentaje fondo Sub.",
										"comment"=>"Se debe colocar el Porcentaje que correspondiente al Fondo de Subsistencia correspondiente",
										"type"=>"double",
										"value" => "",
									],
									[
										"name"=>"deduccion_fondo_subsistencia",
										"label"=>"Deduccion fondo Sub.",
										"comment"=>"Valor Pagado correspondiente a Fondo de Subsistencia por parte del trabajador",
										"type"=>"double",
										"value" => "",
									]
												];

	
	
	// $rowStructure["hora_extra"]=[];
	$arrayTablesData ['nomina_electronica_estructura_conceptos'] = 'INSERT INTO nomina_electronica_estructura_conceptos (nombre,estructura,id_empresa) 
																	VALUES
																	("hora_extra",\''.json_encode($rowStructure["hora_extra"]).'\',replace_SESSION_EMPRESA),
																	("cesantias",\''.json_encode($rowStructure["cesantias"]).'\',replace_SESSION_EMPRESA),
																	("incapacidad",\''.json_encode($rowStructure["incapacidad"]).'\',replace_SESSION_EMPRESA),
																	("licencia",\''.json_encode($rowStructure["licencia"]).'\',replace_SESSION_EMPRESA),
																	("fondo_solidaridad_pensional",\''.json_encode($rowStructure["fondo_solidaridad_pensional"]).'\',replace_SESSION_EMPRESA)
																	';
								

	exit($arrayTablesData ['nomina_electronica_estructura_conceptos']);

	$id_planilla = $_POST["id_planilla"];
	$id_empleado = $_POST["id_empleado"];
	$id_concepto = $_POST["id_concepto"];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'grillaHorasExtras';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_planillas_empleados_conceptos_detalle_horas_extras';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "id_empleado = '$id_empleado' AND id_planilla = '$id_planilla' AND id_concepto = '$id_concepto' AND activo = 1";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= '';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 510;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'cantidad';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Fecha Inicio','fecha_inicio',100);
			$grilla->AddRow('Hora Inicio','hora_inicio',100);
			$grilla->AddRow('Fecha Fin','fecha_fin',100);
			$grilla->AddRow('Hora Fin','hora_fin',100);
			$grilla->AddRow('Cantidad','cantidad',70);
			$grilla->AddRow('Porcentaje (%)','porcentaje',100);
			$grilla->AddRow('Pago','pago',100);
			//$grilla->AddRowImage('','<img src="img/config16.png" style="cursor:pointer" width="16" height="16" title="Conceptos de este Grupo" onclick="ventana_conceptos(\'[id]\',\'[descripcion]\')">',16);


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 245;
			$grilla->FContenedorAlto		= 300;
			$grilla->FColumnaGeneralAncho	= 230;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Registro Hora Extra'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_configurar_horas_extras_nomina.close();');
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 280;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 300;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			//$grilla->VSqlBtnEliminar	= "SELECT id FROM $grilla->TableName WHERE id_empleado = $id_empleado AND id_planilla = $id_empleado AND id_concepto = $id_concepto LIMIT 0,1";	//VALIDA SI EJECUTA BTN ELIMINAR CON UNA CONSULTA SQL
			//$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Fecha Inicio:','fecha_inicio',100,'true','false');
			$grilla->AddTextField('Hora Inicio:','hora_inicio',100,'true','false');
			$grilla->AddTextField('Fecha fin:','fecha_fin',100,'true','false');
			$grilla->AddTextField('Hora Fin:','hora_fin',100,'true','false');
			$grilla->AddTextField('Cantidad:','cantidad',100,'true','false');
			$grilla->AddTextField('Porcentaje (%):','porcentaje',100,'true','false');
			$grilla->AddTextField('Pago:','pago',100,'true','false');

			//VALIDACIONES
			$grilla->AddValidation('cantidad','numero');
			$grilla->AddValidation('porcentaje','numero-real');
			$grilla->AddValidation('pago','numero');
            
			//HIDE FIELDS
			$grilla->AddTextField('','id_planilla',200,'true','true',$id_planilla);
			$grilla->AddTextField('','id_empleado',200,'true','true',$id_empleado);
			$grilla->AddTextField('','id_concepto',200,'true','true',$id_concepto);


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

		function ventana_conceptos(id,descripcion){

			Win_Ventana_definicion_tributaria = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_definicion_tributaria',
			    title       : 'Conceptos de Grupo '+descripcion,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/nomina_conceptos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_grupo : id,
						grupo    : descripcion,
			        }
			    }
			    // ,
			    // tbar        :
			    // [
			    //     {
			    //         xtype   : 'buttongroup',
			    //         columns : 3,
			    //         title   : 'Opciones',
			    //         items   :
			    //         [
			    //             {
			    //                 xtype       : 'button',
			    //                 width       : 60,
			    //                 height      : 56,
			    //                 text        : 'Regresar',
			    //                 scale       : 'large',
			    //                 iconCls     : 'regresar',
			    //                 iconAlign   : 'left',
			    //                 handler     : function(){ Win_Ventana_definicion_tributaria.close(id) }
			    //             }
			    //         ]
			    //     }
			    // ]
			}).show();
		}

	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>
		
		new Ext.form.TimeField({
			applyTo   : 'grillaHorasExtras_hora_inicio',
		    width      : 100,
			format    : "H:i",
			increment : 1
		});

		new Ext.form.TimeField({
			applyTo   : 'grillaHorasExtras_hora_fin',
		    width      : 100,
			format    : "H:i",
			increment : 1
		});
		

		new Ext.form.DateField({
		    format     : 'Y-m-d',
		    width      : 100,
		    allowBlank : false,
		    showToday  : false,
		    applyTo    : 'grillaHorasExtras_fecha_inicio',
		    editable   : false,
		    listeners  : { select: function() {   } }
		});

		new Ext.form.DateField({
		    format     : 'Y-m-d',
		    width      : 100,
		    allowBlank : false,
		    showToday  : false,
		    applyTo    : 'grillaHorasExtras_fecha_fin',
		    editable   : false,
		    listeners  : { select: function() {   } }
		});

	</script>
<?php
}