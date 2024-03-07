<?php
	include('../../../configuracion/define_variables.php');

	if(isset($op) && $op == "guardaFile"){//VARIABLE QUE INDICA QUE DEBE GUARDAR EL ARCHVIO CON EL CONTENIDO EN DISCO
		$file = "temp/".$nombre;
		if (file_exists($file)) {
			$fp = fopen("temp/".$nombre, "w");
		} else {
			file_put_contents("temp/$nombre.pdf","");
			$fp = fopen("temp/".$nombre, "w");
		}

		fwrite($fp, $html);		
		fclose($fp);

	}else{//SI LA VARIABLE DE GUARDADO DE CONTENIDO NO EXISTE GENERA EL PDF

		set_time_limit(240);
		ini_set("memory_limit","500M");

		$file     = "temp/".$nombre;
		$fp       = fopen("temp/".$nombre, "r");
		$contents = fread($fp, filesize($file));
		fclose($fp);
		unlink($file);

		$params = base64_decode($params);
		$options = json_decode($params,true);
		$texto   = base64_decode($contents);

		if($options["orientacion"] == "V"){$orientacion = "P";}
		if($options["orientacion"] == "H"){$orientacion = "L";}
		if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
		if($options["debug"] == "false"){
			include("../../../misc/MPDF54/mpdf.php");
			$mpdf = new mPDF(
				"utf-8",  						// mode - default "
				'A4',	// format - A4, for example, default "
				12,								// font size - default 0
				"",								// default font family
				$options["margins"]["left"],	// margin_left
				$options["margins"]["right"],	// margin right
				$options["margins"]["top"],		// margin top
				$options["margins"]["bottom"],	// margin bottom
				3,								// margin header
				10,								// margin footer
			    $orientacion    				// orientacion
			);
			$mpdf->SetAutoPageBreak(TRUE, 15);
			$mpdf->SetTitle ("GENERADOR DE INFORMES LOGICALSOFT");
			$mpdf->SetAuthor ( "LOGICALSOFT" );
			$mpdf->SetDisplayMode ( "fullpage" );
			$mpdf->SetHeader("");
			$mpdf->WriteHTML(utf8_encode($texto));
			if($options["op"]=="view"){$mpdf->Output($nombre.".pdf","I");}
			if($options["op"]=="download"){$mpdf->Output($nombre.".pdf","D");}
			exit;

		}else{echo $texto;}

	}
?>