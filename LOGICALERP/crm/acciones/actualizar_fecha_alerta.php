<?php
	//include('../../misc/gettext.php');
?>

<div style="margin:25px 0 0 0">
	<center>
        <select id="cuanto_pospongo" name="cuanto_pospongo">
              <option value="300"		>5 Minutos	</option>
              <option value="600"		>10 Minutos	</option>
              <option value="900"		>15 Minutos	</option>
              <option value="1200"		>20 Minutos	</option>
              <option value="1800"		>30 Minutos	</option>
              <option value="2700"		>45 Minutos	</option>
              <option value="3600"		>1 Hora		</option>
              <option value="7200"		>2 Horas	</option>
              <option value="10800"		>3 Horas	</option>
              <option value="14400"		>4 Horas	</option>
              <option value="18000"		>5 Horas	</option>
              <option value="36000"		>10 Horas	</option>
              <option value="43200"		>12 Horas	</option>
              <option value="86400"		>1 Dia		</option>
              <option value="172800"	>2 Dias		</option>
              <option value="259900"	>3 Dias		</option>
              <option value="345600"	>4 Dias		</option>
              <option value="432000"	>5 Dias		</option>
              <option value="864000"	>10 Dias	</option>
              <option value="1296000"	>15 Dias	</option>
              <option value="1728000"	>20 Dias	</option>
              <option value="2592000"	>1 Mes		</option>
              <option value="5184000"	>2 Meses	</option>
              <option value="7776000"	>3 Meses	</option>
              <option value="10368000"	>4 Meses	</option>
              <option value="12960000"	>5 Meses	</option>
              <option value="15552000"	>6 Meses	</option>
        </select>
        <br />
        <br />
        <input name="btn_trc" type="button" onClick="terminar_cambio_fecha_alerta()" value="Aceptar">
	</center>
</div>
<script>
var id = <?php echo $_GET['id_registro'] ?>;
function terminar_cambio_fecha_alerta(){
	var posponer = document.getElementById('cuanto_pospongo').value
	win_posponer_fecha.close();
	Ext.Ajax.request({
		url: 'LOGICALERP/crm/acciones/actualiza_desde_radar.php',
		success	: function (result, request)
				  {
					document.getElementById('capa_segui_'+id).style.color='#999999';
				  },
		failure : function()
				  {
				  	alert(<?php echo "'".'Error Cambiando la fecha de la alerta. por favor intente de nuevo!'."'"; ?>);
				  },
		params	: {
					opcion  : 'posponer_alerta',
					id		  : id,
					posponer: posponer
				  }
	});
}
</script>