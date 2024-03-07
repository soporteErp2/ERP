<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");

	if(isset($VarFiltros)){

		$VarFiltros = addslashes($VarFiltros);
		echo $VarFiltros;
		eval($VarFiltros);
	}

	$VarFiltros = "filtro 			: '".$filtro."',
					filtro_empresa 	: '".$_SESSION["EMPRESA"]."',
					filtro_sucursal : '".$filtro_sucursal."'";

	$SQL1 = "SELECT
				*
			FROM
				empleados
			WHERE
				activo = 1
				AND id_empresa = '$_SESSION[EMPRESA]'
				AND id_sucursal = '$filtro_sucursal'
				AND(
					CONCAT(nombre1,' ',nombre2,' ',apellido1,' ',apellido2) LIKE '%$filtro%'
					OR CONCAT(nombre1,' ',nombre2) LIKE '%$filtro%'
					OR CONCAT(nombre1,' ',apellido1) LIKE '%$filtro%'
					OR CONCAT(nombre2,' ',apellido1) LIKE '%$filtro%'
					OR documento LIKE '%$filtro%'
				)";
	$GrillaName			 = 'Empleados';
	$grilla      		 = new MyGrilla();
	$grilla->MySql		 = $SQL1;//CONSULTA DE LA GRILLA
	$grilla->MySqlLimit  = '0,50'; //POR DEFECTO EL LIMITE DE LA CONSULTA SIEMPRES ON 100 REGISTROS
	$grilla->MySqlInUpDe = 'SELECT * FROM empleados WHERE id='.$elid; //MYSQL DE LA CONSULTA DE INSERT - UPDATE Y DELETE
	$grilla->GrillaName	 = $GrillaName;// NOMBRE DE LA GRILLA
	$grilla->Ancho		 = 560;
	$grilla->Alto		 = 310;
	//$grilla->AddRow('Identificacion','documento',100); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
	$grilla->AddRow('Nombre','nombre',250);
	$grilla->AddRow('Cargo','cargo',210);
	//$grilla->AddRow('Rol','rol',150);
	//$grilla->AddRow('Empresa','empresa',150);
	//$grilla->AddRow('Sucursal','sucursal',150);
	//$grilla->AddRow('id','idv',0);
	//$grilla->AddRowImage('','<img src="../temas/clasico/images/BotonesTabs/historial.png" style="cursor:pointer" width="16" height="16" onclick="alert(\'el id es [id]\');">',50);
	//$grilla->AddTooltipGeneral = "<b>Esta es una prueba de Tooltip</b><br /><br /> la categoria es [id] y el usuario es [usuario]";

	if(isset($contador)){$grilla->ElContador = $contador;}
	if(isset($VarFiltros)){$grilla->VarFiltros = $VarFiltros;}
	if(isset($pagina)){$grilla->PaginaActual = $pagina;} // ES IMPORTATNTE QUE EST METODO ESTE ACTIVADO SI HAY PAGINACION (MAS DE 100 REGISTROS O LOS DEFINIDOS EN EL PARAMETRO ENTERIOR)
	if(isset($opcion)){$grilla->LaOpcion = $opcion;}
	$grilla->GeneraGrilla();

	// SE DEBE DEFINIR LA FUNCION JAVASCRIPT "Editar_< ?php echo $GrillaName ? >(elid)" ESTA FUNCION ES LA QUE SE EJECUTA CUANDO SE DA DOBLECLICK
	// DESPUES DE AGREGRA UN REGISTRO SE DEBE INVOCAR LA FUNCION Inserta_Div_< ?php echo $GrillaName ? >(elid)
	// DESPUES DE ACTUALIZAR UN REGISTRO SE DEBE INVOCAR LA FUNCION Actualiza_Div_< ?php echo $GrillaName ? >(elid)
	// DESPUES DE BORRAR UN REGISTRO SE DEBE INVOCAR LA FUNCION Elimina_Div_< ?php echo $GrillaName ? >(elid)

if(!isset($opcion)){
?>
<script>




</script>
<?php } ?>