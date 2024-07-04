<?php
		class Conexion{
			var $ruta;
			var $usuario;
			var $contrasena;
			var $baseDatos;

			function Conexion(){
				$this->ruta       ="localhost"; //
				$this->usuario    ="root"; //usuario que tengas definido
				$this->contrasena ="serverchkdsk"; //contraseña que tengas definidad
				$this->baseDatos  ="erp_acceso"; //base de datos con los host
			}

			function conectarse($dataBase=NULL){
				//mysql
				$conectarse= mysql_connect($this->ruta,$this->usuario, $this->contrasena) or die(mysql_error()); //conexion al BD
				if($conectarse){
					if(!empty($dataBase)){
						mysql_select_db($dataBase);
					}else{
						mysql_select_db($this->baseDatos);
					}
					return($conectarse);
				}else{
					return ("Error");
					}
				//mysqli
				/*$enlace = mysqli_connect($this->ruta, $this->usuario, $this->contrasena, $this->baseDatos);
				if($enlace){
					echo "Conexion exitosa";	//si la conexion fue exitosa nos muestra este mensaje como prueba, despues lo puedes poner comentarios de nuevo: //
				}else{
					die('Error de Conexión (' . mysqli_connect_errno() . ') '.mysqli_connect_error());
				}
				return($enlace);*/
				// mysqli_close($enlace); //cierra la conexion a nuestra base de datos, un ounto de seguridad importante.
			}
		}

?>
