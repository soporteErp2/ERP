<?php
	include("../../configuracion/conectar.php");
	$id_empleado = $_POST['id_empleado'];
    $ano = $_POST['ano'];
    $mes = $_POST['mes'];
    $dia = 1;

    $permiso_crear_actividad = (user_permisos(194,'false') == 'true')? 'true' : 'false';

    $MES[1] = 'Enero'; $MES[2] = 'Febrero'; $MES[3] = 'Marzo'; $MES[4] = 'Abril'; $MES[5] = 'Mayo'; $MES[6] = 'Junio';
    $MES[7] = 'Julio'; $MES[8] = 'Agosto'; $MES[9] = 'Septiembre'; $MES[10] = 'Octubre'; $MES[11] = 'Noviembre'; $MES[12] = 'Diciembre';

    $SEMANA = array('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');

    $ARRDIASSEMANA = $SEMANA;
    $ARRMES = $MES;

    if(!isset($dia)){$dia = date('d');}	//echo $dia;
    if(!isset($mes)){$mes = date('n');} //echo 'Junio'.$mes;
    if(!isset($ano)){$ano = date('Y');}	//echo $ano;

    $TotalDiasMes           = date('t',mktime(0,0,0,$mes,$dia,$ano));
    $DiaSemanaEmpiezaMes    = date('w',mktime(0,0,0,$mes,1,$ano)); if($DiaSemanaEmpiezaMes==0){$DiaSemanaEmpiezaMes=7;}
    $DiaSemanaTerminaMes    = date('w',mktime(0,0,0,$mes,$TotalDiasMes,$ano));
    $EmpiezaMesCalOffset    = $DiaSemanaEmpiezaMes;
    $TerminaMesCalOffset    = 6 - $DiaSemanaTerminaMes;
    $TotalDeCeldas          = $TotalDiasMes + $DiaSemanaEmpiezaMes + $TerminaMesCalOffset;
    $porcentajeH 			= 20;

	if($TotalDeCeldas > 35 && $TerminaMesCalOffset != 6){
        $porcentajeH = 16.66666666666667;
    }


    if($mes == 1){
		$MesAnterior = 12;
        $AnoAnterior = $ano - 1;
        $MesSiguiente = $mes + 1;
        $AnoSiguiente = $ano;
    }elseif($mes == 12){
        $MesAnterior = $mes - 1;
        $AnoAnterior = $ano;
        $MesSiguiente = 1;
        $AnoSiguiente = $ano + 1;
    }else{
        $MesAnterior = $mes - 1;
        $MesSiguiente = $mes + 1;
        $AnoAnterior = $ano;
        $AnoSiguiente = $ano;
    }
    //echo $MesAnterior.'-';
    //echo $AnoAnterior.'<br />';
    //echo $MesSiguiente.'-';
    //echo $AnoSiguiente;

    echo '<script>
			var Ano			 = '.$ano.';
			var Mes			 = '.$mes.';
            var MesAnterior  = '.$MesAnterior.';
            var MesSiguiente = '.$MesSiguiente.';
            var AnoAnterior  = '.$AnoAnterior.';
            var AnoSiguiente = '.$AnoSiguiente.';
            var fechai       = "'.$ano.'-'.str_pad($mes, 2, "0", STR_PAD_LEFT).'-01";
            var fechaf       = "'.$ano.'-'.str_pad($mes, 2, "0", STR_PAD_LEFT).'-'.str_pad($TotalDiasMes, 2, "0", STR_PAD_LEFT).'";
          </script>';

