<?php
    include('conectar.php');
    $consul = mysql_query("SELECT color_fondo,color_menu FROM empleados WHERE id = $_SESSION[IDUSUARIO]",$link);
    $_SESSION["COLOR_ESCRITORIO"] = mysql_result($consul,0,"color_fondo");
    $_SESSION["COLOR_MENU"]       = mysql_result($consul,0,"color_menu");  

    /***************  FUNCION PARA CALCULAR EL COLOR DE DEGRADE  ******************/
    function ColorDegrade($color){
        $col = explode(',',$color);
        $col[0] = $col[0]+50; if($col[0]  > 255){$col[0] = 255;}
        $col[1] = $col[1]+50; if($col[1]  > 255){$col[1] = 255;}
        $col[2] = $col[2]+50; if($col[2]  > 255){$col[2] = 255;}
        return $col[0].','.$col[1].','.$col[2];
    }
    $NewColorDegrade = ColorDegrade($_SESSION["COLOR_ESCRITORIO"]);
    /***************  FUNCION PARA CALCULAR EL COLOR DE DEGRADE  ******************/
    
    echo $_SESSION["COLOR_ESCRITORIO"].'{.}'.$_SESSION["COLOR_MENU"].'{.}'.$NewColorDegrade;
?>
