<?php
	include('../../../../configuracion/conectar.php');
?>
<style>
 	#filtro_reporte_terceros input{ margin-right: 10px; }
	#filtro_reporte_terceros td { font-size: 12px; }
</style>
<div style="width:100%;">
    <div style="overflow:visible; border-bottom:1px solid #99BBE8; float:left; width:100%; height:24px;">
		<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 0; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_terceros('filter_campos_terceros');" id="tab_filter_campos_terceros">CAMPOS</div>
		<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_terceros('filter_funcionarios_terceros');" id="tab_filter_funcionarios_terceros">FUNCIONARIOS</div>		
	</div>
	<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_campos_terceros">
		<div style="width:100%;padding-bottom: 10px;padding-top:14px;height:30px">
			<div style="width:50%;padding-bottom: 5px;float:left">
				<label style="font-size:12px;padding: 5% 5% 5% 8%;">TIPO</label>
				<select name="tipo_tercero_reporte" id="tipo_tercero_reporte" class="myfield" style="width: 130px; height: 30;" onclick="local_storage_check(this)" onchange="ocultaSelectClase()">				
					<option value="1">Terceros</option>
					<option value="2">Prospectos</option>
				</select>
			</div>
			<div id="divSelectClase" style="width:50%;float:left">
				<label style="font-size:12px;padding: 5% 5% 5% 8%;">CLASE</label>
				<select name="clase_tercero_reporte" id="clase_tercero_reporte" class="myfield" style="width: 130px; height: 30;" onclick="local_storage_check(this)">
					<option value="1">Todos</option>
					<option value="2">Clientes</option>
					<option value="3">Proveedores</option>
				</select>
			</div>
		</div>	
		<div align="left" style="width:100%;padding-left:20px;">
			<form id="filtro_reporte_terceros">
				<table style="padding-left:6%;width:100%">
					<tr>
						<td><input name="tercero_tributario" type="checkbox" id="tercero_tributario_reporte_t" onclick="local_storage_check(this)">Regimen Tributario</td>
						<td><input name="nombre_comercial" type="checkbox" id="nombre_comercial_reporte_t" onclick="local_storage_check(this)">Nombre Comercial</td>
					</tr>
					<tr>
						<td><input name="pais" type="checkbox" id="pais_reporte_t" onclick="local_storage_check(this)">Pais</td>
						<td><input name="cuidad" type="checkbox" id="cuidad_reporte_t" onclick="local_storage_check(this)">Ciudad</td>
					</tr>
					<tr>
						<td><input name="departamento" type="checkbox" id="departamento_reporte_t" onclick="local_storage_check(this)">Departamento</td>
						<td><input name="direccion" type="checkbox" id="direccion_reporte_t" onclick="local_storage_check(this)">Direccion</td>
					</tr>
					<tr>
						<td><input name="nombre1" type="checkbox" id="nombre1_reporte_t" onclick="local_storage_check(this)">Nombre 1</td>
						<td><input name="nombre2" type="checkbox" id="nombre2_reporte_t" onclick="local_storage_check(this)">Nombre 2</td>
					</tr>
					<tr>
						<td><input name="apellido1" type="checkbox" id="apellido1_reporte_t" onclick="local_storage_check(this)">Apellido 1</td>
						<td><input name="apellido2" type="checkbox" id="apellido2_reporte_t" onclick="local_storage_check(this)">Apellido 2</td>
					</tr>
					<tr>
						<td><input name="telefono1" type="checkbox" id="telefono1_reporte_t" onclick="local_storage_check(this)">Telefono 1</td>
						<td><input name="telefono2" type="checkbox" id="telefono2_reporte_t" onclick="local_storage_check(this)">Telefono 2</td>
					</tr>
					<tr>
						<td><input name="celular1" type="checkbox" id="celular1_reporte_t" onclick="local_storage_check(this)">Celular 1</td>
						<td><input name="celular2" type="checkbox" id="celular2_reporte_t" onclick="local_storage_check(this)">Celular 2</td>
					</tr>
					<tr>
						<td><input name="funcionario_asignado" type="checkbox" id="funcionario_asignado" onclick="local_storage_check(this)">Funcionario Asignado</td>
						<td><input name="email1" type="checkbox" id="email1" onclick="local_storage_check(this)">Email 1</td>										
					</tr>
					<tr>					
						<td><input name="email2" type="checkbox" id="email2" onclick="local_storage_check(this)">Email 2</td>
						<td>&nbsp;</td>					
					</tr>
					<tr>
				</table>
			</form>
		</div>
	</div>

	<!-- VENTANA FILTRO VENDEDORES -->

	<div style="width:100%; height:432px; background-color: #CDDBF0; overflow:hidden; display:none;" id="filter_funcionarios_terceros">
		<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR VENDEDORES</div>

		<!-- OPCION TODOS LOS VENDEDORES -->		

		<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
			<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
				<div class="headTablaBoletas">
					<div class="campo0">&nbsp;</div>
					<div class="campo1">Identificacion</div>
					<div class="campo2" style="width: 150px;">Funcionarios</div>
					<div class="campo4" style="width:25px;"><img src="../informes/img/buscar20.png" onclick="ventanaBusquedaTercero_terceros('Funcionarios');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Funcionarios" id="imgBuscarTerceroBC"></div>
				</div>
				<div id="bodyTablaConfiguracionFuncionarios" style="height:140px;"></div>
			</div>
			<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableFuncionariosTerceros"></div>
		</div>
	</div>
