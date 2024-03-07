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
			$grilla->GrillaName	 		= 'costo_autorizadores_ordenes_compra';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'costo_autorizadores_ordenes_compra';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_rango=".$id_rango;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 465;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 320;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'impuesto,valor';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Orden','orden',60);
			$grilla->AddRow('Codigo','codigo_rol',60);
			$grilla->AddRow('Rol','rol',220);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 290;
			$grilla->FColumnaGeneralAncho	= 320;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Agregar Rol que Autorizador'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Rol'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 240;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_autirzadores.close();');

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Rol');
			$grilla->AddTextField('','id_rol',150,'true','true',$id_empresa);
			$grilla->AddTextField('Codigo','codigo_rol',150,'true','false');
			$grilla->AddTextField('Rol','rol',150,'true','false');
			$grilla->AddSeparator('Jerarquia');

			$grilla->AddComboBox('Orden','orden',150,'true','false','1:1,2:2,3:3,4:4,5:5,6:6,7:7,8:8,9:9,10:10','');

			$grilla->AddTextField('','id_rango',150,'true','true',$id_rango);
			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);


		//VALIDACIONES
			$grilla->AddValidation('orden','unico_global',' id_rango='.$id_rango);
			// $grilla->AddValidation('porcentaje','numero-real');

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	 //Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST); //variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	 // Inicializa la Grilla	/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


if(!isset($opcion)){  ?>
	<script>


	</script>
<?php }

if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>

		var inputCodRol = document.getElementById('costo_autorizadores_ordenes_compra_codigo_rol');
		var inputRol    = document.getElementById('costo_autorizadores_ordenes_compra_rol');

		inputCodRol.readOnly = true;
		inputRol.readOnly    = true;

		inputCodRol.setAttribute("style","float:left; width:125px;");
		// inputRol.setAttribute("style","float:left; width:135px;");

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarRol()");
		divBtnPlantilla.setAttribute('title','Buscar Rol');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_costo_autorizadores_ordenes_compra_codigo_rol").appendChild(divBtnPlantilla);

		function ventanaBuscarRol(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_buscar_rol = new Ext.Window({
			    width       : 500,
			    height      : 500,
			    id          : 'Win_Ventana_buscar_rol',
			    title       : 'Seleccionar el Rol',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'autorizaciones_ordenes_compra/buscar_rol.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						cargaFuncion  : 'renderizaRol(id)',
			        }
			    }
			}).show();
		}

		function renderizaRol(id){

			var codigo = document.getElementById('div_empleados_roles_codigo_'+id).innerHTML
			,	nombre = document.getElementById('div_empleados_roles_nombre_'+id).innerHTML;

			document.getElementById('costo_autorizadores_ordenes_compra_id_rol').value     = id;
			document.getElementById('costo_autorizadores_ordenes_compra_codigo_rol').value = codigo;
			document.getElementById('costo_autorizadores_ordenes_compra_rol').value        = nombre;

			Win_Ventana_buscar_rol.close(id);

		}

	</script>

<?php
}
?>