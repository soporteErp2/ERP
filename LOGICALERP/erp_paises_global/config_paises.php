<?php
	switch($_SESSION['PAIS']){
		// ARGENTINA
		case '11':
			$labelDepto             = "Provincia";
			$labelCiudad            = "Departamento";
			$labelMunicipio         = "";
			$url_datos_empresa      = "empresa/empresa";
			$url_sucursales_empresa = "sucursales/sucursales";
			break;
		// BOLIVIA
		case '22':
			$labelDepto             = "Departamento";
			$labelCiudad            = "Provincia";
			$labelMunicipio         = "Municipio";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// CHILE
		case '44':
			$labelDepto             = "Region";
			$labelCiudad            = "Provincia";
			$labelMunicipio         = "Comuna";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// COSTA RICA
		case '55':
			$labelDepto             = "Provincia";
			$labelCiudad            = "Canton";
			$labelMunicipio         = "Distrito";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// REPUBLICA DOMINICANA
		case '63':
			$labelDepto             = "Provincia";
			$labelCiudad            = "Municipio";
			$labelMunicipio         = "Distrito Municipal";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// ECUADOR
		case '64':
			$labelDepto             = "Provincia";
			$labelCiudad            = "Canton";
			$labelMunicipio         = "";
			$url_datos_empresa      = "empresa/empresa";
			$url_sucursales_empresa = "sucursales/sucursales";
			break;
		// MEXICO
		case '140':
			$labelDepto             = "Delegacion";
			$labelCiudad            = "Localidad";
			$labelMunicipio         = "Estado";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// PANAMA
		case '170':
			$labelDepto             = "Provincia";
			$labelCiudad            = "Distrito";
			$labelMunicipio         = "Corregimiento";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// ESTADOS UNIDOS
		case '233':
			$labelDepto             = "Estado";
			$labelCiudad            = "Condado";
			$labelMunicipio         = "Ciudad";
			$url_datos_empresa      = "../erp_paises_global/panel_de_control/empresa/empresa";
			$url_sucursales_empresa = "../erp_paises_global/panel_de_control/sucursales/sucursales";
			break;
		// POR DEFECTO
		default:
			$labelDepto             = "Departamento";
			$labelCiudad            = "Ciudad";
			$labelMunicipio         = "Municipio";
			$url_datos_empresa      = "empresa/empresa";
			$url_sucursales_empresa = "sucursales/sucursales";
			break;
	}
 ?>
