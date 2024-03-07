<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa = $_SESSION["EMPRESA"];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'itemsGeneral';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'items';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa' AND modulo_pos='true' ";	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy 			= 'codigo ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 200;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre_equipo,familia,grupo,subgrupo';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
			$grilla->AddFilter('Disponible','estado_venta','estado_venta');
			$grilla->AddFilter('Familia','familia','familia');
			$grilla->AddFilter('Grupo','grupo','grupo');
			$grilla->AddFilter('Subgrupo','subgrupo','subgrupo');
			$grilla->AddFilter('Tipo de Documento','id_tipo_identificacion','tipo_identificacion');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Cod. Tx',"<center><img src='img/config16.png' onclick='winCodTxItem([id])' title='Configurar Cod. TX de Item' ></center>",'50');
			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Nombre','nombre_equipo',250);
			$grilla->AddRow('Familia','familia',200);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRow('Subgrupo','subgrupo',200);
			// $grilla->AddRowImage('Compra','<center><img src="img/[estado_compra].png"></center>','80');

 		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
	 		$grilla->FContenedorAncho		= 500;
			$grilla->FColumnaGeneralAncho	= 530;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 280;
			$grilla->FColumnaFieldAncho		= 250;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana			= 'Ventana itemsGeneral'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->CerrarDespuesDeEditar  = 'false';
			$grilla->VBarraBotones			= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText			= 'Nuevo Item'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage			= 'addequipo';		//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize			= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 			= 100;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 			= 100;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 100;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto			= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar			= 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar		= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//BOTONES ADICIONALES EN EL TOOLBAR DE LA VENTANA DE INSERT DELETE Y UPDATE

 		// //CONFIGURACION DEL MENU CONTEXTUAL
 		// 	$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 	// 	$grilla->MenuContextEliminar= 'false';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>
	<script>


	</script>
<?php
}


if(!isset($opcion)) { ?>
	<script >

		var winCodTxItem = (id_item) => {
			Win_Ventana_cod_tx_item = new Ext.Window({
			    width       : 300,
			    height      : 200,
			    id          : 'Win_Ventana_cod_tx_item',
			    title       : 'Cod. Tx. Item',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'configuracion_secciones_pos/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc        : 'codTxItem',
						id_item    : id_item,
						id_seccion : "<?= $id_seccion ?>"
			        }
			    },
			    tbar :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Guardar',
			                    scale       : 'large',
			                    iconCls     : 'guardar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); saveCodTx(id_item); }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_cod_tx_item.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		var saveCodTx = (id_item) => {
			MyLoading2('on');
			Ext.Ajax.request({
		        url     : 'configuracion_secciones_pos/bd/bd.php',
			    params  :
			    {
					opc        : 'saveCodTx',
					id_item    : id_item,
					id_seccion : "<?= $id_seccion ?>",
					codTx      : document.getElementById('codTx').value
			    },
			    success :function (response, request){
			    			let result = JSON.parse(response.responseText);
			    			console.log(result);
			                if(result.response == 'success'){ MyLoading2('off') }
			                else{MyLoading2('off',{icono:'fail',texto:result.msg}) }
			            },
			    failure : function(){ console.log("fail"); MyLoading2('off') }
			});
		}

	</script>
<?php
} ?>
