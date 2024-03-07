<?php
    include_once('../../../configuracion/conectar.php');
    include_once('../../../configuracion/define_variables.php');

    $ruta_img_urema   = ($IMPRIME_PDF == 'true') ? '../../img/logo_urema.png' : '../informes/img/logo_urema.png';
    $ruta_img_reserva = ($IMPRIME_PDF == 'true') ? '../../img/logo_reserva.png' : '../informes/img/logo_reserva.png';

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=balance_general.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $calidad = '';

    $sqlEmpleado   = "SELECT * FROM empleados WHERE id='$id' AND activo=1";
    $queryEmpleado = mysql_query($sqlEmpleado,$link);

    if(mysql_num_rows($queryEmpleado)==0){ echo 'No se encontraron resultados'; exit; }

    $id_empleado                  = mysql_result($queryEmpleado, 0, 'id');
    $nombre1                      = mysql_result($queryEmpleado, 0, 'nombre1');
    $nombre2                      = mysql_result($queryEmpleado, 0, 'nombre2');
    $apellido1                    = mysql_result($queryEmpleado, 0, 'apellido1');
    $apellido2                    = mysql_result($queryEmpleado, 0, 'apellido2');
    $tipo_documento_nombre        = mysql_result($queryEmpleado, 0, 'tipo_documento_nombre');
    $documento                    = mysql_result($queryEmpleado, 0, 'documento');
    $nombre_ciudad_identificacion = mysql_result($queryEmpleado, 0, 'ciudad_documento');
    $mail2                        = mysql_result($queryEmpleado, 0, 'email_personal');
    $sexo                         = mysql_result($queryEmpleado, 0, 'sexo');
    $nacimiento                   = mysql_result($queryEmpleado, 0, 'nacimiento');
    $estado_civil                 = mysql_result($queryEmpleado, 0, 'estado_civil');
    $direccion                    = mysql_result($queryEmpleado, 0, 'direccion');
    $departamento_residencia      = mysql_result($queryEmpleado, 0, 'departamento');
    $ciudad_residencia            = mysql_result($queryEmpleado, 0, 'ciudad');
    $barrio_residencia            = mysql_result($queryEmpleado, 0, 'barrio');
    $telefono1                    = mysql_result($queryEmpleado, 0, 'telefono1');//telefono residencia
    $celular1                     = mysql_result($queryEmpleado, 0, 'celular1');//celular
    $estrato_residencia           = mysql_result($queryEmpleado, 0, 'estrato_residencia');
    $tipo_residencia              = mysql_result($queryEmpleado, 0, 'tipo_residencia');
    $direccion_comercial          = mysql_result($queryEmpleado, 0, 'direccion_comercial');
    $telefono_comercial           = mysql_result($queryEmpleado, 0, 'telefono_comercial');
    $ciudad_comercial             = mysql_result($queryEmpleado, 0, 'ciudad_comercial');
    $mail_empresa                 = mysql_result($queryEmpleado, 0, 'email_empresa');//mail comercial
    $correo_electronico2          = mysql_result($queryEmpleado, 0, 'correo_electronico2');
    $observaciones_empleado       = mysql_result($queryEmpleado, 0, 'observaciones_empleado');
    $eps                          = mysql_result($queryEmpleado, 0, 'eps');
    $arp                          = mysql_result($queryEmpleado, 0, 'arp');


    $sqlAdicional   = "SELECT * FROM empleados_adicional WHERE id_empleado='$id_empleado'";
    $queryAdicional = mysql_query($sqlAdicional,$link);

    $fondo_pension   = mysql_result($queryAdicional, 0, 'fondo_pension');
    $ciudad_trabajo  = mysql_result($queryAdicional, 0, 'ciudad_trabajo');
    $tipo_sangre     = mysql_result($queryAdicional, 0, 'tipo_sangre');


    //INFORMACION PERSONAL
    $queryImage = mysql_query("SELECT foto FROM empleados WHERE id=$id_empleado LIMIT 0,1",$link);
    $rowImage   = mysql_fetch_array($queryImage);
    $imageData  = $rowImage['foto'];

    if($imageData==''){ $imageResul = ($IMPRIME_PDF == 'true') ? '<img src="../images/foto0.png" />' : '<img src="../personal/images/foto0.png" />'; }
    else{
        ob_start();
        imagejpeg($imageData, null, 80);
        $data = ob_get_contents();
        ob_end_clean();
        $imageResul= '<img src="data:image/jpg;base64,'.base64_encode($imageData).'" style="width:98px; height:130px;"/>';
    }

    $header =   '<table style="width:740px; font-size:11px; border:1px solid #000; ">
                     <tr>
                         <td style="width:227px; height:43px; border-right:1px solid #000; text-align:center;padding-top:2px"><img src = "../images/plataforma_LOGO.png" alt="logo corporativo" style="width:200px; height:40px;"></td>

                         <td style="width:287px;height:31px; text-align:center;padding-top:14px;font-size:13px;font-weight:bold">HOJA DE VIDA</td>

                         <td style="width:226px;padding-left:5px; font-weight:bold;border-left:none">' . $calidad . '</td>
                     </tr>
                  </table>';


    $body_personal =   '<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>I&nbsp;&nbsp;&nbsp;&nbsp;INFORMACION PERSONAL</b></div>
                        <div style="width:15%;float:left; padding-top:10px;">
                            '.$imageResul.'
                            <div style="font-size:9px;"></div>
                        </div>
                        <div style="width:85%;float:left;height:100px; padding-top:10px;">
                            <div style="width:50%;float:left;">
                                <b>Primer Nombre</b>
                                <span style="margin-left:10px;">'.$nombre1.'</span>
                            </div>

                             <div style="width:50%;float:left;">
                                <b>Segundo Nombre</b>
                                <span style="margin-left:10px;">'.$nombre2.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Primer Apellido</b>
                                <span style="margin-left:10px;">'.$apellido1.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Segundo Apellido</b>
                                <span style="margin-left:10px;">'.$apellido2.'</span>
                            </div>

                            <div style="width:50%;float:left; margin-top: 5px;">
                                <b>Identificacion</b> '.$tipo_documento_nombre.'&nbsp;&nbsp;'.$documento.' <b>De</b>&nbsp;&nbsp;'.$nombre_ciudad_identificacion.'
                            </div>

                             <div style="width:50%;float:left;">
                                <b>Sexo</b>
                                <span style="margin-left:10px;">'.$sexo.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Correo Electronico</b>
                                <span style="margin-left:10px;">'.$mail2.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Estado Civil</b>
                                <span style="margin-left:10px;">'.$estado_civil.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Fecha Nacimiento</b>
                                <span style="margin-left:10px;">'.$nacimiento.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Ciudad de Trabajo</b>
                                <span style="margin-left:10px;">'.$ciudad_trabajo.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>EPS</b>
                                <span style="margin-left:10px;">'.$eps.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>ARP</b>
                                <span style="margin-left:10px;">'.$arp.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Fondo de Pensiones</b>
                                <span style="margin-left:10px;">'.$fondo_pension.'</span>
                            </div>

                            <div style="width:50%;float:left;">
                                <b>Tipo de Sangre</b>
                                <span style="margin-left:10px;">'.$tipo_sangre.'</span>
                            </div>

                        </div>';

    //INFORMACION DIRECCIONES
    $body_direcciones = '<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>II&nbsp;&nbsp;&nbsp;DIRECCIONES</b></div>

                        <div style="overflow:hidden; width:100%;">
                            <div style="width:10%;float:left;"><b>Residencia</b></div>
                            <div style="width:90%;float:left;">
                                <div style="width:50%;float:left;">
                                    <b>Direccion</b>
                                    <span style="margin-left:10px;">'.$direccion.'</span>
                                </div>
                                <div style="width:50%;float:left;">
                                    <b>Barrio</b>
                                    <span style="margin-left:10px;">'.$barrio_residencia.'</span>
                                </div>

                                <div style="width:50%;float:left;">
                                    <b>Departamento</b>
                                    <span style="margin-left:10px;">'.$departamento_residencia.'</span>
                                </div>
                                <div style="width:50%;float:left;">
                                    <b>Ciudad</b>
                                    <span style="margin-left:10px;">'.$ciudad_residencia.'</span>
                                </div>

                                <div style="width:50%;float:left;">
                                    <b>Telefono</b>
                                    <span style="margin-left:10px;">'.$telefono1.'</span>
                                </div>
                                <div style="width:50%;float:left;">
                                    <b>Celular</b>
                                    <span style="margin-left:10px;">'.$celular1.'</span>
                                </div>

                                <div style="width:50%;float:left;">
                                    <b>Estrato</b>
                                    <span style="margin-left:10px;">'.$estrato_residencia.'</span>
                                </div>
                                <div style="width:50%;float:left;">
                                    <b>Vivienda</b>
                                    <span style="margin-left:10px;">'.$tipo_residencia.'</span>
                                </div>
                            </div>
                        </div>
                        ';

    //INFORMACION FAMILIAR
    $body_padre    = '';
    $body_madre    = '';
    $body_esposa   = '';
    $body_hijo     = '';
    $body_familiar = '';
    $sqlFamiliar   = "SELECT * from empleados_informacion_contacto WHERE id_empleado='$id_empleado' AND parentesco <> 'Referencia Personal' AND activo=1 ORDER BY parentesco DESC";
    $queryFAmiliar = mysql_query($sqlFamiliar,$link);

    if(mysql_num_rows($queryFAmiliar)>0){ $body_familiar.='<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>III&nbsp;&nbsp;INFORMACION DE CONTACTO</b></div>'; }

    while($rowFamiliar=mysql_fetch_array($queryFAmiliar)){
        $title = ($rowFamiliar["parentesco"] == 'Hijo' && $body_hijo <> '')? '&nbsp;': $rowFamiliar["parentesco"];

        if($rowFamiliar["parentesco"] == 'Padre'){ $body_padre .= armaBodyFamiliar($title,$rowFamiliar); }
        else if($rowFamiliar["parentesco"] == 'Madre'){ $body_madre .= armaBodyFamiliar($title,$rowFamiliar); }
        else if($rowFamiliar["parentesco"] == 'Esposa'){ $body_esposa .= armaBodyFamiliar($title,$rowFamiliar); }
        else if($rowFamiliar["parentesco"] == 'Hijo'){ $body_hijo .= armaBodyFamiliar($title,$rowFamiliar); }
        else if($rowFamiliar["parentesco"] == 'Referencia Laboral'){ $body_laboral .= armaBodyFamiliar('',$rowFamiliar); }

    }

    function calculaEdad($fechaNacimiento){
        if($fechaNacimiento == '') return;
        list($ano,$mes,$dia) = explode("-",$fechaNacimiento);

        $ano_diferencia = date("Y") - $ano;
        $mes_diferencia = date("m") - $mes;
        $dia_diferencia = date("d") - $dia;

        if ($dia_diferencia < 0 || $mes_diferencia < 0) $ano_diferencia--;
        return $ano_diferencia;
    }

    function armaBodyFamiliar($title,$rowFamiliar){

        $fila_laboral = '';

        if($title != ''){
            $fila_laboral = '<div style="width:33%;float:left;">
                                <b>Principal</b>
                                <span style="margin-left:5px;">'.$rowFamiliar["contacto_principal"].'</span>
                            </div>';
        }
        else{
            $title = '&nbsp;';

        }

        return $salto.'<div style="width:10%;float:left;padding-bottom:15px">
                    <div><b>'.$title.'</b></div>
                </div>
                <div style="width:45%;float:left;height:45px;">
                    <div style="width:100%;float:left;">
                        <b>Nombre</b>
                        <span style="margin-left:5px;">'.$rowFamiliar["nombres"].'</span>
                    </div>
                    <div style="width:100%;float:left;">
                        <b>Apellidos</b>
                        <span style="margin-left:5px;">'.$rowFamiliar["apellidos"].'</span>
                    </div>
                    <div style="width:100%;float:left;">
                        <b>'.$rowFamiliar["tipo_identificacion"].'</b>
                        <span style="margin-left:5px;">'.$rowFamiliar["numero_identificacion"].'</span>
                    </div>
                </div>
                <div style="width:45%;float:left;height:45px;">
                    <div style="width:100%;float:left;">
                        <b>Ocupacion</b>
                        <span style="margin-left:5px;">'.$rowFamiliar["ocupacion"].'</span>
                    </div>
                    <div style="width:100%;float:left;">
                        <b>Direcci&oacute;n</b>
                        <span style="margin-left:5px;">'.$rowFamiliar["direccion"].'</span>
                    </div>
                    '.$fila_laboral.'

                </div>';
    }

    $body_familiar .= $body_padre.$body_madre.$body_esposa.$body_hijo;

    //REFERENCIAS PERSONALES

     $sqlReferencias   = "SELECT * FROM empleados_informacion_contacto WHERE id_empleado='$id_empleado' AND activo=1 AND parentesco = 'Referencia Personal'";
    $queryReferencias = mysql_query($sqlReferencias,$link);

    if(mysql_num_rows($queryReferencias)>0){ $body_referencias.='<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>IV&nbsp;&nbsp;REFERENCIAS PERSONALES</b></div>'; }

    while($rowReferencias=mysql_fetch_array($queryReferencias)){

        $body_referencias .= armaBodyFamiliar('',$rowReferencias);
        //else if($rowReferencias["parentesco"] == 'Madre'){ $body_madre .= armaBodyReferencias($title,$rowFamiliar); }


    }



    //INFORMACION ACADEMICA
    $body_estudios  = '';
    $sqlAcademico   = "SELECT * from empleados_estudios WHERE id_empleado='$id_empleado' AND activo=1";
    $queryAcademico = mysql_query($sqlAcademico,$link);

    if(mysql_num_rows($queryAcademico)>0){ $body_estudios.='<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>V&nbsp;&nbsp;&nbsp;INFORMACION ACADEMICA</b></div>'; }

    while($rowAcademico=mysql_fetch_array($queryAcademico)){

        $modalidad = ($rowAcademico["modalidad_presencial"]=="Otro") ? $rowAcademico["otra_modalidad"] : $rowAcademico["modalidad_presencial"];//si la modalidad es Otro que muestre cual es.
        $label_tarjeta_profesional = ($rowAcademico["tipo_estudio"]=="Universitario Pregrado") ? "Tarjeta Profesional" : "&nbsp;";//si es Pregrado que muestre el label: Tarjeta Profesional

        $body_estudios .= '<br>
                            <div style="width:10%;float:left;">
                                <div><b>'.$rowAcademico["tipo_estudio"].'</b></div>
                            </div>
                            <div style="width:45%;float:left;height:55px;">
                                <div style="width:100%;float:left;">
                                    <b>Institucion</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["institucion"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>Fecha Inicio</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["fecha_inicio"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>Grado</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["grado"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>Modalidad</b>
                                    <span style="margin-left:5px;">'.$modalidad.'</span>
                                </div>
                            </div>

                            <div style="width:45%;float:left;height:55px;">
                                <div style="width:100%;float:left;">
                                    <b>Ciudad</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["ciudad"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>Fecha Fin</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["fecha_fin"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>Ciclo</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["ciclo"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>'.$label_tarjeta_profesional.'</b>
                                    <span style="margin-left:5px;">'.$rowAcademico["tarjeta_profesional"].'</span>
                                </div>
                            </div>';
    }

    //IDIOMAS
    $body_idiomas  = '';
    $sqlIdiomas   = "SELECT * from empleados_idiomas WHERE id_empleado='$id_empleado' AND activo=1";
    $queryIdiomas = mysql_query($sqlIdiomas,$link);

    if(mysql_num_rows($queryIdiomas)>0){ $body_idiomas.='<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>VI&nbsp;&nbsp;&nbsp;IDIOMAS</b></div>'; }

    while($rowIdiomas=mysql_fetch_array($queryIdiomas)){

        // $modalidad = ($rowIdiomas["modalidad_presencial"]=="Otro") ? $rowIdiomas["otra_modalidad"] : $rowIdiomas["modalidad_presencial"];//si la modalidad es Otro que muestre cual es.
        // $label_tarjeta_profesional = ($rowIdiomas["tipo_estudio"]=="Universitario Pregrado") ? "Tarjeta Profesional" : "&nbsp;";//si es Pregrado que muestre el label: Tarjeta Profesional

        if($rowIdiomas["nativo"] == 'si'){
            $fila_nativo = '';
        }
        else{
            $fila_nativo = '<div style="width:100%;float:left;">
                                <b>Institucion</b>
                                <span style="margin-left:5px;">'.$rowIdiomas["institucion"].'</span>
                            </div>
                            <div style="width:100%;float:left;">
                                <b>Ciudad</b>
                                <span style="margin-left:5px;">'.$rowIdiomas["ciudad"].'</span>
                            </div>';
        }


        $body_idiomas .= '<br>
                            <div style="width:10%;float:left;">
                                <div><b>'.$rowIdiomas["idioma"].'</b></div>
                            </div>
                            <div style="width:45%;float:left;height:55px;">
                                <div style="width:100%;float:left;">
                                    <b>Nativo</b>
                                    <span style="margin-left:5px;">'.$rowIdiomas["nativo"].'</span>
                                </div>
                                '.$fila_nativo.'
                                <div style="width:100%;float:left;">
                                    <b>Lectura</b>
                                    <span style="margin-left:5px;">'.$rowIdiomas["lectura"].'</span>
                                </div>
                            </div>
                             <div style="width:45%;float:left;height:55px;">
                                <div style="width:100%;float:left;">
                                    <b>Escritura</b>
                                    <span style="margin-left:5px;">'.$rowIdiomas["escritura"].'</span>
                                </div>
                                <div style="width:100%;float:left;">
                                    <b>Habla</b>
                                    <span style="margin-left:5px;">'.$rowIdiomas["habla"].'</span>
                                </div>
                            </div>
                            ';
    }

    //INFORMACION LABORAL
    $body_laboral = '';
    $sqlLaboral   = "SELECT * from empleados_experiencia_laboral WHERE id_empleado='$id_empleado' AND activo=1";
    $queryLaboral = mysql_query($sqlLaboral,$link);

    if(mysql_num_rows($queryLaboral)>0){ $body_laboral.='<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>VII&nbsp;&nbsp;&nbsp;&nbsp;EXPERIENCIA LABORAL</b></div>'; }

    while($rowLaboral=mysql_fetch_array($queryLaboral)){

        $body_laboral .= '<br><div style="100%">
                            <div style="width:10%;float:left;height:95px;">
                                <div><b>'.$rowLaboral["empresa"].'</b></div>
                            </div>

                                <div style="width:45%;float:left;height:95px;">
                                    <div style="width:100%;float:left;">
                                        <b>Empresa</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["nombre_empresa"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Breve Actividad</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["actividad"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Fecha Inicio</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["fecha_inicio"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Jefe Inmediato</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["jefe_inmediato"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Tipo de salario</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["salario"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Otros ingresos</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["otros_ingresos"].'</span>
                                    </div>
                                </div>

                                <div style="width:45%;float:left;height:95px;">
                                    <div style="width:100%;float:left;">
                                        <b>Ciudad</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["ciudad"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Cargo</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["cargo"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Fecha Terminacion</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["fecha_fin"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Tel&eacute;fono</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["telefono"].'</span>
                                    </div>
                                    <div style="width:100%;float:left;">
                                        <b>Salario Mensual</b>
                                        <span style="margin-left:5px;">'.$rowLaboral["salario_mensual"].'</span>
                                    </div>
                                </div>
                            </div>';
    }

    $body_observaciones = '';

    if($observaciones_empleado != ''){ $body_observaciones .='<div style="height:15px;width:100%;float:left;border-bottom:1px solid black; margin-top:15px;"><b>VIII&nbsp;&nbsp;&nbsp;&nbsp;OBSERVACIONES</b></div>'; }

    $body_observaciones .= '<div style="width:10%;float:left;">
                                <div><b>&nbsp;</b></div>
                            </div>
                                <div style="width:100%;float:left;">
                                    <div style="width:100%;float:left;padding-left:76px">
                                        <span style="margin-left:120px;">'.$observaciones_empleado.'</span>
                                    </div>
                                </div>
                            ';

    $body = '<div style="width:100%; font-family:sans-serif;  min-height:800px;">
                '.$body_personal.'
                '.$body_direcciones.'
                '.$body_familiar.'
                '.$body_referencias.'
                '.$body_estudios.'
                '.$body_idiomas.'
                '.$body_laboral.'
                '.$body_observaciones.'
            </div>';

    $IMPRIME_PDF = 'true';

	if($IMPRIME_PDF == 'true'){
        if(isset($TAM)){ $HOJA = $TAM; }
        else{ $HOJA = 'LETTER'; }

        if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
        if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
        if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

        if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
        else{ $MS=30; $MD=10; $MI=10; $ML=10; }

        if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

		include("../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
					'utf-8',  		// mode - default ''
					$HOJA,			// format - A4, for example, default ''
					8,				// font size - default 0
					'',				// default font family
					$MI,			// margin_left
					$MD,			// margin right
					$MS,			// margin top
					$ML,			// margin bottom
					10,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);
        // $mpdf->debug = true;
        // $mpdf->showImageErrors = true;
        $mpdf->useSubstitutions = true;
        $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		// $mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHtmlHeader($header);
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($body));

		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }
	}
    else{ echo $body; }
?>