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

	$id_empresa    = $_SESSION['EMPRESA'];
	$grupo_empresa = $_SESSION['GRUPOEMPRESARIAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'rango_autorizaciones_ordenes_compra';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'rango_autorizaciones_ordenes_compra';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa = '$id_empresa'";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'id ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            //$grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 465;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 320;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            //$grilla->QuitarAncho        = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            //$grilla->QuitarAlto         = 220;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            // $grilla->AddRow('Codigo','codigo',80);
            $grilla->AddRow('Rango inicial','rango_inicial',150);
            $grilla->AddRow('Rango final','rango_final',150);
            $grilla->AddRowImage('Autorizadores','<center><img src="../../temas/clasico/images/BotonesTabs/user_check.png" onclick="ventana_autorizadores([id])" style="width:15px;height:15px;cursor:pointer;"></center>',80);

            //$grilla->AddColStyle('campoBd','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 350;
            $grilla->FColumnaGeneralAncho   = 330;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 100;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'false';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            $grilla->AddBotton('Nuevo','documentadd','ventana_insert_update()');
            $grilla->AddBotton('Regresar','regresar','Win_Panel_AOC.close();');

            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 290;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 180;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 200;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DEL MENU CONTEXTUAL
        //     $grilla->MenuContext        = 'true';       //MENU CONTEXTUAL
        //     $grilla->MenuContextEliminar= 'false';

        // //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
        //     $grilla->AddMenuContext('label','calendario16','javascript');

            $grilla->AddTextField('Rango Inicial','rango_inicial',140,'true');
            $grilla->AddTextField('Rango Final','rango_final',140,'true');
            $grilla->AddTextField('empresa','id_empresa',170,'true','true',$id_empresa);

            $grilla->AddValidation('rango_inicial','numero-real');
            $grilla->AddValidation('rango_final','numero-real');

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///		        		INICIALIZACION DE LA GRILLA        		  ///**/
	/**/																														/**/
	/**/		 $grilla->Link = $link;  	 			//Conexion a la BD			/**/
	/**/		 $grilla->inicializa($_POST);		//Variables POST				/**/
	/**/		 $grilla->GeneraGrilla(); 	 		//Inicializa la Grilla	/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/


if(!isset($opcion)){  ?>
	<script>

		function Editar_rango_autorizaciones_ordenes_compra(id){
			ventana_insert_update(id)
		}

		function ventana_insert_update(id) {

			var opc = '';
			if(id == '' || typeof(id) == 'undefined'){
				opc 	= 'insert';
				title = 'Insertar Registro';
			}
			else{
				opc   = 'update';
				title = 'Actualizar Registro';
			}

			Win_Ventana_insert_update = new Ext.Window({
		    width       : 300,
		    height      : 150,
		    id          : 'Win_Ventana_insert_update',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    : {
								        url     : 'autorizaciones_ordenes_compra/bd/bd.php',
								        scripts : true,
								        nocache : true,
								        params  : {
																		opc    : 'ventana_insert_update',
																		id     : id,
																		accion : opc,
													        }
								    	},
		    tbar        : []
			}).show();
		}

		// GUARADAR O ACTUALIZAR EL REGITRO
		function insert_update(accion,id){
			Ext.getCmp('btn_save_rango').disable();
			if (accion=='update') { opc = 'actualizarRango'; }
			else if (accion=='insert') { opc = 'guardarRango'; }

			var rango_inicial = document.getElementById('rango_inicial').value
			,	rango_final   = document.getElementById('rango_final').value;

			if (rango_inicial=='' || rango_final=='') {
				Ext.MessageBox.alert('Aviso', 'Los dos campos son obligatorios');
				Ext.getCmp('btn_save_rango').enable();
				return;
			}

			Ext.get('divLoad').load({
				url     : 'autorizaciones_ordenes_compra/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc           : opc,
					id            : id,
					rango_inicial : rango_inicial,
					rango_final   : rango_final,
				}
			});

		}

		function ventana_autorizadores(id_rango){

			Win_Ventana_autirzadores = new Ext.Window({
			    width       : 500,
			    height      : 450,
			    id          : 'Win_Ventana_autorizadores',
			    title       : 'Personas Autorizadoras',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'autorizaciones_ordenes_compra/autorizaciones_ordenes.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_rango : id_rango,
			        }
			    }
			}).show();
		}

		function delete_registro(id){
			Ext.get('divLoad').load({
				url     : 'autorizaciones_ordenes_compra/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc           : 'delete_registro',
					id            : id,
				}
			});
		}

	</script>
<?php }

if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>

	</script>

<?php
}
?>
