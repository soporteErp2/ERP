<?php
	// VALIDAR QUE EL USUARIO NO SEA SOLO EL NOMBRE Y APELLIDO SINO UN CORREO ELECTRONICO

	if (!filter_var($_SESSION['NOMBREUSUARIO'], FILTER_VALIDATE_EMAIL)) {
	    // echo "Esta dirección de correo ($email_a) es válida.";
	    ?>
	    	var htmlUserName = `<style>
						    		.contentUserName{
										width            : 100%;
										height           : 100%;
										background-color : #FFF;
						    		}

						    		.table-form{
										font-family : arial,sans-serif;
										margin-top  : 20px;
										font-size   : 14px;
										/*float       : left;*/
										margin-left : 10px;
										width       : 400px;
										display: inline-flex;
									}

									.table-form .thead{
										background-color : #2A80B9;
										color            : #fff;
									}

									.table-form .thead td{
										padding   : 5px;
										font-size : 14px;
									}

									.table-form td{
										padding: 2px 2px 2px 15px;
									}

									.table-form input, .table-form textarea, .table-form select{
										line-height      : 1.42857143;
										color            : #555;
										background-color : #fff;
										border           : 1px solid #ccc;
										height           : 30px;
										width            : 200px;
										padding-left     : 5px;
									}

									.table-form textarea{
										height: 50px;
									}

									.table-form input[data-requiere="true"], select[data-requiere="true"]{
										background-color:#FFE0E0;
									}

						    	</style>
	    						<div class="contentUserName">
	    							<table class="table-form">
	    								<tr>
	    									<td colspan="2">
	    										Estimado usuario, nos encontramos realizando mejoras en nuestro
		    									sistema, por ese motivo le invitamos a que actualice su usuario de acceso al sistema
		    									con una direccion de correo electronico valida, la cual en adelante sera su nombre de
		    									usuario cada vez que inicie sesion, despues de que realice la actualizacion sera redirigido
		    									a la pagina de inicio de sesion donde ingresara con su nuevo usuario.
	    									</td>
	    								</tr>
	    								<tr>
	    									<td>&nbsp;</td>
	    								</tr>
	    								<tr>
	    									<td><b><i>Nombre de Usuario Actual: </i></b></td>
	    									<td><?php echo$_SESSION['NOMBREUSUARIO']." ".$_SESSION['SUPPORT'] ?></td>
	    								</tr>
	    								<tr>
	    									<td><b><i>Nuevo nombre de Usuario:</i></b></td>
	    									<td><input type="text" id="newUserName" placeholder="ejemplo@correo.com" ></td>
	    								</tr>
	    							</table>

	    						</div>`;

	    	Win_VentanaCambioNombre = new Ext.Window({
				width       : 450,
				height      : 340,
				id          : 'Win_VentanaCambioNombre',
				title       : 'Actualizacion nombre de usuario',
				modal       : true,
				autoScroll  : false,
				closable    : false,
				autoDestroy : true,
				html        : htmlUserName,
	            tbar        :
	            [
	                {
	                    xtype       : 'button',
	                    text        : 'Actualizar',
	                    scale       : 'large',
	                    iconCls     : 'guardar',
	                    iconAlign   : 'top',
	                    handler     : function(){ saveNewUserName() }
	                }
	            ]
	        }).show();

        	var saveNewUserName = () => {
        		var userName = document.getElementById('newUserName').value
        		if( validateEmail(userName) ){
					MyLoading2('on')
					Ext.Ajax.request({
					    url     : 'login/bd.php',
					    params  :
					    {
							opc      : 'updateUserName',
							userName : userName
					    },
					    success :function (result, request){
					    			var jsonResponse = JSON.parse(result.responseText);
					                if(jsonResponse.response == 'success'){
					               	 	MyLoading2('off')
					               	 	document.location = "logout.php"
					                }
					                else{
					                	console.log("false");
					                	MyLoading2('off',{icono:'fail',texto:jsonResponse.detail })
					                }
					            },
					    failure : function(){
					    	console.log("fail");
		                	MyLoading2('off',{icono:'fail',texto:jsonResponse.detail})
					    }
					});
        		}
        		else{
					alert("El correo digitado no es valido");
        		}

        	}

        	var validateEmail = (valor) => {
    		 	if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(valor)){
				   return true;
			  	} else {
				   return false;
		  		}
			}

	    <?php
	    // echo "<script>alert('usuario no correo')</script>";
	}
	$sql = "";
	// $query = $mysql->query($sql,$mysql->link);

?>
