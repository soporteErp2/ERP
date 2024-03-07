<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];

	//CONSULTAR TODOS LOS CONCEPTOS
	$sql="SELECT id_grupo,id,codigo,descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND tipo_concepto='Personal'";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		//CREAR ARRAY CON LOS CONCEPTOS
		$arrayDefinicionTributaria[$row['id_grupo']][$row['id']] = array(	'descripcion' => $row['descripcion'],
																			'codigo'=>$row['codigo']
																		);
	}

	//CONCULTAR LOS GRUPOS DE LOS CONCEPTOS
	$sql="SELECT  id,descripcion FROM nomina_grupos_conceptos WHERE activo=1 AND id_empresa=$id_empresa";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		//ARRAY CON LOS GRUPOS
		$arrayGrupos[$row['id']]=$row['descripcion'];
	}

	//CONSULTAR SI EL CARGO TIENE VALORES YA CONFIGURADOS
	$sql="SELECT id_concepto,id_empleado,valor_concepto FROM nomina_conceptos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		//ARRAY CON LOS DATOS YA GUARDADOS
		$arrayValorConceptos[$row['id_concepto']]= $row['valor_concepto'];
	}

	$body='';
	//RECORRER LOS ARRAY PARA ARMAR EL BODY
	foreach ($arrayGrupos as $id_grupo => $grupo) {
		$body.='<div class="divPadreFieldSetEmpleado">';
		$body.='<fieldset class="fieldSetContenedorEmpleado">';
		$body.='<legend><b>'.$grupo.'</b></legend>';
		foreach ($arrayDefinicionTributaria[$id_grupo] as $id => $resul){

			$valor=(isset($arrayValorConceptos[$id]))? $arrayValorConceptos[$id] : 0 ;

			$body.='<div class="filaCargoConceptosEmpleado">';
			$body.='<div class="labelCargoConceptosEmpleado">'.$resul['descripcion'].'</div>';
			$body.='<div class="campoCargoConceptosEmpleado"><input type="text"  value="'.$valor.'" id="'.$id.'" onkeyup="validaNumero(this)" class="select_input" ></div>';
			//SALTO ENTRE DIV
			$body.='</div>';

			// $acumScript.='arrayInputs['.$id.']={codigo:"'.$resul['codigo'].'",id:"'.$id.'"};';

		}

		$body.='</fieldset>';
		$body.='</div>';

	}

	$disbled_btn = 'false';

	// 	SI TIENE EL CONTRATO CERRADO ENTONCES NO SE MUESTRA LA CONFIGURACION, PERO SI UN AVISO
	if ($estado==1) {
		$body='<center style="font-weight:bold;font-size:14px;margin-top:40px;font-family: Oswald, sans-serif;font-style:italic;color:#999;">
					Este empleado no tiene un contrato activo pero si prestamos pendientes
				</center>';
		$disbled_btn = 'true';
	}

 ?>

<style>

	.divPadreFieldSetEmpleado{
		width     : 365px;
		float     : left;
		font-size : 9px;
	}

	.fieldSetContenedorEmpleado{
		margin  : 10 20 0 10;
		padding : 5px 0 10px 15px;
		border  : none;
		background-color: #FFF;
	}

	.fieldSetContenedorEmpleado>legend{
		height           : 25px;
		background-color : #999;
		color            : #FFF;
		/*font-weight      : bold;*/
		font-family      : sans-serif;
		width            : 100%;
		font-size        : 12px;
		line-height      : 24px;
		padding-left     : 15px;
		margin-left: -28px;
	}

	.filaCargoConceptosEmpleado{
		float  : left;
		margin : 0 0 0 0;
		padding-left: 10px;
		height : 25px;
	}

	.labelCargoConceptosEmpleado{
		width         : 150px;
		float         : left;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
		font-size     : 11px;
		font-family   : sans-serif;
	}
	.campoCargoConceptosEmpleado{
		width  :150px;
		float  :left;
		overflow: hidden;
	}
	.campoCargoConceptosEmpleado>input{
		border:none;
		height: 22px;
		text-align: right;
		background: #EEE;
		width: 100%;
	}

	.campoCargoConceptosEmpleado>input:focus{border:1px solid #999;}

	#divContenedor_barra_empleado{
		/*width       : 100%;*/
		font-size   : 20px;
		font-weight : bold;
		margin-top  : 15px;
		font-weight : bold;
		color       : #727272;
		font-family : Oswald, sans-serif;
	}

</style>

