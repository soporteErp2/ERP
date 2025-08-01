<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];

	// $arrayEmpresa['FV'] = array("cont"=>0);
	// $arrayEmpresa['OC'] = array("cont"=>0);

	// $arraySucursal['FV'] = array("cont"=>0);
	// $arraySucursal['OC'] = array("cont"=>0);

/*
	if(!isset($opcion)){
		$sqlSucursal   = "SELECT COUNT(id) AS contConfig, nombre, tipo, id
							FROM configuracion_documentos_erp
							WHERE id_empresa = '$id_empresa'
								AND id_sucursal = '$filtro_sucursal'
								AND (tipo='FV' OR tipo='OC')
							GROUP BY tipo";
		$querySucursal = mysql_query($sqlSucursal,$link);

		$sqlEmpresa   = "SELECT COUNT(id) AS contConfig, nombre, tipo, id
							FROM configuracion_documentos_erp
							WHERE id_empresa = '$id_empresa'
								AND (tipo='FV' OR tipo='OC')
							GROUP BY tipo
							ORDER BY id DESC
							LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$link);


		while ($rowSucursal = mysql_fetch_assoc($querySucursal)) {
			$tipo = $rowSucursal['nombre'];
			$arraySucursal[$tipo] = array("cont"=>$rowSucursal['nombre'],"cont"=>$rowSucursal['tipo']);
		}

		while ($rowEmpresa = mysql_fetch_assoc($sqlEmpresa)) {
			$arrayEmpresa[$tipo] = array("cont"=>$rowEmpresa['nombre'],"cont"=>$rowEmpresa['tipo']);
		}


		// if($contConfig == 0){
		// 	$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal)
		// 					VALUES ('Factura de venta','FV','$id_empresa','$filtro_sucursal')";
		// 	$queryInsert = mysql_query($sqlInsert,$link);
		// }
	}
*/
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 	= 'Documentos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName		= 'configuracion_documentos_erp';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere		= "id_empresa = '$id_empresa' AND id_sucursal='$filtro_sucursal'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit		= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 	= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 	= 510;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 	= 300;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho	= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,grupo,tipo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Documento','nombre',200);
			$grilla->AddRow('','tipo',50);
			$grilla->AddRowImage('Documento','<center><img src="../../temas/clasico/images/BotonesTabs/doc16.png" style="cursor:pointer" width="16" height="16" onclick="editor_documento(\'[tipo]\',[id]);"></center>',85);

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto	= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	= 'Datos Documento'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones	= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo	= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VAutoResize	= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 	= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 	= 180;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho	= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto	= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll	= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar	= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar= 'false';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/
?>

<?php if(!isset($opcion)){?>
	<script>
		function Editar_Documentos(){}
		function editor_documento(tipo,id){
			var tipo_documento = document.getElementById('div_Documentos_nombre_'+id).innerHTML;

			var myanchoVS = Ext.getBody().getWidth()
			,	myaltoVS  = Ext.getBody().getHeight();

			win_editor = new Ext.Window({
				title		: 'Editor de Documentos',
				id			: 'ventana_edit_documento',
				iconCls		: 'pie2',
				width 		: myanchoVS-25,
				height 		: myaltoVS-35,
				modal		: true,
				autoDestroy : true,
				draggable	: false,
				resizable	: false,
				bodyStyle   : 'background-color:#DFE8F6;',
				autoLoad	:
				{
					url		: 'documentos/documento_Editor.php',
					scripts	: true,
					nocache	: true,
					params  :
					{
						myalto         : myaltoVS,
						myancho        : myanchoVS,
						id_sucursal    : '<?php echo $filtro_sucursal ?>',
						tipo_documento : tipo_documento,
						id_documento   : id
					}
				},
				tbar		:
				[
					{
						xtype		: 'button',
						text		: 'Guardar Formato',
						scale		: 'large',
						iconCls		: 'guardar',
						iconAlign	: 'left',
						handler 	: function(){ guardarBodydocumento(); }
					}
				]
			}).show();
		}

    </script>
<?php } ?>