<div id="RECIBIDOR_FRAME" style="margin:0px; padding:0; width:600px; height:400px; background-image:url(temas/clasico/images/escritorio/fondo.png)">
    <center>
        <div id="loguin-formulario" style="margin:-5px 0 0 -70px;">
            <form method="post">
                <div id="loguin-usuario">
                    <div class="loguin-input-name">
                      <label for="usuario" style="font-size:14px">Usuario:</label>
                    </div>
                    <div class="loguin-input-text-div">
                        <input name="usuario_cf" type="text" class="loguin-input-text" id="usuario_cf" style="width:260px;"/>
                    </div>
                    <div id="flecha1" class="loguin-flecha"><img id="img1" src="s.gif" /></div>
                </div>
                <div id="loguin-password">
                    <div class="loguin-input-name">
                      <label for="password" style="font-size:14px">Contrase&ntilde;a:</label>
                    </div>
                    <div class="loguin-input-text-div">
                        <input name="password_cf" type="password" class="loguin-input-text" id="password_cf" style="width:260px;"/>
                    </div>
                    <div id="flecha2" class="loguin-flecha"><img id="img2" src="s.gif"/></div>
                </div>
        
                <div id="loguin-entrar" style="width:400px">
                    <input type="button"  name="entrar" id="entrar" value="Entrar a la Configuracion" onClick="validar_configuracion()" />
                </div>
            </form>
        </div>
    </center>
</div>

<script>
function validar_configuracion(){
	if(document.getElementById('usuario_cf').value == 'soporte' && document.getElementById('password_cf').value == 'chkdsk'){
	
		Ext.get("RECIBIDOR_FRAME").load
		(
			{
			url: 'configuracion/ventana_configuracion/frame.php',
			scripts: true		
			}
		);
		
	}else{
		document.getElementById('usuario_cf').value = '';
		document.getElementById('password_cf').value = '';
	}
}
</script>