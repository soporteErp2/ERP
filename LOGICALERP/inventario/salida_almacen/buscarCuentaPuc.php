<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
	include("../config_var_global.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($motivoSalida=='Bajas de Mercancia') { $sql=' AND cuenta > 6135 AND cuenta like "6135%" '; }
        else if ($motivoSalida=='Obsequio') { $sql=' AND cuenta >= 529520 AND cuenta like "529520%"'; }
        else if ($motivoSalida=='Gasto Interno') { $sql=' AND cuenta > 5195 AND cuenta like "5195%"'; }

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'grillaCuentaPuc';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'puc';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_empresa = '.$_SESSION['EMPRESA'].$sql;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'cuenta ASC';
			$grilla->MySqlLimit			= '0,200';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 360;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 140;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 190;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'cuenta,descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta','cuenta',100);
			$grilla->AddRow('Descripcion','descripcion',350);

			$grilla->EditLike('cuenta','RIGHT');
			$grilla->AddColStyle('cuenta','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 60;
			$grilla->FColumnaFieldAncho		= 200;




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
		function Editar_grillaCuentaPuc(id){
			cuentaDebito=document.getElementById('div_grillaCuentaPuc_cuenta_'+id).innerHTML;
			Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>.close();
		}



    </script>

<?php } ?>