?>
<style>

	.calTituloMesAno{
        background-color        : 	rgba(<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>,1);
		font-family				:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
		float					:	left;
		width					: 	260px;
		font-size				: 	20px;
		font-weight				: 	normal;
		text-align				: 	center;
		color                   : 	#FFF;
	}
    .calTituloDia{
        background-color        : 	rgba(<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>,1);
		font-family				:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
        font-size               : 	16px;
        color                   : 	#FFF;
        margin                  : 	0 0 0 0;
		padding					: 	3px 0 3px 0;
        float                   : 	left;
        width                   : 	14.28571428571429%; /*14.22%;*/
        text-align              : 	center;
		font-weight				: 	normal;
    }
    .{
        margin                  : 	0 0 0 0;
		border-top				: 	1px solid #EEE;
		border-left				: 	1px solid #EEE;
        float                   : 	left;
        width                   : 	calc(14.28571428571429% - 1px); /*14.22%;*/
        height                  : 	calc(<?php echo $porcentajeH; ?>% - 3.2px);
        overflow                : 	hidden;
    }
    .cal{
        margin                  : 	0 0 0 0;
		border-top				: 	1px solid #EEE;
		border-left				: 	1px solid #EEE;
        float                   : 	left;
        width                   : 	calc(14.28571428571429% - 1px); /*14.22%;*/
        height                  : 	calc(<?php echo $porcentajeH; ?>% - 3.2px);
        overflow                : 	hidden;
    }
	.cal:hover {
		background-color		: 	#FAFAFA;
	}
    .calDiasFilaVacia{
		border					:	hidden;
		background-color		:	transparent;
		background				:	none;
		width                   : 	14.28571428571429%;
		border-top				: 	1px solid #EEE;
    }
    .calDiasHoy .calNumDia{
		color					:	#F00;
		font-size               : 	18px;
    }

    .calNumDia{
        float                   : 	left;
        width                   : 	15px;
        text-align              : 	center;
		font-family				: 	RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
        font-size               : 	16px;
		font-weight				: 	normal;
		margin 					: 	0 0 0 0;
		color					:	#999
    }

    .calDiasFilaUltimaCelda{ /* DIA FESTIVO */
		background-color		:	#F9F9F9;
		border-left				: 	1px solid #C33;
    }
	.calDiasFilaPrimeraCelda{

	}
	.ContenedorEventos{
		float:left;
		width:100%;
		height:calc(100% - 20px);
		overflow:hidden;
		overflow-y:auto;
	}

    .evento{
        float                   : 	left;
		font-family				:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
		margin 					: 	2px 3px 0px 3px;
		padding					: 	4px 0px 2px 18px;
		width					: 	calc(100% - 26px);
		height					: 	20px;
        cursor                  : 	pointer;
		font-size				: 	13px;
	    white-space             : 	nowrap;
        overflow                : 	hidden;
        text-overflow           : 	ellipsis;
		font-weight				: 	bold;
		background-color		: 	<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>;
		color					:	#FFF;
		background-repeat		: no-repeat;
		background-position		: left;
		-moz-border-radius		: 3px;
		-webkit-border-radius	: 3px;
		border-radius			: 3px;
    }

	.Tipo_1{background-image	: url(images/t1.png?v0);}
	.Tipo_2{background-image	: url(images/t2.png?v0);}
	.Tipo_3{background-image	: url(images/t3.png?v0);}
	.Tipo_4{background-image	: url(images/t4.png?v0);}
	.Tipo_5{background-image	: url(images/t5.png?v0);}
	.Tipo_6{background-image	: url(images/t6.png?v0);}

	.divf{
		float	:left;
		width	:100%;
		height	:10%;
		border	:1px
	}

	.Cabecera_mes{
		float			:	left;
		width			:	100%;
		height			:	20px;
		background-color: 	#006699;
	}

	.AddGerente{
		float			:	left;
		width			:	16px;
		height			:	16px;
		margin			:	0 0 0 3px;
		background-image:	url(../../temas/clasico/images/BotonesTabs/add16.png);
		background-repeat:	no-repeat;
		cursor			:	pointer;
	}
</style>


