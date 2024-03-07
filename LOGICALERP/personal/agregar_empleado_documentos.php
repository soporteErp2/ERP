<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	if(isset($ID)){
		$SQL1 = "SELECT * FROM empleados_documentos WHERE id_empleado = $ID";
		//echo $SQL1;
		$consul = mysql_query($SQL1,$link);
	}else{
		$ID='false';
	}
?>
<div id="Documentos_Empleado">
    <div style="float:left; width:100%; margin:0 0 0 0">

        <!--<fieldset style="width:690px; height:140px; padding:0px 0px 15px 5px; margin:10 0 0 10; border:1px solid #999">
		<legend><b>Documentos</b></legend>-->
        <div id="DivBtnDocuments" style="float:left; width:100%; margin:0 0 0 0"></div>
        <div id="DivBGrillDocuments" style="float:left; width:100%x;">
            <div id="DIV_contenedor_documentos" class="my_grilla_contenedor" style="width:945px; height:335px" >

                <div id="DIV_titulo_documentos" style="float:left; background-color:#EEEEEE; overflow:hidden;">
                    <div style="float:left; width:100%">
                        <div class="my_grilla_cabezera" style="float:left; width:30px;  ">No.</div>
                        <div class="my_grilla_cabezera" style="float:left; width:300px;  ">Tipo de Documento</div>
                        <div class="my_grilla_cabezera" style="float:left; width:230px; ">Fecha</div>
                        <div class="my_grilla_cabezera" style="float:left; width:45px; text-align:center ">Ver</div>
                        <div class="my_grilla_cabezera" style="float:left; width:45px; text-align:center ">Borrar</div>
                    </div>
                </div>


                <div id="DIV_listado_documentos" style="float:left; overflow:auto; overflow-x:hidden; background-color:#FFFFFF; height:auto; width:945px">
                    <?php

                        $count = 0;
                        while($row = mysql_fetch_array($consul))
                        {
                            $count = $count + 1;
                    ?>
                            <div class="my_grilla_celdas2" id="item_documentos_<?php echo $row['id']; ?>" style="float:left; min-width:945px; width:100%" >
                                <div ondblclick="">
                                    <div class="my_grilla_columna" style="float:left; width:30px;"><?php echo $count;  ?></div>
                                    <div class="my_grilla_celdas" style="float:left; width:300px;" id="tipo_documento_nombre_<?php echo $row['id']; ?>"><?php echo $row['tipo_documento_nombre']; ?></div>
                                    <div class="my_grilla_celdas" style="float:left; width:230px;"><?php echo fecha_larga_hora($row['fecha_creacion']); ?></div>
                                    <div class="my_grilla_celda" style="float:left; width:45px; text-align:center "><img src="../../../temas/clasico/images/BotonesTabs/buscar16.png" width="16" height="16" onClick="ver_documentos_empleado('<?php echo $row['id']; ?>','<?php echo $row['randomico_documento']; ?>','<?php echo $row['nombre_documento']; ?>','<?php echo $row['ext']; ?>')" style="margin:2px 0 0 0; cursor:pointer"></div>
                                    <div class="my_grilla_celda" style="float:left; width:45px; text-align:center "><img src="images/eliminar.png" width="16" height="16" onClick="EliminarDocumento('<?php echo $row['id']; ?>','<?php echo $ID; ?>','<?php  echo $row['tipo_documento_nombre']; ?>','<?php echo $row['randomico_documento'].'_'.$row['id'].'.'.$row['ext']; ?>')" style="margin:2px 0 0 0; cursor:pointer"></div>
                                </div>
                            </div>
                    <?php
                        }
                    ?>
                    <div id="Recibidor_Celda_documentos<?php echo $count; ?>"></div>
                </div>

            </div>
        </div>
        <script>
            var No_Divs_Documentos = <?php echo $count; ?>;
            function Inserta_Div_Documentos(elid){
                Ext.get('Recibidor_Celda_documentos'+No_Divs_Documentos).load(
                    {
                        url		: 'inserta_div_documentos.php',
                        timeout : 180000,
                        scripts	: true,
                        nocache	: true	,
                        params	:
                            {
                                elid		:	elid,
                                id_empleado	: 	<?php echo $ID; ?>
                            }
                    }
                );
                No_Divs_Documentos = No_Divs_Documentos + parseInt(1);
            }
        </script>

        <!--</fieldset> -->
    </div>

