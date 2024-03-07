<?php										
	if(isset($_POST)){
		echo 'si';
		$ano = $_POST['ano'];
		$mes = $_POST['mes'];		
	}else{
		$ano = date('Y');
		$mes = date('m');	
	}
    $dia = 1;
    
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
    
    if($TotalDeCeldas > 35){
        $porcentajeH = 16.66666666666667;
    }else{
        $porcentajeH = 20; 
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
  
    echo '<script>
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
        background-color        : #ddd;
		font-family             : "Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";	
		float					: left; 
		width					: 93%; 
		font-size				: 16px; 
		font-weight				: bold; 
		text-align				: center;
		color                   : #777;
	}
    .calTituloDia{
        background-color        : #ddd;
        font-family             : "Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";
        font-size               : 12px;
        color                   : #777;
        margin                  : 0 0 2px 0;
        float                   : left;
        width                    : 14.28571428571429%; /*14.22%;*/ 
        text-align              : center;
		font-weight				: bold;  
    }
    .cal{
        margin                   : 0 0 0 0;
        float                    : left;
        width                    : 14.28571428571429%; /*14.22%;*/ 
        height                   : <?php echo $porcentajeH; ?>%;
        overflow                 : hidden;
        overflow-y               : auto;   
	    background              : -moz-linear-gradient(right bottom, 	#F0F0F0 25%, 	#FFF 100%); /* FF3.6+ */   
        background              : -webkit-linear-gradient(right bottom,  #F0F0F0 25%, 	#FFF 100%); /* Chrome10+,Safari5.1+ */
        background              : -o-linear-gradient(right bottom,  #F0F0F0 25%,	#FFF 100%); /* Opera 11.10+ */
        background              : -ms-linear-gradient(right bottom,  #F0F0F0 25%,	#FFF 100%); /* IE10+ */
        background              : linear-gradient(right bottom,  #F0F0F0 25%,	#FFF 100%); /* W3C */
		/*background              : -webkit-gradient(linear, right bottom, color-stop(25%,#F0F0F0), color-stop(100%,#FFF)); /* Chrome,Safari4+ */           
    }
    .calDiasFilaVacia{
		border:hidden;
		background-color:transparent;
		background:none;
    }
    .calDiasHoy{
        background-color        : #fff4e0;
    }    

    .calNumDia{
        border             		: 1px solid #ddd;
        float                   : left;
        width                   : 15px;
        text-align              : center;
        font-family             : "Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";
        font-size               : 10px;
        -webkit-border-radius   : 1px;         
        -webkit-box-shadow      : 0px 0px 2px #333;
        -moz-border-radius      : 1px;
        -moz-box-shadow         : 0px 0px 2px #333;    
    }
    .calDiasFilaUltimaCelda{
        background              : -moz-linear-gradient(right bottom, 	#f4d4d4 25%, 	#FFF 100%); /* FF3.6+ */ 
        background              : -webkit-linear-gradient(right bottom,  #f4d4d4 25%,	#FFF 100%); /* Chrome10+,Safari5.1+ */
        background              : -o-linear-gradient(right bottom,  #f4d4d4 25%,	#FFF 100%); /* Opera 11.10+ */
        background              : -ms-linear-gradient(right bottom,  #f4d4d4 25%,	#FFF 100%); /* IE10+ */
        background              : linear-gradient(right bottom,  #f4d4d4 25%,	#FFF 100%); /* W3C */
		/* background              : -webkit-gradient(linear, 		right bottom, 	color-stop(25%,#f4d4d4),color-stop(100%,#FFF)); /* Chrome,Safari4+ */
    }
	.calDiasFilaPrimeraCelda{

	}
	
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
</style>


<div style="float:left ; width: 100%; ">
	<div style="background-color: 	#ddd;float:lef; border-bottom: 1px solid #CCC;  padding:3px 0 6px 0;">&nbsp;
        <div style="float:left; cursor: pointer;">
            <img src="images/prev.png" onclick="anterior()" />    
        </div>
        <div class="calTituloMesAno">
            <?php echo $MES[$mes].' '.$ano;?>
        </div>    
        <div style="float:right; cursor: pointer;">
            <img src="images/next.png" onclick="siguiente()" />    
        </div>
    </div>   
</div>


<?php
foreach($ARRDIASSEMANA AS $key){
    echo '<div class="calTituloDia">'.$key.'</div>';
}
echo '<div id="ContenedorDeDiasCalendario" style="float:left; background-image: url(calendario/images/fondo.png);" >';
for($a=1;$a <= $TotalDeCeldas; $a++){
     
    if(!isset($b)){$b = 0;}
    if($b == 7){$b = 0;}
    if(!isset($c)){$c = 1;}
    
    if($a >= $EmpiezaMesCalOffset AND $c <= $TotalDiasMes){
        $styleCal = 'off';        
        if($c == date('d') && $mes == date('m') && $ano == date('Y')){// SI ES HOY
           $styleCal = 'calDiasHoy';
        }
        if($b == 6){ // SI ES DOMINGO
           $styleCal = 'calDiasFilaUltimaCelda';
        }
        $LabelDia = '<div class="calNumDia">'.$c.'</div>';
        $NumeroDia = $c;
        if($c<10){$NumeroDiaCompleto = '0'.$c;}else{$NumeroDiaCompleto = $c;} 
        if($mes<10){$mesCompleto = '0'.$mes;}else{$mesCompleto = $mes;}    
        $c++;
    }else{
        $LabelDia = '';
        $NumeroDiaCompleto = 0;  
        $styleCal  = 'calDiasFilaVacia';
        $NumeroDia = '';
        if($mes<10){$mesCompleto = '0'.$mes;}else{$mesCompleto = $mes;} 
    }
    
    echo '<div class="cal '.$styleCal.' minimal ">
            <div style="float:left;width:100%">'.$LabelDia.'</div>
            <div id="Capa_'.$ano.'-'.$mesCompleto.'-'.$NumeroDiaCompleto.'" style="float:left;width:100%"> </div>
         </div>'; 
         
    $b++;
}
echo '</div>';
?>
<div id="Recibidor_Espacios2"></div>

<script>
	//var myalto  = Ext.getBody().getHeight();
	//var myancho  = Ext.getBody().getWidth();
	//document.getElementById('divContenedorRecibidorVisorCalendario').style.width = myancho ;
	//document.getElementById('divContenedorRecibidorVisorCalendario').style.height = myalto-100;
	//document.getElementById('ContenedorDeDiasCalendario').style.width  = myancho;
	//document.getElementById('ContenedorDeDiasCalendario').style.height = myalto  -209;
    //FUNCION PARA LOS SCROLL
    /*$(document).ready(function() {

        }
    );*/
   
    function anterior(){
		//capa = $("#divContenedorRecibidorVisorCalendario");
		//capa.hide( 'slide', {direction: "Right" }, 300, CargaAjax); 
		function CargaAjax(){
			Ext.get('divContenedorRecibidorVisorCalendario').load(
			   {
				   url      : '<?php echo $_SERVER['SCRIPT_NAME']; ?>',
				   scripts  : true,
				   nocache  : true,
				   params   : 	{
								   ano  	:  AnoAnterior,
								   mes  	:  MesAnterior,
							   }
			   }
			) ;
		}
		CargaAjax();
		//capa.show( 'slide', {direction: "left" }, 300);
    }
    
    function siguiente(){
       //capa = $("#divContenedorRecibidorVisorCalendario");
       //capa.hide( 'slide', {direction: "left" }, 300, CargaAjax);        
	   	function CargaAjax(){
			Ext.get('divContenedorRecibidorVisorCalendario').load(
			   {
				   url      : '<?php echo $_SERVER['SCRIPT_NAME']; ?>',
				   scripts  : true,
				   nocache  : true,
				   params   : {
								   ano  	:  AnoAnterior,
								   mes  	:  MesAnterior,
				   }
			   }
		   );
		}
	   	CargaAjax();
       //capa.show( 'slide', {direction: "Right" }, 300);       
    }
      
</script>