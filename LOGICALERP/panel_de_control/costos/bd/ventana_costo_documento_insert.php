<?php

        include("../../../../configuracion/conectar.php");
		include("../../../../configuracion/define_variables.php");

		$id_empresa = $_SESSION['EMPRESA'];

		$SQL    = "SELECT id,nombre FROM costo_tipo WHERE activo=1 AND id_empresa = '$id_empresa'";
		$consul = mysql_query($SQL,$link);
?>

		<div id="contenedor_costos_documento" style="width:20px; height:20px; position:fixed; margin-left:10px;"></div>
 		<div style="float:left;margin: 20px 0 0 10px">
			    <div style="float:left; width:100px; padding:3px 0 0 0">Nombre:</div>
			    <div id="recibidor_nombre_<?php echo $opcGrilla; ?>" style="float:left; height:23px;">
				    <input id="nombre_costo_documento" class = "myfield" style="width:200px;height:19px;vertical-align:middle" />


				</div>
		 </div>
<?php

	   // echo $SQL;
	    $cont = 0;
	    echo "<script>arrayCamposCcos.length = 0;</script>";
        while($row=mysql_fetch_array($consul)){


	    	echo "<script>arrayCamposCcos[$cont] = '$row[id]';</script>";

?>
			<div style="float:left;margin: 5px 0 0 10px;width:90%">
			    <div style="width:100px; padding:3px 0 0 0;font-size:12;font-weight:bold"><?php echo $row['nombre']; ?></div>
			</div>

            <div style="float:left;margin: 7px 0 0 10px;width:90%">
			    <div style="float:left; width:100px; padding:3px 0 0 0">Valor:</div>
			    <div id="recibidor_valor_<?php echo $opcGrilla; ?>" style="float:left;height:23px;">
				    <input id="valor_<?php echo $row['id']; ?>" class = "myfield" style="width:200px;height:19px;vertical-align:middle" />


				</div>
		    </div>

		     <input type="hidden" id="<?php echo $row['id']; ?>_id_centro_costos" style="width:150px;height:23px;vertical-align:middle" />
		     <input type="hidden" id="<?php echo $row['id']; ?>_id_cuenta_colgaap" style="width:150px;height:23px;vertical-align:middle" />
		     <input type="hidden" id="<?php echo $row['id']; ?>_id_cuenta_niif" style="width:150px;height:23px;vertical-align:middle" />



		    <div style="float:left;margin: 7px 0 0 10px;width:90%" id="DIV_centro_costo_<?php echo $row['id']; ?>">
                 <div style="float:left; width:100px; padding:3px 0 0 0">Centro Costos:</div>
			    <div id="recibidor_costos_<?php echo $opcGrilla; ?>" style="float:left;height:23px;">
				    <input id="<?php echo $row['id']; ?>_centro_costos" class = "myfield" style="width:178px;height:19px;vertical-align:middle;" />
				</div>
				<div id="<?php echo $row['id']; ?>_imgEliminarCcos" class ="divBtnBuscarPuc" style="background-color: #FFF;cursor:pointer;background-image: url(img/buscar20.png);background-repeat: no-repeat;float:left;border-left: 1px solid #BDB4B4;"  title="Buscar Centro Costos" onclick="ventanaBuscarCentroCostos(<?php echo $row['id']; ?>)"></div>
			</div>
			<div style="float:left;margin: 7px 0 0px 10px;width:90%" id="DIV_cuenta_colgaap<?php echo $row['id']; ?>">
				<div style="float:left; width:100px; padding:3px 0 0 0">Cuenta Colgaap:</div>
			    <div id="recibidor_colgaap_<?php echo $opcGrilla; ?>" style="float:left; height:23px;">
				    <input id="cuenta_colgaap<?php echo $row['id']; ?>" class = "myfield" style="width:120px;height:23px;vertical-align:middle" />


				</div>
			</div>
			<div style="float:left;margin: 7px 0 15px 10px;width:90%" id="DIV_cuenta_niif<?php echo $row['id']; ?>">
				<div style="float:left; width:100px; padding:3px 0 0 0">Cuenta NIIF:</div>
			    <div id="recibidor_niif_<?php echo $opcGrilla; ?>" style="float:left;height:23px;">
				    <input id="cuenta_niif<?php echo $row['id']; ?>" class = "myfield" style="width:120px;height:23px;vertical-align:middle" />


				</div>
			</div>

			<script>

			    document.getElementById("valor_<?php echo $row['id']; ?>").onkeypress = function(event){return ValidarNumero(event);};;
			    document.getElementById("<?php echo $row['id']; ?>_centro_costos").onkeypress = function(event){return ValidarNumero(event);};;
			    document.getElementById("cuenta_colgaap<?php echo $row['id']; ?>").onkeypress = function(event){return ValidarNumero(event);};;
			    document.getElementById("cuenta_niif<?php echo $row['id']; ?>").onkeypress = function(event){return ValidarNumero(event);};;

			    divOcultarCentroCostos     = document.getElementById("<?php echo $row['id']; ?>_centro_costos").parentNode.parentNode;
				inputCentroCostos          = document.getElementById("<?php echo $row['id']; ?>_centro_costos");
				inputCentroCostos.readOnly = true;
				inputCentroCostos.setAttribute("onclick","ventanaBuscarCentroCostos(<?php echo $row['id']; ?>)");

				cuenta         = document.getElementById("cuenta_colgaap<?php echo $row['id']; ?>");
				cuenta_id      = document.getElementById("<?php echo $row['id']; ?>_id_cuenta_colgaap");
				cuenta_niif    = document.getElementById("cuenta_niif<?php echo $row['id']; ?>");
				cuenta_niif_id = document.getElementById("<?php echo $row['id']; ?>_id_cuenta_niif");


			    agregarBtnBuscarCuentaDocumentos('colgaap',cuenta,cuenta_id);
			    agregarBtnBuscarCuentaDocumentos('niif',cuenta_niif,cuenta_niif_id);
			    agregarBtnSincronizarCuentaDocumentos(cuenta,cuenta_niif,cuenta_niif_id);
				//agregarBtnSincronizarCuentaDocumentos(cuenta_niif);

			    function agregarBtnBuscarCuentaDocumentos(valor,input1,input2){
			            input1.readOnly = true;
			            input1.setAttribute("style","float:left; width:157px;");


			            var idInput  = input1.id;
			            var idInput2 = input2.id;
			            //console.log(idInput);

			            var btnBuscarCuenta = document.createElement('div');
			            btnBuscarCuenta.setAttribute('class','divBtnBuscarPuc');
			            btnBuscarCuenta.setAttribute('title','Buscar cuenta');
			            btnBuscarCuenta.setAttribute('onclick','ventanaBuscarCuentaDocumentos("'+valor+'","'+idInput+'","'+idInput2+'")');
			            btnBuscarCuenta.innerHTML = '<img src="img/buscar20.png" />';
			            document.getElementById('DIV_'+idInput).appendChild(btnBuscarCuenta);
			    }


			    function agregarBtnSincronizarCuentaDocumentos(input,input1,input2){
						var idInput = input.id
						, idInput1  = input1.id
						, idInput2  = input2.id
						,	estado  = idInput.split('_')[1];


						var btnSincronizarCuenta = document.createElement('div');
						btnSincronizarCuenta.setAttribute('class','divBtnBuscarPuc');
						btnSincronizarCuenta.setAttribute('id','btn_sincronizar_'+idInput);
						btnSincronizarCuenta.setAttribute('title','Sincronizar cuenta en Niif');
						btnSincronizarCuenta.innerHTML = '<img src="img/refresh.png" onclick="sincronizaCuentaDocumentosEnNiif(\''+idInput+'\',\''+idInput1+'\',\''+idInput2+'\')"/>';
						document.getElementById('DIV_'+idInput).appendChild(btnSincronizarCuenta);
				}


				function ValidarNumero(e) {//VALIDAR NUMEROS
					tecla = (document.all)?e.keyCode:e.which;
					if (tecla==8 		//BACKSPACE
					 	|| tecla==9 	//TAB
					 	|| tecla==0 	//TAB
					 	|| tecla==13 	//ENTER
					 	) return true;
					patron = /\d/;
					te = String.fromCharCode(tecla);
					return patron.test(te);

				}

			</script>


<?php
            $cont++;
        }