</div>
<script>
	new Ext.Panel
	(
		{
			border		: false,
			renderTo	: 'DivBtnDocuments',
			bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			tbar		:
				[
					{
						xtype		: 'button',
						text		: 'Agregar<br />Documentos',
						scale		: 'large',
						iconCls		: 'documentadd',
						iconAlign	: 'top',
						handler 	: function(){BloqBtn(this);cargaDocumentoEmpleado();}
					}
				]
		}
	);


    var myalto2  = Ext.getBody().getHeight();
    var myancho2 = Ext.getBody().getWidth();

    document.getElementById('DIV_contenedor_documentos').style.width = myancho2 - 70;
    document.getElementById('DIV_contenedor_documentos').style.height = myalto - 240;

    Agregar_Autosize("DIV_contenedor_documentos",70,240,"true","true");

    function ver_documentos_empleado(id,randomico_documento,nombre_documento,ext){
        if(!validaImagen(ext)){
            window.open("bd/bd.php?op=descargarArchivo&nombreDocumento="+nombre_documento+"."+ext+"&nombreRandomico="+randomico_documento+"_"+id+"."+ext);
            // window.location.href=ruta+randomico_documento+'_'+id+'.'+ext;
        }
        else{
            if(ext=='pdf'){  viewDocumentoEmpleado(id,randomico_documento,nombre_documento+'.'+ext,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50); return; }
            else{

                Ext.Ajax.request({
                    url     : "bd/bd.php",
                    success : function(response){
                                response  = response.responseText;
                                response  = JSON.parse(response);
                                var alto  = response.alto
                                ,   ancho = response.ancho;
                                if(response.alto<96){ alto=96; }
                                else if(response.alto>Ext.getBody().getHeight()-170){ alto = Ext.getBody().getHeight()-170; }
                                else{ alto += 10; }

                                if(response.ancho<96){ ancho=96; }
                                else if(response.ancho>Ext.getBody().getWidth()-120){ ancho = Ext.getBody().getWidth()-120; }
                                else{ ancho += 10; }

                                alto  += 100;
                                ancho += 70;

                                viewDocumentoEmpleado(id,randomico_documento,nombre_documento+'.'+ext,ext,ancho,alto);
                              },
                    params  :
                    {
                        op     : 'consultaSizeImageDocumentEmpleado',
                        nombre : randomico_documento+'_'+id+'.'+ext
                    }
                });
            }
        }
    }

    function viewDocumentoEmpleado(id,randomico_documento,nombre_documento,ext,width,height){

        var titulo = document.getElementById('tipo_documento_nombre_'+id).innerHTML;
        Win_Ventana_VerDocumento_items = new Ext.Window({
            width       : width,
            height      : height,
            id          : 'Win_Ventana_VerDocumento_items',
            title       : titulo,
            modal       : true,
            autoScroll  : true,
            closable    : true,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    op              : 'ventanaVerImagenDocumentoEmpleado',
                    nombreImage     : randomico_documento+'_'+id+'.'+ext,
                    nombreDocumento : nombre_documento,
                    type            : ext
                }
            },
            tbar        :
            [
                {
                    xtype     : 'button',
                    text      : 'Regresar',
                    scale     : 'large',
                    iconCls   : 'regresar',
                    iconAlign : 'left',
                    handler   : function(){ Win_Ventana_VerDocumento_items.close(); }
                }
            ]
        }).show();
    }

    function validaImagen(ext){
        var arrayTest = Array('bmp','jpg','png','gif','pdf','BMP','JPG','PNG','GIF','PDF');

        for(i=0; i<arrayTest.length; i++){ if(arrayTest[i]==ext){ return true; } }
        return false;
    }


</script>