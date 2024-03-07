<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	$sql   = "SELECT cabecera_pie_pagina,contenido_pie_pagina FROM ventas_facturas_configuracion_impresion WHERE activo=1 AND id_empresa='$idEmpresa' LIMIT 0,1";
	$query = mysql_query($sql,$link);

	$cabecera_pie_pagina  = mysql_result($query,0,'cabecera_pie_pagina');
	$contenido_pie_pagina = mysql_result($query,0,'contenido_pie_pagina');

?>

<style>
	#contenedor_formato_factura{
		margin      : 10px;
		overflow    : visible;
		width       : calc(100% - 20px);
		/*padding-top : 50px; */
	}

	#bar_btns{
		float        : left;
		width        : 100px;
		height       : 70%;
		margin-right : 10px;
		text-align   : center;
	}

	#bar_btns > div{
		height           : 30px;
		margin-top       : 10px;
		cursor           : move;
		overflow         : hidden;
		border           : 1px solid #8db2e3;
		background-color : #DFE8F6;
		box-shadow       : #eceded;
		border-radius    : 3px;
		width            : calc(100% - 10px - 2px);
	}

	#contenedor_head_formato_factura{
		float    : left;
		width    : calc(100% - 140px);
		height   : 70%;
		overflow : auto;
	}

	#head_formato_factura{
		width      : 700px;
		height     : 400px;
		float      : left;
		overflow   : hidden;
		/*border     : 1px solid #e8e6d3;
		box-shadow : 1px 1px 1px #e8e6d3;*/
		border     : 1px solid #8db2e3;
		box-shadow : 1px 1px 1px #8db2e3;
	}

	#head_formato_factura > div{
		/*border : 1px solid #e8e6d3;*/
		border : 1px solid #8db2e3;
		border-style: dashed;
	}

	.title_btn_config{
		margin-bottom: 3px;
		text-align  : center;
		height      : 15px;
		/*border-top  : 1px solid #8db2e3;*/
	}

	#bar_btns_config{
		font-weight      : bold;
		float            : left;
		width            : 80px;
		height           : 70%;
		display          : none;
		margin-right     : 10px;
		background-color : #DFE8F6;
		border           : 1px solid #8db2e3;
		box-shadow       : #eceded;
		border-radius    : 3px;
	}

	#bar_btns_config > div{
		margin-top    : 5px;
		float         : right;
		height        : 40px;
		width         : 100%;
		border-bottom : 1px solid #8db2e3;
		/*background-color : #DFE8F6;
		border           : 1px solid #8db2e3;
		box-shadow       : #eceded;
		border-radius    : 3px;*/
		/*border-right : 2px solid <?php echo $_SESSION['COLOR_FONDO'] ?>;*/
	}

	#bar_btns > div > div{ overflow: hidden; }
	#bar_btns_config input{ 
		width: 100%; 
		border:none; 
		text-align: center;
	}

	#bar_btns_config img{
		width  : 16px;
		height : 16px;
	}

	.divBtn{ 
		width    : 20%;
		float    : left;
		overflow : hidden;
	}

	.divInput{ 
		width    : 50%;
		float    : left;
		overflow : hidden;
		margin   : 0 3px 0 3px;
	}

