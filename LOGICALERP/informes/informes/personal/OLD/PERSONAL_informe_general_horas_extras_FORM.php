<?php
include_once('../../../../configuracion/conectar.php');

	////////////////////////////////////////////////////////////////////////////////////////////////////
	$self = explode('/',$_SERVER['SCRIPT_NAME']);
    $archivo = str_replace('_FORM.php','',$self[count($self)-1]); //echo $archivo;
    $path = $self[count($self)-3].'/'.$self[count($self)-2].'/';

	$nombre_informe				=$archivo;//NOMBRE DEL ARCHIVO
	$ruta						=$path;
	$CONF_EXPORTAR_PDF_TITULO	='Informe General de Horas Extras'; //TITULO DEL DOCUMENTO
	$CONF_GENERAR				='true'; //HABILITA EL BOTON DE GENERAR
	$CONF_FILTROS_FECHA 		='true'; //MUESTRA EL GRUPO DE FILTROS DE FECHA (fecha_inicial - fecha_final)
	$CONF_FILTROS_FECHA2 		='false'; //MUESTRA EL GRUPO DE FILTROS DE FECHA (fecha)
	$CONF_FILTROS_FECHA3		='false'; //MUESTRA EL GRUPO DE FILTROS DE FECHA (a�o_desde - a�o_hasta)
	$CONF_FILTROS_FECHA4 		='false'; //MUESTRA EL GRUPO DE FILTROS DE FECHA (a�o)
	$CONF_EXPORTAR 				='true'; //MUETRA EL GRUPO DE EXPORTAR
	$CONF_EXPORTAR_PDF 			='true'; //MUESTRA EXPORTAR A PDF
	$CONF_EXPORTAR_PDF_ORIEN	="P"; //ORIENTACION DEL DOCUMENTO PDF "L landscape" O "P portrait"
	$CONF_EXPORTAR_PDF_ESCAIMAG	= 1; //ESCALA DE LA IMAGEN DEL PDF
	$CONF_EXPORTAR_XLS 			='false'; //MUESTRA EXPORTAR A XLS
	$CONF_FILTROS				='false'; //MUESTRA LA BARRA DE FILTROS
	$CONF_FILTROS_EMPRESA       ='true'; //MUESTRA EL GRUPO DE FILTROS DE EMPRESA Y SUCURSAL

	/*$CONF_FILTROS_COMBO		    =array(	0 => array(	'id' => array('0','1','2'),
												'value' => array(
																	'Liquidar Dia a Dia (Causacion)',
																	'Liquidar por fecha de inicio',
																	'Liquidar por fecha de Finalizacion'
																 ),
												'default' => array('0'),
												'enabled' => array('true'),
												'label' => array('Forma de Liquidar')),


										1 => array(	'id' => array('0','1'),
												'value' => array('Liquidar Ingresos de Terceros','No Liquidar Ingresos de Terceros'),
												'default' => array('0'),
												'enabled' => array('true'),
												'label' => array('Opciones de calculo'))
									);*/

	////////////////////////////////////////////////////////////////////////////////////////////////////

	echo'
	<div style="margin:10px">
		<div id="docbody_'.$nombre_informe.'" style="float:left; width:100%">
		</div>
		<div id="reci_'.$nombre_informe.'" class="sdiv" style="width:100%; height:600px">
		</div>
	</div>
	';
	echo '<script>';
	echo 'Ext.onReady(function(){';
	include("../../barra_heramientas.php");
	include("../../barra_filtros.php");
	echo '});';
	include("../../scripts.php");
	echo '</script>';

?>
