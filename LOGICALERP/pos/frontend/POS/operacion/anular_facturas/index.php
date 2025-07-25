<?php

	include('../../../../../../configuracion/conectar.php');
	include('../../../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	$sql   = "SELECT id,nombre FROM ventas_pos_secciones WHERE activo= 1 AND id_empresa=$id_empresa AND restaurante='Si' ";
	$query = $mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$optAmbientes .= "<option value='$row[id]'>$row[nombre]</option>";
	}

    //Saca la fecha de inicio del planning
	// $fechaAuditoria         = explode("-", $_SESSION['empresa_fecha_auditoria']);

	// //Suma el numero de meses configurados para mostrar en el planning a partir de la fecha de auditoria
	// $limmitDatePlanning     = strtotime ("+{$_SESSION['global_planning_meses']} months", strtotime($_SESSION['empresa_fecha_auditoria']));
	// $limmitDatePlanning     = date ('Y-m-d', $limmitDatePlanning);

?>
<div id="tbarAnularComandas" style="width:100%;height:68px;"></div>
<div id="contentAnularFacturas" style="width:100%;height:calc(100% - 100px);"></div>

<script>
	"use strict";
	(function() {

		$W.Add({
		idApply : "tbarAnularComandas",
		items   :
		[
			{
				xtype : "tbar",
				id    : "tbarAnularComandasReservas",
				items :
				[


					{
						xtype : "tbtext",
						width : 185,
						text  : `<div style="width:180px; float:left;">
									<div style="width:100%; float:left;">
										<div style="float:left; width:55px; padding-top:2px;">Desde</div>
										<div style="float:left; width:95px; padding-top:0px;">
											<input type="text" style="width:93px;" id="fechaInicio" value="<?= date('Y-m-d'); ?>" />
										</div>
										<div style="float:left; margin-top:-5px;">
											<i class="material-icons" style="margin">date_range</i>
										</div>
									</div>
									<div style="width:100%; float:left;">
										<div style="float:left; width:55px; padding-top:7px;">Hasta</div>
										<div style="float:left; width:95px; padding-top:5px;">
											<input type="text" style="width:93px;" id="fechaFin" value="<?= date('Y-m-d'); ?>" />
										</div>
										<div style="float:left;">
											<i class="material-icons">date_range</i>
										</div>
									</div>
								</div>`,
						style : ""
					},"--",
					// {
					// 	xtype : "tbtext",
					// 	width : 180,
					// 	text  : `<div style="width:100%; float:left;">
					// 				<div style="width:100%; float:left;">
					// 					<div style="float:left; width:100%; padding-top:7px;">
					// 						<input placeholder="Num. Factura" type="text" id="num_factura" style="width:100%" />
					// 					</div>
					// 				</div>
					// 			</div>`,
					// 	style : ""
					// },"--",

					{
						xtype     : "button",
						text      : "Consultar",
						scale     : "large",
						cls       : "search",
						iconAlign : "top",
						handler   : function(){
							loadGrilla();
						}
					},"--",
					{
						xtype : "tbtext",
						width : 420,
						text  : `<div style="width:100%; float:left;">
									<div style="float:left; width:20px; border-radius:3px; height:15px; margin:3px 0px 0px 0px; background-color:#45af55"></div>
									<div style="width:80px; float:left; font-size:12px; color:#ffffff; padding:3px 0px 0px 3px;">Activa</div>
								</div>
								<div style="width:100%; float:left;">
									<div style="float:left; width:20px; border-radius:3px; height:15px; margin:3px 0px 0px 0px; background-color:#db5957"></div>
									<div style="width:80px; float:left; font-size:12px; color:#ffffff; padding:3px 0px 0px 3px;">Anulada</div>
								</div>
								<div style="width:100%; float:left;">
									<div style="float:left; width:20px; border-radius:3px; height:15px; margin:3px 0px 0px 0px; background-color:#8c8c8c"></div>
									<div style="width:80px; float:left; font-size:12px; color:#ffffff; padding:3px 0px 0px 3px;">Bloqueada</div>
								</div>
								`,
						style : ""
					}
				]
			}
		]})

		/**
		 * loadPlanning Carga las reservas en las habitaciones
		 * @return void
		 */

		// objPlanning.loadPlanning(false, 'false');

		// $W('#form_tbarAnularComandasReservas_mostrarTooltipReservas').on("change",function(){
		// 	let mostrarTooltip='false';
		// 	if(document.getElementById('form_tbarAnularComandasReservas_mostrarTooltipReservas').checked){
		// 		mostrarTooltip='true';
		// 	}
		// 	objPlanning.loadPlanning(false, mostrarTooltip);
		// });


		//Input decha "DESDE"
		$W.Form.field({
			idApply : "fechaInicio",
			type    : "date"
		});

		//Input decha "HASTA"
		$W.Form.field({
			idApply : "fechaFin",
			type    : "date"
		});



	})();

	var loadGrilla = ()=>{
		$W.Load({
			idApply : "contentAnularFacturas",
			url     : "anular_facturas/grilla.php",
			params  : {
				// id_ambiente  : document.getElementById('selectAmbiente').value,
				fecha_inicio : document.getElementById('fechaInicio').value,
				fecha_final  : document.getElementById('fechaFin').value
			}
		})
	}

	loadGrilla();
</script>