</style>
<div id="barraBotonesFromatoFactura"></div>
<div id="contenedor_formato_factura">

	<div id="bar_btns_config">
		<div onclick="mostrarMenuNuevo();" style="height:18px; margin-top:2px;">
			<div class="title_btn_config">Menu nuevo</div>
		</div>
		<div>
			<div class="title_btn_config">Ancho</div>
			<div>
				<div class="divBtn" onclick="resize_div_update('ancho','menos')"><img src="factura_venta_configuracion/img/delete.png" alt="Contraer Ancho"></div>
				<div class="divInput"><input type="text" id="input_ancho" onchange="inputCambiarSizeDiv('ancho',this)"/></div>
				<div class="divBtn" onclick="resize_div_update('ancho','mas')"><img src="factura_venta_configuracion/img/add.png" alt="Expandir Ancho"></div>
			</div>
		</div>
		<div>
			<div class="title_btn_config">Alto</div>
			<div>
				<div class="divBtn" onclick="resize_div_update('alto','menos')"><img src="factura_venta_configuracion/img/delete.png" alt="Contraer Alto"></div>
				<div class="divInput"><input type="text" id="input_alto" onchange="inputCambiarSizeDiv('alto',this)"/></div>
				<div class="divBtn" onclick="resize_div_update('alto','mas')"><img src="factura_venta_configuracion/img/add.png" alt="Expandir Alto"></div>
			</div>
		</div>
		<div>
			<div class="title_btn_config">Bordes</div>
			<div style="margin:auto; border:1px solid #000; width: 16; height:16;"></div>
		</div>
		<div>
			<div class="title_btn_config">Fondo</div>
			<div>botones</div>
		</div>
	</div>

	<div id="bar_btns">
		<div draggable="true" id="etiqueta_texto">Texto</div>
		<div draggable="true" id="etiqueta_imagen">Imagen</div>
		<div draggable="true" id="etiqueta_factura">N. Factura</div>
	</div>
	
	<div id="contenedor_head_formato_factura">
		<div id="head_formato_factura"></div>
	</div>
</div>

