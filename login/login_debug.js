//ARCHIVO OBFUSCADO CON
//http://www.jsobfuscate.com/index.php
//Encoding: NORMAL
//Fast Decode: TRUE
//Special Characters: TRUE  

	var IdEmpresa = 0;
	var cookie = 'false';
	EmpresaFocus = 0;
	UltimaTecla = 0;
	
	function HabilitaCampos(como){
		document.getElementById('usuario').readOnly = como;
		document.getElementById("password").readOnly = como;	
	}
	
	function VaciarDatos(){
		document.getElementById('usuario').value = "";
		document.getElementById("password").value = "";
	}

    function consulta_empresa()
    {
		var empresa = document.getElementById('empresa').value;
		
		if(empresa != ''){
		
			Ext.Ajax.request(
				{
					url: 'login/consulta_empresa.php',
					method: 'POST',
					params:
					{
						empresa: empresa
					},
					success: function (result, request)
					{
						resultado = result.responseText.split('{.}');
						if (resultado[0] == 'true' || resultado[0] == true){
							
							IdEmpresa 		= resultado[2];
							alertify.success('<div class="DivNotify"><img alt="Empresa" src="login/images/empresaB.png" width="32" height="32"/></di><div class="DivNotify">'+resultado[1]+'</div>');
							HabilitaCampos(false);
							
							/////GENERA COMBO DE SUCURSALES/////
							if(resultado[3]=='combo'){
								var ElId 	 = resultado[4].split(',');
								var ElNombre = resultado[5].split(',');
							}
							if(resultado[3]=='field'){
								var ElId 	 = resultado[4];
								var ElNombre = resultado[5];												
							}
							GenerarComboBoxSucursal(resultado[3],ElId,ElNombre);
							//////////////////////////////////////
							
						}else{
							NombreDeEmpresa = 'No Existe';
							IdEmpresa = 0;
							alertify.error('<div class="DivNotify"><img alt="Empresa" src="login/images/empresaB.png" width="32" height="32"/></di><div class="DivNotify">'+resultado[1]+'</div>');
							document.getElementById('empresa').focus();
							HabilitaCampos(true);
							
							GenerarComboBoxSucursal('false','false','false');
						}
					}
				}
			);
		}else{
			HabilitaCampos(true);		
		}
    }
	
	function GenerarComboBoxSucursal(tipo,ElId,ElNombre){
		
		cual = document.getElementById('sucursal');
		padre = cual.parentNode;
		padre.removeChild(cual);
		if(document.getElementById('sucursalnombre')){
			cual = document.getElementById('sucursalnombre');
			padre = cual.parentNode;
			padre.removeChild(cual);
		}
		
		if(tipo == 'field'){
			
			padre.className = "FieldDiv";
			var CapaContenedora = document.getElementById("loguinSucursal");
			var a = document.createElement("input");
			a.setAttribute('type','hidden');
			a.setAttribute('class','mytext');
			a.setAttribute('name','sucursal');
			a.setAttribute('id','sucursal');
			a.setAttribute('value',ElId);
			CapaContenedora.appendChild(a);
			var b = document.createElement("input");
			b.setAttribute('type','text');
			b.setAttribute('class','mytext');
			b.setAttribute('name','sucursalnombre');
			b.setAttribute('id','sucursalnombre');
			b.setAttribute('value',ElNombre);
			b.setAttribute('readonly','readonly');
			CapaContenedora.appendChild(b);
			document.getElementById('usuario').focus();
		}
		
		if(tipo == 'combo'){
			
			padre.className = "FieldDiv2";
			var CapaContenedora = document.getElementById("loguinSucursal");
			var a = document.createElement("select");
			a.setAttribute('style','width:245px;');
			a.setAttribute('class','mytext');
			a.setAttribute('name','sucursal');
			a.setAttribute('id','sucursal');
			a.setAttribute('onChange','VerificaCampo2()');
			CapaContenedora.appendChild(a);
			document.getElementById("sucursal").add(new Option('Seleccione...', 0));
			for(i=0;i<ElId.length;i++){
            	document.getElementById("sucursal").add(new Option(ElNombre[i], ElId[i]));
			}
			if(cookie=='true'){
				document.getElementById('sucursal').value = campo2;
				document.getElementById('password').focus();
			}else{
				document.getElementById('sucursal').focus();
			}
		}
		
		if(tipo == 'false'){
			
			padre.className = "FieldDiv";
			var CapaContenedora = document.getElementById("loguinSucursal");
			var a = document.createElement("input");
			a.setAttribute('type','text');
			a.setAttribute('class','mytext');
			a.setAttribute('name','sucursal');
			a.setAttribute('id','sucursal');
			a.setAttribute('placeholder','Agencia o Sucursal');
			CapaContenedora.appendChild(a);			
		}
	}	

    function consulta_usuario()
    {
		var usuario = document.getElementById('usuario').value;
		var IdSucursal = document.getElementById('sucursal').value;
		
		if(usuario!=''){
			Ext.Ajax.request(
				{
					url: 'login/consulta_nombre.php',
					method: 'POST',
					params:
					{
						usuario		: usuario,
						IdEmpresa 	: IdEmpresa,
						IdSucursal	: IdSucursal
					},
					success: function (result, request)
					{
						resultado = result.responseText.split('{.}');
						if (resultado[0] == 'true' || resultado[0] == true)
						{
	
						}else{
							alertify.error('<div class="DivNotify"><img alt="Usuario" src="login/images/usuarioB.png" width="32" height="32"/></di><div class="DivNotify">'+resultado[2]+'</div>');
							if(resultado[1]=='sucursal'){
								document.getElementById('sucursal').focus();
							}
							if(resultado[1]=='empresa'){
								document.getElementById('empresa').focus();
							}							
						}
					}
				}
			);
		}
    }
	
   function VerificarCookie()
    {
        if (campo1 == ''){
            document.getElementById('empresa').focus();
        }else{
			cookie = 'true';
			IdEmpresa = campo4;
			document.getElementById('empresa').value 		= 	campo1;
			consulta_empresa();
			document.getElementById('usuario').value 		= 	campo3;
			document.getElementById('recordarme').checked	= 	true;
			HabilitaCampos(false);
        }
    }
	
	function VerificaCampo2(){
		if(document.getElementById('sucursal').value==0){
			document.getElementById('sucursal').style.color = "#999";
			document.getElementById('sucursal').style.textShadow = "none";
		}else{
			document.getElementById('sucursal').style.color = "#333";
			document.getElementById('sucursal').style.textShadow = "1px 1px 1px #FFF";
		}
	}
		
    function onKeyPressed(e)
    {
        var keyPressed;
        if (document.all){
            keyPressed = window.event.keyCode;
        }else{
            keyPressed = e.which;
        }
		
		if (keyPressed == 113 && UltimaTecla == 17 && EmpresaFocus == 1){
			BuscaEmpresa();
		}
		UltimaTecla = keyPressed;
		
        if (keyPressed == 13)
        {
            verificar_final();
            return false;
        }
    }
    document.onkeydown = onKeyPressed;
	
	function BuscaEmpresa(){
		//console.log('entra');
		var Tam = TamVentana();
		VBuscaEmpresa = new Ext.Window
		(
			{
				width		:	350,
				height		:	Tam[1] - 100,
				title		:	"Busqueda Empresa",
				modal		: 	true,
				autoScroll	: 	true,
				autoDestroy : 	true,
				closable	:	true,
				//bodyStyle 	: 	'background-color:#FFF',
				autoLoad	:
					{
						url		: 	'login/ListadoEmpresas.php',
						scripts	:	true,
						nocache	:	true
					}
			}
		).show();		

	}


    function verificar_final()
    {
        if (document.getElementById('empresa').value == '')
        {
            alertify.error('<div class="DivNotify"><img alt="Usuario" src="login/images/empresaB.png" width="32" height="32"/></di><div class="DivNotify">Digite un ID de Empresa Valido!</div>');
			document.getElementById('empresa').focus();
			return false;
        }
		
        if (document.getElementById('empresa').value != '' && document.getElementById('sucursal').value == 0)
        {
            alertify.error('<div class="DivNotify"><img alt="Usuario" src="login/images/empresaB.png" width="32" height="32"/></di><div class="DivNotify">Seleccione una Sucursal!</div>');
			document.getElementById('sucursal').focus();
			return false;
        }	
		
        if (document.getElementById('empresa').value != '' && document.getElementById('usuario').value == '')
        {
            alertify.error('<div class="DivNotify"><img alt="Usuario" src="login/images/empresaB.png" width="32" height="32"/></di><div class="DivNotify">Digite su Usuario!</div>');            
			document.getElementById('usuario').focus();
			return false;
        }

        if (document.getElementById('empresa').value != '' && document.getElementById('usuario').value != '' && document.getElementById('password').value == '')
        {
            alertify.error('<div class="DivNotify"><img alt="Usuario" src="login/images/empresaB.png" width="32" height="32"/></di><div class="DivNotify">Digite su Password!</div>');                        
			document.getElementById('password').focus();
			return false;				
        }

        
		if (document.getElementById('empresa').value != '' && document.getElementById('usuario').value != '' && document.getElementById('password').value != '')
        {
            if (document.getElementById('recordarme').checked == true)
            {
                var chekeado = 'true';
            } else
            {
                var chekeado = 'false';
            }
            var empresa = document.getElementById('empresa').value;
			var sucursal = document.getElementById('sucursal').value;
            var usuario = document.getElementById('usuario').value;
            var password = document.getElementById('password').value;
            var recordar = chekeado;

            Ext.Ajax.request(
                {
                    url: 'login/validar.php',
                    method: 'POST',
                    params:
                    {
                        empresa		: 	empresa,
						sucursal	:	sucursal,
                        usuario		: 	usuario,
                        password	: 	password,
                        recordar	: 	recordar,
						IdEmpresa	:	IdEmpresa
                    },
                    success: function (result, request)
                    {
                        resultado = result.responseText.split('{.}');
                        if (resultado[0] == 'true' || resultado[0] == true)
                        {
                            finalizar();
                        }else{
                            alertify.error('<div class="DivNotify"><img alt="Usuario" src="login/images/passwordB.png" width="32" height="32"/></di><div class="DivNotify">'+resultado[1]+'</div>');
                            document.getElementById('password').value = '';
                            document.getElementById('password').focus();
                        }
                    }
                }
            );
        }			
    }

    function finalizar(){document.location = "escritorio.php"}


