<?php
	// include_once("../../../configuracion/conectar.php");
	// include_once("../../../configuracion/define_variables.php");


	function responseUnicoRanomico(){

		//Si es un Nuevo Documento Maestro -->
        $random1 = mktime();             //GENERA PRIMERA PARTE DEL ID UNICO

        $chars = array(
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
                'X', 'Y', 'Z', '1', '2', '3', '4', '5',
                '6', '7', '8', '9', '0'
                );
        $max_chars = count($chars) - 1;
        srand((double) microtime()*1000000);
        $random2 = '';
        for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

    	$randomico = $random1.''.$random2; // ID UNICO
    	return $randomico;
	}
?>