<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	// CONSULTAR LOS ITEMS YA RELACIONADOS A ESE GRUPO
	$sql="SELECT
					FIG.id_inventario_factura_venta,
					FI.valor_impuesto
				FROM
					ventas_facturas_inventario_grupos AS FIG
				INNER JOIN ventas_facturas_inventario AS FI ON FI.id = FIG.id_inventario_factura_venta
				WHERE
					FIG.activo = 1
				AND FIG.id_factura_venta = $id_documento
				AND FIG.id_grupo_factura_venta = $id_grupo";
	$query=$mysql->query($sql,$mysql->link);
	while ($row = $mysql->fetch_array($query)) {
		$whereId .= " AND id<>$row[id_inventario_factura_venta] AND valor_impuesto=$row[valor_impuesto] ";
	}

	if ($VBarraBotones=='') {
		?>
			<!-- MOSTRAR MENSAJE DE INFORMACION DE LOS ITEMS -->
			<style>
				.infMsj{
					color            : #8a6d3b;
					background-color : #fcf8e3;
					border-color     : #faebcc;
					padding          : 10px;
					margin-left      : 10px;
					margin-right     : 10px;
					border           : 1px solid #d0c2aa;
				}
				.infMsj strong{
					font-weight: 700;
				}
			</style>
			<div class="infMsj">
				<strong>Informacion</strong><br>
				Solo se pueden agrupar items de caracteristicas iguales (Iva, Tipo descuento, Precio unitario)
			</div>

		<?php
	}

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa = $_SESSION['EMPRESA'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'buscarItemsGrupos'.$opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ventas_facturas_inventario';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_factura_venta='$id_documento' AND id_impuesto=$id_impuesto $whereId AND id NOT IN(
												SELECT id_inventario_factura_venta AS id FROM ventas_facturas_inventario_grupos WHERE id_factura_venta = $id_documento)";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'CAST(codigo AS CHAR) ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 440;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',100);
			$grilla->AddRow('Articulo','nombre',250);
			$grilla->AddRow('Cantidad','cantidad',50);
			$grilla->AddRow('Costo','costo_unitario',80);

			$grilla->AddColStyle('codigo','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= ''; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Centro De Costos'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 190;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Datos Centro De Costos');
			$grilla->AddTextField('Codigo:','codigo_centro_costos',200,'true','false');
			$grilla->AddValidation('codigo_centro_costos','numero');
			$grilla->AddTextField('Nombre:','nombre_centro_costos',200,'true','false');
			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);


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

		function Agregar_buscarItemsGrupos<?php echo $opcGrillaContable; ?>(){ }
		function Editar_buscarItemsGrupos<?php echo $opcGrillaContable; ?>(id){

			var codigo = document.getElementById('div_buscarItemsGrupos<?php echo $opcGrillaContable; ?>_codigo_'+id).innerHTML
			,	nombre = document.getElementById('div_buscarItemsGrupos<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML

			MyLoading2('on');

			Ext.get('loadForm').load({
				url     : 'facturacion/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc          : 'agregarItemsGrupo',
					id           : id,
					codigo       : codigo,
					nombre       : nombre,
					id_documento : '<?php echo $id_documento; ?>',
					id_grupo     : '<?php echo $id_grupo; ?>'

				}
			});
		}

	</script>
<?php
}?>