?>

<script>


      function eliminaCcosItem (valor) {
      	    //alert(valor);
			document.getElementById(valor+'_imgEliminarCcos').setAttribute('onclick','ventanaBuscarCentroCostos('+valor+')');
			document.getElementById(valor+'_imgEliminarCcos').style.backgroundImage="url('img/buscar20.png')";
			document.getElementById(valor+'_imgEliminarCcos').setAttribute('title','Buscar Centro de Costos');

			document.getElementById(valor+'_id_centro_costos').value = '';
			document.getElementById(valor+'_centro_costos').value    = '';

	  }

      function sincronizaCuentaDocumentosEnNiif(idInput,idInput1,idInput2){


			var cuenta = document.getElementById(idInput).value;
			if(isNaN(cuenta) || cuenta == 0){ alert("Aviso\nSeleccione una cuenta para sincronizar"); return; }

			Ext.get('btn_sincronizar_'+idInput).load({
				url     : 'costos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					op       : 'sincronizaPucImpuestoNiif',
					idInput  :  idInput,
					idInput1 :  idInput1,
					idInput2 :  idInput2,
					cuenta   :  cuenta
				}
			});
		}


      function ventanaBuscarCuentaDocumentos(valor,idInput,idInput2){

			typeCuenta = valor;
			var title  = (typeCuenta == 'colgaap')? 'Seleccione la cuenta Colgaap': 'Seleccione la cuenta Niif';

			Win_VentanaBuscarPucRetenciones = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucRetenciones',
	            title       : title,
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'costos/bd/busqueda_puc_costos.php',
	                scripts : true,
	                nocache : true,
	                params  : { typeCuenta : typeCuenta, idInput : idInput,idInput2 : idInput2 }
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarPucRetenciones.close(); }
	                }
	            ]
	        }).show();
		}

		function ventanaBuscarCentroCostos(valor) {

			Win_Ventana_buscar_centro_costos = new Ext.Window({
			    width       : 540,
			    height      : 450,
			    id          : 'Win_Ventana_buscar_centro_costos',
			    title       : 'Buscar Centro de Costos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'costos/bd/grillaBuscarCentroCostos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc         : valor,
						carpeta_img : 'img',
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
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

</script>