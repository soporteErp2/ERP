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
			$grilla->GrillaName	 		= 'costo_autorizadores_ordenes_compra_area';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'costo_autorizadores_ordenes_compra_area';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_area=".$id_area;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
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
			$grilla->AddRow('Orden','orden',40);
			$grilla->AddRow('Documento','documento_empleado',100);
			$grilla->AddRow('Nombre','nombre_empleado',200);
			$grilla->AddRow('Email','email',200);
			$grilla->AddRow('Cargo','cargo',200);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 290;
			$grilla->FColumnaGeneralAncho	= 320;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Agregar Empleado que Autoriza'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Empleado'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 290;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_autirzadores.close();');

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Empleado');
			$grilla->AddTextField('','id_empleado',150,'true','true',$id_empresa);
			$grilla->AddTextField('Documento','documento_empleado',150,'true','false');
			$grilla->AddTextField('Empleado','nombre_empleado',150,'true','false');
			$grilla->AddTextField('Email','email',150,'true','false');
			$grilla->AddTextField('','id_cargo',150,'true','true',$id_empresa);
			$grilla->AddTextField('Cargo','cargo',150,'true','false');
			$grilla->AddSeparator('Jerarquia');

			// $grilla->AddComboBox('Orden','orden',150,'true','false','1:1,2:2,3:3,4:4,5:5,6:6,7:7,8:8,9:9,10:10','');
			$grilla->AddComboBox('Orden','orden',150,'false','true','costo_autorizadores_ordenes_compra_area,orden,orden,true','activo=1 AND id_empresa='.$id_empresa.' AND id_area='.$id_area);

			$grilla->AddTextField('','id_area',150,'true','true',$id_area);
			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);


		//VALIDACIONES
			$grilla->AddValidation('orden','unico_global',' id_area='.$id_area);
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

		var inputDocEmpleado   = document.getElementById('costo_autorizadores_ordenes_compra_area_documento_empleado')
		,	inputEmpleado      = document.getElementById('costo_autorizadores_ordenes_compra_area_nombre_empleado')
		,	inputEmpleadoEmail = document.getElementById('costo_autorizadores_ordenes_compra_area_email')
		,	inputCargo         = document.getElementById('costo_autorizadores_ordenes_compra_area_cargo')
		,	orden              = document.getElementById('costo_autorizadores_ordenes_compra_area_orden')
		,	arrayOrden         = orden.options
		,	max_orden          = 0
		,	option_max         = document.createElement("option")

		// RECORRER OBJETO ON ELEMENTOS
		Object.keys(arrayOrden).forEach(function (key) {
			max_orden = (arrayOrden[key].value>max_orden)? arrayOrden[key].value : max_orden ;
		});

		max_orden++;
		option_max.text  = max_orden
		option_max.value = max_orden
		orden.add(option_max,orden[max_orden]);
		// console.log(max_orden);

		inputDocEmpleado.readOnly   = true;
		inputEmpleado.readOnly      = true;
		inputEmpleadoEmail.readOnly = true;
		inputCargo.readOnly         = true;

		inputDocEmpleado.setAttribute("style","float:left; width:135px;");

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarEmpleado()");
		divBtnPlantilla.setAttribute('title','Buscar Empleado');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_costo_autorizadores_ordenes_compra_area_documento_empleado").appendChild(divBtnPlantilla);

		function ventanaBuscarEmpleado(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_buscar_empleado = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_buscar_empleado',
			    title       : '',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/BusquedaVendedor.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						cargaFuncion  : 'renderizaEmpleado(id)',
						nombre_grilla : 'empleados',
			        }
			    },
			    tbar        :
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_buscar_empleado.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function renderizaEmpleado(id){

			var documento   = document.getElementById('div_empleados_documento_'+id).innerHTML
			,	nombre      = document.getElementById('div_empleados_nombre_'+id).innerHTML
			,	email      = document.getElementById('email_'+id).innerHTML
			,	id_cargo    = document.getElementById('id_cargo_'+id).innerHTML
			,	cargo       = document.getElementById('cargo_'+id).innerHTML;

			document.getElementById('costo_autorizadores_ordenes_compra_area_id_empleado').value        = id;
			document.getElementById('costo_autorizadores_ordenes_compra_area_documento_empleado').value = documento;
			document.getElementById('costo_autorizadores_ordenes_compra_area_nombre_empleado').value    = nombre;
			document.getElementById('costo_autorizadores_ordenes_compra_area_email').value    = email;
			document.getElementById('costo_autorizadores_ordenes_compra_area_id_cargo').value           = id_cargo;
			document.getElementById('costo_autorizadores_ordenes_compra_area_cargo').value              = cargo;

			Win_Ventana_buscar_empleado.close(id);

		}

	</script>

<?php
}
?>