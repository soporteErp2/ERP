<?php

	include('../../../../../../configuracion/conectar.php');
	include('../../../../../../configuracion/define_variables.php');
	// error_reporting(E_ALL);

	$sql   = "SELECT fecha
				FROM ventas_pos_auditoria_cierre
				WHERE activo=1 AND id_empresa=$_SESSION[EMPRESA] ORDER BY fecha DESC LIMIT 0,1";
	$query = $mysql->query($sql);
	$fechaAudit = $mysql->result($query,0,'fecha');
	$fechaAudit = ($fechaAudit=='')? "<i>Ninguna</i>" : $fechaAudit ;
	$fechaPrecierre = date("Y-m-d");


?>
<div id="tbarPrecierre" style="width:100%;height:68px;"></div>
<div id="contentPrecierre" style="width:100%;height:calc(100% - 68px);"></div>

<script>
	"use strict";
	(function() {

		$W.Add({
		idApply : "tbarPrecierre",
		items   :
		[
			{
				xtype : "tbar",
				id    : "tbarPrecierrePos",
				items :
				[
					{
						xtype : "tbtext",
						width : 130,
						text  : `<div style="width:100%; float:left;">
									<div style="width:100%; float:left; font-size:12px; color:#ffffff; padding:3px 0px 0px 3px;text-align:center;">Fecha ultima auditoria <br/> <span style="font-size:16px;font-weight:bold;"> <?= $fechaAudit ?> </span></div>
								</div>`,
					},"--",
					{
						xtype : "tbtext",
						width : 130,
						text  : `<div style="width:100%; float:left;">
									<div style="width:100%; float:left; font-size:12px; color:#ffffff; padding:3px 0px 0px 3px;text-align:center;">Fecha Precierre <br/> <span style="font-size:16px;font-weight:bold;"> <?= $fechaPrecierre ?> </span></div>
								</div>`,
					},"--",
					{
						xtype     : "button",
						text      : "Validar dia",
						scale     : "large",
						cls       : "assignment",
						iconAlign : "top",
						handler   : function(){
							validarDia();
						}
					},"--",
					{
						xtype     : "button",
						id        : "btnPrecierre",
						text      : "Confirmar precierre",
						scale     : "large",
						cls       : "lock",
						iconAlign : "top",
						disable   : true,
						handler   : function(){
							generarPrecierre();
						}
					},"--",

				]
			}
		]})

	})();

	var validarDia = ()=>{
		$W.Load({
			idApply : "contentPrecierre",
			url     : "precierre/validacion_dia.php",
			params  : {
				fecha : "<?= $fecha ?>",
			}
		})
	}

	var generarPrecierre = () => {
		if (confirm("Se realizara el precierre del POS, este proceso no es reversible, realmente desea continuar?")){

			$W.Loading();
			$W.Ajax({
				url    : "../../../backend/pos_admin/Controller.php",
				params :  {
					method : "generarPrecierre",
					fecha  : "<?= $fechaPrecierre; ?>"
				},
				timeout : 2000,
				success : function(result,xhr){
					console.log(result.responseText); //lee respuesta como texto
					console.log(JSON.parse(result.responseText)); //lee respuesta como json
					let response = JSON.parse(result.responseText);
					if (response.status=='success'){ alert(response.message); WinGlobal.close(); }
					else{ alert(response.message) }
					$W.Loading();
				},
				failure : function(xhr){
					console.log("fail");
					$W.Loading();
				}
			})

		}

	}


</script>