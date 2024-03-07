<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");
			
	$GrillaName			 = 'importarContratos';
	$grilla      		 = new MyGrilla();
	$grilla->MySql		 = "SELECT * FROM empleados_contratos";//CONSULTA DE LA GRILLA
	$grilla->MySqlLimit  = '0,50'; //POR DEFECTO EL LIMITE DE LA CONSULTA SIEMPRES ON 100 REGISTROS
	$grilla->MySqlInUpDe = 'SELECT * FROM empleados_contratos WHERE id='.$elid; //MYSQL DE LA CONSULTA DE INSERT - UPDATE Y DELETE	
	$grilla->GrillaName	 = $GrillaName;// NOMBRE DE LA GRILLA
	$grilla->Ancho		 = 550;
	$grilla->Alto		 = 350;
	$grilla->AutoResize	 = 'true';
	$grilla->QuitarAncho = 240;// PIXELES A RESTAR A LO ANCHO
	$grilla->QuitarAlto	 = 100;// PIXELES A RESTAR A LO ALTO
	$grilla->AddRow('id','id',30,'codigo');	
	$grilla->VentanaAuto	= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL  

	//$grilla->AddRowImage('Importar'   ,'<center><img src="../temas/clasico/images/BotonesTabs/doc16.png" style="cursor:pointer" width="16" height="16" onclick="importar_contrato([id]);"></center>',85);
	$grilla->AddRow('Nombre','nombre',300);
	$grilla->AddRow('Empresa','empresa',200);
	
	//$grilla->AddRowImage('Formato','<center><img src="../../temas/clasico/images/BotonesTabs/doc16.png" style="cursor:pointer" width="16" height="16" onclick="editor_contrato([id]);"></center>',55);
	//$grilla->AddTooltipGeneral = "<b>Esta es una prueba de Tooltip</b><br /><br /> la categoria es [id] y el usuario es [usuario]";
	
	if(isset($contador)){$grilla->ElContador = $contador;}
	if(isset($VarFiltros)){$grilla->VarFiltros = $VarFiltros;}
	if(isset($pagina)){$grilla->PaginaActual = $pagina;} // ES IMPORTANTE QUE ESTE METODO ESTE ACTIVADO SI HAY PAGINACION (MAS DE 100 REGISTROS O LOS DEFINIDOS EN EL PARAMETRO ENTERIOR)
	if(isset($opcion)){$grilla->LaOpcion = $opcion;}
	$grilla->GeneraGrilla();
	
	// SE DEBE DEFINIR LA FUNCION JAVASCRIPT "Editar_< ?php echo $GrillaName ? >(elid)" ESTA FUNCION ES LA QUE SE EJECUTA CUANDO SE DA DOBLECLICK
	// DESPUES DE AGREGRA UN REGISTRO SE DEBE INVOCAR LA FUNCION Inserta_Div_< ?php echo $GrillaName ? >(elid)
	// DESPUES DE ACTUALIZAR UN REGISTRO SE DEBE INVOCAR LA FUNCION Actualiza_Div_< ?php echo $GrillaName ? >(elid)
	// DESPUES DE BORRAR UN REGISTRO SE DEBE INVOCAR LA FUNCION Elimina_Div_< ?php echo $GrillaName ? >(elid)
?>
<script>

function Editar_importarContratos(id){
	
	Ext.Ajax.request
	(
		{
		url		: 'bd/bd.php',
		method	: 'post',
		timeout : 180000,
		params	:
				{
					op				: 	"importarContrato",
					id				: 	id,
					id_empresa		: 	'<?php echo $id_empresa ?>',
					id_sucursal		: 	'<?php echo $id_sucursal ?>'
				},
		success: function (result, request)
			{
				var resultado  =  result.responseText.split("{.}");
				var respuesta = resultado[0];
				var observacion = resultado[1];
				
				if(respuesta == 'false'){
					alert('Error Enviando la Solicitud!\n\n'+observacion);	
				}else{					
					Inserta_Div_Contrato(observacion);
					win_import_contratos.close();
				}
			}
		}
	);
}
</script>