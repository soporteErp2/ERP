<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
<style>
  .tableInforme{
    font-size       : 13px;
    width           : 100%;
    border-collapse : collapse;
  }
  .tableInforme .thead td{
    color : #FFF;
  }
  .tableInforme .thead{
    height      : 25px;
    background  : #999;
    height      : 25px;
    font-size   : 12px;
    color       : #FFF;
    font-weight : bold;
  }
  .tableInforme .total{
    height        : 25px;
    font-weight   : bold;
    color         : #8E8E8E;
  }
  .table thead{
    background : #999;
  }
  .table thead td{
    height       : 30px;
    background   : #999;
    color        : #FFF;
  }
</style>
<input type="text" id="numeroDocumento" value="<?php echo $documento; ?>" style="display:none;">
<div style="margin:10px;padding:5px;">
	<div style="width:98%;float:left;border:#999;background-color:#FFF;padding:5px;"><p><b>Nota:</b> Si desea reenviar el documento electronico a varios destinatarios, por favor escriba cada direccion de correo separado por una coma(,).</p></div>
</div>
<br><br>
<div style="margin:10px;padding:5px;">
	<div style="width: 30%; float: left;"><b>Correo Electronico</b></div>
	<input style="width: 70%; float: left;" id="correoElectronico" type="text" placeholder="correo@direccion.com">
</div>
<br>
<div style="margin:10px;padding:5px;">
	<table class='tableInforme' border='0'>
		<thead class="thead">
			<tr class='total' style='text-align:center;'>
				<td>NOMBRE</td>
        <td>ESTADO</td>
        <td>OPCIONES</td>
			</tr>
		</thead>
		<tbody id="cuerpoTabla" style="background-color:white;text-align:center;">
			<tr style="height:30px;" id="fila0">
        <td id="nombreArchivo0">N/A</td>
				<td id="estadoArchivo0">N/A</td>
				<td id="opcionArchivo0"><input data-cont="0" type="file" accept="*/*" onchange="openFile(event)" id="cargaArchivo0"></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="aqui" style="margin:10px;padding:5px;"></div>
<script>
	var files = [];

  var openFile = function(event){
    var input         = event.target;
    var dataCont      = input.dataset.cont;
    var nombreArchivo = input.files[0].name;
    var reader        = new FileReader();

    reader.onloadend = function(){
      var dataURL   = reader.result;
      var dataState = (reader.readyState == 2)? "OK" : "ERROR";

      if(dataState == "OK"){
      	document.getElementById('nombreArchivo' + dataCont).innerHTML = nombreArchivo;
	      document.getElementById('estadoArchivo' + dataCont).innerHTML = dataState;
	      document.getElementById('opcionArchivo' + dataCont).innerHTML = "<input type='button' value='Eliminar' onclick='eliminarArchivo("+dataCont+")'>";

	      var dataFinal = dataURL.replace(/^data:.+;base64,/, '');

	      files.push({"NombreCompleto" : nombreArchivo, "Base64" : dataFinal, 'id':dataCont});
	      
	      dataCont++;
	      document.getElementById("cuerpoTabla").insertAdjacentHTML("beforeend","<tr style='height:30px;' id='fila"+dataCont+"'><td id='nombreArchivo"+dataCont+"'>N/A</td><td id='estadoArchivo"+dataCont+"'>N/A</td><td id='opcionArchivo"+dataCont+"'><input data-cont='"+dataCont+"' type='file' accept='*/*' onchange='openFile(event)' id='cargaArchivo"+dataCont+"'></td></tr>");
	    }
	    else{
	    	alert('Error al cargar el archivo. Compruebe que no contenga errores.');
	    	return;
	    }
    };
    console.log('agregar');
    console.log(files);
    reader.readAsDataURL(input.files[0]);
  };

  function reenviarCorreoFacturaElectronica(){
  	documento = document.getElementById("numeroDocumento").value;
  	correo    = document.getElementById("correoElectronico").value;
  	archivos  = JSON.stringify(files);

  	if(correo == undefined){
      return;
    }
    else if(correo == ''){
      alert('Por favor digite un correo electronico.');
      return;
    }

  	Win_Ventana_Documentos_FE.close();
  	cargando_documentos('Enviando Documento...','');

  	Ext.Ajax.request({
      url    : 'facturacion_electronica/bd/bd.php',
      params :  {
                  opc             : 'reenviarCorreoFacturaElectronica',
                  numeroDocumento : documento,
                  correo          : correo,
                  archivos        : archivos
                },
      success : function(result,request){
                  alert(result.responseText);
                  document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
                },
      failure : function(){
                  alert('Problema de conexion con el servidor.');
                  document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
                }
    });
  }

  function eliminarArchivo(value){
  	let key = files.findIndex(element => element.id == value);

    files.splice(key,1);

  	fila  = document.getElementById("fila" + value);	
		padre = fila.parentNode;
		padre.removeChild(fila);
  }
</script>
