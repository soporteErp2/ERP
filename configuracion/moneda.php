<?php
	$consulMoneda = mysql_query("SELECT * FROM moneda",$link);
	$consulMoneda2 = mysql_query("SELECT * FROM moneda",$link);
?>
<script language="javascript1.2">
	var LABEL = new Array();
	<?php	
		$index_moneda = 0;
		while($rowMoneda = mysql_fetch_array($consulMoneda)){
	?>
			LABEL[<?php echo $index_moneda?>] = "<?php echo $rowMoneda['label']?>";
	<?php
			$index_moneda = $index_moneda + 1;
		}
	?>
	
	function cual_moneda(moneda){
		switch (moneda) { 
			<?php	
				while($rowMoneda = mysql_fetch_array($consulMoneda2)){
			?>
   			case "<?php echo $rowMoneda['moneda']?>": 
				Ext.getCmp('BOTON_MONEDA').setIconClass('moneda<?php echo $rowMoneda['id']?>');
				Ext.getCmp('BOTON_MONEDA').setText('<?php echo $rowMoneda['label'] ?>');
      	 		break 
			<?php
				}
			?>
		}
	}
</script>