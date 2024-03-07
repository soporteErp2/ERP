<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	$texto=($opc=='entrada')? 'Entrada al Inventario del Articulo <b>'.$nombre.'</b>' : 'Salida del Inventario del Articulo <b>'.$nombre.'</b>' ;
?>
<?php 	/*ila = ".$id."<br>";
		m = ".$id_item."<br>";
		al = ".$filtro_sucursal."<br>";
		 = ".$filtro_bodega."<br>";
		".$opc; */

 ?>
 <!-- <div style="border:1px solid;border-color:#99BBE8;width:100%;margin-bottom:10px;"></div> -->
<div>

	<div id="renderMovimiento"></div>
	<div id="divPadre" style="margin:0 auto 0 auto;width:90%;">

 		<div style="height:25px;width:100%;margin-top:30px;">
 			<div style="float:left;width:170px;">Relacione una nota contable</div>
 			<div style="float:left;">
 				<input type="text" id="consecutivo" class="myfieldObligatorio" onclick="buscarNotaContable()" readonly>

 			</div>
 			<div onclick="buscarNotaContable()" title="Buscar Nota Contable" class="iconBuscarArticulo" style="margin-left:0px;height:20px;border:1px solid #D4D4D4;">
				<img src="images/buscar20.png"/>
			</div>
 		</div>

 		<div style="height:160px;width:100%;margin-top:20px;">
 			<div style="float:left;">Observaciones</div>
 			<div style="float:left;margin-top:5px;"><textarea style="height:130px;"  cols="100" id="observaciones" class="myfieldObligatorio" onkeyup="validarObservacion(event,this)"></textarea></div>
 		</div>

 	</div>
</div>

<script>
	//FUNCION PARA VALIDAR QUE SOLO PERMITA NUMEROS Y EL PUNTO
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
		consecutivo   = document.getElementById("consecutivo").value;
		observaciones = document.getElementById("observaciones").value;

		// console.log("<?php echo $cantidad; ?>");
		if (consecutivo==0 || consecutivo=='') {	alert("Error!\nDebe ingresar un valor para el campo consecutivo");  document.getElementById("consecutivo").focus(); return;}
		if (observaciones==0 || observaciones=='') {	alert("Error!\nDebe ingresar un valor para el campo observaciones");  document.getElementById("observaciones").focus(); return;}

		  Ext.get('renderMovimiento').load({
            url     : 'movimiento_nota/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc            : 'guardarEntradaSalida',
				idFila         : '<?php echo $id; ?>',
				idItem         : '<?php echo $id_item; ?>',
				consecutivo    : consecutivo,
				observaciones  : observaciones,
				idSucursal     : document.getElementById('filtro_sucursal_inventario').value,
				idBodega       : document.getElementById('filtro_ubicacion_inventario').value,


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
		    title       : 'Seleccione la Nota contable',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'movimiento_nota/bd/grillaBuscarNota.php',
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