<div style="width:95%;height:100%;margin-left:20px;">
	<div id="tbarConceptosEmpleados" style="width:100%;height:60px;">
	</div>
	<div id="divLoadConceptosEmpleados"></div>


	<div style="width:100%;height:calc(100% - 60px - 40px);">

		<div style="width:100%;height:calc(100% - 40px - 25px);overflow:auto;" id="contenedorInputsConceptosEmpleados">
			<?php echo $body; ?>
		</div>

	</div>

</div>

 <script>
 	var arrayInputs={};

	var renderTolbar =document.getElementById('tbarConceptosEmpleados');
	var widthTolbar  =document.getElementById('tbarConceptosEmpleados').offsetWidth;

 	var tb = new Ext.Toolbar({
		renderTo : renderTolbar,
		width    : widthTolbar,
		height   : 60,
		style    : 'background-color:#CDDBF0;border-bottom:1px solid #999;',
		items    : [
    	    {
    	        xtype       : 'button',
    	        width		: 60,
				height		: 56,
				id 			: 'btn_guardar_config_conceptos_personales',
    	        text        : 'Guardar',
    	        tooltip		: 'Guardar Configuracion',
    	        scale       : 'large',
    	        iconCls     : 'guardar',
    	        iconAlign   : 'top',
    	        disabled     : <?php echo $disbled_btn; ?>,
    	        handler     : function(){ BloqBtn(this); guardarConceptosEmpleados() }
    	    },
    	    {
				xtype     : 'button',
				width     : 60,
				height    : 56,
				id        : 'btn_prestamo',
				text      : 'Prestamos',
				tooltip   : 'Prestamos del empleado',
				scale     : 'large',
				icon      : '../../temas/clasico/images/BotonesTabs/prestamo.png',
				iconAlign : 'top',
				handler   : function(){ BloqBtn(this); ventana_prestamos() }
    	    }
    	    ,
    	    '->',
    	    {
    	        xtype       : "tbtext",
    	        text        : '<div id="divContenedor_barra_empleado"><?php echo $nombre_empleado; ?><div>',
    	        scale       : "large",
    	    }
    	]
	});

	// <?php echo $acumScript; ?>

	function guardarConceptosEmpleados() {

		var arrayDatos={};

		var inputs = document.getElementById('contenedorInputsConceptosEmpleados').querySelectorAll('.select_input');

		var cont = inputs.length;
		for (i = 0 ; i < cont; i++){

			indice=inputs[i].id;
			arrayDatos[i]={
				id : indice,
				valor : document.getElementById(inputs[i].id).value
			}

		};

		arrayDatos=JSON.stringify(arrayDatos);

		Ext.get('divLoadConceptosEmpleados').load({
			url     : 'conceptos_empleados/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				id_empleado   : '<?php echo $id_empleado ?>',
				arrayDatos : arrayDatos,
			}
		});

		// MyLoading();

	}

	function validaNumero(Input) {
		patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }
	}

	function ventana_prestamos() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_prestamos_empleado = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_prestamos_empleado',
		    title       : 'Prestamos de <?php echo $nombre_empleado; ?>',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'prestamos_empleados/prestamos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_empleado     : '<?php echo $id_empleado ?>',
					nombre_empleado : '<?php echo $nombre_empleado; ?>',
		        }
		    },
		    // tbar        :
		    // [

		    //     {
		    //         xtype   : 'buttongroup',
		    //         columns : 3,
		    //         title   : 'Opciones',
		    //         style   : 'border-right:none;',
		    //         items   :
		    //         [
		    //             {
		    //                 xtype       : 'button',
		    //                 width       : 60,
		    //                 height      : 56,
		    //                 text        : 'Nuevo',
		    //                 scale       : 'large',
		    //                 iconCls     : 'add_new',
		    //                 iconAlign   : 'top',
		    //                 hidden      : false,
		    //                 handler     : function(){ BloqBtn(this); Agregar_nomina_prestamos_empleados() }
		    //             },
		    //             {
		    //                 xtype       : 'button',
		    //                 width       : 60,
		    //                 height      : 56,
		    //                 text        : 'Regresar',
		    //                 scale       : 'large',
		    //                 iconCls     : 'regresar',
		    //                 iconAlign   : 'top',
		    //                 hidden      : false,
		    //                 handler     : function(){ BloqBtn(this); Win_Ventana_prestamos_empleado.close() }
		    //             },

		    //         ]
		    //     }
		    // ]
		}).show();
	}

 </script>