<div style="float:left ; width: 100%; background-color:rgba(<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>,1); padding:8px 0 15px 0; ">
	<div style="width:300px; left:50%; margin:0 0 0 -150px; position:relative">
        <div class="calTituloMesAno" style="float:left; cursor: pointer; width:20px;" onclick="anterior()"><b><</b></div>
        <div class="calTituloMesAno">
            <?php echo $MES[$mes].' '.$ano;?>
        </div>
        <div class="calTituloMesAno" style="float:right; cursor: pointer; width:20px;" onclick="siguiente()"><b>></b></div>
    </div>
</div>


<?php
foreach($ARRDIASSEMANA AS $key){
    echo '<div class="calTituloDia">'.$key.'</div>';
}
echo '<div id="ContenedorDeDiasCalendario" style="float:left; background-color:#FFF;" >';
for($a=1;$a <= $TotalDeCeldas; $a++){

    if(!isset($b)){$b = 0;}
    if($b == 7){$b = 0;}
    if(!isset($c)){$c = 1;}

    if($a >= $EmpiezaMesCalOffset AND $c <= $TotalDiasMes){
        $styleCal = 'cal';
        if($c == date('d') && $mes == date('m') && $ano == date('Y')){// SI ES HOY
           $styleCal = 'cal calDiasHoy';
        }
        if($b == 6){ // SI ES DOMINGO
           $styleCal = 'cal calDiasFilaUltimaCelda';
        }
        $LabelDia = '<div class="calNumDia">'.$c.'</div>';
        $NumeroDia = $c;
		//if($c<10){$NumeroDiaCompleto = '0'.$c;}else{$NumeroDiaCompleto = $c;}
        //if($mes<10){$mesCompleto = '0'.$mes;}else{$mesCompleto = $mes;}
		$NumeroDiaCompleto 	= str_pad($c, 2, "0", STR_PAD_LEFT);
		$mesCompleto 		= str_pad($mes, 2, "0", STR_PAD_LEFT);
		if($permiso_crear_actividad == 'true' || $id_empleado == $_SESSION["IDUSUARIO"]){
			$OnClick = 'onclick="NuevoRegistro(\''.$ano.'-'.$mesCompleto.'-'.$NumeroDiaCompleto.'\')"';
			$Ondblclick = 'ondblclick="alert(\'ver dia\')"';
		}
        $c++;
    }else{
        $LabelDia = '';
        $NumeroDiaCompleto = 0;
        $styleCal  = 'cal calDiasFilaVacia';
		$OnClick = "";
		$Ondblclick = "";
        $NumeroDia = '0';
        $mesCompleto = str_pad($mes, 2, "0", STR_PAD_LEFT);
    }



    echo '<div class="'.$styleCal.' minimal" '.$OnClick.' '.$Ondblclick.'>
            <div style="float:left; width:20px; cursor:pointer">'.$LabelDia.'</div>
            <div class="ContenedorEventos" id="Capa_'.$ano.'-'.$mesCompleto.'-'.$NumeroDiaCompleto.'"> </div>
		  </div>';

    $b++;
}
echo '</div>';
?>

<div id="Recibidor_Espacios2"></div>

