<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/newGrilla/class.newGrilla.php");


	/**//////////////////////////////////////////////**/
	/**///		INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new newGrilla($link);			/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarCuentasColgaap';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empresas';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'id_pais>0';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			= 'id';
			$grilla->OrderBy 			= 'id ASC';

	//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Nombre','nombre',150);
			$grilla->AddRow('Pais','pais',120);
			// $grilla->addCheck('prueba','check1',120,{true->true,false->false});
			$grilla->addHtml('HTML','html1',80,'','<div style="color:red">prueba</div>');

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	// /**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

?>