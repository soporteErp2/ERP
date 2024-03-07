<?php
    include('../../../configuracion/conectar.php');
    include('../../../configuracion/define_variables.php');
    //include('../lenguaje/es.php');
    if(!$_GET['autorizado']){
    	header ("Location: /");
    }

?>

    <link rel="stylesheet" type="text/css" href="../../../misc/extjs3/resources/css/ext-all.php"/>
    <!--<script type="text/javascript" src="js/cssrefresh.js"></script>-->

    <!-- #########################  CSS CALENDARIO  ############################# -->
    <link rel="stylesheet" type="text/css" href="index.css"/>
    <!-- ######################################################################## -->

    <link rel="stylesheet" type="text/css" href="../../../misc/MyGrilla/MyGrilla.css"/>
    <link rel="stylesheet" type="text/css" href="../../../temas/clasico/estilo.php"/>
	<script type="text/javascript" src="../../../misc/extjs3/ext-base.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../../misc/extjs3/ext-all.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../../misc/lib.js?v4.0.0.12-05-2013"></script>
	<script type="text/javascript" src="../../../misc/ckeditor/ckeditor.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../../misc/MyFunctions.js?v4.0.0.12-05-2013"></script>

    <!--##########################  jQUERY   ###############################-->
    <link rel="stylesheet" type="text/css" href="../../../misc/jquery/1.8.19/themes/cupertino/jquery.ui.all.css">
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.ui.mouse.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.ui.autocomplete.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.ui.slider.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.effects.core.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.effects.slide.js"></script>
    <script type="text/javascript" src="../../../misc/jquery/1.8.19/ui/jquery.ui.dialog.js"></script>
    <!--############################################################################-->

    <!--##########################  PICKER HORAS ###############################-->
    <link rel="stylesheet" type="text/css" href="../../../misc/clockpicker/clockpicker.css"/>
    <link rel="stylesheet" type="text/css" href="../../../misc/clockpicker/standalone.css"/>
    <script src="../../../misc/clockpicker/clockpicker.js" type="text/javascript"></script>
    <!--############################################################################-->

    <!--##########################  PICKER COLORES ###############################-->
    <link rel="stylesheet" type="text/css" href="../../../misc/colorbox/colorbox.css"/>
    <script src="../../../misc/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>
    <!--############################################################################-->

    <!--<script type="text/javascript" src="../../misc/dragresize/dragresize.js"></script>-->

	<!--##########################  jQUERY ColoPicker ###############################-->
	<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>-->
	<!-- <script type="text/javascript" src="../../misc/jquery/1.5.2/jquery.min.js"></script>
    <script type="text/javascript" src="../../misc/jscolor/jquery.ps-color-picker.min.js"></script> -->
    <!--<link rel="stylesheet" type="text/css" href="../../misc/jquery/1.8.19/themes/cupertino/jquery.ui.all.css">
    <script type="text/javascript" src="../../misc/jquery/1.8.19/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.ui.mouse.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.ui.autocomplete.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.ui.slider.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.effects.core.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.effects.slide.js"></script>
    <script type="text/javascript" src="../../misc/jquery/1.8.19/ui/jquery.ui.dialog.js"></script>-->

    <!--<link rel="stylesheet" type="text/css" href="../../misc/jquery/clockpick/jquery.clockpick.1.2.9.css"/>
    <script type="text/javascript" src="../../misc/jquery/clockpick/jquery.clockpick.1.2.9.js"></script>-->
    <!--############################################################################-->

    <!--##########################  File Upload Ajax ###############################-->
	<script src="../../../misc/upload2/fileuploader.js" type="text/javascript"></script>
    <!--############################################################################-->

    <!--#####################  Ventana Busqueda Funcionarios #######################-->
    <script src="../../funcionarios/busquedaFuncionarios.js" type="text/javascript"></script>
    <!--############################################################################-->

<?php include('subindex.php') ?>
