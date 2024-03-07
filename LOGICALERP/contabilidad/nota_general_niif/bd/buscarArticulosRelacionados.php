<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa = $_SESSION['EMPRESA'];

	$tipo  = mysql_result(mysql_query("SELECT tipo FROM inventario_movimiento_notas WHERE activo = 1 AND consecutivo_nota=$consecutivo  AND id_empresa=$id_empresa LIMIT 0,1",$link),0,'tipo');
	$tipo2 = ($tipo!='')? $tipo.' de Articulos' : '';
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$filtro_sucursal = $_SESSION['SUCURSAL'];
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'inventario_movimiento_notas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND consecutivo_nota = $consecutivo  AND id_empresa=$id_empresa";						//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre,sucursal,bodega'; //CAMPOS DE BUSQUEDA DE LA GRILLA

		//CONFIGURACION DE CAMPOS EN LA GRILLA

			$grilla->AddRow('Codigo','codigo_item',70);
			$grilla->AddRow('Articulo','nombre',150);
			$grilla->AddRow('Cantidad','cantidad',70);
			$grilla->AddRow('Costo','costo',70);
			$grilla->AddRow('Sucursal','sucursal',120);
			$grilla->AddRow('Bodega','bodega',120);
			$grilla->AddRowImage('','<center><img src="img/delete.png" title="Eliminar Articulo de  la Nota"  style="cursor:pointer" width="16" height="16" onclick="eliminarArticulo([id])" /></center>','25');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**//////////////////////////////////////////////////////////////**/
		/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
		/**/															/**/
		/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
		/**/	$grilla->inicializa($_POST);//variables POST			/**/
		/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
		/**/															/**/
		/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>

	<script>
		document.getElementById('motivoMovimientoArticulos').innerHTML='<?php echo ucfirst($tipo2);  ?>';

		function Editar_<?php echo $opcGrillaContable; ?>(id){ }

		function eliminarArticulo(id){
			mesaje = ('<?php echo $tipo; ?>'=='entrada')? 'saldran' :'regresaran' ;
			if (!confirm("Aviso!\nRealmente desea eliminar este articulo\nLos articulos "+mesaje+" del inventario")) { return; }
			else{
				Ext.Ajax.request({
	            	url     : '<?php echo $carpeta; ?>bd/bd.php',
	            	params  :
	            	{
						opc  : 'eliminarArticuloRelacionado',
						id   : id,
						tipo : '<?php echo $tipo; ?>'
	            	},
	            	success :function (result, request){
	            	            if(result.responseText != 'true'){
	            	                alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
	            	            }
	            	            else{ Elimina_Div_<?php echo $opcGrillaContable; ?>(id); }

	            	        },
	            	failure : function(){ alert('Error de conexion con el servidor!'); }
	        	});
			}
		}

	</script>

<?php
} ?>




