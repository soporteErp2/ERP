<?php 
$filename = "../../ARCHIVOS_PROPIOS/idioma.xml";
if($_POST['submit'] && $_POST['string'])
{ 
	$textfile= fopen($filename,'w+'); 
	fwrite($textfile, $_POST['string']); 
	fclose($textfile);
} 

$contenido = file_get_contents($filename)
?> 
    <html>
    <body>
    <form method=post action="<?php echo $PHP_SELF; ?>" ?> 
    <textarea name=string cols="68" rows="19"><?php echo $contenido; ?></textarea>
    <input type=submit name=submit value="Guardar">
    <input type=button name=button value="Atras" onclick="volver()"></form>
    </body>
    </html>
<?php 
//} 
?>
<script>function volver(){document.location = "index_conf.php";}</script>
