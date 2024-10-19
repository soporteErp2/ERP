<?php
    include('../../configuracion/conectar.php');
    include('../../configuracion/define_variables.php');
    //include('../lenguaje/es.php');

    if(!$_GET['autorizado']){ header ("Location: /"); }
?>

    <link rel="stylesheet" type="text/css" href="../../misc/extjs3/resources/css/ext-all.php"/>
    <link rel="stylesheet" type="text/css" href="index.css"/>
    <link rel="stylesheet" type="text/css" href="../../misc/MyGrilla/MyGrilla.css"/>
    <link rel="stylesheet" type="text/css" href="../../temas/clasico/estilo.php"/>
	<script type="text/javascript" src="../../misc/extjs3/ext-base.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/extjs3/ext-all.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/lib.js?v4.0.0.12-05-2013"></script>
	<script type="text/javascript" src="../../misc/ckeditor/ckeditor.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/MyFunctions.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/dragresize/dragresize.js"></script>

	<!--##########################  jQUERY ColoPicker ###############################-->
	<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>-->
	<script type="text/javascript" src="../../misc/jquery/1.5.2/jquery.min.js"></script>
    <script type="text/javascript" src="../../misc/jscolor/jquery.ps-color-picker.min.js"></script>
    <!--############################################################################-->

    <!--##########################  File Upload Ajax ###############################-->
	<script src="../../misc/upload2/fileuploader.js" type="text/javascript"></script>
    <!--############################################################################-->

    <!--##########################  Select con buscador ###############################-->
    <script src="../../misc/jquery-editable-select/jquery.js" type="text/javascript"></script>
    <script src="../../misc/jquery-editable-select/jquery.editable-select.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../misc/jquery-editable-select/jquery.editable-select.css" type="text/css" />

    <!-- // <script type="text/javascript" src="cssrefresh/cssrefresh.js"></script> -->
    <link rel="stylesheet" href="../../assets/css/tailwind.css">

<?php include('subindex.php') ?>