<script>
	var myalto  = Ext.getBody().getHeight();
	var myancho  = Ext.getBody().getWidth();
	document.getElementById('divContenedorRecibidorVisorCalendario').style.width = myancho ;
	document.getElementById('divContenedorRecibidorVisorCalendario').style.height = myalto-90;
	document.getElementById('ContenedorDeDiasCalendario').style.width  = myancho;
	document.getElementById('ContenedorDeDiasCalendario').style.height = myalto  -95;

    function anterior(){
		capa = $("#divContenedorRecibidorVisorCalendario");
		capa.fadeOut(500, CargaAjax);
		function CargaAjax(){
			Ext.get('divContenedorRecibidorVisorCalendario').load(
			   {
				   	url      : 'calendarioMes.php',
				   	scripts  : true,
				   	nocache  : true,
				   	params   : 	{
						ano  	    :  AnoAnterior,
						mes  	    :  MesAnterior,
						id_empleado	:  '<?php echo $_POST['id_empleado']; ?>'
					}
			   }
			);
		}
		capa.fadeIn(500);

    }

    function siguiente(){

       capa = $("#divContenedorRecibidorVisorCalendario");
       capa.fadeOut(500, CargaAjax);
	   function CargaAjax(){
           Ext.get('divContenedorRecibidorVisorCalendario').load(
               {
                   url      : 'calendarioMes.php',
                   scripts  : true,
                   nocache  : true,
                   params   : {
                       ano  	:  AnoSiguiente,
                       mes  	:  MesSiguiente,
					   id_empleado	:  '<?php echo $_POST['id_empleado']; ?>'
                   }
               }
           );
       }
       capa.fadeIn(500);
    }

	function recarga(){

       capa = $("#divContenedorRecibidorVisorCalendario");
       capa.fadeOut(500, CargaAjax);
	   function CargaAjax(){
           Ext.get('divContenedorRecibidorVisorCalendario').load(
               {
                   url      : 'calendarioMes.php',
                   scripts  : true,
                   nocache  : true,
                   params   : {
                       ano  	:  Ano,
                       mes  	:  Mes,
					   id_empleado	:  '<?php echo $_POST['id_empleado']; ?>'
                   }
               }
           );
       }
       capa.fadeIn(500);
    }

    function CargaDatos(){

	    Ext.get("Recibidor_Espacios2").load(
            {
                url     : 'calendarioMes_data.php',
                scripts : true,
                nocache : true,
                params  : {
                   fechai 	: fechai,
                   fechaf 	: fechaf,
				   id_empleado	:  '<?php echo $_POST['id_empleado']; ?>'
                }
            }

        )
    }

	function CargaDatosEvent(id){

	    Ext.get("Recibidor_Espacios2").load(
            {
                url     : 'calendarioMes_data_event.php',
                scripts : true,
                nocache : true,
                params  : {
                   fechai 		: 	fechai,
                   fechaf 		: 	fechaf,
				   id_evento	:	id,
				   id_empleado	:  '<?php echo $_POST['id_empleado']; ?>'
                }
            }

        )
    }

	function NuevoRegistro(fecha){
		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Agrega_Registro = new Ext.Window({
			id			: 'Win_Agrega_Registro',
			width		: 625,//400,
			height		: myalto-20, //290,
			plain		: false,
			border		: false,
			//title		: 'Nuevo Registro',
			//iconCls 	: 'add16',
			modal		: true,
			autoScroll	: true,
			closable	: false,
			autoDestroy : true,
			bodyStyle	: "background-color:#FFF",

			autoLoad	:
			{
				url		:'actividades/nuevoRegistro.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							fecha	    :  fecha,
							id_empleado	:  '<?php echo $_POST['id_empleado']; ?>'
						}
			}
		}).show();
		Ext.getCmp('Win_Agrega_Registro').center();
	}

	function EditaRegistro(id,fecha){
		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Agrega_Registro = new Ext.Window({
			id			: 'Win_Agrega_Registro',
			width		: 625,//400,
			height		: myalto-20, //290,
			plain		: false,
			border		: false,
			//title		: 'Nuevo Registro',
			//iconCls 	: 'add16',
			modal		: true,
			autoScroll	: true,
			closable	: false,
			autoDestroy : true,
			bodyStyle	: "background-color:#FFF",

			autoLoad	:
			{
				url		:'actividades/editaRegistro.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							fecha : fecha,
							id	  :	id
						}
			}
		}).show();
		Ext.getCmp('Win_Agrega_Registro').center();
	}

	function BuscaClientesObjetivosProyectos(){
		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Busca_COP = new Ext.Window({
			//id			: 'Win_Agrega_Registro',
			width		: 625,
			height		: myalto - 20,
			plain		: false,
			border		: false,
			//title		: 'Nuevo Registro',
			//iconCls 	: 'add16',
			modal		: true,
			autoScroll	: true,
			closable	: false,
			autoDestroy : true,
			bodyStyle	: "background-color:#FFF",

			autoLoad	:
			{
				url		:'buscaCOP.php',
				scripts	:true,
				nocache	:true,
				params	:
						{

						}
			}
		}).show();
	}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                                                                     //
    //                                  FUNCIONES PARA EL CALENDARIO                                       //
    //                                                                                                     //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    //FUNCION PARA CONVERTIR HORAS A MINUTOS EN FORMATOS 00:00:00
    function horasAminutos(hora){
        var vars = hora.split(':');
        var total = (vars[0]*60) - (-vars[1]);
        return total;
    }
    //FUNCION QUE CALCULA EL NUMERO DE DIAS ENTRE DOS FECHAS EN FORMATO 0000:00:00 -> RETORNA EL NUMERO DE DIAS
    function DiferenciaFechas (fechaf,fechai) {

       var fecha1 = fechaf.split('-');
       var fecha2 = fechai.split('-');
       var miFecha1 = new Date( fecha1[0], fecha1[1], fecha1[2]);// alert(fecha1[0]+'-'+ fecha1[1] +'-'+ fecha1[2] ) ;
       var miFecha2 = new Date( fecha2[0], fecha2[1], fecha2[2]);// alert(fecha2[0]+'-'+ fecha2[1] +'-'+ fecha2[2]) ;
       var diferencia = miFecha1.getTime() - miFecha2.getTime() ;
       var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24)) ;
       var segundos = Math.floor(diferencia / 1000) ;
       return dias ;
    }
    //FUNCION QUE GENERA UN OBJETO CON LA FECHAS, SE RECIBE EN FORMATO 0000:00:00
    function fecha(cadena) {
       var newcadena = cadena.split('-');
       this.dia  = parseInt(newcadena[2]) ;
       this.mes  = parseInt(newcadena[1]) ;
       this.anio = parseInt(newcadena[0]) ;
    }

    //FUNCION PARA SUMAR DIAS A UNA FECHA EN FORMATO 0000:00:00 Y EL NUMERO DE DIAS A SUMAR -> RETORNA LA FECHA CON LOS DIAS SUMADOS
    function SumarDias(fechai,dias) {
        var CuantoDias = parseInt(dias * 24 * 60 * 60 * 1000);
		var newcadena = fechai.split('-');
		fechai=fechai.replace("-", "/").replace("-", "/");
		fechaInicial= new Date(fechai);
		fechaInicial.setDate(fechaInicial.getDate()+dias);
		if(fechaInicial.getDate()<10 ){var d = "0"+fechaInicial.getDate(); }else{var d = fechaInicial.getDate();}
        if(fechaInicial.getMonth()+1<10){var m = "0"+(fechaInicial.getMonth()+1);}else{var m = fechaInicial.getMonth()+1;}
		//alert(fechaInicial.getFullYear()+'-'+m+'-'+d);
        return fechaInicial.getFullYear()+'-'+m+'-'+d;
    }

    //DEFINE EL TEXTO DEL TOOLTIP
   /* function DefineContenido(ElArray){
        var Content = 'Documento No.: <b>'+ElArray['pedido'] +'</b><br />';
        Content += 'Actividad : <b>'+ElArray['actividad'] +'</b><br />';
        Content += 'Empresa : <b>'+ElArray['empresa'] +'</b><br />';
        Content += 'Contacto  : <b>'+ElArray['contacto'] +'</b><br />';
        Content += 'Inicio : '+ElArray['fechai'] +'<br />';
        Content += 'Final  : '+ElArray['fechaf'] +'<br />';
        //Content += ElArray['evento'] +'<br />';
        return Content;
    }*/

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////

    CargaDatos();

</script>