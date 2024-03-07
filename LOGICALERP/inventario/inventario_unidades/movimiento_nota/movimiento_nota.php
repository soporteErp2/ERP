<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$texto      = ($opc=='entrada')? 'Entrada al Inventario del Articulo <b>'.$nombre.'</b>' : 'Salida del Inventario del Articulo <b>'.$nombre.'</b>';

	$sqlItem   = "SELECT costos FROM items WHERE id='$id_item' AND id_empresa='$id_empresa' LIMIT 0,1";
	$queryItem = mysql_query($sqlItem,$link);
	$costo     = mysql_result($queryItem, 0, 'costos');

?>
<div>

	<div id="renderMovimiento" style="display: :none;"></div>
	<div id="divPadre" style="margin:0 auto 0 auto;width:90%;">

		<div style="height:25px;width:100%;margin-top:10px;">
 			<div style="float:left;width:170px;">Cantidad</div>
 			<div style="float:left;"><input type="text" id="cantidad" class="myfieldObligatorio" onkeyup="validarNumberPunto(event,this)"></div>
 		</div>

 		<div style="height:25px;width:100%;margin-top:10px;">
 			<div style="float:left;width:170px;">Costo Unitario</div>
 			<div style="float:left;"><input type="text" id="costo" class="myfieldObligatorio" onkeyup="validarNumberPunto(event,this)" value="<?php echo $costo; ?>"></div>
 		</div>

 		<div style="height:25px;width:100%;margin-top:10px;">
 			<div style="float:left;width:170px;">Relacione una nota contable</div>
 			<div style="float:left;"><input type="text" id="consecutivo" class="myfieldObligatorio" onclick="buscarNotaContable()" readonly></div>
 		</div>

 		<div style="height:160px;width:100%;margin-top:10px;">
 			<div style="float:left;">Observaciones</div>
 			<div style="float:left;margin-top:5px;"><textarea style="height:130px;" cols="100" id="observaciones" class="myfieldObligatorio" onkeyup="validarObservacion(event,this)"></textarea></div>
 		</div>

 	</div>
</div>

<script>
	//FUNCION PARA VALIDAR QUE SOLO PERMITA NUMEROS Y EL PUNTO
	function validarNumberPunto(event,input){

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;


        patron = /[^\d.]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(numero=='')return;
        else if(isNaN(numero)){
			var acumValor   = '';
			var arrayNumero = numero.split('.');
			var contNumero  = arrayNumero.length;

        	for(i=0; i<contNumero; i++){
        		if(i==0){ acumValor+= arrayNumero[i]+'.'; continue; }
        		acumValor+= ''+arrayNumero[i];
        	}
        	input.value = acumValor;
        }
    }

	//FUNCION PARA QUITAR ENTER DEL CAMPO OBSERVACIONES
	function validarObservacion(event,input){

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;


        patron = /[\n]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
    }

    function guardarMovimientoNota(){
    	//VALIDAMOS LOS CAMNPOS ANTES DE CONTINUAR CON EL SCRIPT
		var camposError   = ''
		,	cantidad      = document.getElementById("cantidad").value
		,	costo         = document.getElementById("costo").value
		,	consecutivo   = document.getElementById("consecutivo").value
		,	id_nota       = document.getElementById("consecutivo").dataset.id
		,	observaciones = document.getElementById("observaciones").value;

		if (cantidad==0 || cantidad=='') { camposError+="\n* Cantidad"; }
		if (costo==0 || costo=='') { camposError+="\n* Costo unitario"; }
		if (consecutivo==0 || consecutivo=='') { camposError+="\n* Nota Contable"; }
		if (observaciones==0 || observaciones=='') { camposError+="\n* Observacion"; }
		if (id_nota==0 || id_nota=='') { camposError+="\n* Nota contable"; }

		if(camposError != ''){ alert("Aviso,\nLos siguientes campos son obligatorios!\n"+camposError); return; }

		if ('<?php echo $opc; ?>'=='salida') {
			if ((cantidad*1)>(<?php echo $cantidad; ?>*1)) {
				alert("Error!\nLa cantidad de la salida es mayor a la existente en el almacen\nverifique la cantidad y vuelva a intentarlo");
				document.getElementById("cantidad").focus();
				return;
			}
		}

		MyLoading2('on');

		Ext.get('renderMovimiento').load({
            url     : 'inventario_unidades/movimiento_nota/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc            : 'guardarEntradaSalida',
				idFila         : '<?php echo $id; ?>',
				idItem         : '<?php echo $id_item; ?>',
				cantidad       : cantidad,
				costo          : costo,
				tipoMovimiento : '<?php echo $opc; ?>',
				id_nota        : id_nota,
				consecutivo    : consecutivo,
				observaciones  : observaciones,
				idBodega       : '<?php echo $filtro_bodega; ?>',
				idSucursal     : '<?php echo $filtro_sucursal; ?>'
            }
        });
	}

	//FUNCION PARA BUSCAR LA NOTA RELACIONADA
	function buscarNotaContable(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_bucarNotaContable = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_bucarNotaContable',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'inventario_unidades/movimiento_nota/bd/grillaBuscarNota.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            var1 : 'var1',
		            var2 : 'var2',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_bucarNotaContable.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

</script>