<script>	

	
	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotonesFromatoFactura'); 
	tb.add({
		xtype   : 'buttongroup',
		columns : 2,
		items   : 
		[
			{
				text		: 'Guardar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'guardar',
				iconAlign	: 'top',
				handler		: function(){ guardarFormatoFactura(); }
			},
			{
				text		: 'Regresar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'regresar',
				iconAlign	: 'top',
				handler		: function(){ Win_Panel_Global.close(); }
			}
		]
	});
	tb.doLayout();

	function guardarFormatoFactura(){
		cabecera_pie_pagina  = document.getElementById('cabeceraPiePagina');
		contenido_pie_pagina = document.getElementById('contenidoPiePagina');

		if (cabecera_pie_pagina.value=='') { 	alert("Digite la cabecera del pie de pagina!"); cabecera_pie_pagina.focus(); return; }
		else if (contenido_pie_pagina.value=='') { alert("Digite el contenido del pie de pagina!"); contenido_pie_pagina.focus(); return; }


		Ext.get('cargarPiePagina').load({
			url     : 'factura_venta_configuracion/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op                   : 'guardarPiePagina',
				cabecera_pie_pagina  : cabecera_pie_pagina.value,
				contenido_pie_pagina : contenido_pie_pagina.value,
			}
		});
	}

	//============ DRAG AND DROP ============//
	//***************************************//

	var globalEtiquetaUpdate  = ''		//Etiqueta a editar
	,	cont_etiqueta_texto   = 0 		//Contadores
	,	cont_etiqueta_imagen  = 0
	,	cont_etiqueta_factura = 0;

	// FUNCTION DRAG
	function eventoDrag (e) {
      	e.dataTransfer.setData('id_etiqueta_drag', this.id );  	//Se carga el id en el data transfer
	    e.dataTransfer.effectAllowed = 'copy';					//Efecto al arrastrar
	    console.log(this.id+'-');
    }

	document.getElementById('etiqueta_texto').ondragstart   = eventoDrag;
	document.getElementById('etiqueta_imagen').ondragstart  = eventoDrag;
	document.getElementById('etiqueta_factura').ondragstart = eventoDrag;

	//FUNCTION DIV CONTENEDOR
	function divDrop(e) {
      	e.dataTransfer.dropEffect = 'copy';					//Imagen de + en el mouse
  		return false;
    }
    document.getElementById('head_formato_factura').ondragover = divDrop;

    //FUNCTION DROP NUEVO ELEMENTO
    function eventoDrop(e) {
		var bodyDrop       = ''
		,	idEtiquetaDrag = e.dataTransfer.getData('id_etiqueta_drag');

		console.log(idEtiquetaDrag+'*');
		if(idEtiquetaDrag == 'etiqueta_texto'){ 
    		cont_etiqueta_texto++;
    		bodyDrop = '<div draggable="true" contenteditable="true" id="etiqueta_texto_'+cont_etiqueta_texto+'" style="width:400px; height:50px; float:left;" onfocus="editar_etiqueta(this)" onBlur="blur_editar_etiqueta(this)" class="etiqueta_formato_factura">'
    						+'Ingrese el texto aqui!'
    					+'</div>';

    		this.innerHTML = (this.innerHTML) + bodyDrop;
    	}
    	else if(/etiqueta_texto_/g.test(idEtiquetaDrag)){

    		var divMove = document.getElementById(idEtiquetaDrag).innerHTML;
    		
			this.innerHTML  = this.innerHTML.replace(/divMove/g, '');
			this.innerHTML += divMove;
    	}
    }
    document.getElementById('head_formato_factura').ondrop = eventoDrop;

    function editar_etiqueta(divEtiqueta){
    	var ancho = divEtiqueta.style.width
		,	alto  = divEtiqueta.style.height
		,	arrayDivs = document.getElementById('head_formato_factura').querySelectorAll('.etiqueta_formato_factura');

		globalEtiquetaUpdate = divEtiqueta;

		ancho = ancho.replace(/px/g,'')*1;
		alto  = alto.replace(/px/g,'')*1;

		document.getElementById('input_ancho').value = ancho;
		document.getElementById('input_alto').value  = alto;

		
		for(var cont in arrayDivs){
			if(arrayDivs[cont].id){
				arrayDivs[cont].style.backgroundColor = '#FFF';
			}
		}
		divEtiqueta.style.backgroundColor = '#e1ecfd';

		document.getElementById('bar_btns').style.display        = 'none';
		document.getElementById('bar_btns_config').style.display = 'block';

		divEtiqueta.ondragstart = eventoDrag;
    }

    function blur_editar_etiqueta(divEtiqueta){
  //   	globalEtiquetaUpdate = '';

  //   	divEtiqueta.style.backgroundColor = '#FFF';
  //   	document.getElementById('input_ancho').value = '';
		// document.getElementById('input_alto').value  = '';
    }

    function resize_div_update(type,operacion){
    	if(type == 'alto'){ divMedida = document.getElementById('input_alto'); }
    	else{ divMedida = document.getElementById('input_ancho'); }

    	var newSize = divMedida.value;
    	if(isNaN(newSize) || newSize < 0 ){ newSize = 0 }

    	if(newSize < 5){ 					//Si la unidad de medida es inferior a 5
    		divMedida.value = 5;
    		if(type == 'alto'){ globalEtiquetaUpdate.style.height = '5px'; }
    		else{ globalEtiquetaUpdate.style.width = '5px'; }
    		return;
    	}

    	if(operacion == 'mas'){ newSize = (newSize*1)+5; }
    	else{ newSize = (newSize*1)-5; }

		divMedida.value = newSize;
    	if(type == 'alto'){ globalEtiquetaUpdate.style.height = newSize+'px'; }
    	else{ globalEtiquetaUpdate.style.width = newSize+'px'; }
    }

    function mostrarMenuNuevo(){
    	document.getElementById('bar_btns').style.display        = 'block';
		document.getElementById('bar_btns_config').style.display = 'none';

		var arrayDivs = document.getElementById('head_formato_factura').querySelectorAll('.etiqueta_formato_factura');
		for(var cont in arrayDivs){
			if(arrayDivs[cont].id){
				arrayDivs[cont].style.backgroundColor = '#FFF';
			}
		}
    }

    function inputCambiarSizeDiv(type,inputNewSize){
    	var newSize = inputNewSize.value;
    	if(isNaN(newSize) || newSize <=0){
    		newSize = 5;
    		inputNewSize.value = newSize;
    	}

    	if(type == 'alto'){ globalEtiquetaUpdate.style.height = newSize+'px'; }
    	else{ globalEtiquetaUpdate.style.width = newSize+'px'; }


    }
</script>