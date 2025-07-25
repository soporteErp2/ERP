# ERP Software

_Software contable empresarial_

## Comenzando 🚀

_Estas instrucciones te permitirán obtener una copia del proyecto en funcionamiento en tu máquina local para propósitos de desarrollo y pruebas._

Mira **Deployment** para conocer como desplegar el proyecto.

### Pre-requisitos 📋

_version de php inferior_
```
PHP/5.4.31
```
_version de mysql_
```
MySql libmysql - mysqlnd 5.0.10 - 20111026  
```
_Se recomienda usar XAMPP (ya trae php y mysql necesarios para la ejecucion) en la siguiente version_
```
XAMPP - version 5.6.40
https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/5.6.40/
xampp-windows-x64-5.6.40-1-VC11-installer.exe
```

## Instalación 🔧

_Montar una copia de base de datos erp_acceso para acceder al listado de empresas_ 
_Montar una copia de una base de datos de un cliente con datos_
_luego clonar el repo en la ruta del servidor a ejecutar por ejemplo en windows en local para desarrollo usando xammp se clonaria en la carpeta htdocs_
_Luego dentro del repo en la carpeta configuracion se debe crear el archivo conexion.php (este esta excluido en gitignore) y debe contener la siguiente informacion_

```php
<?php
$server = (object) [
"server_name" => "nombre del servidor de bd",
"user" => "usuario de bd",
"password" => "contraseña de bd",
"database" => "base de datos principal donde estan todas las bd (usualmente erp_acceso o erp_bd)",
];
?>
```


## Ejecutar en desarrollo 🚀

_para inicializar la app se accede al servidor y la carpeta deacuerdo a la configuracion del servidor, por ejemplo si es en windows en local usando xammp el proyecto se monta en la carpeta htdocs, pero se accede al servidor desde el navegar con la url localhost, asi que seria localhost/erp (esto varia dependiendo la configuracion del servidor, s.o, etc)_

## Despliegue 📦

_por el momento el despliquegue se realiza mediante acceso FTP actualizando los archivos puntuales que se actualizaron_


## Construido con 🛠️


* [ext.js] - Gestion de interfaces, UI, Ajax
* [CSS] - Css Vainilla
* [PHP] - PHP en el Backend y como renderizador
* [Javascript] - Javascript Vainilla

## Autor ✒️


* **Jonatan Stive Herran Arias** - *Software Developer* - [jonatan2874](https://github.com/jonatan2874)

También puedes mirar la lista de todos los [contribuyentes](https://github.com/your/project/contributors) quíenes han participado en este proyecto. 

## Licencia 📄

Este proyecto está bajo la Licencia (propietario) - mira el archivo [LICENSE.md](LICENSE.md) para detalles

