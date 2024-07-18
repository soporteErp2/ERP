<?php
include('../../configuracion/conectar.php');
include('../../configuracion/define_variables.php');
//include('../lenguaje/es.php');
if(!$_GET['autorizado']){
	header ("Location: /");
}
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
    <script type="text/javascript" src="../../misc/jquery/1.5.2/jquery.min.js?"></script>

    <!-- /////////////////////////////////// COLOR PICKER EN RGB ////////////////////////////////// -->
    <link rel="stylesheet" href="libColorPicker/css/colorpicker.css" type="text/css" />
    <link rel="stylesheet" media="screen" type="text/css" href="libColorPicker/css/layout.css" />

    <script type="text/javascript" src="libColorPicker/js/colorpicker.js"></script>
    <script type="text/javascript" src="libColorPicker/js/eye.js"></script>
    <script type="text/javascript" src="libColorPicker/js/utils.js"></script>
    <script type="text/javascript" src="libColorPicker/js/layout.js"></script>
    <!-- ////////////////////////////////////////////////////////////////////////////////////////// -->


<html>
    <body>
        <?php include('subindex.php') ?>
    </body>
</html>