</div>

<script> 

	//DIV DE ARRAY CON FUNCIONARIOS
	for ( i = 0; i < array_funcionarios_Terceros.length; i++) {
		if (typeof(array_funcionarios_Terceros[i])!="undefined" && array_funcionarios_Terceros[i]!="") {

			//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
    		var div   = document.createElement("div");
    		div.setAttribute("id","fila_funcionarios_Terceros_"+i);
    		div.setAttribute("class","filaBoleta");
    		document.getElementById("bodyTablaConfiguracionFuncionarios").appendChild(div);

    		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
    		document.getElementById("fila_funcionarios_Terceros_"+i).innerHTML=funcionarios_config_Terceros[i];
		}
	}  
	
	// condiciones para obtener el los datos almacenados en localstorage
	
	if(localStorage.tipo_tercero_reporte         ==1) {document.getElementById('tipo_tercero_reporte').selectedIndex=0;}
	else if(localStorage.tipo_tercero_reporte    ==2){document.getElementById('tipo_tercero_reporte').selectedIndex=1;}

	if(localStorage.clase_tercero_reporte         ==2) {document.getElementById('clase_tercero_reporte').selectedIndex=1;}
	else if(localStorage.clase_tercero_reporte    ==3){document.getElementById('clase_tercero_reporte').selectedIndex=2;}
	
	if(localStorage.nombre_comercial_reporte_t   =='true') {document.getElementById('nombre_comercial_reporte_t').checked='true';}
	if(localStorage.direccion_reporte_t          =='true') {document.getElementById('direccion_reporte_t').checked='true';}
	if(localStorage.pais_reporte_t               =='true') {document.getElementById('pais_reporte_t').checked='true';}
	if(localStorage.telefono1_reporte_t          =='true') {document.getElementById('telefono1_reporte_t').checked='true';}
	if(localStorage.celular1_reporte_t           =='true') {document.getElementById('celular1_reporte_t').checked='true';}
	if(localStorage.nombre1_reporte_t            =='true') {document.getElementById('nombre1_reporte_t').checked='true';}
	if(localStorage.apellido1_reporte_t          =='true') {document.getElementById('apellido1_reporte_t').checked='true';}
	if(localStorage.tercero_tributario_reporte_t =='true') {document.getElementById('tercero_tributario_reporte_t').checked='true';}
	if(localStorage.cuidad_reporte_t             =='true') {document.getElementById('cuidad_reporte_t').checked='true';}
	if(localStorage.departamento_reporte_t       =='true') {document.getElementById('departamento_reporte_t').checked='true';}
	if(localStorage.telefono2_reporte_t          =='true') {document.getElementById('telefono2_reporte_t').checked='true';}
	if(localStorage.celular2_reporte_t           =='true') {document.getElementById('celular2_reporte_t').checked='true';}
	if(localStorage.nombre2_reporte_t            =='true') {document.getElementById('nombre2_reporte_t').checked='true';}
	if(localStorage.apellido2_reporte_t          =='true') {document.getElementById('apellido2_reporte_t').checked='true';}
	if(localStorage.funcionario_asignado         =='true') {document.getElementById('funcionario_asignado').checked='true';}
	if(localStorage.email1                       =='true') {document.getElementById('email1').checked='true';}
	if(localStorage.email2                       =='true') {document.getElementById('email2').checked='true';}

	function local_storage_check(val) {

		var element = val.id;
		if (element=='tipo_tercero_reporte') {
			//crea el localstorage, y setea el la clave en la key, metodo localStorage.setItem("lastname", "new name") para que valla poniendo nombres dinamicamente
			 localStorage.setItem(element,document.getElementById(element).value);
		}
		else if(element=='clase_tercero_reporte') {
			//crea el localstorage, y setea el la clave en la key, metodo localStorage.setItem("lastname", "new name") para que valla poniendo nombres dinamicamente
			 localStorage.setItem(element,document.getElementById(element).value);
		}
		else{
			localStorage.setItem(element,document.getElementById(element).checked);
		}
	}

	function ocultaSelectClase(){
		//SI ES PROSPECTOS OCULTAMOS EL SELECT DE CLASE
		valor = document.getElementById('tipo_tercero_reporte').value;	

		if(valor == 2){
			document.getElementById('divSelectClase').style.display = 'none';
			localStorage.clase_tercero_reporte = '';
			document.getElementById('clase_tercero_reporte').value = 1;

		}
		else{
			document.getElementById('divSelectClase').style.display = 'block';
		}		
	}

	function display_filter_terceros(filter){
		document.getElementById("filter_campos_terceros").style.display       = "none";
		document.getElementById("filter_funcionarios_terceros").style.display   = "none";		

		document.getElementById("filter_campos_terceros").style.backgroundColor       = "none";
		document.getElementById("filter_funcionarios_terceros").style.backgroundColor   = "none";		

		document.getElementById("tab_filter_campos_terceros").style.margin       = "2px 1px 0 1px";
		document.getElementById("tab_filter_funcionarios_terceros").style.margin   = "2px 1px 0 1px";		

		document.getElementById(filter).style.display       = "block";
		document.getElementById("tab_"+filter).style.margin = "3px 1px 0 1px";
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero_terceros(opc){
		if (opc=='Funcionarios') {
			tabla   = 'empleados';
			tercero = 'nombre';
			titulo_ventana = 'Empleados';
		}		

        Win_VentanaFuncionario_terceros_terceros = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaFuncionario_terceros_terceros',
            title       : titulo_ventana,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
					tabla             : tabla,
					id_tercero        : 'id',
					tercero           : tercero,
					opcGrillaContable : 'Terceros',
					cargaFuncion      : '',
					nombre_grilla     : '',
				}
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_VentanaFuncionario_terceros_terceros.close(id) }
                }
            ]
        }).show();
	}

	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            if (tabla=='empleados') {
				var div = document.createElement('div');

            	div.setAttribute('id','fila_funcionarios_Terceros_'+cont);
            	div.setAttribute('class','filaBoleta');
            	document.getElementById('bodyTablaConfiguracionFuncionarios').appendChild(div);

            	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
				var nit     = document.getElementById('nit_'+cont).innerHTML;
				var tercero = document.getElementById('tercero_'+cont).innerHTML;

            	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
				var fila = '<div class="campo0">'+contVendedores+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="../informes/img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaFuncionarioTerceros('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	funcionarios_config_Terceros[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_funcionarios_Terceros_'+cont).innerHTML=fila;
            	contVendedores++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	array_funcionarios_Terceros[cont]=checkbox.value;
            }            

		}
		else if (checkbox.checked ==false) {
			if (tabla=='empleados') {
				delete array_funcionarios_Terceros[cont];
				delete funcionarios_config_Terceros[cont];
				(document.getElementById("fila_funcionarios_Terceros_"+cont)).parentNode.removeChild(document.getElementById("fila_funcionarios_Terceros_"+cont));
			}			
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaFuncionarioTerceros(cont,tabla){

		if (tabla=='empleados') {
			delete array_funcionarios_Terceros[cont];
			delete funcionarios_config_Terceros[cont];
			(document.getElementById("fila_funcionarios_Terceros_"+cont)).parentNode.removeChild(document.getElementById("fila_funcionarios_Terceros_"+cont));
		}		
	}
	//CHECKBOX VENDEDORES	

	ocultaSelectClase();

</script>