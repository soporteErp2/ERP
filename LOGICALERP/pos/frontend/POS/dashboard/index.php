<?php


?>
<link rel="stylesheet" href="../../../../../misc/Win/css/Win.min.css" />
<link rel="stylesheet" href="../../../../../misc/Win/css/Win-theme-blue.min.css" />
<script type="text/javascript" src="../../../../..//misc/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../../../../../misc/Win/js/Win.min.js"></script>
<div id="tab-soporte" style="width:100%; height: 100%"></div>
<script>
	$W.Add({
		idApply : "tab-soporte",
		items :
		[
			{
				xtype       : "tabpanel",
				id          : "idTabDashboard",
				items       :
				[
					{
						xtype    : "tab",
						id       : 'dashboard',
						title    : "dashboard",
						selected : true,
						scrollY  : true,
						icon     : '',
						autoLoad : {url : "dashboard/index.php"	}
					},
					// {
					// 	xtype    : "tab",
					// 	title    : "Presupuesto",
					// 	icon	 : '',
					// 	autoLoad : {url : "presupuesto/index.php"	}
					// },
				]
			}
		]
	})


</script>