/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : erp_20

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-03-04 23:22:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for activos_fijos
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos`;
CREATE TABLE `activos_fijos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_bar` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_activo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_grupo` int(2) DEFAULT NULL,
  `id_subgrupo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `subgrupo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_subgrupo` int(2) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_equipo` varchar(150) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(200) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'este campo guarda si o no, cuando el activo fijo es un terreno no se deprecia, asi que no es necesaria cierta informacion de la tabla',
  `vida_util` int(11) DEFAULT NULL,
  `vida_util_restante` int(11) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `nit_proveedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `proveedor` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion_en_inventario` date DEFAULT NULL,
  `fecha_inicio_depreciacion` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `fecha_vencimiento_garantia` date DEFAULT NULL,
  `marca` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `modelo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `color` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `unidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_piezas` int(11) DEFAULT NULL,
  `descripcion1` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion2` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `quien_elimino` varchar(80) COLLATE latin1_general_ci DEFAULT NULL,
  `costo` double(11,2) DEFAULT NULL,
  `costo_sin_depreciar_anual` double(11,2) DEFAULT NULL,
  `valor_salvamento` double(20,2) DEFAULT NULL,
  `valor_salvamento_niif` double(20,2) DEFAULT NULL,
  `documento_contable` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `estado` int(11) DEFAULT '0' COMMENT 'Estado del activo \r\n0 = activo ingresado por un documento pero aun sin configurar\r\n1 = activo ya configurado en el sistema\r\n3 = activo dado de baja',
  `observaciones_eliminacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario_elimino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_elimino` int(11) DEFAULT NULL,
  `id_usuario_creacion` int(11) DEFAULT NULL,
  `id_usuario_compra` int(11) DEFAULT NULL,
  `metodo_depreciacion_colgaap` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `id_documento_referencia` int(11) DEFAULT NULL,
  `documento_referencia` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_referencia_consecutivo` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_referencia_inventario` int(10) DEFAULT NULL,
  `id_cuenta_depreciacion` int(11) DEFAULT NULL,
  `cuenta_depreciacion` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_contrapartida_depreciacion` int(11) DEFAULT NULL,
  `contrapartida_depreciacion` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_depreciacion_niif` int(11) DEFAULT NULL,
  `cuenta_depreciacion_niif` int(11) DEFAULT NULL,
  `id_contrapartida_depreciacion_niif` int(11) DEFAULT NULL,
  `contrapartida_depreciacion_niif` int(11) DEFAULT NULL,
  `fecha_inicio_depreciacion_niif` date DEFAULT NULL,
  `vida_util_niif` int(11) DEFAULT NULL,
  `vida_util_niif_restante` int(11) DEFAULT NULL,
  `metodo_depreciacion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `depreciacion_acumulada` double(20,2) DEFAULT '0.00',
  `depreciacion_acumulada_niif` double(20,2) DEFAULT '0.00',
  `deterioro_acumulado` double(20,2) DEFAULT '0.00',
  `id_cuenta_deterioro_niif_debito` int(11) DEFAULT NULL,
  `cuenta_deterioro_niif_debito` int(11) DEFAULT NULL,
  `id_cuenta_deterioro_niif_credito` int(11) DEFAULT NULL,
  `cuenta_deterioro_niif_credito` int(11) DEFAULT NULL,
  `depreciable` varchar(10) COLLATE latin1_general_ci DEFAULT 'Si' COMMENT 'Indica si el activo se va a depreciar o no, asi este en estado 1 (depreciable)',
  `id_saldo_inicial` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_cuentas`;
CREATE TABLE `activos_fijos_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_activo` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `contabilidad` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_depreciaciones
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_depreciaciones`;
CREATE TABLE `activos_fijos_depreciaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_niif` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cunando se genera el documento',
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `numero_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(255) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(255) DEFAULT NULL,
  `sinc_nota` varchar(100) COLLATE latin1_general_ci DEFAULT '',
  `estado` int(11) DEFAULT '0',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_depreciaciones_inventario
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_depreciaciones_inventario`;
CREATE TABLE `activos_fijos_depreciaciones_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_depreciacion` int(11) DEFAULT NULL,
  `id_activo_fijo` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `code_bar` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_activo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `unidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_depreciar` int(11) DEFAULT NULL,
  `id_nota_contable` int(11) DEFAULT NULL,
  `costo` double(11,2) DEFAULT NULL,
  `cuenta_depreciacion` int(11) DEFAULT NULL,
  `contrapartida_depreciacion` int(11) DEFAULT NULL,
  `cuenta_depreciacion_niif` int(11) DEFAULT NULL,
  `contrapartida_depreciacion_niif` int(11) DEFAULT NULL,
  `depreciacion_acumulada` double DEFAULT NULL,
  `valor` double(11,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_activo_fijo` (`id_activo_fijo`) USING BTREE,
  KEY `id_nota_contable` (`id_nota_contable`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_deterioro
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_deterioro`;
CREATE TABLE `activos_fijos_deterioro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_niif` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cunando se genera el documento',
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `numero_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(255) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(255) DEFAULT NULL,
  `sinc_nota` varchar(100) COLLATE latin1_general_ci DEFAULT '',
  `estado` int(11) DEFAULT '0',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_deterioro_inventario
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_deterioro_inventario`;
CREATE TABLE `activos_fijos_deterioro_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_deterioro` int(11) DEFAULT NULL,
  `id_activo_fijo` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `unidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_depreciar` int(11) DEFAULT NULL,
  `id_nota_contable` int(11) DEFAULT NULL,
  `costo` double(11,2) DEFAULT NULL,
  `cuenta_deterioro` int(11) DEFAULT NULL,
  `contrapartida_deterioro` int(11) DEFAULT NULL,
  `deterioro_acumulado` double DEFAULT NULL,
  `valor` double(11,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_activo_fijo` (`id_activo_fijo`) USING BTREE,
  KEY `id_nota_contable` (`id_nota_contable`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_upload
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_upload`;
CREATE TABLE `activos_fijos_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `nombre_archivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ok` int(11) DEFAULT '0',
  `repetido` int(11) DEFAULT NULL,
  `fail` int(11) DEFAULT '0',
  `estado` int(1) DEFAULT '0',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `tercero` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for activos_fijos_upload_registro
-- ----------------------------
DROP TABLE IF EXISTS `activos_fijos_upload_registro`;
CREATE TABLE `activos_fijos_upload_registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_subgrupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `deteriorable` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `code_bar` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_activo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `metodo_depreciacion_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_nota` int(11) DEFAULT NULL,
  `vida_util` double DEFAULT NULL,
  `valor_salvamento` double DEFAULT NULL,
  `cuenta_colgaap_depreciacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `contrapartida_cuenta_colgaap_depreciacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `metodo_depreciacion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `vida_util_niif` double DEFAULT NULL,
  `valor_salvamento_niif` double DEFAULT NULL,
  `cuenta_niif_depreciacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `contrapartida_niif_depreciacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_niif_deterioro` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `contrapartida_cuenta_niif_deterioro` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'true',
  `id_upload` int(11) DEFAULT NULL,
  `mensaje_error` longtext COLLATE latin1_general_ci,
  `tiene_error` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fila_excel` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for amortizaciones
-- ----------------------------
DROP TABLE IF EXISTS `amortizaciones`;
CREATE TABLE `amortizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_diferidos` date DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0=borrador,1=generado,2=cruzado,3=cancelado',
  `observacion` longblob,
  `id_sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for amortizaciones_diferidos
-- ----------------------------
DROP TABLE IF EXISTS `amortizaciones_diferidos`;
CREATE TABLE `amortizaciones_diferidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_amortizacion` int(11) DEFAULT NULL,
  `id_diferido` int(11) DEFAULT NULL,
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `id_cuenta_debito` int(11) DEFAULT NULL,
  `cuenta_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_debito` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_credito` int(11) DEFAULT NULL,
  `cuenta_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_credito` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `cod_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(11,2) DEFAULT NULL,
  `meses` int(11) DEFAULT NULL,
  `saldo` double(11,2) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for anticipos
-- ----------------------------
DROP TABLE IF EXISTS `anticipos`;
CREATE TABLE `anticipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_anticipo` int(11) DEFAULT NULL,
  `tipo_documento_anticipo` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento_anticipo` int(11) DEFAULT NULL,
  `id_cuenta_anticipo` int(11) DEFAULT NULL,
  `cuenta_colgaap` bigint(20) DEFAULT NULL,
  `cuenta_niif` bigint(20) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `nit_tercero` bigint(20) DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(20,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=31457 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for api_conections
-- ----------------------------
DROP TABLE IF EXISTS `api_conections`;
CREATE TABLE `api_conections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_url` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Direccion del api a consumir',
  `request_method` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `authorization` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `request_url_callback` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `request_method_callback` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `authorization_callback` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Parametro opcional para el API',
  `titulo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'titulo a mostrar en el sistema',
  `window_height` int(11) DEFAULT NULL,
  `window_width` int(11) DEFAULT NULL,
  `id_software` int(11) DEFAULT NULL,
  `software` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `icono` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'icono a mostrar en el sistema',
  `archivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'archivo a cargar para generar el consumo del api',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for asientos_colgaap
-- ----------------------------
DROP TABLE IF EXISTS `asientos_colgaap`;
CREATE TABLE `asientos_colgaap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'FV = Factura de Venta,\r\nFC = Factura de Compra,\r\nRV = Remision de venta,\r\nNDRV=Nota Devolucion Remision de Venta,\r\nNDFV=Nota Devolucion Factura de Venta,\r\nNDFC=Nota Devolucion Factura de Compra,\r\nNCG=Nota Contable General,\r\nSA = Salida de Almacen,\r\nCE=Com',
  `tipo_documento_extendido` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT '0',
  `tipo_documento_cruce` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `numero_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `id_cuenta` int(11) DEFAULT NULL,
  `codigo_cuenta` bigint(20) DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT '0',
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `permiso_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_flujo_efectivo` int(11) DEFAULT NULL,
  `flujo_efectivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_cruce` int(11) DEFAULT NULL,
  `sucursal_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `observacion` longtext COLLATE latin1_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_documento` (`id_documento`) USING BTREE,
  KEY `consecutivo_documento` (`consecutivo_documento`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `tipo_documento_cruce` (`tipo_documento_cruce`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `codigo_cuenta` (`codigo_cuenta`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `tipo_documento` (`tipo_documento`) USING BTREE,
  KEY `numero_documento_cruce` (`numero_documento_cruce`) USING BTREE,
  KEY `fecha` (`fecha`) USING BTREE,
  KEY `activo` (`activo`) USING BTREE,
  KEY `idx_filtros` (`activo`,`id_empresa`,`fecha`,`id_sucursal`)
) ENGINE=MyISAM AUTO_INCREMENT=2349724 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for asientos_colgaap_default
-- ----------------------------
DROP TABLE IF EXISTS `asientos_colgaap_default`;
CREATE TABLE `asientos_colgaap_default` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `detalle_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for asientos_colgaap_default_grupos
-- ----------------------------
DROP TABLE IF EXISTS `asientos_colgaap_default_grupos`;
CREATE TABLE `asientos_colgaap_default_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_grupo` int(11) DEFAULT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `detalle_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for asientos_niif
-- ----------------------------
DROP TABLE IF EXISTS `asientos_niif`;
CREATE TABLE `asientos_niif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'FV = Factura de Venta,\r\nFC = Factura de Compra,\r\nRV = Remision de venta,\r\nNDRV=Nota Devolucion Remision de Venta,\r\nNDFV=Nota Devolucion Factura de Venta,\r\nNDFC=Nota Devolucion Factura de Compra,\r\nNCG=Nota Contable General,\r\nSA = Salida de Almacen,\r\nCE=Com',
  `tipo_documento_extendido` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `tipo_documento_cruce` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `numero_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `id_cuenta` int(11) DEFAULT NULL,
  `codigo_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `permiso_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_flujo_efectivo` int(11) DEFAULT '0',
  `flujo_efectivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_cruce` int(11) DEFAULT NULL,
  `sucursal_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `observacion` longtext COLLATE latin1_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_documento` (`id_documento`) USING BTREE,
  KEY `consecutivo_documento` (`consecutivo_documento`) USING BTREE,
  KEY `tipo_documento` (`tipo_documento`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `tipo_documento_cruce` (`tipo_documento_cruce`) USING BTREE,
  KEY `numero_documento_cruce` (`numero_documento_cruce`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `codigo_cuenta` (`codigo_cuenta`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2105642 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for asientos_niif_default
-- ----------------------------
DROP TABLE IF EXISTS `asientos_niif_default`;
CREATE TABLE `asientos_niif_default` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `detalle_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for asientos_niif_default_grupos
-- ----------------------------
DROP TABLE IF EXISTS `asientos_niif_default_grupos`;
CREATE TABLE `asientos_niif_default_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_grupo` int(11) DEFAULT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `detalle_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for autorizacion_ordenes_compra_area
-- ----------------------------
DROP TABLE IF EXISTS `autorizacion_ordenes_compra_area`;
CREATE TABLE `autorizacion_ordenes_compra_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_autorizacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Autorizada - Aplazada - Rechazada',
  `orden` int(11) DEFAULT NULL COMMENT 'Orden de las personas en que se autoriza un documento',
  `id_area` int(11) DEFAULT NULL COMMENT 'area a autorizar un documento',
  `id_orden_compra` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for autorizacion_requisicion
-- ----------------------------
DROP TABLE IF EXISTS `autorizacion_requisicion`;
CREATE TABLE `autorizacion_requisicion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_autorizacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Autorizada - Aplazada - Rechazada',
  `orden` int(11) DEFAULT NULL COMMENT 'Orden de las personas en que se autoriza un documento',
  `id_area` int(11) DEFAULT NULL COMMENT 'area a autorizar un documento',
  `id_requisicion` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for boletas_formulario
-- ----------------------------
DROP TABLE IF EXISTS `boletas_formulario`;
CREATE TABLE `boletas_formulario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_venta` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_boleta` int(10) DEFAULT NULL,
  `usuario_registro` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `accion` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `asistio` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `cedula_invitado` int(11) DEFAULT NULL,
  `nombre_invitado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_invitado` datetime DEFAULT NULL,
  `evento` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for calendario
-- ----------------------------
DROP TABLE IF EXISTS `calendario`;
CREATE TABLE `calendario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` int(1) DEFAULT '3' COMMENT '1 ->tarea  2->llamada  3->cita',
  `icono` int(2) DEFAULT NULL,
  `fechai` date DEFAULT NULL,
  `horai` time DEFAULT NULL,
  `fechaf` date DEFAULT NULL,
  `horaf` time DEFAULT NULL,
  `tema` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `lugar` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  `color` varchar(7) COLLATE latin1_general_ci DEFAULT '#FFF',
  `id_objetivo_crm` int(11) NOT NULL DEFAULT '0',
  `id_actividad_crm` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for calendario_notificaciones
-- ----------------------------
DROP TABLE IF EXISTS `calendario_notificaciones`;
CREATE TABLE `calendario_notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_calendario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `tema` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `time_type` varchar(1) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `fecha_pospuesto` datetime DEFAULT NULL,
  `pospuesto` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `descartar` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for calendario_notificaciones_personas
-- ----------------------------
DROP TABLE IF EXISTS `calendario_notificaciones_personas`;
CREATE TABLE `calendario_notificaciones_personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_notificacion` int(11) DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `id_calendario` int(11) DEFAULT NULL,
  `asignado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for calendario_personas
-- ----------------------------
DROP TABLE IF EXISTS `calendario_personas`;
CREATE TABLE `calendario_personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_calendario` int(11) DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `asignado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for centro_costos
-- ----------------------------
DROP TABLE IF EXISTS `centro_costos`;
CREATE TABLE `centro_costos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `debug` int(11) DEFAULT NULL,
  `campo1` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `campo2` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `campo3` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `campo4` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for certificado_ingreso_retenciones_empleados_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `certificado_ingreso_retenciones_empleados_conceptos`;
CREATE TABLE `certificado_ingreso_retenciones_empleados_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_seccion` int(11) DEFAULT NULL,
  `id_fila` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for certificado_ingreso_retenciones_empleados_secciones
-- ----------------------------
DROP TABLE IF EXISTS `certificado_ingreso_retenciones_empleados_secciones`;
CREATE TABLE `certificado_ingreso_retenciones_empleados_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` longtext COLLATE latin1_general_ci,
  `nombre_total` longtext COLLATE latin1_general_ci,
  `codigo_total` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for certificado_ingreso_retenciones_empleados_secciones_filas
-- ----------------------------
DROP TABLE IF EXISTS `certificado_ingreso_retenciones_empleados_secciones_filas`;
CREATE TABLE `certificado_ingreso_retenciones_empleados_secciones_filas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_seccion` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `codigo` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for cierre_por_periodo
-- ----------------------------
DROP TABLE IF EXISTS `cierre_por_periodo`;
CREATE TABLE `cierre_por_periodo` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0-> sin generar , 1-> generado',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for codigo_paises
-- ----------------------------
DROP TABLE IF EXISTS `codigo_paises`;
CREATE TABLE `codigo_paises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_entrada_almacen
-- ----------------------------
DROP TABLE IF EXISTS `compras_entrada_almacen`;
CREATE TABLE `compras_entrada_almacen` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `pendientes_facturar` double(15,2) DEFAULT '0.00' COMMENT 'items facturados de la remision',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `cod_proveedor` int(11) DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `proveedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_entrada` varchar(10) COLLATE latin1_general_ci DEFAULT 'EA' COMMENT 'EA :  Entrada de Almacen normal -> contabiliza cuentas de transito\r\nAI :  Ajuste de inventario -> se contabiliza las cuentas del item, pero en reversa, es decir incrementa el invntario y se resta la contrapartida configurada en el Item',
  `id_centro_costo` int(11) DEFAULT NULL,
  `codigo_centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(15) DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura, 3 -> cancelado, 4 -> ingresadas todas las unidades',
  `total_unidades` double(20,2) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_cliente` (`id_proveedor`) USING BTREE,
  KEY `plantillas_id` (`plantillas_id`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1140 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_entrada_almacen_inventario
-- ----------------------------
DROP TABLE IF EXISTS `compras_entrada_almacen_inventario`;
CREATE TABLE `compras_entrada_almacen_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_entrada_almacen` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tabla_inventario_referencia` int(11) DEFAULT NULL,
  `id_consecutivo_referencia` int(15) DEFAULT NULL,
  `consecutivo_referencia` int(15) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT '',
  `cantidad` double(20,2) DEFAULT '0.00',
  `saldo_cantidad` double(20,2) DEFAULT '0.00',
  `costo_unitario` double(20,2) DEFAULT '0.00',
  `costo_inventario` double(20,2) DEFAULT NULL,
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `descuento` double(20,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_impuesto` double(20,2) DEFAULT NULL,
  `check_opcion_contable` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `opcion_activo_fijo` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_remision_vernta` (`id_entrada_almacen`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_tabla_inventario_referencia` (`id_tabla_inventario_referencia`) USING BTREE,
  KEY `id_consecutivo_referencia` (`id_consecutivo_referencia`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20893 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas`;
CREATE TABLE `compras_facturas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cuando se genera la factura, cuando cambia de estado 0  a 1',
  `hora_generacion` time DEFAULT NULL,
  `id_resolucion` int(10) DEFAULT NULL,
  `id_configuracion_cuenta_pago` int(11) DEFAULT NULL,
  `configuracion_cuenta_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_pago` int(11) DEFAULT NULL,
  `cuenta_pago` int(11) DEFAULT NULL,
  `cuenta_pago_niif` int(11) DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `forma_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_pago` int(11) DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `prefijo_factura` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `numero_factura` bigint(50) DEFAULT '0',
  `consecutivo` int(11) DEFAULT NULL,
  `tipo_documento` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(15) DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_proveedor` int(15) DEFAULT NULL,
  `cod_proveedor` int(11) DEFAULT NULL,
  `proveedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_recibe_en_almacen` int(11) DEFAULT '0',
  `usuario_recibe_en_almacen` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(15) DEFAULT '0',
  `estado` int(2) DEFAULT '0' COMMENT '0 -> sin guardar, 1 -> cerrada',
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `total_factura` double(20,2) DEFAULT '0.00',
  `total_factura_sin_abono` double(20,2) DEFAULT '0.00',
  `contabilidad_manual` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `subtotal_manual` double(20,2) DEFAULT '0.00',
  `iva_manual` double(20,2) DEFAULT '0.00',
  `total_manual` double(20,2) DEFAULT '0.00',
  `activo` int(1) NOT NULL DEFAULT '1',
  `id_saldo_inicial` int(11) DEFAULT '0',
  `factura_por_cuentas` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `debug` int(1) DEFAULT '0',
  `debug_ccos` int(11) DEFAULT NULL,
  `tipo` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `cuenta_pago_config_tercero` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `cuenta_pago_nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cuenta_pago_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_factura` int(11) DEFAULT NULL,
  `id_metodo_pago` int(10) DEFAULT NULL,
  `fecha_DS` date DEFAULT NULL,
  `hora_DS` time DEFAULT '00:00:00',
  `response_DS` longtext COLLATE latin1_general_ci,
  `UUID` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_DS` int(10) DEFAULT NULL,
  `nombre_usuario_DS` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `cedula_usuario_DS` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `cufe` varchar(150) COLLATE latin1_general_ci DEFAULT NULL,
  `json_api` longtext COLLATE latin1_general_ci COMMENT 'Json recibido de aplicacion externa para generar el documentoÂ enÂ ERP',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=48116 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_archivos_adjuntos
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_archivos_adjuntos`;
CREATE TABLE `compras_facturas_archivos_adjuntos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_compra` int(15) DEFAULT NULL,
  `tipo_documento` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `prefijo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_archivo` varchar(300) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` int(11) DEFAULT NULL,
  `nombre_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_factura_compra`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_contabilidad_manual
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_contabilidad_manual`;
CREATE TABLE `compras_facturas_contabilidad_manual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_factura_compra` int(11) DEFAULT '0',
  `subtotal_manual` double(20,2) DEFAULT '0.00',
  `id_cuenta_subtotal` int(11) DEFAULT '0',
  `cuenta_subtotal` int(50) DEFAULT '0',
  `descripcion_cuenta_subtotal` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_subtotal` int(11) DEFAULT '0',
  `cuenta_niif_subtotal` int(20) DEFAULT '0',
  `descripcion_niif_subtotal` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `iva_manual` double(20,2) DEFAULT '0.00',
  `id_cuenta_iva` int(11) DEFAULT '0',
  `cuenta_iva` int(20) DEFAULT '0',
  `descripcion_cuenta_iva` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_iva` int(11) DEFAULT '0',
  `cuenta_niif_iva` int(20) DEFAULT '0',
  `descripcion_niif_iva` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `total_manual` double(20,2) DEFAULT '0.00',
  `id_cuenta_total` int(11) DEFAULT '0',
  `cuenta_total` int(20) DEFAULT '0',
  `descripcion_cuenta_total` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_total` int(11) DEFAULT '0',
  `cuenta_niif_total` int(20) DEFAULT '0',
  `descripcion_niif_total` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` varchar(100) COLLATE latin1_general_ci DEFAULT '',
  `nombre_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura_compra` (`id_factura_compra`) USING BTREE,
  KEY `id_cuenta_subtotal` (`id_cuenta_subtotal`) USING BTREE,
  KEY `id_cuenta_niif_subtotal` (`id_cuenta_niif_subtotal`) USING BTREE,
  KEY `id_cuenta_iva` (`id_cuenta_iva`) USING BTREE,
  KEY `id_cuenta_niif_iva` (`id_cuenta_niif_iva`) USING BTREE,
  KEY `id_cuenta_total` (`id_cuenta_total`) USING BTREE,
  KEY `id_cuenta_niif_total` (`id_cuenta_niif_total`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_cuentas`;
CREATE TABLE `compras_facturas_cuentas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_compra` int(15) DEFAULT NULL,
  `id_tabla_referencia` int(11) DEFAULT '0' COMMENT 'id de la tabla de comprobante_egreso_cuentas, para poder restarle el saldo a la cuenta del comprobante',
  `id_puc` int(15) NOT NULL,
  `cuenta_puc` int(20) DEFAULT NULL,
  `descripcion_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_niif` int(15) DEFAULT NULL,
  `cuenta_niif` int(15) DEFAULT NULL,
  `descripcion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento_cruce` int(11) DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` int(11) DEFAULT '0',
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `observacion` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_factura_compra`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `cuenta_puc` (`cuenta_puc`) USING BTREE,
  KEY `id_niif` (`id_niif`) USING BTREE,
  KEY `cuenta_niif` (`cuenta_niif`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=22929 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_inventario
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_inventario`;
CREATE TABLE `compras_facturas_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_compra` int(15) DEFAULT NULL,
  `id_empresa` int(15) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `id_tabla_referencia` int(11) DEFAULT NULL,
  `id_consecutivo_referencia` int(11) DEFAULT NULL,
  `consecutivo_referencia` int(11) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `id_inventario` int(15) DEFAULT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_descuento` varchar(50) COLLATE latin1_general_ci DEFAULT 'porcentaje',
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `cuenta_impuesto` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `cuenta_impuesto_niif` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `check_opcion_contable` varchar(15) COLLATE latin1_general_ci DEFAULT '',
  `opcion_gasto` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `opcion_costo` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `opcion_activo_fijo` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `id_centro_costos` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_tabla_referencia` (`id_tabla_referencia`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=70321 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_inventario_archivos_adjuntos
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_inventario_archivos_adjuntos`;
CREATE TABLE `compras_facturas_inventario_archivos_adjuntos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_compra` int(15) DEFAULT NULL,
  `id_empresa` int(15) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `id_tabla_referencia` int(11) DEFAULT NULL,
  `id_consecutivo_referencia` int(11) DEFAULT NULL,
  `consecutivo_referencia` int(11) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `id_inventario` int(15) DEFAULT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,0) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_descuento` varchar(50) COLLATE latin1_general_ci DEFAULT 'porcentaje',
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `cuenta_impuesto` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `cuenta_impuesto_niif` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `check_opcion_contable` varchar(15) COLLATE latin1_general_ci DEFAULT '',
  `opcion_gasto` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `opcion_costo` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `opcion_activo_fijo` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `id_centro_costos` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_tabla_referencia` (`id_tabla_referencia`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_retenciones
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_retenciones`;
CREATE TABLE `compras_facturas_retenciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_factura_compra` int(11) DEFAULT NULL,
  `id_retencion` int(11) DEFAULT NULL,
  `tipo_retencion` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `retencion` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(20,3) DEFAULT NULL,
  `base` double(20,0) DEFAULT '0',
  `codigo_cuenta` int(11) DEFAULT NULL,
  `codigo_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_autoretencion` int(11) DEFAULT NULL,
  `cuenta_autoretencion_niif` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id` (`id`) USING BTREE,
  KEY `id_factura_compra` (`id_factura_compra`) USING BTREE,
  KEY `id_retencion` (`id_retencion`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7802 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_facturas_tipos
-- ----------------------------
DROP TABLE IF EXISTS `compras_facturas_tipos`;
CREATE TABLE `compras_facturas_tipos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_ordenes
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes`;
CREATE TABLE `compras_ordenes` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `pendientes_facturar` double(15,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_siip` int(11) DEFAULT NULL,
  `fecha_registro` date NOT NULL COMMENT 'Fecha cuando se crea el documento',
  `fecha_inicio` date DEFAULT NULL COMMENT 'Fecha escogida por el usuario cuando inicia la orden',
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'fecha cuando vence la orden de compra',
  `id_forma_pago` int(15) DEFAULT NULL,
  `forma_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_proveedor` int(15) NOT NULL,
  `cod_proveedor` int(11) DEFAULT NULL,
  `proveedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT '0',
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_recibe_en_almacen` int(11) DEFAULT NULL,
  `usuario_recibe_en_almacen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_area_solicitante` int(11) DEFAULT NULL,
  `codigo_area_solicitante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `area_solicitante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(15) DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `validacion` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_usuario_validacion` int(11) DEFAULT NULL,
  `usuario_validacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura,3->cancelada',
  `activo` int(1) DEFAULT '1',
  `tasa_cambio` double(10,2) DEFAULT NULL,
  `id_moneda` int(11) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `tipo_nombre` varchar(40) COLLATE latin1_general_ci DEFAULT NULL,
  `autorizado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_proveedor` (`id_proveedor`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=298 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_ordenes_autorizaciones
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes_autorizaciones`;
CREATE TABLE `compras_ordenes_autorizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_orden_compra` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `tipo_autorizacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_ordenes_documentos
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes_documentos`;
CREATE TABLE `compras_ordenes_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activo` int(1) DEFAULT '1',
  `id_orden_compra` int(15) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_ordenes_doc_cruce
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes_doc_cruce`;
CREATE TABLE `compras_ordenes_doc_cruce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_orden` int(15) DEFAULT NULL,
  `id_documento_cruce` int(15) DEFAULT NULL,
  `consecutivo_cruce` int(15) DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_ordenes_inventario
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes_inventario`;
CREATE TABLE `compras_ordenes_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_orden_compra` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tabla_inventario_referencia` int(11) DEFAULT NULL,
  `id_consecutivo_referencia` int(15) DEFAULT NULL,
  `consecutivo_referencia` int(15) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci NOT NULL,
  `cantidad` double(15,2) NOT NULL DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) NOT NULL DEFAULT '0.00',
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT '0',
  `observaciones` longtext COLLATE latin1_general_ci,
  `check_opcion_contable` varchar(15) COLLATE latin1_general_ci DEFAULT 'false',
  `opcion_gasto` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `opcion_costo` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `opcion_activo_fijo` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_orden_compra` (`id_orden_compra`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_ordenes_retenciones
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes_retenciones`;
CREATE TABLE `compras_ordenes_retenciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_orden_compra` int(11) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `id_retencion` int(11) DEFAULT NULL,
  `retencion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for compras_ordenes_tipos
-- ----------------------------
DROP TABLE IF EXISTS `compras_ordenes_tipos`;
CREATE TABLE `compras_ordenes_tipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_requisicion
-- ----------------------------
DROP TABLE IF EXISTS `compras_requisicion`;
CREATE TABLE `compras_requisicion` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `pendientes_facturar` double(15,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date NOT NULL COMMENT 'Fecha cuando se crea el documento',
  `fecha_inicio` date DEFAULT NULL COMMENT 'Fecha escogida por el usuario cuando inicia la orden',
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'fecha cuando vence la orden de compra',
  `id_centro_costo` int(15) DEFAULT NULL,
  `codigo_centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_solicitante` int(15) NOT NULL,
  `documento_solicitante` int(255) DEFAULT NULL,
  `nombre_solicitante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_area_solicitante` int(11) DEFAULT NULL,
  `codigo_area_solicitante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `area_solicitante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT '0',
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(15) DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `autorizado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_tipo` int(11) DEFAULT NULL,
  `tipo_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura,3->cancelada, 4 -> autorizado',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_proveedor` (`id_solicitante`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=235 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_requisicion_documentos
-- ----------------------------
DROP TABLE IF EXISTS `compras_requisicion_documentos`;
CREATE TABLE `compras_requisicion_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activo` int(1) DEFAULT '1',
  `id_requisicion_compra` int(15) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_requisicion_doc_cruce
-- ----------------------------
DROP TABLE IF EXISTS `compras_requisicion_doc_cruce`;
CREATE TABLE `compras_requisicion_doc_cruce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_requisicion` int(15) DEFAULT NULL,
  `id_documento_cruce` int(15) DEFAULT NULL,
  `consecutivo_cruce` int(15) DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_requisicion_inventario
-- ----------------------------
DROP TABLE IF EXISTS `compras_requisicion_inventario`;
CREATE TABLE `compras_requisicion_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_requisicion_compra` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci NOT NULL,
  `cantidad` double(15,2) NOT NULL DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) NOT NULL DEFAULT '0.00',
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costo` int(11) DEFAULT NULL,
  `centro_costo` varchar(225) COLLATE latin1_general_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_orden_compra` (`id_requisicion_compra`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for compras_requisicion_tipo
-- ----------------------------
DROP TABLE IF EXISTS `compras_requisicion_tipo`;
CREATE TABLE `compras_requisicion_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for comprobante_egreso
-- ----------------------------
DROP TABLE IF EXISTS `comprobante_egreso`;
CREATE TABLE `comprobante_egreso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicial` date DEFAULT NULL,
  `fecha_comprobante` date DEFAULT NULL,
  `fecha_generado` date DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `numero_cheque` int(11) DEFAULT NULL,
  `id_configuracion_cuenta` int(11) DEFAULT '0',
  `configuracion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `estado` int(1) DEFAULT '0',
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `id_flujo_efectivo` int(11) DEFAULT '0',
  `flujo_efectivo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tipo` varchar(10) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Si el comprobante de egreso es un abono, este campo estara vacio, si es igual a cuentas, quiere decir que  el comprobante de egreso es por cuentas',
  `debug` int(1) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=14061 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for comprobante_egreso_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `comprobante_egreso_cuentas`;
CREATE TABLE `comprobante_egreso_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_comprobante_egreso` int(11) DEFAULT NULL,
  `id_puc` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `debito` double(20,2) DEFAULT '0.00',
  `credito` double(20,2) DEFAULT '0.00',
  `saldo_pendiente` double(20,2) DEFAULT '0.00',
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `id_tabla_referencia` int(11) DEFAULT '0' COMMENT 'id de la fila de la tabla del documento cruce',
  `tipo_documento_cruce` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `numero_documento_cruce` bigint(50) DEFAULT '0',
  `observaciones` longtext COLLATE latin1_general_ci,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` bigint(20) DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_comprobante_egreso` (`id_comprobante_egreso`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=34040 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for conciliaciones
-- ----------------------------
DROP TABLE IF EXISTS `conciliaciones`;
CREATE TABLE `conciliaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_extracto` date DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `fecha_ini` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `saldo_extracto` double(11,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0=borrador, 1=generado, 2=cruzado, 3=cancelado',
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for conciliacion_bancos
-- ----------------------------
DROP TABLE IF EXISTS `conciliacion_bancos`;
CREATE TABLE `conciliacion_bancos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` bigint(50) DEFAULT NULL,
  `descripcion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for conciliacion_bancos_items
-- ----------------------------
DROP TABLE IF EXISTS `conciliacion_bancos_items`;
CREATE TABLE `conciliacion_bancos_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_conciliacion` int(11) DEFAULT NULL,
  `id_asiento` int(11) DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_arl
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_arl`;
CREATE TABLE `configuracion_arl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_arl` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_carnet
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_carnet`;
CREATE TABLE `configuracion_carnet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fondo` mediumblob,
  `fondo_ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_carnet_datos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_carnet_datos`;
CREATE TABLE `configuracion_carnet_datos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_carnet` int(11) NOT NULL,
  `campo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `width` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `height` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `top` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `left` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `font` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fontFamily` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `color` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codebar` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_carnet` (`id_carnet`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_clausulas_contrato
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_clausulas_contrato`;
CREATE TABLE `configuracion_clausulas_contrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `propiedad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `default_1` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `default_2` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_3` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `default_3` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_4` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `default_4` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_5` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `default_5` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_comprobante_egreso
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_comprobante_egreso`;
CREATE TABLE `configuracion_comprobante_egreso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_consecutivos_documentos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_consecutivos_documentos`;
CREATE TABLE `configuracion_consecutivos_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(1) DEFAULT '1',
  `digitos` int(11) DEFAULT '0',
  `modulo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_cuentas_pago
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_cuentas_pago`;
CREATE TABLE `configuracion_cuentas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `nombre_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `estado` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) NOT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_tercero` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `id_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(200) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_cuentas_pago_pos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_cuentas_pago_pos`;
CREATE TABLE `configuracion_cuentas_pago_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `consecutivo` int(11) DEFAULT '1' COMMENT 'este consecutivo se usa en caso de cheque cuenta o cortesia',
  `id_cuenta` int(11) DEFAULT NULL,
  `nombre_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `estado` varchar(20) COLLATE latin1_general_ci DEFAULT 'Contado',
  `id_empresa` int(11) NOT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_tercero` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `id_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(200) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_cuenta_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_costo_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_descuentos_pos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_descuentos_pos`;
CREATE TABLE `configuracion_descuentos_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double DEFAULT NULL COMMENT 'valor porcentual del descuento (solo el numero) ejemplo 10',
  `requiere_permiso` enum('No','Si') COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Validar si ese descuento requiere permiso o no',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_documentos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_documentos`;
CREATE TABLE `configuracion_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `texto` longblob,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `update_change` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_documentos_erp
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_documentos_erp`;
CREATE TABLE `configuracion_documentos_erp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `texto` longblob,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_documentos_tipo
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_documentos_tipo`;
CREATE TABLE `configuracion_documentos_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tabla_principal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_estados_proyectos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_estados_proyectos`;
CREATE TABLE `configuracion_estados_proyectos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_festivos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_festivos`;
CREATE TABLE `configuracion_festivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_formas_pago
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_formas_pago`;
CREATE TABLE `configuracion_formas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plazo` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_general
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_general`;
CREATE TABLE `configuracion_general` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `data` longtext,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for configuracion_global
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_global`;
CREATE TABLE `configuracion_global` (
  `id` int(11) NOT NULL,
  `propina` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `origen` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false' COMMENT 'define si el documento maestro captura el origen o fuente ',
  `ejecutivo` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false' COMMENT 'Define si en el documento maestro firma quien crea el documento, o si se selecciona  la persona que debe firmar',
  `tipo_servicio` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'true',
  `valor_en_pedido` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `totales_en_pedido` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `iva_espacios` int(2) NOT NULL DEFAULT '10',
  `nombre_empresa` varchar(255) COLLATE latin1_general_ci DEFAULT 'nombre empresa',
  `nombre_sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT 'nombre sucursal',
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '0000000000-0',
  `representante` varchar(255) COLLATE latin1_general_ci DEFAULT 'Representante Legal',
  `cedula` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `origen_cedula` varchar(255) COLLATE latin1_general_ci DEFAULT 'Ciudad - Departamento',
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT 'direccion',
  `Telefonos` varchar(255) COLLATE latin1_general_ci DEFAULT '(00-0) 000000000',
  `hora_inicio_busqueda` time NOT NULL DEFAULT '07:00:00',
  `hora_final_busqueda` time NOT NULL DEFAULT '18:00:00',
  `repetir_encabezado_remision` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `path` varchar(255) COLLATE latin1_general_ci DEFAULT 'localhost' COMMENT 'DEFINE CUAL ES EL PATH DE LA APLICACION (INCLUDES CON PARAMETROS)',
  `correo_SMTP` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `servidor_SMTP` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `puerto_SMTP` int(10) NOT NULL DEFAULT '25',
  `autenticacion_SMTP` varchar(255) COLLATE latin1_general_ci DEFAULT 'Si',
  `usuario_SMTP` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `password_SMTP` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `seguridad_SMTP` varchar(255) COLLATE latin1_general_ci DEFAULT 'Ninguna',
  `escala_imagen_pdf` int(2) NOT NULL DEFAULT '4',
  `debug1` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false' COMMENT 'define si muestra o no el contexmenu',
  `debug2` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false' COMMENT 'DEBUG PARA LA PANTALLA DE ENVIO DE MAIL',
  `consecutivo_pedido` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `multipropiedad` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `items_propiedad` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `smtp_propiedad` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `Path_temp` varchar(255) COLLATE latin1_general_ci DEFAULT '/opt/lampp/htdocs/ASISTE/ARCHIVOS_PROPIOS/adjuntos/',
  `puerto_predeterminado` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'true',
  `crm` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `conexion_siip3` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `salario_minimo` float NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_global_api_google
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_global_api_google`;
CREATE TABLE `configuracion_global_api_google` (
  `activo` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dominio` varchar(255) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_global_app
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_global_app`;
CREATE TABLE `configuracion_global_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_app` int(2) NOT NULL,
  `app` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `style_backimage_login` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `style_backimage_login_autoresize` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `style_login_validation` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `style_backimage_desktop` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `style_backimage_desktop_autoresize` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `style_backcolor` varchar(10) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_horas_extras
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_horas_extras`;
CREATE TABLE `configuracion_horas_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `ano` int(4) DEFAULT NULL,
  `hed` float(11,2) DEFAULT NULL,
  `hen` float(11,2) DEFAULT NULL,
  `hedf` float(11,2) DEFAULT NULL,
  `henf` float(11,2) DEFAULT NULL,
  `salario_base` float(11,2) DEFAULT NULL,
  `inicia_diurno` time DEFAULT '06:00:00',
  `inicia_nocturno` time DEFAULT '22:00:00',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_imagenes_documentos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_imagenes_documentos`;
CREATE TABLE `configuracion_imagenes_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_informe_estado_flujo_efectivo
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_informe_estado_flujo_efectivo`;
CREATE TABLE `configuracion_informe_estado_flujo_efectivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `clasificacion` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `contabilidad` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_informe_estado_resultado_niif
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_informe_estado_resultado_niif`;
CREATE TABLE `configuracion_informe_estado_resultado_niif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_puc_niif` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `clasificacion` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `informe` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_puc_niif` (`id_puc_niif`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_inventarios_documentos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_inventarios_documentos`;
CREATE TABLE `configuracion_inventarios_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_lineas_negocio
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_lineas_negocio`;
CREATE TABLE `configuracion_lineas_negocio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_mantenimiento_checklist
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_mantenimiento_checklist`;
CREATE TABLE `configuracion_mantenimiento_checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_mantenimiento_checklist_detalles
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_mantenimiento_checklist_detalles`;
CREATE TABLE `configuracion_mantenimiento_checklist_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_checklist` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `obligatorio` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_metodos_pago
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_metodos_pago`;
CREATE TABLE `configuracion_metodos_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `codigo_metodo_pago_dian` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_moneda
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_moneda`;
CREATE TABLE `configuracion_moneda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moneda` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `simbolo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `decimales` int(1) DEFAULT NULL,
  `label` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `Formato_hora` int(1) NOT NULL DEFAULT '1' COMMENT '1 ->  am  2-> 24',
  `idioma` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`,`moneda`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_motivos_cancelacion
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_motivos_cancelacion`;
CREATE TABLE `configuracion_motivos_cancelacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `motivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_origen
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_origen`;
CREATE TABLE `configuracion_origen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `recomendado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_propinas_pos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_propinas_pos`;
CREATE TABLE `configuracion_propinas_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double DEFAULT NULL COMMENT 'valor porcentual del descuento (solo el numero) ejemplo 10',
  `id_cuenta` int(11) DEFAULT NULL,
  `nombre_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_tx` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `cod_item_fe` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_proyectos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_proyectos`;
CREATE TABLE `configuracion_proyectos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(12) COLLATE latin1_general_ci NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_proyectos_actividades
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_proyectos_actividades`;
CREATE TABLE `configuracion_proyectos_actividades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_proyecto` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_sector_empresarial
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_sector_empresarial`;
CREATE TABLE `configuracion_sector_empresarial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `grupo_empresarial` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_tipos_contacto
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_tipos_contacto`;
CREATE TABLE `configuracion_tipos_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `unico` varchar(3) COLLATE latin1_general_ci DEFAULT 'no' COMMENT 'si es un unico parentesco por ej: una sola esposa',
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_vencimiento_documentos
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_vencimiento_documentos`;
CREATE TABLE `configuracion_vencimiento_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dias_vencimiento` int(11) DEFAULT NULL,
  `documento` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_zonas
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_zonas`;
CREATE TABLE `configuracion_zonas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(2) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(2) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `mapa` mediumblob,
  `ext_mapa` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `ancho_mapa` int(5) DEFAULT NULL,
  `alto_mapa` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for configuracion_zona_horaria
-- ----------------------------
DROP TABLE IF EXISTS `configuracion_zona_horaria`;
CREATE TABLE `configuracion_zona_horaria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zona_horaria` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for consecutivos
-- ----------------------------
DROP TABLE IF EXISTS `consecutivos`;
CREATE TABLE `consecutivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for contabilizacion_compra_venta
-- ----------------------------
DROP TABLE IF EXISTS `contabilizacion_compra_venta`;
CREATE TABLE `contabilizacion_compra_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_puc` int(11) DEFAULT NULL,
  `codigo_puc` int(11) DEFAULT NULL,
  `caracter` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double(11,2) NOT NULL,
  `descripcion` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `id_documento` (`id_documento`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1794251 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for contabilizacion_compra_venta_niif
-- ----------------------------
DROP TABLE IF EXISTS `contabilizacion_compra_venta_niif`;
CREATE TABLE `contabilizacion_compra_venta_niif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_puc` int(11) DEFAULT NULL,
  `codigo_puc` int(11) DEFAULT NULL,
  `caracter` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double(11,2) NOT NULL,
  `descripcion` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `id_documento` (`id_documento`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1701447 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for control_gastos_concepto
-- ----------------------------
DROP TABLE IF EXISTS `control_gastos_concepto`;
CREATE TABLE `control_gastos_concepto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `detalles` varchar(5000) COLLATE latin1_general_ci NOT NULL,
  `valor` float(11,2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for control_gastos_documento
-- ----------------------------
DROP TABLE IF EXISTS `control_gastos_documento`;
CREATE TABLE `control_gastos_documento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empleado` int(11) NOT NULL,
  `empleado` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `id_empleado_reviso` int(11) NOT NULL,
  `empleado_reviso` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` int(1) NOT NULL COMMENT '1 - Documento Abierto  \r\n2 - Documento Cerrado  \r\n3 - Documento Contabilizado  ',
  PRIMARY KEY (`id`,`empleado`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for control_gastos_soportes
-- ----------------------------
DROP TABLE IF EXISTS `control_gastos_soportes`;
CREATE TABLE `control_gastos_soportes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_concepto` int(11) NOT NULL,
  `documento` longblob NOT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `document_type` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_autorizadores_ordenes_compra
-- ----------------------------
DROP TABLE IF EXISTS `costo_autorizadores_ordenes_compra`;
CREATE TABLE `costo_autorizadores_ordenes_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT '1',
  `codigo_rol` int(11) DEFAULT NULL,
  `rol` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_rango` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_autorizadores_ordenes_compra_area
-- ----------------------------
DROP TABLE IF EXISTS `costo_autorizadores_ordenes_compra_area`;
CREATE TABLE `costo_autorizadores_ordenes_compra_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT '1',
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_autorizadores_requisicion
-- ----------------------------
DROP TABLE IF EXISTS `costo_autorizadores_requisicion`;
CREATE TABLE `costo_autorizadores_requisicion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT '1',
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_cuentas_transito
-- ----------------------------
DROP TABLE IF EXISTS `costo_cuentas_transito`;
CREATE TABLE `costo_cuentas_transito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta_colgaap_debito` int(11) DEFAULT NULL,
  `cuenta_colgaap_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif_debito` int(11) DEFAULT NULL,
  `cuenta_niif_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap_credito` int(11) DEFAULT NULL,
  `cuenta_colgaap_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif_credito` int(11) DEFAULT NULL,
  `cuenta_niif_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_departamentos
-- ----------------------------
DROP TABLE IF EXISTS `costo_departamentos`;
CREATE TABLE `costo_departamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `modulo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_documento
-- ----------------------------
DROP TABLE IF EXISTS `costo_documento`;
CREATE TABLE `costo_documento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for costo_documento_porcentaje
-- ----------------------------
DROP TABLE IF EXISTS `costo_documento_porcentaje`;
CREATE TABLE `costo_documento_porcentaje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_costo_documento` int(11) DEFAULT NULL,
  `id_costo_tipo` int(11) DEFAULT NULL,
  `costo_tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` bigint(50) DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` bigint(50) DEFAULT NULL,
  `valor` double(50,0) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_configuracion_actividades
-- ----------------------------
DROP TABLE IF EXISTS `crm_configuracion_actividades`;
CREATE TABLE `crm_configuracion_actividades` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_completa` varchar(6) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'cuando lleva fecha de inicio y fecha final',
  `fecha_vencimiento` varchar(6) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'cuando solo lleva fecha de vencimiento',
  `copiar_crm_obligatorio` varchar(5) COLLATE latin1_general_ci DEFAULT 'false' COMMENT 'check copia CRM obligatorio',
  `icono` int(2) DEFAULT NULL,
  `genera_visita` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `genera_llamada` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `id_departamento` tinyint(3) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_configuracion_actividades_departamentos
-- ----------------------------
DROP TABLE IF EXISTS `crm_configuracion_actividades_departamentos`;
CREATE TABLE `crm_configuracion_actividades_departamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for crm_configuracion_tipos_proyecto
-- ----------------------------
DROP TABLE IF EXISTS `crm_configuracion_tipos_proyecto`;
CREATE TABLE `crm_configuracion_tipos_proyecto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_log
-- ----------------------------
DROP TABLE IF EXISTS `crm_log`;
CREATE TABLE `crm_log` (
  `id` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `log` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_notificaciones
-- ----------------------------
DROP TABLE IF EXISTS `crm_notificaciones`;
CREATE TABLE `crm_notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `objetivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_actividad` int(11) DEFAULT NULL,
  `tema` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_actividad` int(1) DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `asignado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `alarma` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ID` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_objetivos
-- ----------------------------
DROP TABLE IF EXISTS `crm_objetivos`;
CREATE TABLE `crm_objetivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) DEFAULT NULL,
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `tipo` int(1) DEFAULT NULL COMMENT '1.Documento   2.Personalizado',
  `referencia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `objetivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `vencimiento` datetime DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(1) DEFAULT '0',
  `observacion_finaliza` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_finaliza` int(11) DEFAULT NULL,
  `usuario_finaliza` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_finaliza` datetime DEFAULT NULL,
  `actividades` int(11) DEFAULT '0',
  `acciones` int(11) DEFAULT '0',
  `valor` double(20,2) DEFAULT '0.00',
  `id_linea` int(11) DEFAULT NULL,
  `linea_negocio` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `probabilidad_exito` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `prioridad` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `estado_proyecto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `tipo_proyecto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ID` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_objetivos_actividades
-- ----------------------------
DROP TABLE IF EXISTS `crm_objetivos_actividades`;
CREATE TABLE `crm_objetivos_actividades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_objetivo` int(1) DEFAULT NULL,
  `referencia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `objetivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` int(1) DEFAULT NULL,
  `tipo_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `icono` int(2) DEFAULT NULL,
  `tema` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `asignado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `fecha_actividad` datetime DEFAULT NULL,
  `fechai` date DEFAULT NULL,
  `horai` time DEFAULT NULL,
  `fechaf` date DEFAULT NULL,
  `horaf` time DEFAULT NULL,
  `observacion` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(1) DEFAULT '0',
  `observacion_finaliza` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_finaliza` int(11) DEFAULT NULL,
  `usuario_finaliza` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_finaliza` datetime DEFAULT NULL,
  `acciones` smallint(3) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ID` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_objetivos_actividades_acciones
-- ----------------------------
DROP TABLE IF EXISTS `crm_objetivos_actividades_acciones`;
CREATE TABLE `crm_objetivos_actividades_acciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `id_actividad` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `accion` varchar(1000) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ID` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_objetivos_actividades_personas
-- ----------------------------
DROP TABLE IF EXISTS `crm_objetivos_actividades_personas`;
CREATE TABLE `crm_objetivos_actividades_personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_actividad` int(11) DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `asignado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_objetivos_adjuntos
-- ----------------------------
DROP TABLE IF EXISTS `crm_objetivos_adjuntos`;
CREATE TABLE `crm_objetivos_adjuntos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(15) DEFAULT NULL,
  `nombre_archivo` varchar(300) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_objetivo`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for crm_objetivos_log
-- ----------------------------
DROP TABLE IF EXISTS `crm_objetivos_log`;
CREATE TABLE `crm_objetivos_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `estado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `accion` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for cuentas_default_activos_fijos
-- ----------------------------
DROP TABLE IF EXISTS `cuentas_default_activos_fijos`;
CREATE TABLE `cuentas_default_activos_fijos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_grupo` int(11) DEFAULT NULL,
  `codigo_grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_depreciacion_colgaap_debito` int(11) DEFAULT NULL,
  `cuenta_depreciacion_colgaap_debito` int(11) DEFAULT NULL,
  `descripcion_cuenta_depreciacion_colgaap_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_depreciacion_colgaap_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_depreciacion_colgaap_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_depreciacion_colgaap_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_depreciacion_niif_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_depreciacion_niif_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_depreciacion_niif_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_depreciacion_niif_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_depreciacion_niif_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_depreciacion_niif_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_deterioro_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_deterioro_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_deterioro_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_deterioro_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_deterioro_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_deterioro_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_cuenta` (`id_cuenta_depreciacion_colgaap_debito`) USING BTREE,
  KEY `cuenta` (`cuenta_depreciacion_colgaap_debito`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for debug
-- ----------------------------
DROP TABLE IF EXISTS `debug`;
CREATE TABLE `debug` (
  `prueba` varchar(255) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for deterioro_cartera_clientes
-- ----------------------------
DROP TABLE IF EXISTS `deterioro_cartera_clientes`;
CREATE TABLE `deterioro_cartera_clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tasa_descuento` double(11,2) DEFAULT NULL,
  `rotacion` double(11,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(11) DEFAULT '0',
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for deterioro_cartera_clientes_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `deterioro_cartera_clientes_cuentas`;
CREATE TABLE `deterioro_cartera_clientes_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for deterioro_cartera_clientes_facturas
-- ----------------------------
DROP TABLE IF EXISTS `deterioro_cartera_clientes_facturas`;
CREATE TABLE `deterioro_cartera_clientes_facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_deterioro_cliente` int(11) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` int(11) DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `numero_factura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_factura` double(11,2) DEFAULT NULL,
  `id_sucursal_factura` int(11) DEFAULT NULL,
  `sucursal_factura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costo` int(11) DEFAULT NULL,
  `estado` varchar(50) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Puede ser:\r\n->compromiso de pago\r\n-> incobrable\r\n-> juridico',
  `tiempo_estimado_pago` double(11,2) DEFAULT NULL,
  `porcentaje_recaudo` double(11,2) DEFAULT NULL,
  `deterioro` double(11,2) DEFAULT '0.00',
  `deteriorable` varchar(50) COLLATE latin1_general_ci DEFAULT 'true',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for deterioro_cartera_proveedores
-- ----------------------------
DROP TABLE IF EXISTS `deterioro_cartera_proveedores`;
CREATE TABLE `deterioro_cartera_proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tasa_descuento` double(11,2) DEFAULT NULL,
  `rotacion` double(11,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(11) DEFAULT '0',
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for deterioro_cartera_proveedores_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `deterioro_cartera_proveedores_cuentas`;
CREATE TABLE `deterioro_cartera_proveedores_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for deterioro_cartera_proveedores_facturas
-- ----------------------------
DROP TABLE IF EXISTS `deterioro_cartera_proveedores_facturas`;
CREATE TABLE `deterioro_cartera_proveedores_facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_deterioro_cliente` int(11) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` int(11) DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `numero_factura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_factura` double(11,2) DEFAULT NULL,
  `id_sucursal_factura` int(11) DEFAULT NULL,
  `sucursal_factura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costo` int(11) DEFAULT NULL,
  `estado` varchar(50) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Puede ser:\r\n->compromiso de pago\r\n-> incobrable\r\n-> juridico',
  `tiempo_estimado_pago` double(11,2) DEFAULT NULL,
  `porcentaje_recaudo` double(11,2) DEFAULT NULL,
  `deterioro` double(11,2) DEFAULT '0.00',
  `deteriorable` varchar(50) COLLATE latin1_general_ci DEFAULT 'true',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for devoluciones_compra
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones_compra`;
CREATE TABLE `devoluciones_compra` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_documento_compra` int(11) DEFAULT NULL,
  `documento_compra` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento_compra` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `hora_finalizacion` time DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_proveedor` int(15) DEFAULT NULL,
  `cod_proveedor` int(11) DEFAULT NULL,
  `proveedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(15) DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura, 3 -> cancelado, 4 -> ingresadas todas las unidades',
  `total_nota_sin_abono` double(11,2) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_documento_compra` (`id_documento_compra`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_proveedor` (`id_proveedor`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for devoluciones_compra_inventario
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones_compra_inventario`;
CREATE TABLE `devoluciones_compra_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_devolucion_compra` int(15) DEFAULT NULL,
  `id_fila_cargada` int(11) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `costo_unitario` double(15,2) DEFAULT '0.00',
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `check_opcion_contable` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_devolucion_compra` (`id_devolucion_compra`) USING BTREE,
  KEY `id_fila_cargada` (`id_fila_cargada`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1398 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for devoluciones_venta
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones_venta`;
CREATE TABLE `devoluciones_venta` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_documento_venta` int(11) DEFAULT NULL COMMENT 'id del registro cargado, de remision o factura',
  `documento_venta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'que documento es si en remision o factura',
  `numero_documento_venta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'es el consecutivo de la remision o el numero de la factura',
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `hora_finalizacion` time DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_cliente` int(15) DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(15) DEFAULT NULL,
  `id_motivo_dian` int(1) DEFAULT NULL,
  `descripcion_motivo_dian` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_metodo_pago` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `metodo_pago` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 3 -> cancelado',
  `fecha_DE` date DEFAULT NULL,
  `hora_DE` time DEFAULT NULL,
  `response_DE` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_DE` int(10) DEFAULT NULL,
  `nombre_usuario_DE` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `cedula_usuario_DE` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `total_nota_sin_abono` double(11,2) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `UUID` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_documento_venta` (`id_documento_venta`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_cliente` (`id_cliente`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=822 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for devoluciones_venta_grupos
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones_venta_grupos`;
CREATE TABLE `devoluciones_venta_grupos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_devolucion_venta` int(15) DEFAULT NULL,
  `id_fila_grupo_factura_venta` int(11) DEFAULT NULL COMMENT 'id de la fila del grupo de la factura deventa',
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `costo_unitario` double(15,2) DEFAULT '0.00' COMMENT 'precio de venta si iva',
  `observaciones` longtext COLLATE latin1_general_ci,
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `nombre_impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje_impuesto` double(11,2) DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT '0.00',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura_venta` (`id_devolucion_venta`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for devoluciones_venta_inventario
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones_venta_inventario`;
CREATE TABLE `devoluciones_venta_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_devolucion_venta` int(15) DEFAULT NULL,
  `id_fila_cargada` int(11) DEFAULT NULL,
  `id_inventario` int(15) DEFAULT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,6) DEFAULT '0.000000',
  `costo_inventario` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) DEFAULT '0.00',
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `descuento` double(10,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `check_opcion_contable` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `opcion_contable` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_devolucion_venta` (`id_devolucion_venta`) USING BTREE,
  KEY `id_fila_cargada` (`id_fila_cargada`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1573 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for diferidos
-- ----------------------------
DROP TABLE IF EXISTS `diferidos`;
CREATE TABLE `diferidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `estado` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_debito` int(11) DEFAULT NULL,
  `cuenta_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_debito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_debito` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_credito` int(11) DEFAULT NULL,
  `cuenta_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_credito` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_credito` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `cod_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(11,2) DEFAULT NULL,
  `meses` int(11) DEFAULT NULL,
  `saldo` double(11,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados
-- ----------------------------
DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` int(2) DEFAULT '1',
  `tipo_documento_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(3) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(3) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_negocio` int(3) DEFAULT NULL,
  `unidad_negocio` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_rol` int(2) DEFAULT NULL,
  `rol` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(255) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `username` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email_empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email_notificaciones` varchar(255) COLLATE latin1_general_ci DEFAULT 'email_empresa',
  `nacimiento` date DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `barrio` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_residencia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email_personal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `celular1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_celular_empresa` int(11) DEFAULT NULL,
  `celular_empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `foto` longblob,
  `id_contrato` int(3) DEFAULT NULL,
  `contrato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `salario_base` float(11,2) DEFAULT '566700.00',
  `salario` float(11,2) DEFAULT '0.00',
  `ad_contrato` blob,
  `ad_certificado_judicial` blob,
  `ad_cedula` blob,
  `ad_certificado_estudios` blob,
  `ad_hoja_vida` blob,
  `ad_afiliaciones` blob,
  `alerta_actualizacion` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `activo` int(1) DEFAULT '1',
  `ciudad_cedula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `eps` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `arp` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tecnico_operativo` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `conductor` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `vendedor` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `qrcode` mediumblob,
  `color_menu` varchar(11) COLLATE latin1_general_ci DEFAULT '0,0,0',
  `color_fondo` varchar(11) COLLATE latin1_general_ci DEFAULT '32,124,229',
  `change_update` int(11) DEFAULT NULL,
  `sinc_tercero` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_tercero` int(11) DEFAULT NULL,
  `acceso_sistema` varchar(10) COLLATE latin1_general_ci DEFAULT 'true',
  `fecha_nacimiento` date DEFAULT NULL,
  `id_pais_nacimiento` int(11) DEFAULT NULL,
  `pais_nacimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento_nacimiento` int(11) DEFAULT NULL,
  `departamento_nacimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad_nacimiento` int(11) DEFAULT NULL,
  `ciudad_nacimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pais_documento` int(11) DEFAULT NULL,
  `pais_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento_documento` int(11) DEFAULT NULL,
  `departamento_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad_documento` int(11) DEFAULT NULL,
  `ciudad_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `sexo` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `extranjero_obligado_cotizar` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `residente_en_exterior` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_cotizante` int(11) DEFAULT NULL,
  `subtipo_cotizante` int(11) DEFAULT NULL,
  `codigo_departamento_laboral` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_municipio_laboral` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_administradora_pensiones` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_entidad_salud` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_EPS_EOC` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_CCF` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `estado_civil` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `estrato_residencia` tinyint(2) DEFAULT NULL,
  `observaciones_empleado` longtext COLLATE latin1_general_ci,
  `notificacion_correo_cartera` varchar(5) CHARACTER SET latin1 DEFAULT 'false' COMMENT 'Campo para recibir notificacion al correo (Job).',
  `acepta_terminos_condiciones` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_aceptacion_terminos_condiciones` date DEFAULT NULL,
  `hora_aceptacion_terminos_condiciones` time DEFAULT NULL,
  `ip_aceptacion_terminos_condiciones` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `token` longtext COLLATE latin1_general_ci,
  `token_pos` longtext COLLATE latin1_general_ci,
  `pin` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_unidad_negocio` (`id_unidad_negocio`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_rol` (`id_rol`) USING BTREE,
  KEY `id_cargo` (`id_cargo`) USING BTREE,
  KEY `id_celular_empresa` (`id_celular_empresa`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=234 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_adicional
-- ----------------------------
DROP TABLE IF EXISTS `empleados_adicional`;
CREATE TABLE `empleados_adicional` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(15) DEFAULT NULL,
  `ciudad_trabajo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fondo_pension` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `factor_rh` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo_sanguineo` varchar(3) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_sangre` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `alergico_medicamento` varchar(3) COLLATE latin1_general_ci DEFAULT 'no',
  `cual_alergico_medicamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `toma_medicamento` varchar(3) COLLATE latin1_general_ci DEFAULT 'no',
  `cual_toma_medicamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_cargos
-- ----------------------------
DROP TABLE IF EXISTS `empleados_cargos`;
CREATE TABLE `empleados_cargos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_contratos
-- ----------------------------
DROP TABLE IF EXISTS `empleados_contratos`;
CREATE TABLE `empleados_contratos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT '0',
  `tipo_documento_empleado` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_contrato` int(11) DEFAULT NULL,
  `fecha_inicio_contrato` date DEFAULT NULL,
  `fecha_fin_contrato` date DEFAULT NULL,
  `salario_basico` double(20,2) DEFAULT '0.00',
  `salario_integral` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `fecha_inicio_nomina` date DEFAULT NULL,
  `id_tipo_contrato` int(11) DEFAULT NULL,
  `tipo_contrato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_cuenta_bancaria` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo_trabajo` int(11) DEFAULT NULL,
  `grupo_trabajo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_nivel_riesgo_laboral` int(11) DEFAULT '0',
  `nivel_riesgo_laboral` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_nivel_riesgo_laboral` double(20,3) DEFAULT '0.000',
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` int(11) DEFAULT '0',
  `nombre_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_cancelacion` date DEFAULT NULL,
  `id_motivo_cancelacion` int(11) DEFAULT NULL,
  `motivo_cancelacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion_cancelacion` longtext COLLATE latin1_general_ci,
  `estado` int(11) DEFAULT '0' COMMENT '0 = vigente , 1=terminado, 2=vacaciones',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_sucursal_creacion` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `debug` int(1) DEFAULT NULL,
  `vencimiento_firmado` varchar(5) COLLATE latin1_general_ci DEFAULT 'false',
  `usuario_vencimiento_firmado` int(11) DEFAULT NULL,
  `fecha_vencimiento_firmado` datetime DEFAULT NULL,
  `archivo_vencimiento_firmado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_trabajador` int(11) DEFAULT NULL,
  `id_subtipo_trabajador` int(11) DEFAULT NULL,
  `id_forma_pago` int(11) DEFAULT NULL,
  `id_medio_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_banco` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_cuenta_bancaria` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_tipo_contrato` (`id_tipo_contrato`) USING BTREE,
  KEY `id_grupo_trabajo` (`id_grupo_trabajo`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=172 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_contratos_documentos
-- ----------------------------
DROP TABLE IF EXISTS `empleados_contratos_documentos`;
CREATE TABLE `empleados_contratos_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `randomico_archivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ID` (`id`) USING BTREE,
  KEY `ID_PEDIDO` (`id_contrato`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_contratos_entidades
-- ----------------------------
DROP TABLE IF EXISTS `empleados_contratos_entidades`;
CREATE TABLE `empleados_contratos_entidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_entidad` int(11) DEFAULT NULL,
  `entidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_entidad` (`id_entidad`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1155 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_contratos_entidades_traslados
-- ----------------------------
DROP TABLE IF EXISTS `empleados_contratos_entidades_traslados`;
CREATE TABLE `empleados_contratos_entidades_traslados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `documento_empleado` int(20) DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_entidad` int(11) DEFAULT NULL,
  `documento_entidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_entidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_contratos_modificacion_salarios
-- ----------------------------
DROP TABLE IF EXISTS `empleados_contratos_modificacion_salarios`;
CREATE TABLE `empleados_contratos_modificacion_salarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT '0',
  `tipo_documento_empleado` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `salario_anterior` double(20,2) DEFAULT '0.00',
  `salario_nuevo` double(20,2) DEFAULT '0.00',
  `fecha_modificacion` date DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_documentos
-- ----------------------------
DROP TABLE IF EXISTS `empleados_documentos`;
CREATE TABLE `empleados_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `randomico_documento` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `nombre_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` int(11) NOT NULL,
  `tipo_documento_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `document_type` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_estudios
-- ----------------------------
DROP TABLE IF EXISTS `empleados_estudios`;
CREATE TABLE `empleados_estudios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_estudio` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `institucion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `grado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `ciclo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `modalidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `otra_modalidad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `otro` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tarjeta_profesional` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_experiencia_laboral
-- ----------------------------
DROP TABLE IF EXISTS `empleados_experiencia_laboral`;
CREATE TABLE `empleados_experiencia_laboral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `empresa` varchar(255) DEFAULT NULL,
  `nombre_empresa` varchar(255) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `actividad` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `jefe_inmediato` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `salario` varchar(255) DEFAULT NULL,
  `salario_mensual` varchar(255) DEFAULT NULL,
  `otros_ingresos` varchar(255) DEFAULT NULL,
  `mensual` varchar(255) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_historial_vinculacion
-- ----------------------------
DROP TABLE IF EXISTS `empleados_historial_vinculacion`;
CREATE TABLE `empleados_historial_vinculacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `motivo` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `id_empresa` int(3) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(3) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=242 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_hoja_de_vida
-- ----------------------------
DROP TABLE IF EXISTS `empleados_hoja_de_vida`;
CREATE TABLE `empleados_hoja_de_vida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `ciudad_expedicion_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_nacimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_horas_extras
-- ----------------------------
DROP TABLE IF EXISTS `empleados_horas_extras`;
CREATE TABLE `empleados_horas_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fechai` date NOT NULL,
  `horai` time NOT NULL,
  `fechas` date NOT NULL,
  `horas` time NOT NULL,
  `med` float(11,2) DEFAULT '0.00',
  `med_valor` float(11,2) DEFAULT '0.00',
  `men` float(11,2) DEFAULT '0.00',
  `men_valor` float(11,2) DEFAULT '0.00',
  `medf` float(11,2) DEFAULT '0.00',
  `medf_valor` float(11,2) DEFAULT '0.00',
  `menf` float(11,2) DEFAULT '0.00',
  `menf_valor` float(11,2) DEFAULT '0.00',
  `total` float(11,2) DEFAULT '0.00',
  `change_update` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `cedula` (`cedula`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_idiomas
-- ----------------------------
DROP TABLE IF EXISTS `empleados_idiomas`;
CREATE TABLE `empleados_idiomas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `idioma` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nativo` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `institucion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `lectura` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `escritura` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `habla` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_informacion_contacto
-- ----------------------------
DROP TABLE IF EXISTS `empleados_informacion_contacto`;
CREATE TABLE `empleados_informacion_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `id_parentesco` int(11) DEFAULT NULL,
  `parentesco` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_completo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombres` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellidos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `celular` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ocupacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `contacto_principal` varchar(3) COLLATE latin1_general_ci DEFAULT 'no',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_permisos
-- ----------------------------
DROP TABLE IF EXISTS `empleados_permisos`;
CREATE TABLE `empleados_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden` int(11) NOT NULL,
  `nivel` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `root` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `modulo` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_registro
-- ----------------------------
DROP TABLE IF EXISTS `empleados_registro`;
CREATE TABLE `empleados_registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `hora` time NOT NULL,
  `fecha` date NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(5) COLLATE latin1_general_ci DEFAULT 'in',
  PRIMARY KEY (`id`,`fecha`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_roles
-- ----------------------------
DROP TABLE IF EXISTS `empleados_roles`;
CREATE TABLE `empleados_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` int(2) NOT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_roles_permisos
-- ----------------------------
DROP TABLE IF EXISTS `empleados_roles_permisos`;
CREATE TABLE `empleados_roles_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_rol` (`id_rol`) USING BTREE,
  KEY `id_permiso` (`id_permiso`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6075 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for empleados_sucursales_traslados
-- ----------------------------
DROP TABLE IF EXISTS `empleados_sucursales_traslados`;
CREATE TABLE `empleados_sucursales_traslados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empleados_tipo_documento
-- ----------------------------
DROP TABLE IF EXISTS `empleados_tipo_documento`;
CREATE TABLE `empleados_tipo_documento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empresas
-- ----------------------------
DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` int(11) DEFAULT NULL,
  `tipo_documento_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento` bigint(50) DEFAULT NULL,
  `digito_verificacion` int(10) DEFAULT NULL,
  `nit_completo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_regimen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `razon_social` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `actividad_economica` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_responsable` int(11) DEFAULT NULL,
  `responsable` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `sucursales` int(3) DEFAULT '0',
  `telefono` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `celular` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `zona_horaria` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_moneda` int(11) DEFAULT NULL,
  `simbolo_moneda` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `decimales_moneda` int(11) DEFAULT NULL,
  `descripcion_moneda` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `formato_hora` varchar(6) COLLATE latin1_general_ci DEFAULT 'AM/PM',
  `interface` varchar(20) COLLATE latin1_general_ci DEFAULT 'false',
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `client_token` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `access_token` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_persona_codigo` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_persona_nombre` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `primer_apellido` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `segundo_apellido` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `primer_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `otros_nombres` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE,
  KEY `id_responsable` (`id_responsable`) USING BTREE,
  KEY `id_moneda` (`id_moneda`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empresas_config_correo
-- ----------------------------
DROP TABLE IF EXISTS `empresas_config_correo`;
CREATE TABLE `empresas_config_correo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `correo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `servidor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `puerto` int(11) NOT NULL,
  `autenticacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `seguridad_smtp` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empresas_formatos
-- ----------------------------
DROP TABLE IF EXISTS `empresas_formatos`;
CREATE TABLE `empresas_formatos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `ext_formato` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `formato` mediumblob,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empresas_sucursales
-- ----------------------------
DROP TABLE IF EXISTS `empresas_sucursales`;
CREATE TABLE `empresas_sucursales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_responsable` int(11) NOT NULL,
  `responsable` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `bodegas` int(11) NOT NULL DEFAULT '0',
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo` int(11) DEFAULT '0',
  `activo` int(1) NOT NULL DEFAULT '1',
  `direccion` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_postal` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_matricula_mercantil` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `UUID` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_responsable` (`id_responsable`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for empresas_sucursales_bodegas
-- ----------------------------
DROP TABLE IF EXISTS `empresas_sucursales_bodegas`;
CREATE TABLE `empresas_sucursales_bodegas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for equipos_registro
-- ----------------------------
DROP TABLE IF EXISTS `equipos_registro`;
CREATE TABLE `equipos_registro` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `serial` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(2) DEFAULT '0',
  `fecha` datetime DEFAULT NULL,
  `persona_solicita` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `autorizado` int(5) DEFAULT '0',
  `sucursal` int(5) DEFAULT NULL,
  `sucursal_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(5) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_autorizacion` datetime DEFAULT NULL,
  `envio_confirmacion` int(5) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for extractos
-- ----------------------------
DROP TABLE IF EXISTS `extractos`;
CREATE TABLE `extractos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_extracto` date DEFAULT NULL,
  `id_tercero` int(10) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `saldo_extracto` double(11,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0=borrador, 1=generado, 2=cruzado, 3=cancelado',
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for extractos_detalle
-- ----------------------------
DROP TABLE IF EXISTS `extractos_detalle`;
CREATE TABLE `extractos_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_extracto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `valor` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for facturas_saldos_iniciales
-- ----------------------------
DROP TABLE IF EXISTS `facturas_saldos_iniciales`;
CREATE TABLE `facturas_saldos_iniciales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `fecha_generacion` date DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_factura` varchar(10) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'FV=factura venta, FC= factura compra',
  `id_cuenta_pago` int(11) DEFAULT NULL,
  `cuenta_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `contrapartida_colgaap` int(11) DEFAULT NULL,
  `contrapartida_niif` int(11) DEFAULT NULL,
  `estado` int(10) DEFAULT '0' COMMENT '0= sin guardar, 1 guardado',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_cuenta_pago` (`id_cuenta_pago`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for flujo_efectivo
-- ----------------------------
DROP TABLE IF EXISTS `flujo_efectivo`;
CREATE TABLE `flujo_efectivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for grupos_empresariales
-- ----------------------------
DROP TABLE IF EXISTS `grupos_empresariales`;
CREATE TABLE `grupos_empresariales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for historico_equipos
-- ----------------------------
DROP TABLE IF EXISTS `historico_equipos`;
CREATE TABLE `historico_equipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` int(2) DEFAULT NULL,
  `tipo_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `tipo_user` int(1) DEFAULT NULL COMMENT '0 -> usuario  ,   1 -> Cliente',
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_evento` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2187 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for impuestos
-- ----------------------------
DROP TABLE IF EXISTS `impuestos`;
CREATE TABLE `impuestos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `impuesto` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_impuesto_dian` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(11,2) DEFAULT NULL,
  `compra` varchar(2) COLLATE latin1_general_ci DEFAULT 'Si',
  `cuenta_compra` int(20) DEFAULT NULL,
  `cuenta_compra_niif` int(20) DEFAULT NULL,
  `cuenta_compra_devolucion` int(20) DEFAULT NULL,
  `cuenta_compra_devolucion_niif` int(20) DEFAULT NULL,
  `venta` varchar(2) COLLATE latin1_general_ci DEFAULT 'Si',
  `cuenta_venta` int(20) DEFAULT NULL,
  `cuenta_venta_niif` int(20) DEFAULT NULL,
  `cuenta_venta_devolucion` int(20) DEFAULT NULL,
  `cuenta_venta_devolucion_niif` int(20) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `cruzar_costo_activo_fijo` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos`;
CREATE TABLE `informes_formatos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `nombre` longtext COLLATE latin1_general_ci,
  `filtro_terceros` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_ccos` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_corte_anual` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_corte_mensual` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_rango_fechas` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_cuentas` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones`;
CREATE TABLE `informes_formatos_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `titulo` longtext COLLATE latin1_general_ci,
  `totalizado` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones_columnas
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones_columnas`;
CREATE TABLE `informes_formatos_secciones_columnas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(255) DEFAULT '0',
  `id_seccion` int(11) DEFAULT NULL,
  `formato` longtext COLLATE latin1_general_ci,
  `orden` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `titulo` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones_filas
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones_filas`;
CREATE TABLE `informes_formatos_secciones_filas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(255) DEFAULT '0',
  `id_seccion` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `tercero_unico` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones_filas_centro_costos
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones_filas_centro_costos`;
CREATE TABLE `informes_formatos_secciones_filas_centro_costos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `codigo_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_columna` int(11) DEFAULT NULL,
  `columna` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_fila` int(11) DEFAULT NULL,
  `fila` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones_filas_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones_filas_cuentas`;
CREATE TABLE `informes_formatos_secciones_filas_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `codigo_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_columna` int(11) DEFAULT NULL,
  `columna` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_fila` int(11) DEFAULT NULL,
  `fila` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_inicial` int(11) DEFAULT NULL,
  `cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_final` int(11) DEFAULT NULL,
  `cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `forma_calculo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Son las 5 maneras como se calcula la cuenta\r\n\r\nsuma_debitos:\r\n     Suma acumulada de los debitos\r\nsuma_creditos:\r\n     Suma acumulada de los creditos\r\ndebito_menos_credito:\r\n     Suma acumulada de debito menos la suma      acumulada de creditos\r\nsaldo_actual\r\n     Es el saldo actual de la cuenta\r\n     (saldo anterior + suma acumulada debitos) -             suma acumulada creditos\r\nsaldo_inicial\r\n     Saldo anterior de la cuenta',
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones_filas_documentos
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones_filas_documentos`;
CREATE TABLE `informes_formatos_secciones_filas_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `codigo_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_columna` int(11) DEFAULT NULL,
  `columna` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_fila` int(11) DEFAULT NULL,
  `fila` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_formatos_secciones_filas_terceros
-- ----------------------------
DROP TABLE IF EXISTS `informes_formatos_secciones_filas_terceros`;
CREATE TABLE `informes_formatos_secciones_filas_terceros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `codigo_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_columna` int(11) DEFAULT NULL,
  `columna` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_fila` int(11) DEFAULT NULL,
  `fila` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_listado
-- ----------------------------
DROP TABLE IF EXISTS `informes_listado`;
CREATE TABLE `informes_listado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `informe` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_informe` int(11) NOT NULL,
  `clase_informe` varchar(255) COLLATE latin1_general_ci DEFAULT 'general',
  `icono` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_niif_formatos
-- ----------------------------
DROP TABLE IF EXISTS `informes_niif_formatos`;
CREATE TABLE `informes_niif_formatos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `nombre` longtext COLLATE latin1_general_ci,
  `filtro_terceros` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_ccos` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_corte_anual` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_corte_mensual` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_rango_fechas` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `filtro_cuentas` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_niif_formatos_secciones
-- ----------------------------
DROP TABLE IF EXISTS `informes_niif_formatos_secciones`;
CREATE TABLE `informes_niif_formatos_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_seccion` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `id_formato` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `formula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `totalizado` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `label_totalizado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `formula_totalizado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Formula para calcular el valor del total de la seccion en el reporte\r\n[codigo fila] = los corchetes para reemplazar por filas\r\n{codigo seccion} = llaves para valores de seccion\r\n\r\nen ambos casos si son valores de una fila o seccion de otro informe el codigo del informe ira primero seguido de un mayor que, seguido del codigo de fila o seccion del otro informe, ejemplo\r\n[21000>01]\r\n{21000>01}',
  `codigo_seccion_padre` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `padding` int(11) DEFAULT '0',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_niif_formatos_secciones_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `informes_niif_formatos_secciones_cuentas`;
CREATE TABLE `informes_niif_formatos_secciones_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `id_seccion` int(11) DEFAULT NULL,
  `id_cuenta_inicial` int(11) DEFAULT NULL,
  `cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_final` int(11) DEFAULT NULL,
  `cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `forma_calculo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Son las 5 maneras como se calcula la cuenta\r\n\r\nsuma_debitos:\r\n     Suma acumulada de los debitos\r\nsuma_creditos:\r\n     Suma acumulada de los creditos\r\ndebito_menos_credito:\r\n     Suma acumulada de debito menos la suma      acumulada de creditos\r\nsaldo_actual\r\n     Es el saldo actual de la cuenta\r\n     (saldo anterior + suma acumulada debitos) -             suma acumulada creditos\r\nsaldo_inicial\r\n     Saldo anterior de la cuenta',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_niif_formatos_secciones_filas
-- ----------------------------
DROP TABLE IF EXISTS `informes_niif_formatos_secciones_filas`;
CREATE TABLE `informes_niif_formatos_secciones_filas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(255) DEFAULT '0',
  `id_seccion` int(11) DEFAULT NULL,
  `codigo` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `naturaleza` varchar(100) COLLATE latin1_general_ci DEFAULT '',
  `formula` longtext COLLATE latin1_general_ci COMMENT 'Formula para calcular el valor de la fila en el reporte\r\n[codigo fila] = los corchetes para reemplazar por filas\r\n{codigo seccion} = llaves para valores de seccion\r\n\r\nen ambos casos si son valores de una fila o seccion de otro informe el codigo del informe ira primero seguido de un mayor que seguido del codigo de fila o seccion del otro informe\r\n[21000>01]\r\n{21000>01}',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for informes_niif_formatos_secciones_filas_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `informes_niif_formatos_secciones_filas_cuentas`;
CREATE TABLE `informes_niif_formatos_secciones_filas_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `id_seccion` int(11) DEFAULT NULL,
  `id_fila` int(11) DEFAULT NULL,
  `id_cuenta_inicial` int(11) DEFAULT NULL,
  `cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_final` int(11) DEFAULT NULL,
  `cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `forma_calculo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Son las 5 maneras como se calcula la cuenta\r\n\r\nsuma_debitos:\r\n     Suma acumulada de los debitos\r\nsuma_creditos:\r\n     Suma acumulada de los creditos\r\ndebito_menos_credito:\r\n     Suma acumulada de debito menos la suma      acumulada de creditos\r\nsaldo_actual\r\n     Es el saldo actual de la cuenta\r\n     (saldo anterior + suma acumulada debitos) -             suma acumulada creditos\r\nsaldo_inicial\r\n     Saldo anterior de la cuenta',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_ajuste
-- ----------------------------
DROP TABLE IF EXISTS `inventario_ajuste`;
CREATE TABLE `inventario_ajuste` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_documento` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(15) DEFAULT NULL,
  `cod_tercero` int(11) DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura, 3 -> cancelado, 4 -> ingresadas todas las unidades',
  `id_centro_costo` int(11) DEFAULT '0',
  `codigo_centro_costo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_remision_venta` int(11) DEFAULT NULL,
  `consecutivo_remision_venta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_entrada_almacen` int(11) DEFAULT NULL,
  `consecutivo_entrada_almacen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_centro_costo` (`id_centro_costo`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2370 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_ajuste_detalle
-- ----------------------------
DROP TABLE IF EXISTS `inventario_ajuste_detalle`;
CREATE TABLE `inventario_ajuste_detalle` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_ajuste_inventario` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT '',
  `cantidad_inventario` double(20,2) DEFAULT NULL,
  `costo_inventario` double(20,2) DEFAULT NULL,
  `cantidad` double(20,2) DEFAULT '0.00',
  `costo_unitario` double(20,2) DEFAULT '0.00',
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_ajuste_inventario` (`id_ajuste_inventario`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=173551 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_consecutivo
-- ----------------------------
DROP TABLE IF EXISTS `inventario_consecutivo`;
CREATE TABLE `inventario_consecutivo` (
  `consecutivo` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for inventario_documentos
-- ----------------------------
DROP TABLE IF EXISTS `inventario_documentos`;
CREATE TABLE `inventario_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventario` int(11) DEFAULT NULL,
  `tipo_documento` int(11) DEFAULT NULL,
  `documento` longblob,
  `tipo_documento_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `document_type` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `nombre` varchar(250) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id` (`id`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_estadisticas_uso
-- ----------------------------
DROP TABLE IF EXISTS `inventario_estadisticas_uso`;
CREATE TABLE `inventario_estadisticas_uso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_equipo` int(11) DEFAULT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `pedido` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pedido` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `desde` datetime DEFAULT NULL,
  `hasta` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_grupo
-- ----------------------------
DROP TABLE IF EXISTS `inventario_grupo`;
CREATE TABLE `inventario_grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_grupo` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `nombre_grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `vida_util` int(11) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_grupo_subgrupo
-- ----------------------------
DROP TABLE IF EXISTS `inventario_grupo_subgrupo`;
CREATE TABLE `inventario_grupo_subgrupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_subgrupo` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `nombre_subgrupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_grupo` (`id_grupo`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_movimiento_notas
-- ----------------------------
DROP TABLE IF EXISTS `inventario_movimiento_notas`;
CREATE TABLE `inventario_movimiento_notas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) DEFAULT NULL,
  `codigo_item` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_inventario_total` int(11) DEFAULT NULL,
  `id_nota` int(11) DEFAULT NULL,
  `consecutivo_nota` int(11) DEFAULT NULL,
  `cantidad` double(11,2) DEFAULT NULL,
  `tipo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_inventario_total` (`id_inventario_total`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=498 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_prestamos
-- ----------------------------
DROP TABLE IF EXISTS `inventario_prestamos`;
CREATE TABLE `inventario_prestamos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_equipo` int(11) NOT NULL,
  `nombre_equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `id_empresa_origen` int(11) NOT NULL,
  `nombre_empresa_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_origen` int(11) NOT NULL,
  `nombre_sucursal_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_origen` int(11) NOT NULL,
  `nombre_bodega_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa_destino` int(11) NOT NULL,
  `nombre_empresa_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_destino` int(11) NOT NULL,
  `nombre_sucursal_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_destino` int(11) NOT NULL,
  `nombre_bodega_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `codigo` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `prestamos_devolucion` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'true',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_proceso
-- ----------------------------
DROP TABLE IF EXISTS `inventario_proceso`;
CREATE TABLE `inventario_proceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) NOT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT '0',
  `activo` int(11) NOT NULL DEFAULT '1',
  `id_usuario_inventario` int(11) NOT NULL,
  `usuario_inventario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario_inventario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio_inventario` date NOT NULL,
  `hora_inicio_inventario` time NOT NULL,
  `fecha_fin_inventario` date NOT NULL,
  `hora_fin_inventario` time NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_proceso_items
-- ----------------------------
DROP TABLE IF EXISTS `inventario_proceso_items`;
CREATE TABLE `inventario_proceso_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventario_proceso` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `codigo` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariado` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `prestado` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `es_prestado` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_proceso_items_out
-- ----------------------------
DROP TABLE IF EXISTS `inventario_proceso_items_out`;
CREATE TABLE `inventario_proceso_items_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventario_proceso` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `codigo` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) NOT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `pertenece_inventario_global` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `prestado` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `es_prestado` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa_prestamo` int(11) DEFAULT NULL,
  `empresa_prestamo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_prestamo` int(11) DEFAULT NULL,
  `sucursal_prestamo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_prestamo` int(11) DEFAULT NULL,
  `bodega_prestamo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_totales
-- ----------------------------
DROP TABLE IF EXISTS `inventario_totales`;
CREATE TABLE `inventario_totales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `codigo` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `code_bar` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_equipo` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidades` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_minima_stock` double(20,0) DEFAULT '0',
  `cantidad_maxima_stock` double(20,0) DEFAULT '0',
  `costos` double(20,4) DEFAULT NULL,
  `precio_venta` double(20,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ubicacion` int(11) DEFAULT NULL,
  `ubicacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_familia` int(11) DEFAULT NULL,
  `familia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_subgrupo` int(11) DEFAULT NULL,
  `subgrupo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_impuesto` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `cantidad` double(20,2) NOT NULL,
  `cantidad_pendiente` double(20,2) DEFAULT '0.00',
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT 'true',
  `estado_compra` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `estado_venta` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `debug` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_update` int(11) DEFAULT NULL,
  `tipo_documento_update` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento_update` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_ubicacion` (`id_ubicacion`) USING BTREE,
  KEY `id_familia` (`id_familia`) USING BTREE,
  KEY `id_grupo` (`id_grupo`) USING BTREE,
  KEY `id_subgrupo` (`id_subgrupo`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16990 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_totales_historico
-- ----------------------------
DROP TABLE IF EXISTS `inventario_totales_historico`;
CREATE TABLE `inventario_totales_historico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `codigo` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `code_bar` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_equipo` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `costos` double(20,2) DEFAULT '0.00',
  `precio_venta` double(20,2) DEFAULT NULL,
  `cantidad_documento` double(20,2) DEFAULT NULL,
  `cantidad` double(20,2) NOT NULL,
  `id_documento` int(11) DEFAULT NULL,
  `documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inventario` datetime DEFAULT NULL COMMENT 'fecha de inventario para el log',
  `fecha_registro` datetime DEFAULT NULL COMMENT 'fecha en donde se realizo el insert a la bd',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=221377 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_totales_log
-- ----------------------------
DROP TABLE IF EXISTS `inventario_totales_log`;
CREATE TABLE `inventario_totales_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `codigo` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `code_bar` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_equipo` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidades` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `costo_anterior` double(20,2) DEFAULT '0.00',
  `costo_nuevo` double(20,2) DEFAULT '0.00',
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ubicacion` int(11) DEFAULT NULL,
  `ubicacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_anterior` double(20,2) NOT NULL,
  `cantidad_nueva` double(20,2) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_ubicacion` (`id_ubicacion`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1005320 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_totales_log_mensual
-- ----------------------------
DROP TABLE IF EXISTS `inventario_totales_log_mensual`;
CREATE TABLE `inventario_totales_log_mensual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `unidad_medida` varchar(255) DEFAULT NULL,
  `cantidad_unidades` varchar(255) DEFAULT NULL,
  `costo` double(20,4) DEFAULT NULL,
  `precio_venta` double(20,2) DEFAULT NULL,
  `id_impuesto` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(50) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(50) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) DEFAULT NULL,
  `id_familia` int(11) DEFAULT NULL,
  `familia` varchar(255) DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `grupo` varchar(255) DEFAULT NULL,
  `id_subgrupo` int(11) DEFAULT NULL,
  `subgrupo` varchar(50) DEFAULT NULL,
  `cantidad` double(20,2) NOT NULL,
  `inventariable` varchar(6) DEFAULT 'true',
  `estado_compra` varchar(6) DEFAULT NULL,
  `estado_venta` varchar(6) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_familia` (`id_familia`) USING BTREE,
  KEY `id_grupo` (`id_grupo`) USING BTREE,
  KEY `id_subgrupo` (`id_subgrupo`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=46438 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for inventario_totales_traslados
-- ----------------------------
DROP TABLE IF EXISTS `inventario_totales_traslados`;
CREATE TABLE `inventario_totales_traslados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) DEFAULT NULL,
  `id_equipo` int(11) NOT NULL,
  `nombre_equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `id_sucursal_origen` int(11) DEFAULT NULL,
  `nombre_sucursal_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_origen` int(11) DEFAULT NULL,
  `nombre_bodega_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `nombre_empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_destino` int(11) DEFAULT NULL,
  `nombre_sucursal_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_destino` int(11) DEFAULT NULL,
  `nombre_bodega_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `costo` double(20,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_equipo` (`id_equipo`) USING BTREE,
  KEY `id_sucursal_origen` (`id_sucursal_origen`) USING BTREE,
  KEY `id_bodega_origen` (`id_bodega_origen`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal_destino` (`id_sucursal_destino`) USING BTREE,
  KEY `id_bodega_destino` (`id_bodega_destino`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_totales_traslados_manual
-- ----------------------------
DROP TABLE IF EXISTS `inventario_totales_traslados_manual`;
CREATE TABLE `inventario_totales_traslados_manual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) DEFAULT NULL,
  `id_equipo` int(11) NOT NULL,
  `nombre_equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `id_sucursal_origen` int(11) DEFAULT NULL,
  `nombre_sucursal_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_origen` int(11) DEFAULT NULL,
  `nombre_bodega_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `nombre_empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_destino` int(11) DEFAULT NULL,
  `nombre_sucursal_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_destino` int(11) DEFAULT NULL,
  `nombre_bodega_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `costo` double(20,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_equipo` (`id_equipo`) USING BTREE,
  KEY `id_sucursal_origen` (`id_sucursal_origen`) USING BTREE,
  KEY `id_bodega_origen` (`id_bodega_origen`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal_destino` (`id_sucursal_destino`) USING BTREE,
  KEY `id_bodega_destino` (`id_bodega_destino`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_traslados
-- ----------------------------
DROP TABLE IF EXISTS `inventario_traslados`;
CREATE TABLE `inventario_traslados` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date NOT NULL COMMENT 'Fecha cuando se crea el documento',
  `fecha_documento` date DEFAULT NULL COMMENT 'Fecha escogida por el usuario cuando inicia la orden',
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT '0',
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_traslado` int(11) DEFAULT NULL,
  `sucursal_traslado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_traslado` int(11) DEFAULT NULL,
  `bodega_traslado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura,3->cancelada, 4 -> autorizado',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11086 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_traslados_unidades
-- ----------------------------
DROP TABLE IF EXISTS `inventario_traslados_unidades`;
CREATE TABLE `inventario_traslados_unidades` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_traslado` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci NOT NULL,
  `cantidad` double(15,2) NOT NULL DEFAULT '0.00',
  `costo_unitario` double(15,2) NOT NULL DEFAULT '0.00',
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_orden_compra` (`id_traslado`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=94403 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for inventario_unidades
-- ----------------------------
DROP TABLE IF EXISTS `inventario_unidades`;
CREATE TABLE `inventario_unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `unidades` int(7) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `codigo_dian` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_auto` varchar(5) COLLATE latin1_general_ci DEFAULT 'true',
  `codigo` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `code_bar` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidades` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_minima_stock` double DEFAULT '0',
  `cantidad_maxima_stock` double DEFAULT '0',
  `id_empresa` int(11) NOT NULL DEFAULT '0',
  `empresa` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `unidad` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_familia` int(50) DEFAULT '0',
  `familia` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo` int(50) DEFAULT '0',
  `grupo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_subgrupo` int(11) DEFAULT NULL,
  `subgrupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT '0',
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_equipo` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `id_unidad` int(11) DEFAULT NULL,
  `vida_util` int(11) DEFAULT NULL,
  `fecha_creacion_en_inventario` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `fecha_vencimiento_garantia` date DEFAULT NULL,
  `marca` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `modelo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `color` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_piezas` int(11) DEFAULT NULL,
  `descripcion1` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion2` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `quien_elimino` varchar(80) COLLATE latin1_general_ci DEFAULT NULL,
  `costos` double(11,2) DEFAULT '0.00',
  `precio_venta` double(11,2) DEFAULT '0.00',
  `documento_contable` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `observaciones_eliminacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario_elimino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_elimino` int(11) DEFAULT NULL,
  `id_usuario_creacion` int(11) DEFAULT NULL,
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `estado_compra` varchar(6) COLLATE latin1_general_ci DEFAULT 'true',
  `estado_venta` varchar(6) COLLATE latin1_general_ci DEFAULT 'true',
  `opcion_costo` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `opcion_gasto` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `opcion_activo_fijo` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `modulo_pos` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `minibar` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `codigo_transaccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_produccion` int(11) DEFAULT NULL COMMENT 'id de la bodega de donde se producira el producto terminado, se usara para que despues se descuente del inventario de esa bodega',
  `activo_pos` int(11) DEFAULT '1' COMMENT 'Si esta activo o no para el POS',
  `id_termino` int(11) DEFAULT NULL COMMENT 'id del termino con el que se congifura el plato terminado',
  `precio_venta_1` double DEFAULT NULL,
  `precio_venta_2` double DEFAULT NULL,
  `precio_venta_3` double DEFAULT NULL,
  `precio_venta_4` double DEFAULT NULL,
  `precio_venta_5` double DEFAULT NULL,
  `disponible_asiste` varchar(10) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Si es un item para sincronizar en asiste esta opcion debe estar true',
  `id_categoria_asiste` int(11) DEFAULT NULL COMMENT 'id de la categoria a la que pertenecera el item en asiste este id se inserta con base a la api de asiste que retorna las categorias',
  `grupo_empresarial` int(11) DEFAULT '0',
  `item_produccion` varchar(255) COLLATE latin1_general_ci DEFAULT 'false',
  `item_transformacion` varchar(255) COLLATE latin1_general_ci DEFAULT 'false',
  `id_item_transformacion` int(11) DEFAULT '0',
  `codigo_item_transformacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `nombre_item_transformacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cantidad_transformacion` double(20,2) DEFAULT '0.00',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_familia` (`id_familia`) USING BTREE,
  KEY `id_grupo` (`id_grupo`) USING BTREE,
  KEY `id_subgrupo` (`id_subgrupo`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_unidad` (`id_unidad`) USING BTREE,
  KEY `id_usuario_elimino` (`id_usuario_elimino`) USING BTREE,
  KEY `id_usuario_creacion` (`id_usuario_creacion`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2187 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_cod_tx
-- ----------------------------
DROP TABLE IF EXISTS `items_cod_tx`;
CREATE TABLE `items_cod_tx` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `cod_tx` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_items` (`id_item`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `items_cuentas`;
CREATE TABLE `items_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_items` int(11) DEFAULT NULL,
  `codigo_items` bigint(30) DEFAULT NULL,
  `id_puc` int(11) DEFAULT NULL,
  `puc` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(8) COLLATE latin1_general_ci DEFAULT 'debito',
  `id_empresa` int(11) DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_items` (`id_items`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=22313 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_cuentas_niif
-- ----------------------------
DROP TABLE IF EXISTS `items_cuentas_niif`;
CREATE TABLE `items_cuentas_niif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_items` int(11) DEFAULT NULL,
  `codigo_items` bigint(30) DEFAULT NULL,
  `id_puc` int(11) DEFAULT NULL,
  `puc` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(8) COLLATE latin1_general_ci DEFAULT 'debito',
  `id_empresa` int(11) DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  `debug` int(1) DEFAULT '0',
  `cuenta_colgaap` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_items` (`id_items`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20616 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_documentos
-- ----------------------------
DROP TABLE IF EXISTS `items_documentos`;
CREATE TABLE `items_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventario` int(11) DEFAULT NULL,
  `randomico_documento` varchar(250) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` int(11) DEFAULT NULL,
  `nombre_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `document_type` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_familia
-- ----------------------------
DROP TABLE IF EXISTS `items_familia`;
CREATE TABLE `items_familia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_familia_grupo
-- ----------------------------
DROP TABLE IF EXISTS `items_familia_grupo`;
CREATE TABLE `items_familia_grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_familia` int(11) DEFAULT NULL,
  `cod_familia` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `familia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_familia` (`id_familia`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_familia_grupo_subgrupo
-- ----------------------------
DROP TABLE IF EXISTS `items_familia_grupo_subgrupo`;
CREATE TABLE `items_familia_grupo_subgrupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_familia` int(2) DEFAULT NULL,
  `cod_familia` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `familia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo` int(2) NOT NULL,
  `cod_grupo` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_familia` (`id_familia`) USING BTREE,
  KEY `id_grupo` (`id_grupo`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_recetas
-- ----------------------------
DROP TABLE IF EXISTS `items_recetas`;
CREATE TABLE `items_recetas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) DEFAULT '0',
  `codigo_item` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `code_bar_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_item` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_item_materia_prima` varchar(255) COLLATE latin1_general_ci DEFAULT '0',
  `codigo_item_materia_prima` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `code_bar_item_materia_prima` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_item_materia_prima` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cantidad_item_materia_prima` double(20,2) DEFAULT '0.00',
  `id_unidad_medida` int(11) DEFAULT NULL,
  `unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4311 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_terminos
-- ----------------------------
DROP TABLE IF EXISTS `items_terminos`;
CREATE TABLE `items_terminos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for items_terminos_detalle
-- ----------------------------
DROP TABLE IF EXISTS `items_terminos_detalle`;
CREATE TABLE `items_terminos_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_termino` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for kanban_actividades
-- ----------------------------
DROP TABLE IF EXISTS `kanban_actividades`;
CREATE TABLE `kanban_actividades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_proyecto` int(11) DEFAULT NULL,
  `id_git` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `prioridad` int(1) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `fecha_pendiente` datetime DEFAULT NULL,
  `fecha_proceso` datetime DEFAULT NULL,
  `fecha_realizado` datetime DEFAULT NULL,
  `estado` int(1) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for kanban_proyectos
-- ----------------------------
DROP TABLE IF EXISTS `kanban_proyectos`;
CREATE TABLE `kanban_proyectos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final_desarrollo` date DEFAULT NULL,
  `fecha_final_soporte` date DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_responsable` int(11) DEFAULT NULL,
  `responsable` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `pendiente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `proceso` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `terminado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_actualizacion` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for licencia_soporte
-- ----------------------------
DROP TABLE IF EXISTS `licencia_soporte`;
CREATE TABLE `licencia_soporte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `id_unico` int(11) NOT NULL,
  `autorizado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id_unico` (`id_unico`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `user` int(11) NOT NULL,
  `username` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `log` varchar(5000) COLLATE latin1_general_ci NOT NULL,
  `modulo` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=22465 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for logistico_vehiculos
-- ----------------------------
DROP TABLE IF EXISTS `logistico_vehiculos`;
CREATE TABLE `logistico_vehiculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `vehiculo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `placa` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for logs_inventario
-- ----------------------------
DROP TABLE IF EXISTS `logs_inventario`;
CREATE TABLE `logs_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) DEFAULT NULL,
  `consecutivo_documento` varchar(255) DEFAULT NULL,
  `fecha_documento` date DEFAULT NULL,
  `accion_documento` varchar(100) DEFAULT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `hora_movimiento` time DEFAULT NULL,
  `accion_inventario` varchar(100) DEFAULT NULL,
  `id_item` int(11) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `item` varchar(100) DEFAULT NULL,
  `unidad_medida` varchar(255) DEFAULT NULL,
  `cantidad_unidades` varchar(255) DEFAULT NULL,
  `costo` double(20,4) DEFAULT NULL,
  `cantidad` double(20,2) DEFAULT '0.00',
  `fijar_costo` varchar(10) DEFAULT NULL,
  `costo_anterior` double(20,4) DEFAULT NULL,
  `costo_nuevo` double(20,4) DEFAULT NULL,
  `cantidad_anterior` double(20,2) NOT NULL,
  `cantidad_nueva` double(20,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(100) DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(50) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(50) DEFAULT NULL,
  `sql` longblob,
  `sql_estado` varchar(50) DEFAULT NULL,
  `sql_respuesta` longblob,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=203159 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for logs_mysql
-- ----------------------------
DROP TABLE IF EXISTS `logs_mysql`;
CREATE TABLE `logs_mysql` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabla` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `campo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_registro` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(6) COLLATE latin1_general_ci DEFAULT NULL,
  `oldData` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `newData` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4426 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for log_documentos_contables
-- ----------------------------
DROP TABLE IF EXISTS `log_documentos_contables`;
CREATE TABLE `log_documentos_contables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_documento` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `actividad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_documento` (`id_documento`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=151855 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for log_js
-- ----------------------------
DROP TABLE IF EXISTS `log_js`;
CREATE TABLE `log_js` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `user` int(11) NOT NULL,
  `username` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `log` varchar(5000) COLLATE latin1_general_ci NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `Index` (`fecha`,`user`,`username`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for mantenimiento
-- ----------------------------
DROP TABLE IF EXISTS `mantenimiento`;
CREATE TABLE `mantenimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_inventario` int(50) NOT NULL,
  `equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(50) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(50) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(50) NOT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_mantenimiento` date NOT NULL,
  `fecha_hora_mantenimiento` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `cod_equipo` varchar(50) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for mantenimiento_datos
-- ----------------------------
DROP TABLE IF EXISTS `mantenimiento_datos`;
CREATE TABLE `mantenimiento_datos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mantenimiento` int(11) NOT NULL,
  `id_checklist` int(11) NOT NULL,
  `dato` varchar(1500) COLLATE latin1_general_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `checklist` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_checklist_detalle` int(11) NOT NULL,
  `checklist_detalle` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for medios_magneticos_formatos
-- ----------------------------
DROP TABLE IF EXISTS `medios_magneticos_formatos`;
CREATE TABLE `medios_magneticos_formatos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(255) DEFAULT '0',
  `nombre` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for medios_magneticos_formatos_columnas
-- ----------------------------
DROP TABLE IF EXISTS `medios_magneticos_formatos_columnas`;
CREATE TABLE `medios_magneticos_formatos_columnas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(255) DEFAULT '0',
  `formato` longtext COLLATE latin1_general_ci,
  `orden` int(11) DEFAULT NULL,
  `nombre` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for medios_magneticos_formatos_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `medios_magneticos_formatos_conceptos`;
CREATE TABLE `medios_magneticos_formatos_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(255) DEFAULT '0',
  `concepto` int(11) DEFAULT NULL,
  `descripcion` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for medios_magneticos_formatos_conceptos_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `medios_magneticos_formatos_conceptos_cuentas`;
CREATE TABLE `medios_magneticos_formatos_conceptos_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formato` int(11) DEFAULT '0',
  `codigo_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_formato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_inicial` int(11) DEFAULT NULL,
  `cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_inicial` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_final` int(11) DEFAULT NULL,
  `cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_final` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `forma_calculo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Son las 5 maneras como se calcula la cuenta\r\n\r\nsuma_debitos:\r\n     Suma acumulada de los debitos\r\nsuma_creditos:\r\n     Suma acumulada de los creditos\r\ndebito_menos_credito:\r\n     Suma acumulada de debito menos la suma      acumulada de creditos\r\nsaldo_actual\r\n     Es el saldo actual de la cuenta\r\n     (saldo anterior + suma acumulada debitos) -             suma acumulada creditos\r\nsaldo_inicial\r\n     Saldo anterior de la cuenta',
  `id_columna_formato` int(11) DEFAULT NULL,
  `nombre_columna_formato` longtext COLLATE latin1_general_ci,
  `tope` double(255,2) DEFAULT '0.00' COMMENT 'base o tope para declarar un beneficiario',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for meet
-- ----------------------------
DROP TABLE IF EXISTS `meet`;
CREATE TABLE `meet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `sala` varchar(40) COLLATE latin1_general_ci DEFAULT NULL,
  `fechai` date NOT NULL,
  `horai` time NOT NULL,
  `invitados` blob,
  `privada` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `sala_espera` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `ini_mic_off` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `ini_cam_off` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `part_ini_mic` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `part_ini_cam` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `activo` smallint(1) NOT NULL DEFAULT '1' COMMENT '1: activo 0: inactivo',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for meet_config
-- ----------------------------
DROP TABLE IF EXISTS `meet_config`;
CREATE TABLE `meet_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_actual` int(11) DEFAULT NULL,
  `plan_actual_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fechai` date DEFAULT NULL,
  `fechaf` date DEFAULT NULL,
  `promo_10_dias` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'false',
  `promoi` date DEFAULT NULL,
  `promof` date DEFAULT NULL,
  `num_reuniones` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for meet_suscripcion
-- ----------------------------
DROP TABLE IF EXISTS `meet_suscripcion`;
CREATE TABLE `meet_suscripcion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan` int(11) DEFAULT NULL,
  `plan_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fechai` date DEFAULT NULL,
  `fechaf` date DEFAULT NULL,
  `usuario_activa_id` int(11) DEFAULT NULL,
  `usuario_activa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` int(1) DEFAULT NULL COMMENT '1-> automatica ,   2->manual',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for modulos_erp
-- ----------------------------
DROP TABLE IF EXISTS `modulos_erp`;
CREATE TABLE `modulos_erp` (
  `id` int(11) NOT NULL DEFAULT '0',
  `nombre` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `icono26` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `icono44` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `ejecuta` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `pasar_variables` varchar(5) COLLATE latin1_general_ci DEFAULT 'true',
  `escritorio` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `inicio` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `ancho` int(10) NOT NULL DEFAULT '0',
  `alto` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_conceptos`;
CREATE TABLE `nomina_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_grupo` int(11) DEFAULT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo` varchar(100) COLLATE latin1_general_ci DEFAULT '0',
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `naturaleza` varchar(50) COLLATE latin1_general_ci DEFAULT '' COMMENT 'devengo=si aumenta el valor a pagar al empleado\r\ndeduccion= si resta el valor a pagar al empleado\r\nApropiacion=sino genera ninguna accion sobre el valor a pagar al empleado, ejemplo:las provisiones',
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter` varchar(100) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'si es debito o credito',
  `centro_costos` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_cuenta_contrapartida_colgaap` int(11) DEFAULT NULL,
  `cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `descripcion_cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter_contrapartida` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_contrapartida` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_cuenta_colgaap_liquidacion` int(11) DEFAULT '0',
  `cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_liquidacion` int(11) DEFAULT '0',
  `cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `concepto_ajustable` varchar(50) COLLATE latin1_general_ci DEFAULT 'false',
  `id_cuenta_colgaap_ajuste` int(11) DEFAULT NULL,
  `cuenta_colgaap_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif_ajuste` int(11) DEFAULT NULL,
  `cuenta_niif_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT 'false',
  `imprimir_volante` varchar(10) COLLATE latin1_general_ci DEFAULT 'true' COMMENT 'true si se imprime en el volante de pago para el empleado, false si no',
  `carga_automatica` varchar(10) COLLATE latin1_general_ci DEFAULT 'true' COMMENT 'true si se carga automaticamente en la planilla de nomina false si no',
  `base` double DEFAULT '0',
  `nivel_formula` int(11) DEFAULT '1',
  `formula` varchar(1000) COLLATE latin1_general_ci DEFAULT '',
  `nivel_formula_liquidacion` int(11) DEFAULT '1',
  `formula_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `formula_formato` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tipo_concepto` varchar(50) COLLATE latin1_general_ci DEFAULT 'General' COMMENT 'General: para todos, personal : individual en cada empleado',
  `id_tercero` int(11) DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT 'Empleado',
  `tercero_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT 'Empleado',
  `tercero_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT 'Empleado',
  `tercero_cruce_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT 'Empleado',
  `resta_dias` varchar(50) COLLATE latin1_general_ci DEFAULT 'false',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `clasificacion` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_grupo` (`id_grupo`) USING BTREE,
  KEY `id_cuenta_colgaap` (`id_cuenta_colgaap`) USING BTREE,
  KEY `id_cuenta_niif` (`id_cuenta_niif`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_conceptos_base_liquidacion
-- ----------------------------
DROP TABLE IF EXISTS `nomina_conceptos_base_liquidacion`;
CREATE TABLE `nomina_conceptos_base_liquidacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_concepto_base` int(11) DEFAULT NULL,
  `codigo_concepto_base` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto_base` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza_base` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_conceptos_cargo
-- ----------------------------
DROP TABLE IF EXISTS `nomina_conceptos_cargo`;
CREATE TABLE `nomina_conceptos_cargo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_concepto` double DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_cargo` (`id_cargo`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_conceptos_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_conceptos_empleados`;
CREATE TABLE `nomina_conceptos_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_concepto` double DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_cargo` (`id_empleado`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=777 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_conceptos_grupos_trabajo
-- ----------------------------
DROP TABLE IF EXISTS `nomina_conceptos_grupos_trabajo`;
CREATE TABLE `nomina_conceptos_grupos_trabajo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo_trabajo` int(11) DEFAULT NULL,
  `grupo_trabajo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter` varchar(100) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'si es debito o credito',
  `centro_costos` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_cuenta_contrapartida_colgaap` int(11) DEFAULT NULL,
  `cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `descripcion_cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter_contrapartida` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_contrapartida` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_cuenta_colgaap_liquidacion` int(11) DEFAULT '0',
  `cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_liquidacion` int(11) DEFAULT '0',
  `cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero_cruce_liquidacion` varchar(100) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_colgaap_ajuste` int(11) DEFAULT NULL,
  `cuenta_colgaap_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif_ajuste` int(11) DEFAULT NULL,
  `cuenta_niif_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT 'false',
  `tercero_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT 'Empleado',
  `base` double DEFAULT '0',
  `nivel_formula` int(11) DEFAULT '0',
  `formula` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `nivel_formula_liquidacion` int(11) DEFAULT '1',
  `formula_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `formula_formato` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_cuenta_colgaap` (`id_cuenta_colgaap`) USING BTREE,
  KEY `id_cuenta_niif` (`id_cuenta_niif`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_consecutivos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_consecutivos`;
CREATE TABLE `nomina_configuracion_consecutivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefijo` varchar(255) DEFAULT NULL,
  `consecutivo` int(1) DEFAULT '1',
  `codigo` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_formas_pago
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_formas_pago`;
CREATE TABLE `nomina_configuracion_formas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_hora_extra
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_hora_extra`;
CREATE TABLE `nomina_configuracion_hora_extra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_hora` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_hora_extra_recargo
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_hora_extra_recargo`;
CREATE TABLE `nomina_configuracion_hora_extra_recargo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `porcentaje` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_idiomas
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_idiomas`;
CREATE TABLE `nomina_configuracion_idiomas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `ISO_639_1` varchar(255) DEFAULT NULL,
  `ISO_639_2` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=185 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_medios_pago
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_medios_pago`;
CREATE TABLE `nomina_configuracion_medios_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_monedas
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_monedas`;
CREATE TABLE `nomina_configuracion_monedas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `divisa` varchar(255) DEFAULT NULL,
  `pais` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=180 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_subtipo_trabajador
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_subtipo_trabajador`;
CREATE TABLE `nomina_configuracion_subtipo_trabajador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_tipo_documentos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_tipo_documentos`;
CREATE TABLE `nomina_configuracion_tipo_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_tipo_documentos_ajuste
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_tipo_documentos_ajuste`;
CREATE TABLE `nomina_configuracion_tipo_documentos_ajuste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_configuracion_tipo_trabajador
-- ----------------------------
DROP TABLE IF EXISTS `nomina_configuracion_tipo_trabajador`;
CREATE TABLE `nomina_configuracion_tipo_trabajador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `is_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_consolidacion_provision
-- ----------------------------
DROP TABLE IF EXISTS `nomina_consolidacion_provision`;
CREATE TABLE `nomina_consolidacion_provision` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap_cruce` int(11) DEFAULT NULL,
  `cuenta_colgaap_cruce` int(11) DEFAULT NULL,
  `descripcion_cuenta_colgaap_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif_cruce` int(11) DEFAULT NULL,
  `cuenta_niif_cruce` int(11) DEFAULT NULL,
  `descripcion_cuenta_niif_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_nota` date DEFAULT NULL COMMENT 'fecha de la nota para los asientos',
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha de inicio de las planillas a cargar',
  `fecha_final` date DEFAULT NULL COMMENT 'fecha final de las planillas a cargar',
  `fecha_finalizacion` date DEFAULT NULL,
  `id_concepto` int(11) DEFAULT '0',
  `concepto` varchar(225) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_tercero` int(15) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `numero_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_identificacion_tercero` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `cedula_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `total` double DEFAULT '0',
  `total_sin_abono` double DEFAULT '0',
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0-> sin generar , 1-> generada',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_tipo_nota` (`id_concepto`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_consolidacion_provision_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `nomina_consolidacion_provision_cuentas`;
CREATE TABLE `nomina_consolidacion_provision_cuentas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_consolidacion_provision` int(15) DEFAULT NULL,
  `id_puc` int(15) NOT NULL,
  `cuenta_puc` int(20) DEFAULT NULL,
  `descripcion_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_niif` int(15) DEFAULT NULL,
  `cuenta_niif` int(15) DEFAULT NULL,
  `descripcion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tabla_referencia` int(11) DEFAULT '0' COMMENT 'id del campo de la tabla de carga',
  `id_documento_cruce` int(11) DEFAULT NULL,
  `numero_documento_cruce` int(11) DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_consolidacion_provision`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `cuenta_puc` (`cuenta_puc`) USING BTREE,
  KEY `id_niif` (`id_niif`) USING BTREE,
  KEY `cuenta_niif` (`cuenta_niif`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_cuentas_pago
-- ----------------------------
DROP TABLE IF EXISTS `nomina_cuentas_pago`;
CREATE TABLE `nomina_cuentas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `nombre_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `estado` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) NOT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_electronica_estructura_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_electronica_estructura_conceptos`;
CREATE TABLE `nomina_electronica_estructura_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `estructura` longtext,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_formulas_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_formulas_conceptos`;
CREATE TABLE `nomina_formulas_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `formula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_grupos_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_grupos_conceptos`;
CREATE TABLE `nomina_grupos_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio_vigencia` date DEFAULT NULL,
  `fecha_fin_vigencia` date DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_grupos_trabajo
-- ----------------------------
DROP TABLE IF EXISTS `nomina_grupos_trabajo`;
CREATE TABLE `nomina_grupos_trabajo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_motivo_fin_contrato
-- ----------------------------
DROP TABLE IF EXISTS `nomina_motivo_fin_contrato`;
CREATE TABLE `nomina_motivo_fin_contrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_niveles_riesgos_laborales
-- ----------------------------
DROP TABLE IF EXISTS `nomina_niveles_riesgos_laborales`;
CREATE TABLE `nomina_niveles_riesgos_laborales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double(20,3) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas`;
CREATE TABLE `nomina_planillas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL COMMENT 'fecha cuando se crea la planilla',
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cuando se genera la planilla',
  `fecha_documento` date DEFAULT NULL COMMENT 'fecha que se usa para los asientos',
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha inicial a pagar la planilla',
  `fecha_final` date DEFAULT NULL COMMENT 'fecha final a pagar la planilla',
  `consecutivo` int(11) DEFAULT NULL,
  `id_tipo_liquidacion` int(11) DEFAULT NULL,
  `tipo_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(11) DEFAULT '0',
  `id_planilla_liquidacion` int(11) DEFAULT '0' COMMENT 'si tiene id quiere decir que es una planilla de provision, y el id corresponde ',
  `consecutivo_planilla_liquidacion` int(11) DEFAULT NULL,
  `id_sucursal` int(255) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `debug` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=542 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_ajuste
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_ajuste`;
CREATE TABLE `nomina_planillas_ajuste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL COMMENT 'fecha cuando se crea la planilla',
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cuando se genera la planilla',
  `fecha_documento` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha inicial a pagar la planilla',
  `fecha_final` date DEFAULT NULL COMMENT 'fecha final a pagar la planilla',
  `consecutivo` int(11) DEFAULT NULL,
  `id_tipo_liquidacion` int(11) DEFAULT NULL,
  `tipo_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(11) DEFAULT '0',
  `id_sucursal` int(255) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_ajuste_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_ajuste_empleados`;
CREATE TABLE `nomina_planillas_ajuste_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_laborados` int(11) DEFAULT '0',
  `dias_laborados_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `terminar_contrato` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `verificado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `email_enviado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_ajuste_empleados_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_ajuste_empleados_conceptos`;
CREATE TABLE `nomina_planillas_ajuste_empleados_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `imprimir_volante` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_concepto` double(20,2) DEFAULT NULL,
  `valor_concepto_ajustado` double(20,2) DEFAULT '0.00',
  `saldo_restante` double(20,2) DEFAULT '0.00',
  `id_tercero` int(11) DEFAULT NULL,
  `id_empleado_cruce` int(11) DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero_contrapartida` int(11) DEFAULT NULL,
  `id_empleado_cruce_contrapartida` int(11) DEFAULT NULL,
  `id_tercero_cruce_liquidacion` int(11) DEFAULT NULL,
  `id_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap_ajuste` int(11) DEFAULT '0',
  `cuenta_colgaap_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_colgaap_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_ajuste` int(11) DEFAULT '0',
  `cuenta_niif_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_niif_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_tercero_ajuste` int(11) DEFAULT NULL,
  `centro_costos_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos_ajuste` int(11) DEFAULT NULL,
  `codigo_centro_costos_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos_ajuste` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter_contrapartida` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_contrapartida` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos_contrapartida` int(11) DEFAULT NULL,
  `codigo_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nivel_formula` int(11) DEFAULT '0',
  `formula_original` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_campo_texto` double DEFAULT '0',
  `formula` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `dias_laborados` int(11) DEFAULT '0',
  `id_prestamo` int(11) DEFAULT '0' COMMENT 'id del documento de relaionado a un prestamo si el concepto se carga a partir de un prestamo',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_consolidacion_provision
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_consolidacion_provision`;
CREATE TABLE `nomina_planillas_consolidacion_provision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL COMMENT 'fecha cuando se crea la planilla',
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cuando se genera la planilla',
  `fecha_documento` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha inicial a pagar la planilla',
  `fecha_final` date DEFAULT NULL COMMENT 'fecha final a pagar la planilla',
  `consecutivo` int(11) DEFAULT NULL,
  `id_tipo_liquidacion` int(11) DEFAULT NULL,
  `tipo_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(11) DEFAULT '0',
  `saldo_inicial` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_sucursal` int(255) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_consolidacion_provision_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_consolidacion_provision_empleados`;
CREATE TABLE `nomina_planillas_consolidacion_provision_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_laborados` int(11) DEFAULT '0',
  `dias_laborados_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `terminar_contrato` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `id_motivo_fin_contrato` int(11) DEFAULT NULL,
  `motivo_fin_contrato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `vacaciones` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `verificado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `email_enviado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `observaciones` longblob,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_consolidacion_provision_empleados_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_consolidacion_provision_empleados_conceptos`;
CREATE TABLE `nomina_planillas_consolidacion_provision_empleados_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `imprimir_volante` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_concepto` double(20,2) DEFAULT NULL,
  `valor_concepto_ajustado` double(20,2) DEFAULT '0.00',
  `saldo_restante` double(20,2) DEFAULT '0.00',
  `id_tercero` int(11) DEFAULT NULL,
  `id_empleado_cruce` int(11) DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero_contrapartida` int(11) DEFAULT NULL,
  `id_empleado_cruce_contrapartida` int(11) DEFAULT NULL,
  `id_tercero_cruce_liquidacion` int(11) DEFAULT NULL,
  `id_empleado_cruce_liquidacion` int(11) DEFAULT NULL,
  `id_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap_liquidacion` int(11) DEFAULT '0',
  `cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_liquidacion` int(11) DEFAULT '0',
  `cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `caracter_contrapartida` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_contrapartida` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos_contrapartida` int(11) DEFAULT NULL,
  `codigo_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nivel_formula` int(11) DEFAULT '0',
  `formula_original` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_campo_texto` double DEFAULT '0',
  `formula` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `dias_laborados` int(11) DEFAULT '0',
  `base` double(20,2) DEFAULT '0.00',
  `id_prestamo` int(11) DEFAULT '0' COMMENT 'id del documento de relaionado a un prestamo si el concepto se carga a partir de un prestamo',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_electronica
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_electronica`;
CREATE TABLE `nomina_planillas_electronica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `fecha_generacion` date DEFAULT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_tipo_liquidacion` int(11) DEFAULT NULL,
  `tipo_liquidacion` varchar(255) DEFAULT NULL,
  `dias_liquidacion` int(11) DEFAULT NULL,
  `codigo_tipo_documento` varchar(255) DEFAULT NULL,
  `tipo_documento` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `observacion` longtext,
  `estado` int(11) DEFAULT '0',
  `id_planilla_liquidacion` int(11) DEFAULT NULL,
  `consecutivo_planilla_liquidacion` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_electronica_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_electronica_empleados`;
CREATE TABLE `nomina_planillas_electronica_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) DEFAULT NULL,
  `documento_empleado` varchar(255) DEFAULT NULL,
  `nombre_empleado` varchar(255) DEFAULT NULL,
  `dias_laborados` int(11) DEFAULT NULL,
  `dias_laborados_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `prefijo` varchar(11) DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `tiempo_laborado` int(11) DEFAULT NULL,
  `codigo_tipo_ajuste` varchar(100) DEFAULT NULL,
  `planilla_relacionada_al_ajuste` varchar(100) DEFAULT NULL,
  `id_usuario_NE` int(11) DEFAULT NULL,
  `nombre_usuario_NE` varchar(255) DEFAULT NULL,
  `cedula_usuario_NE` varchar(255) DEFAULT NULL,
  `fecha_NE` date DEFAULT NULL,
  `hora_NE` time DEFAULT NULL,
  `response_NE` longtext,
  `UUID` varchar(255) DEFAULT NULL,
  `verificado` varchar(255) DEFAULT NULL,
  `observaciones` longtext,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1214 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_electronica_empleados_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_electronica_empleados_conceptos`;
CREATE TABLE `nomina_planillas_electronica_empleados_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `naturaleza` varchar(100) DEFAULT NULL,
  `valor_concepto` double DEFAULT NULL,
  `valor_campo_texto` double DEFAULT NULL,
  `data` longtext,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `debug_nomina` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16181 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_electronica_empleados_fechas_pago
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_electronica_empleados_fechas_pago`;
CREATE TABLE `nomina_planillas_electronica_empleados_fechas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=584 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_electronica_fechas_pago
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_electronica_fechas_pago`;
CREATE TABLE `nomina_planillas_electronica_fechas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for nomina_planillas_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_empleados`;
CREATE TABLE `nomina_planillas_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_laborados` int(11) DEFAULT '0',
  `dias_laborados_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `terminar_contrato` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `verificado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `email_enviado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `observaciones` longtext COLLATE latin1_general_ci,
  `recalcular_concepto` varchar(10) COLLATE latin1_general_ci DEFAULT 'true' COMMENT 'true = cada vez que un concepto cambia, se recalculan los otros que dependen de el.\r\nfalse = no se recalculan los conceptos. ',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6880 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_empleados_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_empleados_conceptos`;
CREATE TABLE `nomina_planillas_empleados_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `imprimir_volante` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_concepto` double DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `id_empleado_cruce` int(11) DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero_contrapartida` int(11) DEFAULT NULL,
  `id_empleado_cruce_contrapartida` int(11) DEFAULT NULL,
  `id_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter_contrapartida` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_contrapartida` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos_contrapartida` int(11) DEFAULT NULL,
  `codigo_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nivel_formula` int(11) DEFAULT '0',
  `formula_original` varchar(1000) COLLATE latin1_general_ci DEFAULT '',
  `valor_campo_texto` double DEFAULT '0',
  `formula` varchar(1000) COLLATE latin1_general_ci DEFAULT '',
  `id_prestamo` int(11) DEFAULT '0' COMMENT 'id del documento de relaionado a un prestamo si el concepto se carga a partir de un prestamo',
  `resta_dias` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `dias_laborados` int(11) DEFAULT '0',
  `saldo_dias_laborados` int(11) DEFAULT NULL,
  `id_planilla_cruce` int(11) DEFAULT '0',
  `tipo_planilla_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `debug_nomina` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=95265 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_empleados_conceptos_datos_nomina_electronica
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_empleados_conceptos_datos_nomina_electronica`;
CREATE TABLE `nomina_planillas_empleados_conceptos_datos_nomina_electronica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estructura` int(11) DEFAULT NULL,
  `tipo_planilla` varchar(11) DEFAULT NULL,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `data` longtext,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2634 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_empleados_contabilizacion
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_empleados_contabilizacion`;
CREATE TABLE `nomina_planillas_empleados_contabilizacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empleado_cruce` int(11) DEFAULT NULL,
  `documento_empleado_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `empleado_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_planilla` varchar(10) COLLATE latin1_general_ci DEFAULT 'LN',
  `id_planilla` int(11) DEFAULT NULL,
  `consecutivo_planilla` int(11) DEFAULT NULL,
  `fecha_inicio_planilla` date DEFAULT NULL,
  `fecha_final_planilla` date DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` int(11) DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `debito` double DEFAULT NULL,
  `credito` double DEFAULT NULL,
  `total_sin_abono` double DEFAULT NULL,
  `total_sin_abono_provision` double DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `debug_nomina` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=218307 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_liquidacion
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_liquidacion`;
CREATE TABLE `nomina_planillas_liquidacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL COMMENT 'fecha cuando se crea la planilla',
  `fecha_generacion` date DEFAULT NULL COMMENT 'fecha cuando se genera la planilla',
  `fecha_documento` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha inicial a pagar la planilla',
  `fecha_final` date DEFAULT NULL COMMENT 'fecha final a pagar la planilla',
  `consecutivo` int(11) DEFAULT NULL,
  `id_tipo_liquidacion` int(11) DEFAULT NULL,
  `tipo_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(11) DEFAULT '0',
  `id_sucursal` int(255) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=358 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_liquidacion_conceptos_deducir
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_liquidacion_conceptos_deducir`;
CREATE TABLE `nomina_planillas_liquidacion_conceptos_deducir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_prestamo` int(11) DEFAULT '0',
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_concepto_deducir` int(11) DEFAULT NULL,
  `concepto_deducir` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_deducir` double(20,2) DEFAULT NULL,
  `cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_prestamo` (`id_prestamo`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=265 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_liquidacion_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_liquidacion_empleados`;
CREATE TABLE `nomina_planillas_liquidacion_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias_laborados` int(11) DEFAULT '0',
  `dias_laborados_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `terminar_contrato` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `id_motivo_fin_contrato` int(11) DEFAULT NULL,
  `motivo_fin_contrato` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_fin_contrato` date DEFAULT NULL,
  `vacaciones` varchar(10) COLLATE latin1_general_ci DEFAULT 'No',
  `verificado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `email_enviado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `provision_vacaciones` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `observaciones` longblob,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1263 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_planillas_liquidacion_empleados_conceptos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_planillas_liquidacion_empleados_conceptos`;
CREATE TABLE `nomina_planillas_liquidacion_empleados_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `codigo_concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `naturaleza` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `imprimir_volante` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_concepto` double(20,2) DEFAULT NULL,
  `valor_concepto_ajustado` double(20,2) DEFAULT '0.00',
  `saldo_restante` double(20,2) DEFAULT '0.00',
  `id_tercero` int(11) DEFAULT NULL,
  `id_empleado_cruce` int(11) DEFAULT NULL,
  `id_cuenta_colgaap` int(11) DEFAULT NULL,
  `cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `caracter` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero_contrapartida` int(11) DEFAULT NULL,
  `id_empleado_cruce_contrapartida` int(11) DEFAULT NULL,
  `id_tercero_cruce_liquidacion` int(11) DEFAULT NULL,
  `id_empleado_cruce_liquidacion` int(11) DEFAULT NULL,
  `id_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_contrapartida_niif` int(11) DEFAULT NULL,
  `cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion_cuenta_contrapartida_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_colgaap_liquidacion` int(11) DEFAULT '0',
  `cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_colgaap_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_cuenta_niif_liquidacion` int(11) DEFAULT '0',
  `cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `descripcion_cuenta_niif_liquidacion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `caracter_contrapartida` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos_contrapartida` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos_contrapartida` int(11) DEFAULT NULL,
  `codigo_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_centro_costos_contrapartida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nivel_formula` int(11) DEFAULT '0',
  `formula_original` longtext COLLATE latin1_general_ci,
  `valor_campo_texto` double DEFAULT '0',
  `formula` longtext COLLATE latin1_general_ci,
  `dias_laborados` int(11) DEFAULT '0',
  `dias_adicionales` int(11) DEFAULT '0' COMMENT 'dias adicionales para el calculo de la prima del segundo periodo',
  `base` double(20,2) DEFAULT '0.00',
  `id_prestamo` int(11) DEFAULT '0' COMMENT 'id del documento de relaionado a un prestamo si el concepto se carga a partir de un prestamo',
  `cierra_total_provision` varchar(10) COLLATE latin1_general_ci DEFAULT 'true' COMMENT 'true = cierra el total de la provision de vacaciones\r\nfalse = no cierra toda la provision de vacaciones',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_concepto` (`id_concepto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4311 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_prestamos_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_prestamos_empleados`;
CREATE TABLE `nomina_prestamos_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) DEFAULT NULL,
  `id_comprobante_egreso` int(11) DEFAULT NULL,
  `consecutivo_comprobante_egreso` int(11) DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `numero_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `fecha_inicio_pago` date DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT '0' COMMENT 'El tercero a quien el empleado le hace el prestamo',
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'El tercero a quien el empleado le hace el prestamo',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'El tercero a quien el empleado le hace el prestamo',
  `id_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_prestamo` double(20,2) DEFAULT NULL,
  `cuotas` int(11) DEFAULT NULL,
  `valor_cuota` double(20,2) DEFAULT NULL,
  `valor_prestamo_restante` double(20,2) DEFAULT NULL,
  `cuotas_restantes` int(11) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0=sin cruzar, 1=cruzado (con comprobante o nomina)',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_prestamos_empleados_pagos
-- ----------------------------
DROP TABLE IF EXISTS `nomina_prestamos_empleados_pagos`;
CREATE TABLE `nomina_prestamos_empleados_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `id_documento` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_extendido` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(20,2) DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `id_prestamo` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_tipos_liquidacion
-- ----------------------------
DROP TABLE IF EXISTS `nomina_tipos_liquidacion`;
CREATE TABLE `nomina_tipos_liquidacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_tipo_contrato
-- ----------------------------
DROP TABLE IF EXISTS `nomina_tipo_contrato`;
CREATE TABLE `nomina_tipo_contrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dias` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `codigo_dian` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_vacaciones_empleados
-- ----------------------------
DROP TABLE IF EXISTS `nomina_vacaciones_empleados`;
CREATE TABLE `nomina_vacaciones_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_planilla` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `fecha_inicio_contrato` date DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_empleado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicio_periodo_vacaciones` date DEFAULT NULL COMMENT 'fecha inicial del año de las vacaciones',
  `fecha_final_periodo_vacaciones` date DEFAULT NULL COMMENT 'fecha final del año de las vacaciones',
  `fecha_inicio_vacaciones_disfrutadas` date DEFAULT NULL,
  `fecha_fin_vacaciones_disfrutadas` date DEFAULT NULL,
  `dias_vacaciones_disfrutadas` int(11) DEFAULT NULL,
  `id_concepto_vacaciones` int(11) DEFAULT NULL,
  `concepto_vacaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_base` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'ultimo salario - promedio',
  `base` double(20,2) DEFAULT NULL,
  `valor_vacaciones_disfrutadas` double(20,2) DEFAULT NULL,
  `dias_vacaciones_compensadas` int(11) DEFAULT NULL,
  `valor_vacaciones_compensadas` double(20,2) DEFAULT NULL,
  `fecha_inicio_labores` date DEFAULT NULL,
  `tipo_pago_vacaciones` varchar(255) COLLATE latin1_general_ci DEFAULT 'completas' COMMENT 'las vacaciones se pueden pagar total o parcialmente, de manera que si el pago es parcial entonces solo se debe cerrar el valor de la provision, con el valor parcial, y si es total entonces el valor completo de las vacaciones',
  `estado` int(11) DEFAULT '0',
  `id_sucursal` int(11) DEFAULT '0',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_planilla` (`id_planilla`) USING BTREE,
  KEY `id_empleado` (`id_empleado`) USING BTREE,
  KEY `id_contrato` (`id_contrato`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nomina_wizard_process
-- ----------------------------
DROP TABLE IF EXISTS `nomina_wizard_process`;
CREATE TABLE `nomina_wizard_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nota_cierre
-- ----------------------------
DROP TABLE IF EXISTS `nota_cierre`;
CREATE TABLE `nota_cierre` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_niif` int(11) DEFAULT NULL,
  `sinc_nota` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_nota` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_tipo_nota` int(11) DEFAULT '0',
  `tipo_nota` varchar(225) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_tercero` int(15) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `numero_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tipo_identificacion_tercero` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `cedula_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0-> sin generar , 1-> generada',
  `nota_auto` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `activo` int(1) DEFAULT '1',
  `consecutivo_ws` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_tipo_nota` (`id_tipo_nota`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nota_cierre_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `nota_cierre_cuentas`;
CREATE TABLE `nota_cierre_cuentas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_nota_general` int(15) DEFAULT NULL,
  `id_puc` int(15) NOT NULL,
  `cuenta_puc` int(20) DEFAULT NULL,
  `descripcion_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_niif` int(15) DEFAULT NULL,
  `cuenta_niif` int(15) DEFAULT NULL,
  `descripcion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento_cruce` int(11) DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `tiene_centro_costo` varchar(5) COLLATE latin1_general_ci DEFAULT 'No',
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` bigint(20) DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_nota_general`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `cuenta_puc` (`cuenta_puc`) USING BTREE,
  KEY `id_niif` (`id_niif`) USING BTREE,
  KEY `cuenta_niif` (`cuenta_niif`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=260521 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nota_cierre_niif
-- ----------------------------
DROP TABLE IF EXISTS `nota_cierre_niif`;
CREATE TABLE `nota_cierre_niif` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_niif` int(11) DEFAULT NULL,
  `sinc_nota` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_nota` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_tipo_nota` int(11) DEFAULT '0',
  `tipo_nota` varchar(225) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_tercero` int(15) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `numero_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tipo_identificacion_tercero` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `cedula_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0-> sin generar , 1-> generada',
  `nota_auto` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `activo` int(1) DEFAULT '1',
  `consecutivo_ws` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_tipo_nota` (`id_tipo_nota`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nota_cierre_niif_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `nota_cierre_niif_cuentas`;
CREATE TABLE `nota_cierre_niif_cuentas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_nota_general` int(15) DEFAULT NULL,
  `id_puc` int(15) NOT NULL,
  `cuenta_puc` int(20) DEFAULT NULL,
  `descripcion_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_niif` int(15) DEFAULT NULL,
  `cuenta_niif` int(15) DEFAULT NULL,
  `descripcion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento_cruce` int(11) DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `tiene_centro_costo` varchar(5) COLLATE latin1_general_ci DEFAULT 'No',
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` bigint(20) DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_nota_general`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `cuenta_puc` (`cuenta_puc`) USING BTREE,
  KEY `id_niif` (`id_niif`) USING BTREE,
  KEY `cuenta_niif` (`cuenta_niif`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nota_contable_general
-- ----------------------------
DROP TABLE IF EXISTS `nota_contable_general`;
CREATE TABLE `nota_contable_general` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_niif` int(11) DEFAULT NULL,
  `sinc_nota` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_nota` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_tipo_nota` int(11) DEFAULT '0',
  `tipo_nota` varchar(225) COLLATE latin1_general_ci DEFAULT NULL,
  `id_centro_costos` int(15) DEFAULT NULL,
  `id_tercero` int(15) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `numero_identificacion_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tipo_identificacion_tercero` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `cedula_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0-> sin generar , 1-> generada',
  `nota_auto` varchar(6) COLLATE latin1_general_ci DEFAULT 'false',
  `activo` int(1) DEFAULT '1',
  `consecutivo_ws` int(11) DEFAULT NULL,
  `json_api` longblob COMMENT 'String json recibido al insertar la nota',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_tipo_nota` (`id_tipo_nota`) USING BTREE,
  KEY `id_centro_costos` (`id_centro_costos`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=39988 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nota_contable_general_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `nota_contable_general_cuentas`;
CREATE TABLE `nota_contable_general_cuentas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_nota_general` int(15) DEFAULT NULL,
  `id_puc` int(15) NOT NULL,
  `cuenta_puc` int(20) DEFAULT NULL,
  `descripcion_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_niif` int(15) DEFAULT NULL,
  `cuenta_niif` int(15) DEFAULT NULL,
  `descripcion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento_cruce` int(11) DEFAULT NULL,
  `debe` double(20,2) DEFAULT '0.00',
  `haber` double(20,2) DEFAULT '0.00',
  `tiene_centro_costo` varchar(5) COLLATE latin1_general_ci DEFAULT 'No',
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` bigint(20) DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_nota_general`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `cuenta_puc` (`cuenta_puc`) USING BTREE,
  KEY `id_niif` (`id_niif`) USING BTREE,
  KEY `cuenta_niif` (`cuenta_niif`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=697022 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for planes
-- ----------------------------
DROP TABLE IF EXISTS `planes`;
CREATE TABLE `planes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `usuarios` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `sucursales` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for plantillas
-- ----------------------------
DROP TABLE IF EXISTS `plantillas`;
CREATE TABLE `plantillas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `referencia` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for plantillas_configuracion
-- ----------------------------
DROP TABLE IF EXISTS `plantillas_configuracion`;
CREATE TABLE `plantillas_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plantillas_id` int(11) NOT NULL,
  `codigo_puc` int(11) NOT NULL,
  `codigo_niif` int(11) NOT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje` double(11,2) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `plantillas_id` (`plantillas_id`) USING BTREE,
  KEY `codigo_niif` (`codigo_niif`) USING BTREE,
  KEY `codigo_puc` (`codigo_puc`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for principios_niif
-- ----------------------------
DROP TABLE IF EXISTS `principios_niif`;
CREATE TABLE `principios_niif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for propiedades
-- ----------------------------
DROP TABLE IF EXISTS `propiedades`;
CREATE TABLE `propiedades` (
  `id_propiedades` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET latin1 NOT NULL,
  `activo` int(1) NOT NULL,
  PRIMARY KEY (`id_propiedades`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for prospectos_upload_registro
-- ----------------------------
DROP TABLE IF EXISTS `prospectos_upload_registro`;
CREATE TABLE `prospectos_upload_registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_comercial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `clasificacion` varchar(1) COLLATE latin1_general_ci DEFAULT 'D',
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'true',
  `id_upload` int(11) DEFAULT NULL,
  `mensaje_error` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tiene_error` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fila_excel` int(11) DEFAULT NULL,
  `tercero` tinyint(1) DEFAULT NULL,
  `tipo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `iso2` varchar(3) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for puc
-- ----------------------------
DROP TABLE IF EXISTS `puc`;
CREATE TABLE `puc` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(255) DEFAULT '',
  `cuenta` int(20) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `centro_costo` varchar(2) DEFAULT 'No',
  `cuenta_cruce` varchar(2) DEFAULT 'No',
  `tipo` varchar(50) DEFAULT '',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1999 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for puc_carga_auxiliares
-- ----------------------------
DROP TABLE IF EXISTS `puc_carga_auxiliares`;
CREATE TABLE `puc_carga_auxiliares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta` bigint(20) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for puc_configuracion
-- ----------------------------
DROP TABLE IF EXISTS `puc_configuracion`;
CREATE TABLE `puc_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `digitos` int(1) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for puc_configuraciones_default
-- ----------------------------
DROP TABLE IF EXISTS `puc_configuraciones_default`;
CREATE TABLE `puc_configuraciones_default` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `cuenta_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for puc_niff_respaldo
-- ----------------------------
DROP TABLE IF EXISTS `puc_niff_respaldo`;
CREATE TABLE `puc_niff_respaldo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for puc_niif
-- ----------------------------
DROP TABLE IF EXISTS `puc_niif`;
CREATE TABLE `puc_niif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cuenta` int(20) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(2) COLLATE latin1_general_ci DEFAULT 'No',
  `cuenta_cruce` varchar(2) COLLATE latin1_general_ci DEFAULT 'No',
  `tipo` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `cuenta` (`cuenta`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1962 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for rango_autorizaciones_ordenes_compra
-- ----------------------------
DROP TABLE IF EXISTS `rango_autorizaciones_ordenes_compra`;
CREATE TABLE `rango_autorizaciones_ordenes_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rango_inicial` double(20,2) DEFAULT NULL,
  `rango_final` double(20,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for recibo_caja
-- ----------------------------
DROP TABLE IF EXISTS `recibo_caja`;
CREATE TABLE `recibo_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_inicial` date DEFAULT NULL,
  `fecha_recibo` date DEFAULT NULL,
  `fecha_generado` date DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_configuracion_cuenta` int(11) DEFAULT '0',
  `configuracion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `descripcion_cuenta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `estado` int(1) DEFAULT '0',
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `observacion` longtext COLLATE latin1_general_ci,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_flujo_efectivo` int(11) DEFAULT '0',
  `flujo_efectivo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `debug` int(1) DEFAULT '0',
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'ws si es de siho',
  `json_api` longtext COLLATE latin1_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_configuracion_cuenta` (`id_configuracion_cuenta`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_flujo_efectivo` (`id_flujo_efectivo`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=52918 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for recibo_caja_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `recibo_caja_cuentas`;
CREATE TABLE `recibo_caja_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_recibo_caja` int(11) DEFAULT NULL,
  `id_puc` int(11) DEFAULT NULL,
  `cuenta` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `debito` double(20,2) DEFAULT NULL,
  `credito` double(20,2) DEFAULT '0.00',
  `saldo_pendiente` double(20,2) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `tipo_documento_cruce` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `numero_documento_cruce` int(11) DEFAULT '0',
  `observaciones` longtext COLLATE latin1_general_ci,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` bigint(20) DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_recibo_caja` (`id_recibo_caja`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=63942 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for reparacion
-- ----------------------------
DROP TABLE IF EXISTS `reparacion`;
CREATE TABLE `reparacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventario` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_reparacion` date NOT NULL,
  `quien_reparo` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `observaciones_reparacion` varchar(1000) COLLATE latin1_general_ci NOT NULL,
  `insumos` varchar(1000) COLLATE latin1_general_ci NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) NOT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_equipo` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `equipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for resolucion_documento_soporte
-- ----------------------------
DROP TABLE IF EXISTS `resolucion_documento_soporte`;
CREATE TABLE `resolucion_documento_soporte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefijo` varchar(255) DEFAULT '',
  `numero_resolucion` varchar(255) DEFAULT '1',
  `fecha_resolucion` date DEFAULT NULL,
  `fecha_inicio_resolucion` date DEFAULT NULL,
  `fecha_final_resolucion` date DEFAULT NULL,
  `numero_inicial_resolucion` int(11) DEFAULT NULL,
  `numero_final_resolucion` int(11) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT '' COMMENT 'tipo de resolucion "Documento Soporte, Ajuste documento Soporte"',
  `consecutivo` int(11) DEFAULT NULL COMMENT 'consecutivo donde iniciara el proximo documento',
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for resolucion_documento_soporte_sucursales
-- ----------------------------
DROP TABLE IF EXISTS `resolucion_documento_soporte_sucursales`;
CREATE TABLE `resolucion_documento_soporte_sucursales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_resolucion` int(11) DEFAULT NULL,
  `numero_resolucion` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `predeterminada` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'No' COMMENT 'Si es la resolucion prederteminada se tomara al momento de crear la factura',
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for retenciones
-- ----------------------------
DROP TABLE IF EXISTS `retenciones`;
CREATE TABLE `retenciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `retencion` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_retencion` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(20,5) DEFAULT NULL,
  `base` double(20,2) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_autoretencion` int(11) DEFAULT NULL,
  `cuenta_autoretencion_niif` int(11) DEFAULT NULL,
  `modulo` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `factura_auto` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `id_empresa` int(11) DEFAULT NULL,
  `id_departamento` int(11) DEFAULT '0',
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT '0',
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for reunion_coope
-- ----------------------------
DROP TABLE IF EXISTS `reunion_coope`;
CREATE TABLE `reunion_coope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) NOT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_configuracion_reunion_coope` int(11) NOT NULL,
  `id_configuracion_reunion_coope_checklist` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `fecha` date NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for reunion_coope_datos
-- ----------------------------
DROP TABLE IF EXISTS `reunion_coope_datos`;
CREATE TABLE `reunion_coope_datos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_reunion_coope` int(11) NOT NULL,
  `id_checklist` int(11) NOT NULL,
  `dato_boleano` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dato_area` varchar(2500) COLLATE latin1_general_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `checklist` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_checklist_detalle` int(11) NOT NULL,
  `checklist_detalle` varchar(1500) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seccion_items
-- ----------------------------
DROP TABLE IF EXISTS `seccion_items`;
CREATE TABLE `seccion_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for solicitudes
-- ----------------------------
DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE `solicitudes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tipo` tinyint(1) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `solicitante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cedula` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombres` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellidos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `galeon` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `correo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `siip` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `comercial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `logistico` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `administrativo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `financiero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `gerencial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `multiciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `accion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `razon` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `propiedad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros
-- ----------------------------
DROP TABLE IF EXISTS `terceros`;
CREATE TABLE `terceros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_otros` int(11) DEFAULT NULL,
  `id_sucursal_otros` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_identificacion` int(11) DEFAULT NULL,
  `tipo_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dv` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_comercial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `iso2` varchar(3) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `web` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `contactos` int(4) DEFAULT '0',
  `nombre_establecimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `representante_legal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_identificacion_representante` int(11) DEFAULT NULL,
  `tipo_identificacion_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `identificacion_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_id_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_matricula_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `libro_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_matricula_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_escritura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_escritura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `notaria_escritura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_notaria` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `id_sector_empresarial` int(11) DEFAULT NULL,
  `sector_empresarial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `pagina_web` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_configuracion_origen` int(11) DEFAULT NULL,
  `configuracion_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `UserIdLog` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `id_tercero_tributario` int(11) DEFAULT NULL,
  `tercero_tributario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_persona_dian` int(1) DEFAULT NULL,
  `gravable` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `retener_ica` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `retener_iva` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `agente_retenedor` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `clasificacion` varchar(1) COLLATE latin1_general_ci DEFAULT 'D',
  `id_proyecto` int(11) DEFAULT NULL,
  `proyecto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_actividad` int(11) DEFAULT NULL,
  `actividad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_cliente` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo` int(11) DEFAULT NULL,
  `tipo_proveedor` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero_tipo` int(11) DEFAULT NULL,
  `id_forma_pago` int(11) DEFAULT '0',
  `id_metodo_pago` int(11) DEFAULT NULL,
  `id_forma_cobro` int(11) DEFAULT NULL,
  `exento_iva` varchar(5) COLLATE latin1_general_ci DEFAULT '',
  `tercero_empleado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `nombre1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `update` int(1) DEFAULT '0',
  `debug` int(11) DEFAULT NULL,
  `ficha_tecnica` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `tercero` int(1) NOT NULL DEFAULT '1' COMMENT 'prospecto => 0 , tercero => 1',
  `prioridad_prospecto` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `banco` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_cuenta_bancaria` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_cuenta_bancaria` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_otros` (`id_otros`) USING BTREE,
  KEY `id_sucursal_otros` (`id_sucursal_otros`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_tipo_identificacion` (`id_tipo_identificacion`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE,
  KEY `id_tipo_identificacion_representante` (`id_tipo_identificacion_representante`) USING BTREE,
  KEY `ciudad_id_representante` (`ciudad_id_representante`) USING BTREE,
  KEY `id_sector_empresarial` (`id_sector_empresarial`) USING BTREE,
  KEY `id_tercero_tributario` (`id_tercero_tributario`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=62333 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_asignados
-- ----------------------------
DROP TABLE IF EXISTS `terceros_asignados`;
CREATE TABLE `terceros_asignados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `asignado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_contactos
-- ----------------------------
DROP TABLE IF EXISTS `terceros_contactos`;
CREATE TABLE `terceros_contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) NOT NULL,
  `id_tipo_identificacion` int(11) NOT NULL,
  `tipo_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tratamiento` int(11) NOT NULL,
  `tratamiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cargo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `nacimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observaciones` blob,
  `sexo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `emails` int(4) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `ContactoAuto` int(1) NOT NULL DEFAULT '0' COMMENT 'campo controlador para cuando el contacto es una persona  natural y se crea automaticamente',
  `id_empresa` int(11) DEFAULT NULL,
  `id_siip` int(11) DEFAULT NULL,
  `grupo_empresarial` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_tipo_identificacion` (`id_tipo_identificacion`) USING BTREE,
  KEY `id_tratamiento` (`id_tratamiento`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_siip` (`id_siip`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=57975 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_contactos_email
-- ----------------------------
DROP TABLE IF EXISTS `terceros_contactos_email`;
CREATE TABLE `terceros_contactos_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contacto` int(11) NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_siip` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_contacto` (`id_contacto`) USING BTREE,
  KEY `id_siip` (`id_siip`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_datos_asiste
-- ----------------------------
DROP TABLE IF EXISTS `terceros_datos_asiste`;
CREATE TABLE `terceros_datos_asiste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `tipo_conexion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ip_conexion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario_conexion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `clave_conexion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `bd` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario_bd` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `clave_bd` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observaciones` varchar(5000) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_direcciones
-- ----------------------------
DROP TABLE IF EXISTS `terceros_direcciones`;
CREATE TABLE `terceros_direcciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(2) DEFAULT '1',
  `telefono1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion_principal` int(1) DEFAULT '0',
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_siip` int(11) DEFAULT '0',
  `debug` int(1) DEFAULT '0',
  `emails` int(4) DEFAULT NULL,
  `codigo_postal` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_matricula_mercantil` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_siip` (`id_siip`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=62355 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_direcciones_email
-- ----------------------------
DROP TABLE IF EXISTS `terceros_direcciones_email`;
CREATE TABLE `terceros_direcciones_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_direccion` int(11) NOT NULL,
  `contacto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_siip` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_contacto` (`id_direccion`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=343 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_documentos
-- ----------------------------
DROP TABLE IF EXISTS `terceros_documentos`;
CREATE TABLE `terceros_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) NOT NULL,
  `tipo_documento` int(11) NOT NULL,
  `tipo_documento_nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `document_type` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(2) DEFAULT '1',
  `nombre` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_emails
-- ----------------------------
DROP TABLE IF EXISTS `terceros_emails`;
CREATE TABLE `terceros_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_ficha_tecnica
-- ----------------------------
DROP TABLE IF EXISTS `terceros_ficha_tecnica`;
CREATE TABLE `terceros_ficha_tecnica` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `gran_contribuyente` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `responsable_iva` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `autoretenedor` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `responsable_ica` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_actividad_economica` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cod_ciiu` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `autoretenedor_ica` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `tarifa_por_mil` double(20,2) DEFAULT NULL,
  `pago_orden` varchar(55) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_cuenta` varchar(55) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_cuenta` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `entidad` varchar(55) COLLATE latin1_general_ci DEFAULT NULL,
  `contacto_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_proveedor` varchar(55) COLLATE latin1_general_ci DEFAULT NULL,
  `forma_pago` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `desc_pronto_pago` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `porc_desc_pronto_pago` double(20,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_contacto_cartera` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cargo_contacto_cartera` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_contacto_cartera` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `extension_contacto_cartera` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `email_contacto_cartera` varchar(55) COLLATE latin1_general_ci DEFAULT NULL,
  `fax_contacto_cartera` varchar(55) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_forma_pago` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_log
-- ----------------------------
DROP TABLE IF EXISTS `terceros_log`;
CREATE TABLE `terceros_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `observacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `accion` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ID` (`id`) USING BTREE,
  KEY `ID_PEDIDO` (`id_tercero`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=62333 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_retenciones
-- ----------------------------
DROP TABLE IF EXISTS `terceros_retenciones`;
CREATE TABLE `terceros_retenciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_retencion` int(11) DEFAULT NULL,
  `retencion` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor` double(20,3) DEFAULT NULL,
  `modulo` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(2) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_retencion` (`id_retencion`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE,
  KEY `id_proveedor` (`id_proveedor`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for terceros_tipo
-- ----------------------------
DROP TABLE IF EXISTS `terceros_tipo`;
CREATE TABLE `terceros_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` smallint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_tipo_documento
-- ----------------------------
DROP TABLE IF EXISTS `terceros_tipo_documento`;
CREATE TABLE `terceros_tipo_documento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_tratamiento
-- ----------------------------
DROP TABLE IF EXISTS `terceros_tratamiento`;
CREATE TABLE `terceros_tratamiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_tributario
-- ----------------------------
DROP TABLE IF EXISTS `terceros_tributario`;
CREATE TABLE `terceros_tributario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pais` int(11) NOT NULL,
  `codigo_regimen_dian` int(1) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_upload
-- ----------------------------
DROP TABLE IF EXISTS `terceros_upload`;
CREATE TABLE `terceros_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `nombre_archivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ok` int(11) DEFAULT '0',
  `repetido` int(11) DEFAULT NULL,
  `fail` int(11) DEFAULT '0',
  `estado` int(1) DEFAULT '0',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `tercero` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for terceros_upload_registro
-- ----------------------------
DROP TABLE IF EXISTS `terceros_upload_registro`;
CREATE TABLE `terceros_upload_registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_identificacion` int(11) DEFAULT NULL,
  `tipo_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `dv` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(50) COLLATE latin1_general_ci DEFAULT 'false',
  `ciudad_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_identificacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_comercial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `telefono2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular1` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `celular2` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `iso2` varchar(3) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `web` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_establecimiento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `representante_legal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tipo_identificacion_representante` int(11) DEFAULT NULL,
  `tipo_identificacion_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `identificacion_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_id_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_representante` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_matricula_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `libro_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_matricula_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_camara` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_escritura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_escritura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `notaria_escritura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ciudad_notaria` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `id_sector_empresarial` int(11) DEFAULT NULL,
  `sector_empresarial` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `pagina_web` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_configuracion_origen` int(11) DEFAULT NULL,
  `configuracion_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `id_tercero_tributario` int(11) DEFAULT NULL,
  `tercero_tributario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `agente_retenedor` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `clasificacion` varchar(1) COLLATE latin1_general_ci DEFAULT 'D',
  `id_proyecto` int(11) DEFAULT NULL,
  `proyecto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_actividad` int(11) DEFAULT NULL,
  `actividad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_cliente` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_proveedor` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero_tipo` int(11) DEFAULT NULL,
  `id_forma_pago` int(11) DEFAULT '0',
  `id_forma_cobro` int(11) DEFAULT NULL,
  `exento_iva` varchar(5) COLLATE latin1_general_ci DEFAULT '',
  `tercero_empleado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `nombre1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'true',
  `id_upload` int(11) DEFAULT NULL,
  `mensaje_error` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tiene_error` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fila_excel` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_tipo_identificacion` (`id_tipo_identificacion`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE,
  KEY `id_ciudad` (`id_ciudad`) USING BTREE,
  KEY `id_tipo_identificacion_representante` (`id_tipo_identificacion_representante`) USING BTREE,
  KEY `ciudad_id_representante` (`ciudad_id_representante`) USING BTREE,
  KEY `id_sector_empresarial` (`id_sector_empresarial`) USING BTREE,
  KEY `id_tercero_tributario` (`id_tercero_tributario`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tipo_documento
-- ----------------------------
DROP TABLE IF EXISTS `tipo_documento`;
CREATE TABLE `tipo_documento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT '0',
  `codigo_tributario` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `detalle` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `codigo_tipo_documento_dian` int(2) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tipo_nota_contable
-- ----------------------------
DROP TABLE IF EXISTS `tipo_nota_contable`;
CREATE TABLE `tipo_nota_contable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `consecutivo_niif` int(11) DEFAULT NULL,
  `documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT 'Si',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ubicacion_ciudad
-- ----------------------------
DROP TABLE IF EXISTS `ubicacion_ciudad`;
CREATE TABLE `ubicacion_ciudad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciudad` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_ciudad` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `codigo_departamento` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE,
  KEY `id_departamento` (`id_departamento`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5167 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ubicacion_departamento
-- ----------------------------
DROP TABLE IF EXISTS `ubicacion_departamento`;
CREATE TABLE `ubicacion_departamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departamento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_departamento` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_pais` int(11) DEFAULT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pais` (`id_pais`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1379 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ubicacion_pais
-- ----------------------------
DROP TABLE IF EXISTS `ubicacion_pais`;
CREATE TABLE `ubicacion_pais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `continente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `subcontinente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `prefijo` int(11) NOT NULL,
  `moneda` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `nombre-moneda` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `iso2` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `iso3` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `time_zone` varchar(255) COLLATE latin1_general_ci DEFAULT 'America/Bogota',
  `activo` int(1) NOT NULL DEFAULT '1',
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=243 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ubicacion_pais_copy
-- ----------------------------
DROP TABLE IF EXISTS `ubicacion_pais_copy`;
CREATE TABLE `ubicacion_pais_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pais` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `continente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `subcontinente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `prefijo` int(11) NOT NULL,
  `moneda` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `nombre-moneda` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `iso2` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `iso3` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `time_zone` varchar(255) COLLATE latin1_general_ci DEFAULT 'America/Bogota',
  `activo` int(1) NOT NULL DEFAULT '1',
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for variables
-- ----------------------------
DROP TABLE IF EXISTS `variables`;
CREATE TABLE `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `detalle` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_grupo` int(11) NOT NULL,
  `grupo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `campo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tabla` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `funcion` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `automatica` int(1) NOT NULL DEFAULT '1',
  `where` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `change_update` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `proyecto` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for variables_grupos
-- ----------------------------
DROP TABLE IF EXISTS `variables_grupos`;
CREATE TABLE `variables_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) DEFAULT '1',
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_cotizaciones
-- ----------------------------
DROP TABLE IF EXISTS `ventas_cotizaciones`;
CREATE TABLE `ventas_cotizaciones` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `total_unidades` double(15,0) DEFAULT '0' COMMENT 'total de items de la cotizacion',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_vendedor` varchar(255) COLLATE latin1_general_ci DEFAULT '0',
  `documento_vendedor` int(11) DEFAULT NULL,
  `nombre_vendedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cliente` int(15) DEFAULT '0',
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cod_cliente` int(11) DEFAULT NULL,
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_cliente` int(11) DEFAULT NULL,
  `sucursal_cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `exento_iva` varchar(5) COLLATE latin1_general_ci DEFAULT '',
  `plantillas_id` int(15) DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura, 3 -> cancelado',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_vendedor` (`id_vendedor`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_cliente` (`id_cliente`) USING BTREE,
  KEY `id_sucursal_cliente` (`id_sucursal_cliente`) USING BTREE,
  KEY `plantillas_id` (`plantillas_id`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_cotizaciones_inventario
-- ----------------------------
DROP TABLE IF EXISTS `ventas_cotizaciones_inventario`;
CREATE TABLE `ventas_cotizaciones_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_cotizacion_venta` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) DEFAULT '0.00',
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_cotizacion_venta` (`id_cotizacion_venta`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas`;
CREATE TABLE `ventas_facturas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL COMMENT 'fecha de creacion',
  `fecha_contabilizado` date DEFAULT NULL COMMENT 'fecha vencimiento forma de pago',
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha generacion',
  `hora_inicio` time DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'fecha vencimiento forma de pago',
  `id_configuracion_resolucion` int(11) DEFAULT '0',
  `prefijo` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `numero_factura` int(11) DEFAULT '0',
  `numero_factura_completo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_vendedor` int(11) DEFAULT '0',
  `documento_vendedor` int(11) DEFAULT NULL,
  `nombre_vendedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cliente` int(15) DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_cliente` int(11) DEFAULT '0',
  `sucursal_cliente` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `exento_iva` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `plantillas_id` int(11) DEFAULT '0',
  `estado` int(2) DEFAULT '0' COMMENT '0 -> sin guardar, 1 ->generada, 3->cancelada, 4 -> bloqueada',
  `id_configuracion_cuenta_pago` int(11) DEFAULT NULL,
  `configuracion_cuenta_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cuenta_pago` int(11) DEFAULT '0',
  `cuenta_pago` int(11) DEFAULT NULL,
  `cuenta_pago_niif` int(11) DEFAULT NULL,
  `id_metodo_pago` int(15) DEFAULT NULL,
  `metodo_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `dias_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `forma_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT '0',
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `observacion` longtext COLLATE latin1_general_ci,
  `orden_compra` longtext COLLATE latin1_general_ci,
  `total_factura` double(20,2) DEFAULT '0.00',
  `total_factura_sin_abono` double(20,2) DEFAULT '0.00',
  `cuenta_anticipo` int(10) DEFAULT '0',
  `valor_anticipo` double(20,2) DEFAULT '0.00',
  `id_centro_costo` int(11) DEFAULT '0',
  `codigo_centro_costo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_FE` int(10) DEFAULT NULL,
  `nombre_usuario_FE` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `cedula_usuario_FE` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_FE` date DEFAULT NULL,
  `hora_FE` time DEFAULT '00:00:01',
  `response_FE` longtext COLLATE latin1_general_ci,
  `UUID` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `email_fe` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_saldo_inicial` int(11) DEFAULT '0',
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `keyws` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `json_api` longtext COLLATE latin1_general_ci COMMENT 'Json recibido de aplicacion externa para generar la factura en ERP',
  `cufe` varchar(150) COLLATE latin1_general_ci DEFAULT NULL,
  `info_reserva` longtext COLLATE latin1_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_configuracion_resolucion` (`id_configuracion_resolucion`) USING BTREE,
  KEY `id_vendedor` (`id_vendedor`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_cliente` (`id_cliente`) USING BTREE,
  KEY `id_sucursal_cliente` (`id_sucursal_cliente`) USING BTREE,
  KEY `plantillas_id` (`plantillas_id`) USING BTREE,
  KEY `id_configuracion_cuenta_pago` (`id_configuracion_cuenta_pago`) USING BTREE,
  KEY `id_cuenta_pago` (`id_cuenta_pago`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_centro_costo` (`id_centro_costo`) USING BTREE,
  KEY `id_saldo_inicial` (`id_saldo_inicial`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=78705 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_configuracion
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_configuracion`;
CREATE TABLE `ventas_facturas_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dias_vencimiento` int(11) DEFAULT NULL,
  `prefijo` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `consecutivo_resolucion` varchar(255) COLLATE latin1_general_ci DEFAULT '1',
  `fecha_resolucion` date DEFAULT NULL,
  `fecha_final_resolucion` date DEFAULT NULL,
  `numero_inicial_resolucion` int(11) DEFAULT NULL,
  `numero_final_resolucion` int(11) DEFAULT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'tipo de resolucion "Factura por computador, factura Electronica, Facturacion Manual"',
  `consecutivo_factura` int(11) DEFAULT NULL COMMENT 'consecutivo donde iniciara la proxima factura',
  `digitos` int(11) DEFAULT '0',
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_configuracion_sucursales
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_configuracion_sucursales`;
CREATE TABLE `ventas_facturas_configuracion_sucursales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_resolucion` int(11) DEFAULT NULL,
  `numero_resolucion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `predeterminada` varchar(50) COLLATE latin1_general_ci DEFAULT 'No' COMMENT 'Si es la resolucion prederteminada se tomara al momento de crear la factura',
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_cuentas
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_cuentas`;
CREATE TABLE `ventas_facturas_cuentas` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_venta` int(15) DEFAULT NULL,
  `id_tabla_referencia` int(11) DEFAULT '0' COMMENT 'id de la tabla de comprobante_egreso_cuentas, para poder restarle el saldo a la cuenta del comprobante',
  `id_puc` int(15) NOT NULL,
  `cuenta_puc` int(20) DEFAULT NULL,
  `descripcion_puc` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_niif` int(15) DEFAULT NULL,
  `cuenta_niif` int(15) DEFAULT NULL,
  `descripcion_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo_tercero` int(11) DEFAULT NULL,
  `nit_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento_cruce` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_documento_cruce` int(11) DEFAULT NULL,
  `prefijo_documento_cruce` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_documento_cruce` int(11) DEFAULT NULL,
  `debito` double(20,2) DEFAULT '0.00',
  `credito` double(20,2) DEFAULT '0.00',
  `id_centro_costos` int(11) DEFAULT '0',
  `codigo_centro_costos` int(11) DEFAULT '0',
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `codigo_concepto` int(11) DEFAULT NULL,
  `concepto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_nota_general` (`id_factura_venta`) USING BTREE,
  KEY `id_puc` (`id_puc`) USING BTREE,
  KEY `cuenta_puc` (`cuenta_puc`) USING BTREE,
  KEY `id_niif` (`id_niif`) USING BTREE,
  KEY `cuenta_niif` (`cuenta_niif`) USING BTREE,
  KEY `id_tercero` (`id_tercero`) USING BTREE,
  KEY `id_documento_cruce` (`id_documento_cruce`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=185045 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_documentos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_documentos`;
CREATE TABLE `ventas_facturas_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activo` int(1) DEFAULT '1',
  `id_factura_venta` int(15) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_grupos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_grupos`;
CREATE TABLE `ventas_facturas_grupos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_venta` int(15) DEFAULT NULL,
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `costo_unitario` double(15,2) DEFAULT '0.00' COMMENT 'precio de venta si iva',
  `observaciones` longtext COLLATE latin1_general_ci,
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `nombre_impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje_impuesto` double(11,2) DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT '0.00',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura_venta` (`id_factura_venta`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_inventario
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_inventario`;
CREATE TABLE `ventas_facturas_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_venta` int(15) DEFAULT NULL,
  `id_inventario` int(15) DEFAULT NULL,
  `id_tabla_inventario_referencia` int(15) DEFAULT NULL,
  `id_consecutivo_referencia` int(11) DEFAULT NULL,
  `consecutivo_referencia` int(11) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_proveedor` int(20) DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) DEFAULT NULL COMMENT 'precio de venta si iva',
  `costo_inventario` double(15,2) DEFAULT NULL,
  `observaciones` longtext COLLATE latin1_general_ci,
  `tipo_descuento` varchar(50) COLLATE latin1_general_ci DEFAULT 'porcentaje',
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `id_fila_item_receta` int(11) DEFAULT '0' COMMENT 'si este campo tiene un valor quiere decir que el item pertenece a le receta de un item, para este caso el item de la fila con este id',
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura_venta` (`id_factura_venta`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_tabla_inventario_referencia` (`id_tabla_inventario_referencia`) USING BTREE,
  KEY `id_consecutivo_referencia` (`id_consecutivo_referencia`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=83197 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_inventario_grupos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_inventario_grupos`;
CREATE TABLE `ventas_facturas_inventario_grupos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_factura_venta` int(15) DEFAULT NULL,
  `id_grupo_factura_venta` int(11) DEFAULT NULL,
  `id_inventario_factura_venta` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura_venta` (`id_factura_venta`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_retenciones
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_retenciones`;
CREATE TABLE `ventas_facturas_retenciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_factura_venta` int(11) DEFAULT NULL,
  `id_retencion` int(11) DEFAULT NULL,
  `tipo_retencion` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `retencion` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(20,3) DEFAULT NULL,
  `base` double DEFAULT NULL,
  `codigo_cuenta` int(11) DEFAULT NULL,
  `codigo_cuenta_niif` int(11) DEFAULT NULL,
  `cuenta_autoretencion` int(11) DEFAULT NULL,
  `cuenta_autoretencion_niif` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura_venta` (`id_factura_venta`) USING BTREE,
  KEY `id_retencion` (`id_retencion`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3661 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_facturas_update_fecha
-- ----------------------------
DROP TABLE IF EXISTS `ventas_facturas_update_fecha`;
CREATE TABLE `ventas_facturas_update_fecha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_factura` (`id_factura`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pedidos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pedidos`;
CREATE TABLE `ventas_pedidos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `unidades_pendientes` double DEFAULT '0' COMMENT 'items pendientes a remisionar o facturar',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT '0',
  `documento_vendedor` int(11) DEFAULT NULL,
  `nombre_vendedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cliente` int(15) DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cod_cliente` int(11) DEFAULT NULL,
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `exento_iva` varchar(5) COLLATE latin1_general_ci DEFAULT '',
  `plantillas_id` int(15) DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura, 3 -> cancelado',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_vendedor` (`id_vendedor`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_cliente` (`id_cliente`) USING BTREE,
  KEY `plantillas_id` (`plantillas_id`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pedidos_configuracion
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pedidos_configuracion`;
CREATE TABLE `ventas_pedidos_configuracion` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `valor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pedidos_inventario
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pedidos_inventario`;
CREATE TABLE `ventas_pedidos_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_pedido_venta` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `id_tabla_inventario_referencia` int(11) DEFAULT NULL,
  `id_consecutivo_referencia` int(11) DEFAULT NULL,
  `consecutivo_referencia` int(11) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(50) COLLATE latin1_general_ci DEFAULT '',
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `costo_unitario` double(15,2) DEFAULT '0.00',
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `descuento` double(15,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pedido_venta` (`id_pedido_venta`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_tabla_inventario_referencia` (`id_tabla_inventario_referencia`) USING BTREE,
  KEY `id_consecutivo_referencia` (`id_consecutivo_referencia`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos`;
CREATE TABLE `ventas_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `randomico` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `prefijo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `id_configuracion_resolucion` int(11) DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL COMMENT 'fecha cuando se inserta el registro en la bd',
  `fecha_generado` date DEFAULT NULL COMMENT 'fecha cuando se genera el recibo',
  `hora_generado` time DEFAULT NULL COMMENT 'hora cuando se genera el recibo',
  `fecha_documento` date DEFAULT NULL,
  `hora_documento` time DEFAULT NULL,
  `id_caja` int(11) DEFAULT NULL,
  `caja` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_mesa` int(11) DEFAULT NULL,
  `codigo_mesa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `mesa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'este campo corresponde al restaurante donde se esta vendiendo, es obligatorio para manejar tambien el inventario',
  `id_reserva` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_reserva` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `habitacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_huesped` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `direccion_mac` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `monto_recibido` double(11,2) DEFAULT '0.00' COMMENT 'la cantidad que el cliente pago',
  `subtotal_pos` double NOT NULL DEFAULT '0',
  `total_pos` double(11,2) DEFAULT '0.00' COMMENT 'total del ticket',
  `id_descuento` int(11) DEFAULT NULL,
  `nombre_descuento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje_descuento` double DEFAULT NULL,
  `valor_descuento` double DEFAULT NULL,
  `id_propina` int(11) DEFAULT NULL,
  `nombre_propina` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje_propina` double DEFAULT NULL,
  `valor_propina` double DEFAULT NULL,
  `fecha_auditoria` date DEFAULT NULL,
  `hora_auditoria` time DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0 sin generar, 1 generado, 2 bloqueado (auditado)',
  `detalle_estado` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Detalle del error (si se produce) para despues corregirlo y generar de nuevo el tiquet',
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT '''restaurantes'' = el tiquet pertenece al POS de restaurantes\r\n''general'' = el tiquet es una venta en el POS general',
  `json_log` longblob,
  `activo` int(11) DEFAULT '1',
  `id_factura` int(11) DEFAULT NULL,
  `prefijo_factura` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_factura` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `numero_factura_completo` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `id_entrada_almacen` int(11) DEFAULT NULL,
  `consecutivo_entrada_almacen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_configuracion_resolucion` (`id_configuracion_resolucion`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=37827 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_auditoria_cierre
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_auditoria_cierre`;
CREATE TABLE `ventas_pos_auditoria_cierre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1563 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_auditoria_precierre
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_auditoria_precierre`;
CREATE TABLE `ventas_pos_auditoria_precierre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` int(11) DEFAULT NULL COMMENT '1 = generada,  2 bloqueada por auditoria',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1693 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_cajas
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_cajas`;
CREATE TABLE `ventas_pos_cajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_caja` int(11) DEFAULT NULL,
  `direccion_mac` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'true' COMMENT 'true=caja funcionando normalmente\r\ndisabled=caja bloqueada\r\nblock=caja bloqueada para la descarga de consecutivos\r\nchanged = se va a cambiar la caja',
  `nombre_equipo` longtext COLLATE latin1_general_ci,
  `serial_equipo` longtext COLLATE latin1_general_ci,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_cajas_movimientos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_cajas_movimientos`;
CREATE TABLE `ventas_pos_cajas_movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_caja` int(11) DEFAULT NULL,
  `nombre_caja` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE latin1_general_ci DEFAULT 'Abierta' COMMENT 'Abierta = Cuando ya se ha iniciado caja con provision\r\nCerrada = Cuando se cierra la caja a final de un turno',
  `fecha_apertura` date DEFAULT NULL COMMENT 'Fecha en que se realiza la apertura de la caja',
  `hora_apertura` time DEFAULT NULL COMMENT 'Fecha en que se realiza la apertura de la caja',
  `provision` double DEFAULT NULL COMMENT 'Dinero con el que se abre la caja al iniciar el turno',
  `observacion_apertura` longtext COLLATE latin1_general_ci,
  `id_usuario_cierre` int(11) DEFAULT NULL,
  `documento_usuario_cierre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario_cierre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL COMMENT 'Fecha en que se realiza el cierre de la caja',
  `hora_cierre` time DEFAULT NULL COMMENT 'Hora en que se realiza el cierre de la caja',
  `valor_cierre` double DEFAULT NULL COMMENT 'Dinero final con el que se cierra la caja',
  `observacion_cierre` longtext COLLATE latin1_general_ci,
  `fecha_auditoria` date DEFAULT NULL,
  `hora_auditoria` time DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3789 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_cajas_secciones
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_cajas_secciones`;
CREATE TABLE `ventas_pos_cajas_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_caja` int(11) DEFAULT NULL,
  `nombre_caja` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_comanda
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_comanda`;
CREATE TABLE `ventas_pos_comanda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL COMMENT 'id de la cuenta de la mesa',
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `randomico` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_caja` int(11) DEFAULT NULL,
  `nombre_caja` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0 sin generar, 1 generado, 2 editado, 3 anulado',
  `id_usuario_anulacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `documento_usuario_anulacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario_anulacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_anulacion` date DEFAULT NULL,
  `hora_anulacion` time DEFAULT NULL,
  `observacion_anulacion` longtext COLLATE latin1_general_ci,
  `fecha_auditoria` date DEFAULT NULL,
  `hora_auditoria` time DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=57029 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_configuracion
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_configuracion`;
CREATE TABLE `ventas_pos_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_resolucion_dian` bigint(200) DEFAULT NULL,
  `fecha_resolucion_dian` date DEFAULT NULL,
  `vigencia` int(11) NOT NULL,
  `grandes_contribuyentes` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `prefijo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_inicial` int(20) DEFAULT NULL,
  `consecutivo_final` int(20) DEFAULT NULL,
  `cantidad_consecutivos` int(11) DEFAULT '100',
  `consecutivo_pos` int(11) DEFAULT NULL COMMENT 'consecutivo actual en que se iniciara el siguiente tiquet',
  `id_configuracion_cuenta_cobro` int(11) DEFAULT NULL,
  `descripcion_cuenta_cobro` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_por_cobrar_colgaap` int(11) DEFAULT NULL,
  `cuenta_por_cobrar_niif` int(11) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'true' COMMENT 'true = si no se han terminado, block= si ya se terminaron',
  `id_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_configuracion_cuenta_cobro` (`id_configuracion_cuenta_cobro`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_configuracion_consecutivos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_configuracion_consecutivos`;
CREATE TABLE `ventas_pos_configuracion_consecutivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for ventas_pos_configuracion_sucursales
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_configuracion_sucursales`;
CREATE TABLE `ventas_pos_configuracion_sucursales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_resolucion` int(11) DEFAULT NULL,
  `numero_resolucion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT '0',
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `predeterminada` varchar(50) COLLATE latin1_general_ci DEFAULT 'No' COMMENT 'Si es la resolucion prederteminada se tomara al momento de crear la factura',
  `id_empresa` int(11) DEFAULT '0',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_consecutivos_caja
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_consecutivos_caja`;
CREATE TABLE `ventas_pos_consecutivos_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_caja` int(11) DEFAULT NULL,
  `direccion_mac` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `consecutivo_inicial` int(11) DEFAULT NULL,
  `consecutivo_final` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_resolucion` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'false',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_resolucion` (`id_resolucion`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_consecutivos_liberados
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_consecutivos_liberados`;
CREATE TABLE `ventas_pos_consecutivos_liberados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo_inicial` int(11) DEFAULT NULL,
  `consecutivo_final` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_resolucion` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_resolucion` (`id_resolucion`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for ventas_pos_cuenta_comensales_porborrar
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_cuenta_comensales_porborrar`;
CREATE TABLE `ventas_pos_cuenta_comensales_porborrar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL,
  `randomico` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0 sin generar, 1 generado',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_cuenta_porborrar
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_cuenta_porborrar`;
CREATE TABLE `ventas_pos_cuenta_porborrar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `randomico` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT '0' COMMENT '0 sin generar, 1 generado',
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_formas_pago
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_formas_pago`;
CREATE TABLE `ventas_pos_formas_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pos` int(11) DEFAULT NULL,
  `id_forma_pago` int(11) DEFAULT NULL,
  `forma_pago` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` double(100,2) DEFAULT NULL COMMENT 'valor pagado por el cliente con esta forma de pago',
  `n_tarjeta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `n_aprobacion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=38096 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_inventario
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_inventario`;
CREATE TABLE `ventas_pos_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_comanda` int(11) DEFAULT NULL COMMENT 'id de la comanda si tiene relacionado el pos de restaurante',
  `id_row_item_cuenta` int(11) DEFAULT NULL COMMENT 'id unico de la fila que tiene el item guardado en la cuenta de la mesa, se usa para actualizar la cantidad de item y darle manejo al saldo',
  `id_pos` int(15) DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `id_cuenta_item` int(11) DEFAULT NULL,
  `id_item` int(15) DEFAULT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_barras` int(11) DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `saldo_cantidad` double(15,2) DEFAULT NULL,
  `precio_venta` double(15,2) DEFAULT NULL COMMENT 'precio de venta si iva',
  `costo_inventario` double(15,2) DEFAULT NULL,
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `valor_impuesto` double(11,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pos` (`id_pos`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=155837 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_inventario_receta
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_inventario_receta`;
CREATE TABLE `ventas_pos_inventario_receta` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_pos` int(15) DEFAULT NULL,
  `id_cuenta` int(11) DEFAULT NULL,
  `id_cuenta_item` int(11) DEFAULT NULL COMMENT 'id de la fila del producto de la cuenta a la que pertenece la receta',
  `id_item_producto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'id del item produco terminado del que pertenece esta receta',
  `id_item` int(15) DEFAULT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double(15,2) DEFAULT '0.00',
  `costo` double(15,2) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_pos` (`id_pos`) USING BTREE,
  KEY `id_item` (`id_item`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=512076 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas`;
CREATE TABLE `ventas_pos_mesas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `estado` varchar(10) COLLATE latin1_general_ci DEFAULT 'Activa' COMMENT 'si esta activa o inactiva',
  `id_seccion` int(11) DEFAULT NULL,
  `seccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas_cuenta
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas_cuenta`;
CREATE TABLE `ventas_pos_mesas_cuenta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `randomico` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_mesa` int(11) DEFAULT NULL,
  `nombre_mesa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'Disponible = cuando la mesa esta libre para uso\r\nOcupado Sin Comandar = cuando la mesa esta en uso , ocupada pero no se realizado mningun pedido\r\nOcupado Comandado = cuando la mesa esta en uso y se hizo un pedido pero no se ha facturado',
  `estado_mesa` varchar(255) COLLATE latin1_general_ci DEFAULT '' COMMENT 'disponible =  es cuando la mesa se libero, por tanto este estado de mesa ya se finalizo\r\nno_disponible = es cuando la mesa esta en uso por tanto no se puede abrir, si no ya usarla con los comensales que se abrieron',
  `color_estado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_apertura` date DEFAULT NULL COMMENT 'Fecha en que se realiza la apertura de la caja',
  `hora_apertura` time DEFAULT NULL COMMENT 'Fecha en que se realiza la apertura de la caja',
  `id_usuario_apertura` int(11) DEFAULT NULL,
  `documento_usuario_apertura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario_apertura` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL COMMENT 'Fecha en que se realiza el cierre de la caja',
  `hora_cierre` time DEFAULT NULL COMMENT 'Hora en que se realiza el cierre de la caja',
  `id_usuario_cierre` int(11) DEFAULT NULL,
  `documento_usuario_cierre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_usuario_cierre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` enum('Cerrada','Abierta') COLLATE latin1_general_ci DEFAULT 'Abierta' COMMENT 'Abierta = la cuenta de la mesa se encuentra abierta\r\nCerrada = la cuenta de la mesa esta cerrada',
  `fecha_auditoria` date DEFAULT NULL,
  `hora_auditoria` time DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=35606 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas_cuenta_comensales
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas_cuenta_comensales`;
CREATE TABLE `ventas_pos_mesas_cuenta_comensales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL COMMENT 'id de la cuenta que se abrio en la mesa',
  `tipo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'tipo de comensal:\r\nHombres\r\nMujeres\r\nNiños\r\nHuesped',
  `cantidad` int(11) DEFAULT NULL COMMENT 'cantidad de comensales de ese tipo',
  `id_reserva` int(11) DEFAULT NULL,
  `numero_reserva` int(11) DEFAULT NULL,
  `numero_habitacion` int(11) DEFAULT NULL,
  `id_comensal` int(11) DEFAULT NULL,
  `documento_comensal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `comensal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'si el comensal es un huesped se llenan estos datos',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=60584 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas_cuenta_items
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas_cuenta_items`;
CREATE TABLE `ventas_pos_mesas_cuenta_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL COMMENT 'id de la cuenta que se abrio en la mesa',
  `id_item` int(11) DEFAULT NULL,
  `codigo_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `cantidad_pendiente` double DEFAULT NULL,
  `termino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `id_impuesto` int(11) DEFAULT NULL,
  `nombre_impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `porcentaje_impuesto` double DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_comanda` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega_produccion` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  `id_comensal` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=89931 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas_cuenta_items_recetas
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas_cuenta_items_recetas`;
CREATE TABLE `ventas_pos_mesas_cuenta_items_recetas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL COMMENT 'id de la cuenta que se abrio en la mesa',
  `id_cuenta_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'id de la tabla ventas_pos_cuenta_items',
  `id_item` int(11) DEFAULT NULL,
  `codigo_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_item` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=463897 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas_estados
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas_estados`;
CREATE TABLE `ventas_pos_mesas_estados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `color` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `estado` varchar(255) COLLATE latin1_general_ci DEFAULT 'disponible' COMMENT 'disponible = Se puede abrir la mesa para usarse\r\nno_disponible = La mesa esta en uso',
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_mesas_traslados
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_mesas_traslados`;
CREATE TABLE `ventas_pos_mesas_traslados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `documento_usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_mesa_origen` int(11) DEFAULT NULL,
  `mesa_origen` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_mesa_destino` int(11) DEFAULT NULL,
  `mesa_destino` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4179 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_secciones
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_secciones`;
CREATE TABLE `ventas_pos_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_padre` int(11) DEFAULT '0',
  `padding` int(11) DEFAULT NULL,
  `restaurante` varchar(10) COLLATE latin1_general_ci DEFAULT 'No' COMMENT 'Si la seccion es un restaurante entonces es Si de lo contrario es No, si es restaurante se le podran crear mesas',
  `id_sucursal` int(11) DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `id_centro_costos` int(11) DEFAULT NULL,
  `codigo_centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_ingreso_colgaap` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cuenta_ingreso_niif` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `eventos_asiste` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_transaccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cambia_precio_items` varchar(10) COLLATE latin1_general_ci DEFAULT 'Si',
  `id_empresa` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(11) DEFAULT '1',
  `cuenta_pago` int(11) DEFAULT NULL,
  `metodo_pago` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ventas_pos_terceros
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_terceros`;
CREATE TABLE `ventas_pos_terceros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `documento_tercero` int(11) DEFAULT NULL,
  `nombre_tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_pos_tope_facturacion
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pos_tope_facturacion`;
CREATE TABLE `ventas_pos_tope_facturacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tope` double DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ventas_recibo_caja_documentos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_recibo_caja_documentos`;
CREATE TABLE `ventas_recibo_caja_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activo` int(1) DEFAULT '1',
  `id_recibo_caja` int(15) DEFAULT NULL,
  `nombre` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `ext` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_remisiones
-- ----------------------------
DROP TABLE IF EXISTS `ventas_remisiones`;
CREATE TABLE `ventas_remisiones` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `random` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `pendientes_facturar` double(15,2) DEFAULT '0.00' COMMENT 'items facturados de la remision',
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `sucursal` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_bodega` int(11) DEFAULT NULL,
  `bodega` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT '0',
  `documento_vendedor` int(11) DEFAULT NULL,
  `nombre_vendedor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT '0',
  `documento_usuario` int(50) DEFAULT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_cliente` int(15) DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_sucursal_cliente` int(11) DEFAULT NULL,
  `sucursal_cliente` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `exento_iva` varchar(5) COLLATE latin1_general_ci DEFAULT '',
  `plantillas_id` int(15) DEFAULT NULL,
  `id_forma_pago` int(15) DEFAULT NULL,
  `referencia` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `observacion` longtext COLLATE latin1_general_ci,
  `estado` int(1) DEFAULT '0' COMMENT '0 -> nuevo, 1 -> guardado, 2 -> ingresado con factura, 3 -> cancelado, 4 -> ingresadas todas las unidades',
  `id_centro_costo` int(11) DEFAULT '0',
  `codigo_centro_costo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `centro_costo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `consecutivo_siip` varchar(30) COLLATE latin1_general_ci DEFAULT '',
  `activo` int(1) DEFAULT '1',
  `json_api` longtext COLLATE latin1_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE,
  KEY `id_sucursal` (`id_sucursal`) USING BTREE,
  KEY `id_bodega` (`id_bodega`) USING BTREE,
  KEY `id_vendedor` (`id_vendedor`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_cliente` (`id_cliente`) USING BTREE,
  KEY `id_sucursal_cliente` (`id_sucursal_cliente`) USING BTREE,
  KEY `plantillas_id` (`plantillas_id`) USING BTREE,
  KEY `id_forma_pago` (`id_forma_pago`) USING BTREE,
  KEY `id_centro_costo` (`id_centro_costo`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=43564 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_remisiones_configuracion
-- ----------------------------
DROP TABLE IF EXISTS `ventas_remisiones_configuracion`;
CREATE TABLE `ventas_remisiones_configuracion` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `valor` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_empresa` (`id_empresa`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for ventas_remisiones_inventario
-- ----------------------------
DROP TABLE IF EXISTS `ventas_remisiones_inventario`;
CREATE TABLE `ventas_remisiones_inventario` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_remision_venta` int(15) DEFAULT NULL,
  `id_inventario` int(15) NOT NULL,
  `codigo` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `id_tabla_inventario_referencia` int(11) DEFAULT NULL,
  `id_consecutivo_referencia` int(15) DEFAULT NULL,
  `consecutivo_referencia` int(15) DEFAULT NULL,
  `nombre_consecutivo_referencia` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `id_unidad_medida` int(11) DEFAULT NULL,
  `nombre_unidad_medida` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `cantidad_unidad_medida` int(11) DEFAULT NULL,
  `nombre` varchar(600) COLLATE latin1_general_ci DEFAULT '',
  `cantidad` double(20,2) DEFAULT '0.00',
  `saldo_cantidad` double(20,2) DEFAULT '0.00',
  `costo_unitario` double(20,2) DEFAULT '0.00',
  `costo_inventario` double(20,2) DEFAULT NULL,
  `tipo_descuento` varchar(20) COLLATE latin1_general_ci DEFAULT '',
  `descuento` double(20,2) DEFAULT '0.00',
  `id_impuesto` int(11) DEFAULT NULL,
  `impuesto` varchar(255) COLLATE latin1_general_ci DEFAULT '',
  `valor_impuesto` double(20,2) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `inventariable` varchar(6) COLLATE latin1_general_ci DEFAULT '',
  `tipo` varchar(10) COLLATE latin1_general_ci DEFAULT '',
  `id_fila_item_receta` int(11) DEFAULT '0' COMMENT 'si este campo tiene un valor quiere decir que el item pertenece a le receta de un item, para este caso el item de la fila con este id',
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `id_remision_vernta` (`id_remision_venta`) USING BTREE,
  KEY `id_inventario` (`id_inventario`) USING BTREE,
  KEY `id_tabla_inventario_referencia` (`id_tabla_inventario_referencia`) USING BTREE,
  KEY `id_consecutivo_referencia` (`id_consecutivo_referencia`) USING BTREE,
  KEY `id_unidad_medida` (`id_unidad_medida`) USING BTREE,
  KEY `id_impuesto` (`id_impuesto`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=424381 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for web_service_log
-- ----------------------------
DROP TABLE IF EXISTS `web_service_log`;
CREATE TABLE `web_service_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `detalle` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `detalle2` int(11) DEFAULT NULL,
  `fecha_service` date DEFAULT NULL,
  `fecha_ejecucion` date DEFAULT NULL,
  `hora_ejecucion` time DEFAULT NULL,
  `metodo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=62337 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for web_service_metodos
-- ----------------------------
DROP TABLE IF EXISTS `web_service_metodos`;
CREATE TABLE `web_service_metodos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `direccion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `metodo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `titulo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `modulo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_software` int(11) DEFAULT NULL,
  `software` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `icono` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `archivo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `configuracion` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `btn_configuracion` varchar(10) COLLATE latin1_general_ci DEFAULT 'no',
  `propiedad` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for web_service_software
-- ----------------------------
DROP TABLE IF EXISTS `web_service_software`;
CREATE TABLE `web_service_software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `software` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `icono` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `carpeta` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for web_service_tercero_causacion
-- ----------------------------
DROP TABLE IF EXISTS `web_service_tercero_causacion`;
CREATE TABLE `web_service_tercero_causacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tercero` int(11) DEFAULT NULL,
  `codigo` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `nit` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `tercero` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `activo` int(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- View structure for vista_saldo_sin_restar_bug_fc
-- ----------------------------
DROP VIEW IF EXISTS `vista_saldo_sin_restar_bug_fc`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER  VIEW `vista_saldo_sin_restar_bug_fc` AS select `AC`.`id` AS `id_asiento`,`CF`.`id` AS `id_factura`,`CF`.`id_empresa` AS `id_empresa`,`CF`.`sucursal` AS `sucursal`,`CF`.`bodega` AS `bodega`,`CF`.`fecha_registro` AS `fecha_registro`,`CF`.`cuenta_pago` AS `cuenta_pago`,`CF`.`prefijo_factura` AS `prefijo_factura`,`CF`.`numero_factura` AS `numero_factura`,`CF`.`consecutivo` AS `consecutivo`,`CF`.`proveedor` AS `proveedor`,`CF`.`total_factura` AS `total_factura`,`CF`.`total_factura_sin_abono` AS `total_factura_sin_abono`,sum(`AC`.`debe`) AS `sum_debe`,`AC`.`numero_documento_cruce` AS `numero_factura_asiento`,`AC`.`consecutivo_documento` AS `consecutivo_CE` from (`compras_facturas` `CF` join `asientos_colgaap` `AC`) where ((`CF`.`id` = `AC`.`id_documento_cruce`) and (`AC`.`tipo_documento_cruce` = 'FC') and ((`AC`.`tipo_documento` = 'CE') or (`AC`.`tipo_documento` = 'NDFC')) and (`CF`.`total_factura_sin_abono` > 0) and (`CF`.`id_cuenta_pago` = `AC`.`id_cuenta`) and (`CF`.`id_empresa` = `AC`.`id_empresa`)) group by `CF`.`id` having (`CF`.`total_factura` < (`CF`.`total_factura_sin_abono` + sum(`AC`.`debe`))) order by `CF`.`consecutivo`; ;

-- ----------------------------
-- View structure for vista_saldo_sin_restar_bug_fv
-- ----------------------------
DROP VIEW IF EXISTS `vista_saldo_sin_restar_bug_fv`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER  VIEW `vista_saldo_sin_restar_bug_fv` AS select `AC`.`id` AS `id_asiento`,`CF`.`id` AS `id_factura`,`CF`.`id_empresa` AS `id_empresa`,`CF`.`sucursal` AS `sucursal`,`CF`.`bodega` AS `bodega`,`CF`.`fecha_contabilizado` AS `fecha_registro`,`CF`.`cuenta_pago` AS `cuenta_pago`,`CF`.`prefijo` AS `prefijo_factura`,`CF`.`numero_factura` AS `numero_factura`,`CF`.`cliente` AS `cliente`,`CF`.`total_factura` AS `total_factura`,`CF`.`total_factura_sin_abono` AS `total_factura_sin_abono`,sum(`AC`.`haber`) AS `sum_haber`,`AC`.`numero_documento_cruce` AS `numero_factura_asiento`,`AC`.`consecutivo_documento` AS `consecutivo_RC` from (`ventas_facturas` `CF` join `asientos_colgaap` `AC`) where ((`CF`.`id` = `AC`.`id_documento_cruce`) and (`AC`.`tipo_documento_cruce` = 'FV') and (`AC`.`tipo_documento` = 'NDFV') and (`CF`.`id_cuenta_pago` = `AC`.`id_cuenta`) and (`CF`.`id_empresa` = `AC`.`id_empresa`)) group by `CF`.`id` having (`CF`.`total_factura` = sum(`AC`.`haber`)) order by `CF`.`numero_factura`; ;

-- ----------------------------
-- View structure for vista_sucursales_empresas
-- ----------------------------
DROP VIEW IF EXISTS `vista_sucursales_empresas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER  VIEW `vista_sucursales_empresas` AS select `empresas_sucursales`.`id` AS `id_sucursal`,`empresas_sucursales`.`nombre` AS `sucursal`,`empresas`.`nombre` AS `empresa`,`empresas`.`id` AS `id_empresa`,`empresas`.`id_pais` AS `id_pais`,`empresas`.`pais` AS `pais`,`empresas`.`id_moneda` AS `id_moneda`,`empresas`.`documento` AS `documento`,`empresas`.`nit_completo` AS `nit_completo`,`empresas`.`simbolo_moneda` AS `simbolo_moneda`,`empresas`.`decimales_moneda` AS `decimales_moneda`,`empresas`.`grupo_empresarial` AS `grupo_empresarial`,`empresas`.`descripcion_moneda` AS `descripcion_moneda` from (`empresas_sucursales` join `empresas` on((`empresas`.`id` = `empresas_sucursales`.`id_empresa`))) where (`empresas_sucursales`.`activo` = 1); ;

-- ----------------------------
-- Procedure structure for InsertLogs
-- ----------------------------
DROP PROCEDURE IF EXISTS `InsertLogs`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertLogs`(`tabla` varchar(255),`campo` varchar(255),`id` int(11),`tipo`varchar(255),`oldData` varchar(255),`newData` varchar(255),`id_usuario`int(11))
BEGIN
		INSERT INTO logs_mysql (tabla,campo,id_registro,tipo,oldData,newData,id_usuario,fecha)VALUES(tabla,campo,id,tipo,oldData,newData,id_usuario,NOW()); 
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for ObtenerActualizarConsecutivoInventario
-- ----------------------------
DROP FUNCTION IF EXISTS `ObtenerActualizarConsecutivoInventario`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `ObtenerActualizarConsecutivoInventario`() RETURNS int(11)
BEGIN
    DECLARE nLast_val INT; 
 
    SET nLast_val =  (SELECT consecutivo FROM inventario_consecutivo);
		UPDATE inventario_consecutivo SET consecutivo = nLast_val + 1;
 
    SET @ret = nLast_val;
    RETURN @ret;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for ObtenerInventarioOut
-- ----------------------------
DROP FUNCTION IF EXISTS `ObtenerInventarioOut`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `ObtenerInventarioOut`(`codigo_equipo` text(20)) RETURNS int(11)
BEGIN
		DECLARE nLast_val INT;
		SET nLast_val =  (SELECT id FROM inventarios_vista WHERE codigo=@codigo_equipo);
 
    SET @ret = nLast_val;
    RETURN @ret;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for Retorna_Minutos
-- ----------------------------
DROP FUNCTION IF EXISTS `Retorna_Minutos`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `Retorna_Minutos`(`horai` time,`horaf` time) RETURNS int(11)
BEGIN
		DECLARE MINUTOS INT(11); 

		SET MINUTOS =  (TIME_TO_SEC(horaf) - TIME_TO_SEC(horai))/60;

		RETURN MINUTOS;
#    '08:00:00','09:00:00
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_INSERT` BEFORE INSERT ON `activos_fijos` FOR EACH ROW BEGIN

SET NEW.costo_sin_depreciar_anual = NEW.costo;
SET NEW.empresa=(SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);

SET NEW.proveedor = (SELECT nombre FROM terceros WHERE id=NEW.id_proveedor);
SET NEW.code_bar = (SELECT code_bar FROM items WHERE id=NEW.id_item);
SET NEW.unidad = (SELECT unidad_medida FROM items WHERE id=NEW.id_item);

SET NEW.fecha_creacion_en_inventario=NOW();

SET NEW.id_cuenta_depreciacion = (SELECT id FROM puc WHERE id_empresa = NEW.id_empresa AND cuenta=NEW.cuenta_depreciacion);
SET NEW.id_contrapartida_depreciacion = (SELECT id FROM puc WHERE id_empresa = NEW.id_empresa AND cuenta=NEW.contrapartida_depreciacion); 

IF ISNULL(NEW.id_centro_costos) OR NEW.id_centro_costos='' THEN
    SET NEW.id_centro_costos = (SELECT id_centro_costos FROM items WHERE id=NEW.id_item);
    SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
    SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costos);
ELSE
    SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
    SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costos);
END IF;

IF NEW.documento_referencia ='FC' THEN
     SET NEW.documento_referencia_consecutivo = (SELECT consecutivo FROM compras_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_documento_referencia);
END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_UPDATE` BEFORE UPDATE ON `activos_fijos` FOR EACH ROW BEGIN

SET NEW.empresa=(SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);
SET NEW.grupo=(SELECT nombre_grupo FROM inventario_grupo WHERE id = NEW.id_grupo);
SET NEW.subgrupo=(SELECT nombre_subgrupo FROM inventario_grupo_subgrupo WHERE id = NEW.id_subgrupo);
SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
SET NEW.centro_costos=(SELECT nombre FROM centro_costos WHERE id = NEW.id_centro_costos);

#SET NEW.unidad = (SELECT unidad_medida FROM items WHERE id=NEW.id_item);

#validamos los datos necesarios para cambiar el estado del activo a 1, es decir para dejarlo totalmente terminado
IF NEW.id_grupo>0 AND NEW.id_subgrupo>0  AND NEW.id_centro_costos>0  AND OLD.estado=0 THEN
  SET NEW.estado=1;
END IF;

SET NEW.id_cuenta_depreciacion = (SELECT id FROM puc WHERE id_empresa = NEW.id_empresa AND cuenta=NEW.cuenta_depreciacion);
SET NEW.id_contrapartida_depreciacion = (SELECT id FROM puc WHERE id_empresa = NEW.id_empresa AND cuenta=NEW.contrapartida_depreciacion);

IF NEW.documento_referencia ='FC' THEN
     SET NEW.documento_referencia_consecutivo = (SELECT consecutivo FROM compras_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_documento_referencia);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_depreciaciones_INSERT`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_depreciaciones_INSERT` BEFORE INSERT ON `activos_fijos_depreciaciones` FOR EACH ROW BEGIN
     SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_depreciaciones_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_depreciaciones_UPDATE` BEFORE UPDATE ON `activos_fijos_depreciaciones` FOR EACH ROW BEGIN

IF NEW.estado=1 THEN
     #GENERAR LA ACTUALIZACION
     SET NEW.fecha_generacion=NOW();
END IF;

IF NEW.sinc_nota = 'colgaap' THEN
    IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
        SET @consecutivo_colgaap=(SELECT consecutivo FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal ORDER BY consecutivo DESC LIMIT 0,1);
        #VALIDAR SI SON NULOS PARA IGUALARLOS A 1 O INCREMENTARLOS EN 1
        IF ISNULL(@consecutivo_colgaap) THEN 
            SET @consecutivo_colgaap=1; 
        ELSE 
            SET @consecutivo_colgaap=@consecutivo_colgaap+1; 
        END IF;
        SET NEW.consecutivo=@consecutivo_colgaap;
    END IF;
ELSE
    IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo_niif<1 OR ISNULL(OLD.consecutivo_niif)) THEN
        SET @consecutivo_niif=(SELECT consecutivo_niif FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal ORDER BY consecutivo_niif DESC LIMIT 0,1);
         IF ISNULL(@consecutivo_niif) THEN
             SET @consecutivo_niif=1; 
         ELSE
              SET @consecutivo_niif=@consecutivo_niif+1;  
         END IF;
              SET NEW.consecutivo_niif=@consecutivo_niif;
    END IF;
END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_depreciaciones_inventario_INSERT`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_depreciaciones_inventario_INSERT` BEFORE INSERT ON `activos_fijos_depreciaciones_inventario` FOR EACH ROW BEGIN
     SET NEW.id_item=(SELECT id_item FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo);
     SET NEW.code_bar=(SELECT code_bar FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.codigo_activo=(SELECT codigo_activo FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.nombre=(SELECT nombre_equipo FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.unidad=(SELECT unidad FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);

     #CUENTAS DEPRECION
     SET NEW.cuenta_depreciacion=(SELECT cuenta_depreciacion FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.contrapartida_depreciacion=(SELECT contrapartida_depreciacion FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.cuenta_depreciacion_niif=(SELECT cuenta_depreciacion_niif FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.contrapartida_depreciacion_niif=(SELECT contrapartida_depreciacion_niif FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);

     SET NEW.id_centro_costos = (SELECT id_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.codigo_centro_costos =  (SELECT codigo_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.centro_costos =  (SELECT centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_depreciaciones_inventario_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_depreciaciones_inventario_UPDATE` BEFORE UPDATE ON `activos_fijos_depreciaciones_inventario` FOR EACH ROW BEGIN     
     SET NEW.id_item=(SELECT id_item FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo);
     SET NEW.code_bar=(SELECT code_bar FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.codigo_activo=(SELECT codigo_activo FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.nombre=(SELECT nombre_equipo FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.unidad=(SELECT unidad FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);

     #CUENTAS DEPRECION
     SET NEW.cuenta_depreciacion=(SELECT cuenta_depreciacion FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.contrapartida_depreciacion=(SELECT contrapartida_depreciacion FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.cuenta_depreciacion_niif=(SELECT cuenta_depreciacion_niif FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.contrapartida_depreciacion_niif=(SELECT contrapartida_depreciacion_niif FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);

     SET NEW.id_centro_costos = (SELECT id_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.codigo_centro_costos =  (SELECT codigo_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.centro_costos =  (SELECT centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_deterioro_INSERT`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_deterioro_INSERT` BEFORE INSERT ON `activos_fijos_deterioro` FOR EACH ROW BEGIN
     SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_deterioro_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_deterioro_UPDATE` BEFORE UPDATE ON `activos_fijos_deterioro` FOR EACH ROW BEGIN

IF NEW.estado=1 THEN
     #GENERAR LA ACTUALIZACION
     SET NEW.fecha_generacion=NOW();
END IF;

    IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
        SET @consecutivo =(SELECT consecutivo FROM activos_fijos_deterioro WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal ORDER BY consecutivo DESC LIMIT 0,1);
        #VALIDAR SI SON NULOS PARA IGUALARLOS A 1 O INCREMENTARLOS EN 1
        IF ISNULL(@consecutivo) THEN 
            SET @consecutivo=1; 
        ELSE 
            SET @consecutivo=@consecutivo+1; 
        END IF;
        SET NEW.consecutivo=@consecutivo;
    END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_depreciaciones_inventario_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_depreciaciones_inventario_INSERT_copy` BEFORE INSERT ON `activos_fijos_deterioro_inventario` FOR EACH ROW BEGIN
     SET NEW.id_item                 =(SELECT id_item FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo);
     SET NEW.codigo                  =(SELECT code_bar FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.nombre                =(SELECT nombre_equipo FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.unidad                  =(SELECT unidad FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     
     #CUENTAS DETERIORO
     SET NEW.cuenta_deterioro             =(SELECT cuenta_deterioro_niif_debito FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.contrapartida_deterioro =(SELECT cuenta_deterioro_niif_credito FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     
     SET NEW.id_centro_costos             = (SELECT id_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.codigo_centro_costos    =  (SELECT codigo_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.centro_costos                  =  (SELECT centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `activos_fijos_depreciaciones_inventario_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `activos_fijos_depreciaciones_inventario_UPDATE_copy` BEFORE UPDATE ON `activos_fijos_deterioro_inventario` FOR EACH ROW BEGIN
     SET NEW.id_item=(SELECT id_item FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo);
     SET NEW.codigo=(SELECT code_bar FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.nombre=(SELECT nombre_equipo FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.unidad=(SELECT unidad FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);

     #CUENTAS DETERIORO
     SET NEW.cuenta_deterioro =(SELECT cuenta_deterioro_niif_debito FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);
     SET NEW.contrapartida_deterioro =(SELECT cuenta_deterioro_niif_credito FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_activo_fijo);

     SET NEW.id_centro_costos = (SELECT id_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.codigo_centro_costos =  (SELECT codigo_centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );
     SET NEW.centro_costos =  (SELECT centro_costos FROM activos_fijos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id=NEW.id_activo_fijo );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_upload_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `terceros_upload_INSERT_copy` BEFORE INSERT ON `activos_fijos_upload` FOR EACH ROW BEGIN

DECLARE cont_upload INT;

SET cont_upload = (SELECT consecutivo FROM activos_fijos_upload WHERE consecutivo > 0 AND id_empresa=NEW.id_empresa ORDER BY consecutivo DESC LIMIT 0,1);

IF cont_upload > 0 THEN SET NEW.consecutivo = cont_upload+1;
ELSE SET NEW.consecutivo=1;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `amotizaciones_INSERT`;
DELIMITER ;;
CREATE TRIGGER `amotizaciones_INSERT` BEFORE INSERT ON `amortizaciones` FOR EACH ROW BEGIN

IF NEW.id_sucursal>0 THEN
     SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal );
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `amortizaciones_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `amortizaciones_UPDATE` BEFORE UPDATE ON `amortizaciones` FOR EACH ROW BEGIN

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
   SET @consecutivo=(SELECT consecutivo FROM amortizaciones WHERE id_empresa=NEW.id_empresa AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
    IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
          SET NEW.consecutivo=1;
    ELSE
          SET NEW.consecutivo=@consecutivo+1;
    END IF;
END IF;

IF NEW.id_sucursal>0 THEN
     SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal );
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_colgaap_INSERT`;
DELIMITER ;;
CREATE TRIGGER `asientos_colgaap_INSERT` BEFORE INSERT ON `asientos_colgaap` FOR EACH ROW BEGIN

SET NEW.id_cuenta=(SELECT id FROM puc WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1); 
SET NEW.cuenta=(SELECT descripcion FROM puc WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);
SET NEW.permiso_sucursal=(SELECT id_sucursal FROM puc WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);

IF ISNULL(NEW.fecha) THEN
    SET NEW.fecha=now();
END IF;

IF NEW.id_tercero>0 THEN
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
    SET NEW.tercero=(SELECT nombre FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
END IF;

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal AND id_empresa=NEW.id_empresa LIMIT 0,1);

#============== TRIGGER SUCURSAL CRUCE ============#
IF (NEW.id_documento<>NEW.id_documento_cruce OR NEW.tipo_documento<>NEW.tipo_documento_cruce) AND NEW.id_documento_cruce > 0 THEN
    SET NEW.id_sucursal_cruce = (SELECT id_sucursal FROM asientos_colgaap WHERE id_empresa=NEW.id_empresa AND id_documento=NEW.id_documento_cruce AND tipo_documento=NEW.tipo_documento_cruce AND codigo_cuenta=NEW.codigo_cuenta LIMIT 0,1); 
    
    IF NEW.id_sucursal_cruce = NEW.id_sucursal THEN SET NEW.sucursal_cruce=NEW.sucursal;
    ELSE SET NEW.sucursal_cruce = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal_cruce AND id_empresa=NEW.id_empresa LIMIT 0,1);
    END IF;

ELSE 
    SET NEW.id_sucursal_cruce = NEW.id_sucursal;
    SET NEW.sucursal_cruce = NEW.sucursal;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_colgaap_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `asientos_colgaap_UPDATE` BEFORE UPDATE ON `asientos_colgaap` FOR EACH ROW BEGIN

SET NEW.id_cuenta=(SELECT id FROM puc WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1); 
SET NEW.cuenta=(SELECT descripcion FROM puc WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);
SET NEW.permiso_sucursal=(SELECT id_sucursal FROM puc WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);

IF ISNULL(NEW.fecha) THEN
    SET NEW.fecha=now();
END IF;

IF NEW.id_tercero>0 THEN
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
    SET NEW.tercero=(SELECT nombre FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
END IF;

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal AND id_empresa=NEW.id_empresa LIMIT 0,1);

#============== TRIGGER SUCURSAL CRUCE ============#
IF (NEW.id_documento<>NEW.id_documento_cruce OR NEW.tipo_documento<>NEW.tipo_documento_cruce) AND NEW.id_documento_cruce > 0 THEN
    SET NEW.id_sucursal_cruce = (SELECT id_sucursal FROM asientos_colgaap WHERE id_empresa=NEW.id_empresa AND id_documento=NEW.id_documento_cruce AND tipo_documento=NEW.tipo_documento_cruce AND codigo_cuenta=NEW.codigo_cuenta LIMIT 0,1); 
    
    IF NEW.id_sucursal_cruce = NEW.id_sucursal THEN SET NEW.sucursal_cruce=NEW.sucursal;
    ELSE SET NEW.sucursal_cruce = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal_cruce AND id_empresa=NEW.id_empresa LIMIT 0,1);
    END IF;

ELSE 
    SET NEW.id_sucursal_cruce = NEW.id_sucursal;
    SET NEW.sucursal_cruce = NEW.sucursal;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_colgaap_default_INSERT`;
DELIMITER ;;
CREATE TRIGGER `asientos_colgaap_default_INSERT` BEFORE INSERT ON `asientos_colgaap_default` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_colgaap_default_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `asientos_colgaap_default_UPDATE` BEFORE UPDATE ON `asientos_colgaap_default` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_colgaap_default_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `asientos_colgaap_default_INSERT_copy` BEFORE INSERT ON `asientos_colgaap_default_grupos` FOR EACH ROW BEGIN

SET NEW.grupo=(SELECT nombre FROM items_familia_grupo WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_grupo);
SET NEW.id_cuenta = (SELECT id FROM puc WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_colgaap_default_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `asientos_colgaap_default_UPDATE_copy` BEFORE UPDATE ON `asientos_colgaap_default_grupos` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.grupo=(SELECT nombre FROM items_familia_grupo WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_grupo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_niif_INSERT`;
DELIMITER ;;
CREATE TRIGGER `asientos_niif_INSERT` BEFORE INSERT ON `asientos_niif` FOR EACH ROW BEGIN

SET NEW.id_cuenta=(SELECT id FROM puc_niif WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1); 
SET NEW.cuenta=(SELECT descripcion FROM puc_niif WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);
SET NEW.permiso_sucursal=(SELECT id_sucursal FROM puc_niif WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);

IF ISNULL(NEW.fecha) THEN
    SET NEW.fecha=now();
END IF;

IF NEW.id_tercero>0 THEN
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
    SET NEW.tercero=(SELECT nombre FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
END IF;

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal AND id_empresa=NEW.id_empresa LIMIT 0,1);

#============== TRIGGER SUCURSAL CRUCE ============#
IF (NEW.id_documento<>NEW.id_documento_cruce OR NEW.tipo_documento<>NEW.tipo_documento_cruce) AND NEW.id_documento_cruce > 0 THEN
    SET NEW.id_sucursal_cruce = (SELECT id_sucursal FROM asientos_colgaap WHERE id_empresa=NEW.id_empresa AND id_documento=NEW.id_documento_cruce AND tipo_documento=NEW.tipo_documento_cruce AND codigo_cuenta=NEW.codigo_cuenta LIMIT 0,1); 
    
    IF NEW.id_sucursal_cruce = NEW.id_sucursal THEN SET NEW.sucursal_cruce=NEW.sucursal;
    ELSE SET NEW.sucursal_cruce = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal_cruce AND id_empresa=NEW.id_empresa LIMIT 0,1);
    END IF;

ELSE 
    SET NEW.id_sucursal_cruce = NEW.id_sucursal;
    SET NEW.sucursal_cruce = NEW.sucursal;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_niif_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `asientos_niif_UPDATE` BEFORE UPDATE ON `asientos_niif` FOR EACH ROW BEGIN

SET NEW.id_cuenta=(SELECT id FROM puc_niif WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1); 
SET NEW.cuenta=(SELECT descripcion FROM puc_niif WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);
SET NEW.permiso_sucursal=(SELECT id_sucursal FROM puc_niif WHERE activo=1 AND cuenta=NEW.codigo_cuenta AND id_empresa=NEW.id_empresa LIMIT 0,1);

IF ISNULL(NEW.fecha) THEN
    SET NEW.fecha=now();
END IF;

IF NEW.id_tercero>0 THEN
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
    SET NEW.tercero=(SELECT nombre FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa );
END IF;

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal AND id_empresa=NEW.id_empresa LIMIT 0,1);

#============== TRIGGER SUCURSAL CRUCE ============#
IF (NEW.id_documento<>NEW.id_documento_cruce OR NEW.tipo_documento<>NEW.tipo_documento_cruce) AND NEW.id_documento_cruce > 0 THEN
    SET NEW.id_sucursal_cruce = (SELECT id_sucursal FROM asientos_colgaap WHERE id_empresa=NEW.id_empresa AND id_documento=NEW.id_documento_cruce AND tipo_documento=NEW.tipo_documento_cruce AND codigo_cuenta=NEW.codigo_cuenta LIMIT 0,1); 
    
    IF NEW.id_sucursal_cruce = NEW.id_sucursal THEN SET NEW.sucursal_cruce=NEW.sucursal;
    ELSE SET NEW.sucursal_cruce = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal_cruce AND id_empresa=NEW.id_empresa LIMIT 0,1);
    END IF;

ELSE 
    SET NEW.id_sucursal_cruce = NEW.id_sucursal;
    SET NEW.sucursal_cruce = NEW.sucursal;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_niif_default_INSERT`;
DELIMITER ;;
CREATE TRIGGER `asientos_niif_default_INSERT` BEFORE INSERT ON `asientos_niif_default` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc_niif WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_niif_default_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `asientos_niif_default_UPDATE` BEFORE UPDATE ON `asientos_niif_default` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc_niif WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_niif_default_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `asientos_niif_default_INSERT_copy` BEFORE INSERT ON `asientos_niif_default_grupos` FOR EACH ROW BEGIN

SET NEW.grupo=(SELECT nombre FROM items_familia_grupo WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_grupo);
SET NEW.id_cuenta = (SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc_niif WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `asientos_niif_default_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `asientos_niif_default_UPDATE_copy` BEFORE UPDATE ON `asientos_niif_default_grupos` FOR EACH ROW BEGIN

SET NEW.grupo=(SELECT nombre FROM items_familia_grupo WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_grupo);
SET NEW.id_cuenta = (SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.detalle_cuenta = (SELECT descripcion FROM puc_niif WHERE  cuenta=NEW.cuenta AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `INSERT_calendario`;
DELIMITER ;;
CREATE TRIGGER `INSERT_calendario` BEFORE INSERT ON `calendario` FOR EACH ROW BEGIN
     SET NEW.icono = (SELECT icono FROM crm_configuracion_actividades WHERE id = NEW.tipo );
     SET NEW.empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id=NEW.id_empleado);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `UPDATE_calendario`;
DELIMITER ;;
CREATE TRIGGER `UPDATE_calendario` BEFORE UPDATE ON `calendario` FOR EACH ROW BEGIN
     SET NEW.icono = (SELECT icono FROM crm_configuracion_actividades WHERE id = NEW.tipo );
     SET NEW.empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id=NEW.id_empleado);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `calendario_notificaciones_personasINSERT`;
DELIMITER ;;
CREATE TRIGGER `calendario_notificaciones_personasINSERT` BEFORE INSERT ON `calendario_notificaciones_personas` FOR EACH ROW BEGIN

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `calendario_personasINSERT`;
DELIMITER ;;
CREATE TRIGGER `calendario_personasINSERT` BEFORE INSERT ON `calendario_personas` FOR EACH ROW BEGIN

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `centro_costos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `centro_costos_UPDATE` BEFORE UPDATE ON `centro_costos` FOR EACH ROW BEGIN

SET NEW.campo1 = LEFT(NEW.codigo,2);
SET NEW.campo2 = RIGHT(LEFT(NEW.codigo,4),2);
SET NEW.campo3 = RIGHT(LEFT(NEW.codigo,6),2);
SET NEW.campo4 = RIGHT(LEFT(NEW.codigo,8),2);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_base_liquidacion_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_base_liquidacion_INSERT_copy` BEFORE INSERT ON `certificado_ingreso_retenciones_empleados_conceptos` FOR EACH ROW BEGIN 

    SET NEW.codigo_concepto=(SELECT  codigo FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.concepto=(SELECT  descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.naturaleza=(SELECT  naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_base_liquidacion_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_base_liquidacion_UPDATE_copy` BEFORE UPDATE ON `certificado_ingreso_retenciones_empleados_conceptos` FOR EACH ROW BEGIN 

    SET NEW.codigo_concepto=(SELECT  codigo FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.concepto=(SELECT  descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.naturaleza=(SELECT  naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `cierre_por_periodo_INSERT`;
DELIMITER ;;
CREATE TRIGGER `cierre_por_periodo_INSERT` BEFORE INSERT ON `cierre_por_periodo` FOR EACH ROW BEGIN

# IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
          SET @consecutivo=(SELECT consecutivo FROM cierre_por_periodo WHERE id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
          IF ISNULL(@consecutivo) THEN
               SET NEW.consecutivo=1;
           ELSE   
               SET NEW.consecutivo=@consecutivo+1;
          END IF;
#END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `cierre_por_periodo_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `cierre_por_periodo_UPDATE` BEFORE UPDATE ON `cierre_por_periodo` FOR EACH ROW BEGIN

IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
          SET @consecutivo=(SELECT consecutivo FROM cierre_por_periodo WHERE id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
          IF ISNULL(@consecutivo) THEN
               SET NEW.consecutivo=1;
           ELSE   
               SET NEW.consecutivo=@consecutivo+1;
          END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_entrada_almacen_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_entrada_almacen_INSERT` BEFORE INSERT ON `compras_entrada_almacen` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

#SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_entrada_almacen_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_entrada_almacen_UPDATE` BEFORE UPDATE ON `compras_entrada_almacen` FOR EACH ROW BEGIN

SET NEW.proveedor = (SELECT nombre FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET NEW.cod_proveedor =(SELECT codigo FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);

#CONSECUTIVO DOCUMENTO
IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
     SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='entrada_de_almacen' AND modulo='compra' LIMIT 0,1);
     
     IF NEW.consecutivo > 0 THEN
          UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo+1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='entrada_de_almacen' AND modulo='compra';
      ELSE 
           SET NEW.consecutivo=1;
           INSERT INTO configuracion_consecutivos_documentos (consecutivo,id_empresa,id_sucursal,documento,modulo) VALUES(NEW.consecutivo+1,NEW.id_empresa,NEW.id_sucursal,'entrada_de_almacen','compra');
      END IF;

END IF;
#SET NEW.observacion=CONCAT('SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=',NEW.id_empresa,' AND id_sucursal=',NEW.id_sucursal,' AND activo=1 AND documento="entrada_de_almacen" AND modulo="compra" LIMIT 0,1');
#ACTUALIZA CANTIDAD EN INVENTARIO
SET NEW.pendientes_facturar = (SELECT SUM(saldo_cantidad) FROM compras_entrada_almacen_inventario WHERE activo=1 AND id_entrada_almacen = NEW.id);

SET NEW.codigo_centro_costo = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.centro_costo = (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_entrada_almacen_inventario_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_entrada_almacen_inventario_INSERT` BEFORE INSERT ON `compras_entrada_almacen_inventario` FOR EACH ROW BEGIN

    DECLARE id_empresa INTEGER;
    DECLARE id_sucursal INTEGER;
    DECLARE id_bodega INTEGER;

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    #IF ISNULL(NEW.valor_impuesto) THEN
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    #END IF;
        
    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET id_empresa = (SELECT id_empresa FROM ventas_remisiones WHERE id=NEW.id_entrada_almacen);
    SET id_sucursal = (SELECT id_sucursal FROM ventas_remisiones WHERE id=NEW.id_entrada_almacen);
    SET id_bodega = (SELECT id_bodega FROM ventas_remisiones WHERE id=NEW.id_entrada_almacen);

    SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=id_empresa AND id_sucursal=id_sucursal AND id_ubicacion=id_bodega AND activo=1 GROUP BY id LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_entrada_almacen_inventario_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_entrada_almacen_inventario_UPDATE` BEFORE UPDATE ON `compras_entrada_almacen_inventario` FOR EACH ROW BEGIN

    DECLARE id_empresa_db INTEGER;
    DECLARE id_sucursal_db INTEGER;
    DECLARE id_bodega_db INTEGER;

  IF NEW.id_inventario <>OLD.id_inventario THEN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    

    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET id_empresa_db = (SELECT id_empresa FROM ventas_remisiones WHERE id=NEW.id_entrada_almacen);
    SET id_sucursal_db = (SELECT id_sucursal FROM ventas_remisiones WHERE id=NEW.id_entrada_almacen);
    SET id_bodega_db = (SELECT id_bodega FROM ventas_remisiones WHERE id=NEW.id_entrada_almacen);

    SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=id_empresa_db AND id_sucursal=id_sucursal_db AND id_ubicacion=id_bodega_db AND activo=1 GROUP BY id LIMIT 0,1);
  
  END IF;

IF NEW.id_impuesto <> OLD.id_impuesto THEN
         #SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
         SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturasINSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_facturasINSERT` BEFORE INSERT ON `compras_facturas` FOR EACH ROW BEGIN

SET NEW.cod_proveedor= (SELECT codigo FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET NEW.proveedor = (SELECT nombre FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);

SET NEW.fecha_registro =now() - INTERVAL 5 HOUR;
SET NEW.fecha_inicio =now() - INTERVAL 5 HOUR;

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.fecha_pago=(DATE_ADD(NEW.fecha_inicio, INTERVAL  NEW.dias_pago DAY));

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturasUPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_facturasUPDATE` BEFORE UPDATE ON `compras_facturas` FOR EACH ROW BEGIN

	SET NEW.cod_proveedor= (SELECT codigo FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
	SET NEW.proveedor = (SELECT nombre FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
	SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
	SET NEW.usuario_recibe_en_almacen=(SELECT nombre FROM empleados WHERE id=NEW.id_usuario_recibe_en_almacen);

	SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
	SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

	IF NEW.id_forma_pago <> OLD.id_forma_pago THEN
		SET NEW.forma_pago=(SELECT nombre FROM configuracion_formas_pago WHERE id=NEW.id_forma_pago );
		SET  NEW.dias_pago = (SELECT plazo FROM configuracion_formas_pago WHERE id=NEW.id_forma_pago AND activo=1 LIMIT 0,1);
	END IF;

	SET NEW.configuracion_cuenta_pago = (SELECT nombre FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta_pago);
	IF ISNULL(NEW.plantillas_id) OR NEW.plantillas_id=0 THEN
		SET NEW.cuenta_pago = (SELECT cuenta FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta_pago);
		SET NEW.cuenta_pago_niif = (SELECT cuenta_niif FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta_pago);
	END IF;
	
	SET NEW.id_cuenta_pago = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_pago LIMIT 0,1);

	IF  NEW.dias_pago >= 0 THEN
		SET NEW.fecha_pago=(DATE_ADD(NEW.fecha_inicio, INTERVAL  NEW.dias_pago DAY));
	END IF;

	#ASIGNACION DE CONSECUTIVOS
	IF OLD.estado = 0 AND NEW.estado = 1 AND NEW.id_saldo_inicial=0 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
		SET NEW.fecha_generacion=now() - INTERVAL 5 HOUR;

		IF OLD.tipo_documento = "05" THEN
			SET NEW.consecutivo=(SELECT consecutivo FROM resolucion_documento_soporte WHERE id=OLD.id_resolucion);
			SET NEW.prefijo_factura=(SELECT prefijo FROM resolucion_documento_soporte WHERE id=OLD.id_resolucion);
			UPDATE resolucion_documento_soporte SET consecutivo =  NEW.consecutivo + 1 WHERE id=OLD.id_resolucion;
		ELSE
			SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='factura' AND modulo='compra' LIMIT 0,1);
			UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='factura' AND modulo='compra';
    END IF;
   #SI ES UNA FACTURA DE COMPRA POR CUENTAS, Y NO LE ENVIA EL NUMERO DE LA FACTURA, QUE LE ASIGNE EL CONSECUTIVO COMO NUMERO DE FACTURA
		IF OLD.factura_por_cuentas = 'true' AND NEW.numero_factura='' THEN
			SET NEW.numero_factura=NEW.consecutivo;
		END IF;

	END IF;

	IF OLD.estado = 0 AND NEW.estado = 1 THEN
		SET NEW.hora_generacion=NOW() - INTERVAL 5 HOUR;
	END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_archivos_adjuntos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_archivos_adjuntos_INSERT` BEFORE INSERT ON `compras_facturas_archivos_adjuntos` FOR EACH ROW BEGIN

     SET NEW.fecha_creacion=NOW();
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario );
     SET NEW.documento_tercero = (SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tercero);
     SET NEW.nombre_tercero = (SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tercero);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `contabilidad_manual_INSERT`;
DELIMITER ;;
CREATE TRIGGER `contabilidad_manual_INSERT` BEFORE INSERT ON `compras_facturas_contabilidad_manual` FOR EACH ROW BEGIN

SET NEW.codigo_centro_costos= (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
SET NEW.nombre_centro_costos= (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costos);

#CUENTAS COLGAAP Y NIIF DEL SUBTOTAL
SET NEW.cuenta_subtotal=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_subtotal);
SET NEW.descripcion_cuenta_subtotal=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_subtotal );
SET NEW.cuenta_niif_subtotal=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_subtotal);
SET NEW.descripcion_niif_subtotal=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_subtotal );

#CUENTAS COLGAAP Y NIIF DEL IVA
SET NEW.cuenta_iva=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_iva);
SET NEW.descripcion_cuenta_iva=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_iva );
SET NEW.cuenta_niif_iva=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_iva);
SET NEW.descripcion_niif_iva=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_iva );

#CUENTAS COLGAAP Y NIIF DEL TOTAL
SET NEW.cuenta_total=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_total);
SET NEW.descripcion_cuenta_total=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_total );
SET NEW.cuenta_niif_total=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_total);
SET NEW.descripcion_niif_total=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_total );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `contabilidad_manual_update`;
DELIMITER ;;
CREATE TRIGGER `contabilidad_manual_update` BEFORE UPDATE ON `compras_facturas_contabilidad_manual` FOR EACH ROW BEGIN

IF OLD.id_centro_costos <> NEW.id_centro_costos THEN
      SET NEW.codigo_centro_costos= (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
      SET NEW.nombre_centro_costos= (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costos);
END IF;

#CUENTAS COLGAAP Y NIIF DEL SUBTOTAL
IF OLD.id_cuenta_subtotal <> NEW.id_cuenta_subtotal THEN
     SET NEW.cuenta_subtotal=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_subtotal);
     SET NEW.descripcion_cuenta_subtotal=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_subtotal );
END IF;

IF OLD.id_cuenta_niif_subtotal <> NEW.id_cuenta_niif_subtotal THEN
     SET NEW.cuenta_niif_subtotal=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_subtotal);
     SET NEW.descripcion_niif_subtotal=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_subtotal );
END IF;

#CUENTAS COLGAAP Y NIIF DEL IVA
IF OLD.id_cuenta_iva <> NEW.id_cuenta_iva THEN
    SET NEW.cuenta_iva=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_iva);
    SET NEW.descripcion_cuenta_iva=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_iva );
END IF;

IF OLD.id_cuenta_niif_iva <> NEW.id_cuenta_niif_iva THEN
     SET NEW.cuenta_niif_iva=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_iva);
     SET NEW.descripcion_niif_iva=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_iva );
END IF;

#CUENTAS COLGAAP Y NIIF DEL TOTAL
IF OLD.id_cuenta_total <> NEW.id_cuenta_total THEN
    SET NEW.cuenta_total=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_total);
    SET NEW.descripcion_cuenta_total=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_total );
END IF;

IF OLD.id_cuenta_niif_total <> NEW.id_cuenta_niif_total THEN
     SET NEW.cuenta_niif_total=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_total);
     SET NEW.descripcion_niif_total=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_total );
END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_cuentas_INSERT` BEFORE INSERT ON `compras_facturas_cuentas` FOR EACH ROW BEGIN

     SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.tercero=(SELECT nombre FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

    SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_cuentas_UPDATE` BEFORE UPDATE ON `compras_facturas_cuentas` FOR EACH ROW BEGIN

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    
     IF NEW.id_puc<>OLD.id_puc THEN
        SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    ELSE
        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
   END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_inventarioINSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_inventarioINSERT` BEFORE INSERT ON `compras_facturas_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    IF NEW.id_impuesto='' OR ISNULL(NEW.id_impuesto) THEN
        SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;
    
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.cuenta_impuesto = (SELECT cuenta_compra FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.cuenta_impuesto_niif = (SELECT cuenta_compra_niif FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    IF    NEW.id_centro_costos='' THEN
         SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_empresa= (SELECT id_empresa FROM compras_facturas WHERE id=NEW.id_factura_compra LIMIT 0,1);
    SET NEW.id_sucursal= (SELECT id_sucursal FROM compras_facturas WHERE id=NEW.id_factura_compra LIMIT 0,1);
    SET NEW.id_bodega= (SELECT id_bodega FROM compras_facturas WHERE id=NEW.id_factura_compra LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.saldo_cantidad=NEW.cantidad;

    SET NEW.opcion_gasto = (SELECT opcion_gasto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.opcion_costo = (SELECT opcion_costo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.opcion_activo_fijo = (SELECT opcion_activo_fijo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_inventarioUPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_inventarioUPDATE` BEFORE UPDATE ON `compras_facturas_inventario` FOR EACH ROW BEGIN

    SET @estado=(SELECT estado FROM compras_facturas WHERE id=NEW.id_factura_compra);
    IF @estado<1 THEN
       SET NEW.saldo_cantidad=NEW.cantidad;
    END IF;

    IF NEW.id_inventario <>OLD.id_inventario THEN
         SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

         SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
         SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1); 
         SET NEW.cuenta_impuesto = (SELECT cuenta_compra FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
         SET NEW.cuenta_impuesto_niif = (SELECT cuenta_compra_niif FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    
         SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
         SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    
         SET NEW.opcion_gasto = (SELECT opcion_gasto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.opcion_costo = (SELECT opcion_costo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.opcion_activo_fijo = (SELECT opcion_activo_fijo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

         SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_inventarioINSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_inventarioINSERT_copy` BEFORE INSERT ON `compras_facturas_inventario_archivos_adjuntos` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    IF NEW.id_impuesto='' OR ISNULL(NEW.id_impuesto) THEN
        SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;
    
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.cuenta_impuesto = (SELECT cuenta_compra FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.cuenta_impuesto_niif = (SELECT cuenta_compra_niif FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    IF    NEW.id_centro_costos='' THEN
         SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_empresa= (SELECT id_empresa FROM compras_facturas WHERE id=NEW.id_factura_compra LIMIT 0,1);
    SET NEW.id_sucursal= (SELECT id_sucursal FROM compras_facturas WHERE id=NEW.id_factura_compra LIMIT 0,1);
    SET NEW.id_bodega= (SELECT id_bodega FROM compras_facturas WHERE id=NEW.id_factura_compra LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.saldo_cantidad=NEW.cantidad;

    SET NEW.opcion_gasto = (SELECT opcion_gasto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.opcion_costo = (SELECT opcion_costo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.opcion_activo_fijo = (SELECT opcion_activo_fijo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_inventarioUPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_inventarioUPDATE_copy` BEFORE UPDATE ON `compras_facturas_inventario_archivos_adjuntos` FOR EACH ROW BEGIN

    SET @estado=(SELECT estado FROM compras_facturas WHERE id=NEW.id_factura_compra);
    IF @estado<1 THEN
       SET NEW.saldo_cantidad=NEW.cantidad;
    END IF;

    IF NEW.id_inventario <>OLD.id_inventario THEN
         SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

         SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
         SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1); 
         SET NEW.cuenta_impuesto = (SELECT cuenta_compra FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
         SET NEW.cuenta_impuesto_niif = (SELECT cuenta_compra_niif FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    
         SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
         SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    
         SET NEW.opcion_gasto = (SELECT opcion_gasto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.opcion_costo = (SELECT opcion_costo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
         SET NEW.opcion_activo_fijo = (SELECT opcion_activo_fijo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

         SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_facturas_retenciones_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_facturas_retenciones_INSERT` BEFORE INSERT ON `compras_facturas_retenciones` FOR EACH ROW BEGIN

SET NEW.tipo_retencion  = (SELECT tipo_retencion FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.retencion  = (SELECT retencion FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.valor          = (SELECT valor FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.base          = (SELECT base FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.codigo_cuenta  = (SELECT cuenta FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.codigo_cuenta_niif  = (SELECT cuenta_niif FROM retenciones WHERE id=NEW.id_retencion);

SET NEW.cuenta_autoretencion  = (SELECT cuenta_autoretencion FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.cuenta_autoretencion_niif  = (SELECT cuenta_autoretencion_niif FROM retenciones WHERE id=NEW.id_retencion);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_ordenesINSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_ordenesINSERT` BEFORE INSERT ON `compras_ordenes` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_ordenesUPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_ordenesUPDATE` BEFORE UPDATE ON `compras_ordenes` FOR EACH ROW BEGIN

SET new.proveedor = (SELECT nombre FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET new.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET new.cod_proveedor = (SELECT codigo FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);


#IF NEW.id_forma_pago <> OLD.id_forma_pago THEN
     SET NEW.forma_pago=(SELECT nombre FROM configuracion_formas_pago WHERE id=NEW.id_forma_pago );
     #SET  NEW.dias_pago = (SELECT plazo FROM configuracion_formas_pago WHERE id=NEW.id_forma_pago AND activo=1 LIMIT 0,1);
#END IF;

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='orden_de_compra' AND modulo='compra' LIMIT 0,1);
    UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='orden_de_compra' AND modulo='compra';
END IF;

#ACTUALIZA CANTIDAD EN QUE ESTA PENDIENTE POR FACTURAR
SET NEW.pendientes_facturar = (SELECT SUM(saldo_cantidad) FROM compras_ordenes_inventario WHERE activo=1 AND id_orden_compra=NEW.id);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_ordenes_invertario_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_ordenes_invertario_INSERT` BEFORE INSERT ON `compras_ordenes_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    IF ISNULL(NEW.id_centro_costos) OR NEW.id_centro_costos='' THEN
        SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_ordenes_invertario_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_ordenes_invertario_UPDATE` BEFORE UPDATE ON `compras_ordenes_inventario` FOR EACH ROW BEGIN

    IF NEW.id_inventario <>OLD.id_inventario THEN
                  SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
	SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
	SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
	SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
	SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

	SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
	SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
	SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

                   SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_requisicion_INSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_requisicion_INSERT` BEFORE INSERT ON `compras_requisicion` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.codigo_centro_costo = (SELECT codigo FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costo);
SET NEW.centro_costo = (SELECT nombre  FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costo);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_requisicion_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_requisicion_UPDATE` BEFORE UPDATE ON `compras_requisicion` FOR EACH ROW BEGIN

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
     SET @consecutivo=(SELECT consecutivo FROM compras_requisicion WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;

SET NEW.codigo_centro_costo = (SELECT codigo FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costo);
SET NEW.centro_costo = (SELECT nombre  FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costo);

#ACTUALIZA CANTIDAD EN QUE ESTA PENDIENTE POR FACTURAR
SET NEW.pendientes_facturar = (SELECT SUM(saldo_cantidad) FROM compras_requisicion_inventario WHERE activo=1 AND id_requisicion_compra=NEW.id);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_requisiciones_invertarioINSERT`;
DELIMITER ;;
CREATE TRIGGER `compras_requisiciones_invertarioINSERT` BEFORE INSERT ON `compras_requisicion_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `compras_requisiciones_invertarioUPDATE`;
DELIMITER ;;
CREATE TRIGGER `compras_requisiciones_invertarioUPDATE` BEFORE UPDATE ON `compras_requisicion_inventario` FOR EACH ROW BEGIN

    IF NEW.id_inventario <>OLD.id_inventario THEN

        SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

        SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
        SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
        
        SET NEW.id_centro_costos=(SELECT id_centro_costos FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `comprobante_egreso_INSERT`;
DELIMITER ;;
CREATE TRIGGER `comprobante_egreso_INSERT` BEFORE INSERT ON `comprobante_egreso` FOR EACH ROW BEGIN

SET NEW.fecha_inicial=NOW();
SET NEW.fecha_comprobante=NOW();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `comprobante_egreso_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `comprobante_egreso_UPDATE` BEFORE UPDATE ON `comprobante_egreso` FOR EACH ROW BEGIN

SET NEW.configuracion_cuenta = (SELECT nombre FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.cuenta = (SELECT cuenta FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.cuenta_niif=(SELECT cuenta_niif FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.descripcion_cuenta= (SELECT nombre_cuenta FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);

#CONSECUTIVO DOCUMENTO
     IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
           SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='comprobante_de_egreso' AND modulo='compra' LIMIT 0,1);
           UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='comprobante_de_egreso' AND modulo='compra';
     END IF;

IF NEW.debug=2 THEN
     SET NEW.id_tercero = (SELECT id FROM terceros WHERE id_empresa=NEW.id_empresa AND activo=1 AND numero_identificacion=NEW.nit_tercero AND codigo=NEW.codigo_tercero);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `comprobante_egreso_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `comprobante_egreso_cuentas_INSERT` BEFORE INSERT ON `comprobante_egreso_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta=(SELECT cuenta FROM puc WHERE id=NEW.id_puc);
SET NEW.descripcion=(SELECT descripcion FROM puc WHERE id=NEW.id_puc);
SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc);

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero);
SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero);
SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `comprobante_egreso_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `comprobante_egreso_cuentas_UPDATE` BEFORE UPDATE ON `comprobante_egreso_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta=(SELECT cuenta FROM puc WHERE id=NEW.id_puc);
SET NEW.descripcion=(SELECT descripcion FROM puc WHERE id=NEW.id_puc);
SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc);

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero);
SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero);
SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `conciliaciones_extractos_copy`;
DELIMITER ;;
CREATE TRIGGER `conciliaciones_extractos_copy` BEFORE UPDATE ON `conciliaciones` FOR EACH ROW IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
   SET @consecutivo=(SELECT consecutivo FROM extractos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
    IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
          SET NEW.consecutivo=1;
    ELSE
          SET NEW.consecutivo=@consecutivo+1;
    END IF;
END IF
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_cuentas_pago_INSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_cuentas_pago_INSERT` BEFORE INSERT ON `configuracion_cuentas_pago` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.id_sucursal = (SELECT id_sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.sucursal = (SELECT sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);

IF(NEW.tipo='Compra' AND NEW.estado='Credito') THEN SET NEW.tipo_tercero='true'; END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_cuentas_pago_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_cuentas_pago_UPDATE` BEFORE UPDATE ON `configuracion_cuentas_pago` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.id_sucursal = (SELECT id_sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.sucursal = (SELECT sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);

IF(NEW.tipo='Compra' AND NEW.estado='Credito') THEN SET NEW.tipo_tercero='true'; END IF;

SET NEW.tercero = (SELECT nombre FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);
SET NEW.nit_tercero = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_cuentas_pago_pos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_cuentas_pago_pos_INSERT` BEFORE INSERT ON `configuracion_cuentas_pago_pos` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.id_sucursal = (SELECT id_sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.sucursal = (SELECT sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);

IF(NEW.tipo='Compra' AND NEW.estado='Credito') THEN SET NEW.tipo_tercero='true'; END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_cuentas_pago_pos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_cuentas_pago_pos_UPDATE` BEFORE UPDATE ON `configuracion_cuentas_pago_pos` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.id_sucursal = (SELECT id_sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.sucursal = (SELECT sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);

IF(NEW.tipo='Compra' AND NEW.estado='Credito') THEN SET NEW.tipo_tercero='true'; END IF;

SET NEW.tercero = (SELECT nombre FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);
SET NEW.nit_tercero = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_documentos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_documentos_INSERT` BEFORE INSERT ON `configuracion_documentos` FOR EACH ROW BEGIN

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.tipo = (SELECT nombre FROM configuracion_documentos_tipo WHERE id=NEW.id_tipo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_documentos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_documentos_UPDATE` BEFORE UPDATE ON `configuracion_documentos` FOR EACH ROW BEGIN

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.tipo = (SELECT nombre FROM configuracion_documentos_tipo WHERE id=NEW.id_tipo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_documentos_erp_INSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_documentos_erp_INSERT` BEFORE INSERT ON `configuracion_documentos_erp` FOR EACH ROW BEGIN

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

IF NEW.texto IS NULL THEN
    SET NEW.texto = (SELECT texto FROM configuracion_documentos_erp WHERE id_empresa=NEW.id_empresa AND tipo=NEW.tipo ORDER BY id DESC LIMIT 0,1); 
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_documentos_erp_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_documentos_erp_UPDATE` BEFORE UPDATE ON `configuracion_documentos_erp` FOR EACH ROW BEGIN

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_festivosINSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_festivosINSERT` BEFORE INSERT ON `configuracion_festivos` FOR EACH ROW BEGIN
SET NEW.pais = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_horas_extrasINSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_horas_extrasINSERT` BEFORE INSERT ON `configuracion_horas_extras` FOR EACH ROW BEGIN
SET NEW.pais = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_horas_extrasUPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_horas_extrasUPDATE` BEFORE UPDATE ON `configuracion_horas_extras` FOR EACH ROW BEGIN
SET NEW.pais = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_informe_estado_flujo_efectivo_INSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_informe_estado_flujo_efectivo_INSERT` BEFORE INSERT ON `configuracion_informe_estado_flujo_efectivo` FOR EACH ROW BEGIN

IF NEW.contabilidad='niif' THEN
    SET NEW.cuenta=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
    SET NEW.descripcion = (SELECT descripcion FROM puc_niif WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
ELSE
    SET NEW.cuenta=(SELECT cuenta FROM puc WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
    SET NEW.descripcion = (SELECT descripcion FROM puc WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_informe_estado_flujo_efectivo_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_informe_estado_flujo_efectivo_UPDATE` BEFORE UPDATE ON `configuracion_informe_estado_flujo_efectivo` FOR EACH ROW BEGIN

IF NEW.contabilidad='niif' THEN
    SET NEW.cuenta=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
    SET NEW.descripcion = (SELECT descripcion FROM puc_niif WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
ELSE
    SET NEW.cuenta=(SELECT cuenta FROM puc WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
    SET NEW.descripcion = (SELECT descripcion FROM puc WHERE id=NEW.id_cuenta AND id_empresa=NEW.id_empresa AND activo=1);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_informe_estado_resultado_niif_INSERT`;
DELIMITER ;;
CREATE TRIGGER `configuracion_informe_estado_resultado_niif_INSERT` BEFORE INSERT ON `configuracion_informe_estado_resultado_niif` FOR EACH ROW BEGIN
SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_puc_niif AND id_empresa=NEW.id_empresa AND activo=1);
SET NEW.descripcion_cuenta_niif = (SELECT descripcion FROM puc_niif WHERE id=NEW.id_puc_niif AND id_empresa=NEW.id_empresa AND activo=1);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_informe_estado_resultado_niif_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `configuracion_informe_estado_resultado_niif_UPDATE` BEFORE UPDATE ON `configuracion_informe_estado_resultado_niif` FOR EACH ROW BEGIN
SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_puc_niif AND id_empresa=NEW.id_empresa AND activo=1);
SET NEW.descripcion_cuenta_niif = (SELECT descripcion FROM puc_niif WHERE id=NEW.id_puc_niif AND id_empresa=NEW.id_empresa AND activo=1);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `propinas_pos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `propinas_pos_INSERT` BEFORE INSERT ON `configuracion_propinas_pos` FOR EACH ROW BEGIN
SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `propinas_pos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `propinas_pos_UPDATE` BEFORE UPDATE ON `configuracion_propinas_pos` FOR EACH ROW BEGIN
SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ZonasInsert`;
DELIMITER ;;
CREATE TRIGGER `ZonasInsert` BEFORE INSERT ON `configuracion_zonas` FOR EACH ROW BEGIN

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ZonasUpdate`;
DELIMITER ;;
CREATE TRIGGER `ZonasUpdate` BEFORE UPDATE ON `configuracion_zonas` FOR EACH ROW BEGIN

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `costo_cuentas_transito_INSERT`;
DELIMITER ;;
CREATE TRIGGER `costo_cuentas_transito_INSERT` BEFORE INSERT ON `costo_cuentas_transito` FOR EACH ROW BEGIN

IF NEW.id_cuenta_colgaap_debito>0 THEN
    SET NEW.cuenta_colgaap_debito=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_debito);
    SET NEW.descripcion_cuenta_colgaap_debito=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_debito);
END IF;
IF NEW.id_cuenta_colgaap_credito>0 THEN
    SET NEW.cuenta_colgaap_credito=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_credito);
    SET NEW.descripcion_cuenta_colgaap_credito=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_credito);
END IF;

IF NEW.id_cuenta_niif_debito>0 THEN
    SET NEW.cuenta_niif_debito=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_debito);
    SET NEW.descripcion_cuenta_niif_debito=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_debito);
END IF;
IF NEW.id_cuenta_niif_credito>0 THEN
    SET NEW.cuenta_niif_credito=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_credito);
    SET NEW.descripcion_cuenta_niif_credito=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_credito);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `costo_cuentas_transito_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `costo_cuentas_transito_UPDATE` BEFORE UPDATE ON `costo_cuentas_transito` FOR EACH ROW BEGIN

IF NEW.id_cuenta_colgaap_debito>0 THEN
    SET NEW.cuenta_colgaap_debito=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_debito);
    SET NEW.descripcion_cuenta_colgaap_debito=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_debito);
END IF;
IF NEW.id_cuenta_colgaap_credito>0 THEN
    SET NEW.cuenta_colgaap_credito=(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_credito);
    SET NEW.descripcion_cuenta_colgaap_credito=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_credito);
END IF;

IF NEW.id_cuenta_niif_debito>0 THEN
    SET NEW.cuenta_niif_debito=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_debito);
    SET NEW.descripcion_cuenta_niif_debito=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_debito);
END IF;
IF NEW.id_cuenta_niif_credito>0 THEN
    SET NEW.cuenta_niif_credito=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_credito);
    SET NEW.descripcion_cuenta_niif_credito=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_credito);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `costo_documento_porcentajeINSERT`;
DELIMITER ;;
CREATE TRIGGER `costo_documento_porcentajeINSERT` BEFORE INSERT ON `costo_documento_porcentaje` FOR EACH ROW BEGIN

SET NEW.costo_tipo =(SELECT nombre FROM costo_tipo WHERE id=NEW.id_costo_tipo);
SET NEW.codigo_centro_costo =(SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
SET NEW.centro_costos =(SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costos);
SET NEW.cuenta_colgaap =(SELECT cuenta FROM puc WHERE id=NEW.id_cuenta_colgaap);
SET NEW.cuenta_niif =(SELECT cuenta FROM puc_niif WHERE id=NEW.id_cuenta_niif);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `costo_documento_porcentajeUPDATE`;
DELIMITER ;;
CREATE TRIGGER `costo_documento_porcentajeUPDATE` BEFORE UPDATE ON `costo_documento_porcentaje` FOR EACH ROW BEGIN

SET NEW.codigo_centro_costo =(SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos);
SET NEW.centro_costos =(SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costos);
SET NEW.cuenta_colgaap =(SELECT cuenta FROM puc WHERE id=NEW.id_cuenta_colgaap);
SET NEW.cuenta_niif =(SELECT cuenta FROM puc_niif WHERE id=NEW.id_cuenta_niif);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `INSERT_crm_configuracion_actividades`;
DELIMITER ;;
CREATE TRIGGER `INSERT_crm_configuracion_actividades` BEFORE INSERT ON `crm_configuracion_actividades` FOR EACH ROW BEGIN

IF(NEW.fecha_completa='false') THEN SET NEW.fecha_vencimiento = 'true'; END IF ;
IF(NEW.fecha_completa='true') THEN SET NEW.fecha_vencimiento = 'false'; END IF ;

IF NEW.id_departamento = 1 THEN
     SET NEW.departamento = "Departamento Comercial";
ELSEIF NEW.id_departamento = 2 THEN
     SET NEW.departamento = "Departamento de Operaciones";
ELSEIF NEW.id_departamento = 3 THEN
     SET NEW.departamento = "Departamento de Calidad";
ELSEIF NEW.id_departamento = 4 THEN
     SET NEW.departamento = "Departamento Financiero";
ELSEIF NEW.id_departamento =5 THEN
     SET NEW.departamento = "SubDireccion";
ELSEIF NEW.id_departamento =6 THEN
     SET NEW.departamento = "Nuevos Proyectos";
ELSEIF NEW.id_departamento = 7 THEN
     SET NEW.departamento = "Relaciones Publicas";
ELSEIF NEW.id_departamento =8 THEN
     SET NEW.departamento = "Departamento Juridico";
END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `UPDATE_crm_configuracion_actividades`;
DELIMITER ;;
CREATE TRIGGER `UPDATE_crm_configuracion_actividades` BEFORE UPDATE ON `crm_configuracion_actividades` FOR EACH ROW BEGIN

IF(NEW.fecha_completa='none') THEN SET NEW.fecha_vencimiento = 'inline'; END IF ;
IF(NEW.fecha_completa='inline') THEN SET NEW.fecha_vencimiento = 'none'; END IF ;
 /*
IF NEW.id_departamento = 1 THEN
     SET NEW.departamento = "Departamento Comercial";
ELSEIF NEW.id_departamento = 2 THEN
     SET NEW.departamento = "Departamento de Operaciones";
ELSEIF NEW.id_departamento = 3 THEN
     SET NEW.departamento = "Departamento de Calidad";
ELSEIF NEW.id_departamento = 4 THEN
     SET NEW.departamento = "Departamento Financiero";
ELSEIF NEW.id_departamento =5 THEN
     SET NEW.departamento = "SubDireccion";
ELSEIF NEW.id_departamento =6 THEN
     SET NEW.departamento = "Nuevos Proyectos";
ELSEIF NEW.id_departamento = 7 THEN
     SET NEW.departamento = "Relaciones Publicas";
ELSEIF NEW.id_departamento =8 THEN
     SET NEW.departamento = "Departamento Juridico";
END IF;
*/

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_notificacionesINSERT`;
DELIMITER ;;
CREATE TRIGGER `crm_notificacionesINSERT` BEFORE INSERT ON `crm_notificaciones` FOR EACH ROW BEGIN

     SET NEW.objetivo = (SELECT objetivo FROM crm_objetivos WHERE id = NEW.id_objetivo);
     SET NEW.tema = (SELECT tema FROM crm_objetivos_actividades WHERE id = NEW.id_actividad);
     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivosINSERT`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivosINSERT` BEFORE INSERT ON `crm_objetivos` FOR EACH ROW BEGIN

SET NEW.fecha_creacion = NOW();
SET NEW.linea_negocio = (SELECT nombre FROM configuracion_lineas_negocio WHERE id = NEW.id_linea);
SET NEW.estado_proyecto = (SELECT nombre FROM configuracion_estados_proyectos WHERE id = NEW.id_estado);
SET NEW.tipo_proyecto = (SELECT nombre FROM crm_configuracion_tipos_proyecto WHERE id = NEW.id_tipo);

IF(NEW.tipo = 1) THEN
     
     SET NEW.id_cliente = (SELECT id_cliente FROM pedido WHERE id = NEW.referencia);
     SET NEW.cliente = (SELECT nombre_cliente FROM pedido WHERE id = NEW.referencia);
     SET NEW.objetivo = CONCAT('Documento ',LPAD((SELECT consecutivo_cotizacion FROM pedido WHERE id = NEW.referencia),7,0));
     SET NEW.vencimiento = CONCAT( (SELECT fecha_final FROM pedido WHERE id = NEW.referencia),' ', (SELECT hora_final FROM pedido WHERE id = NEW.referencia));
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.id_empresa = (SELECT id_empresa FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.id_sucursal = (SELECT id_sucursal FROM empleados WHERE id = NEW.id_usuario);

END IF;


IF(NEW.tipo = 2) THEN
          
     SET NEW.cliente = (SELECT nombre_comercial  FROM terceros WHERE id = NEW.id_cliente);
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.id_empresa = (SELECT id_empresa FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.id_sucursal = (SELECT id_sucursal FROM empleados WHERE id = NEW.id_usuario);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivosAFTERINSERT`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivosAFTERINSERT` AFTER INSERT ON `crm_objetivos` FOR EACH ROW BEGIN
          #GUARDA EN EL LOG EL ESTADO DEL PROYECTO
          INSERT INTO crm_objetivos_log(id_objetivo,fecha,hora,id_estado,estado,id_usuario,nombre,accion) VALUES (NEW.id,NOW(),NOW(),NEW.id_estado,NEW.estado_proyecto,NEW.id_usuario,NEW.usuario,'cambio de estado');
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivosUPDATE` BEFORE UPDATE ON `crm_objetivos` FOR EACH ROW BEGIN     

     IF(NEW.id_usuario_finaliza IS NOT  NULL) THEN
              SET NEW.usuario_finaliza = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario_finaliza);
     END IF;

     SET NEW.id_empresa = (SELECT id_empresa FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.fecha_actualizacion = NOW();
     SET NEW.id_sucursal = (SELECT id_sucursal FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.linea_negocio = (SELECT nombre FROM configuracion_lineas_negocio WHERE id = NEW.id_linea);
     SET NEW.estado_proyecto = (SELECT nombre FROM configuracion_estados_proyectos WHERE id = NEW.id_estado);
     SET NEW.tipo_proyecto = (SELECT nombre FROM crm_configuracion_tipos_proyecto WHERE id = NEW.id_tipo);
     SET NEW.fecha_actualizacion = NOW();

     #SI HAY UN CAMBIO DE ESTADO ENTONCES GUARDA EN EL HISTORICO
     IF(OLD.id_estado <> NEW.id_estado) THEN
     INSERT INTO crm_objetivos_log(id_objetivo,fecha,hora,id_estado,estado,id_usuario,nombre,accion) VALUES (NEW.id,NOW(),NOW(),NEW.id_estado,NEW.estado_proyecto,NEW.id_usuario,NEW.usuario,'cambio de estado');
     END IF;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivos_actividadesINSERT`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivos_actividadesINSERT` BEFORE INSERT ON `crm_objetivos_actividades` FOR EACH ROW BEGIN    

     if(NEW.id_objetivo != 0)THEN 
          SET NEW.tipo_objetivo = (SELECT tipo FROM crm_objetivos WHERE id = NEW.id_objetivo);
          SET NEW.referencia = (SELECT referencia FROM crm_objetivos WHERE id = NEW.id_objetivo);
          SET NEW.id_cliente = (SELECT id_cliente FROM crm_objetivos WHERE id = NEW.id_objetivo); 
          SET NEW.objetivo = (SELECT objetivo FROM crm_objetivos WHERE id = NEW.id_objetivo);
          UPDATE crm_objetivos SET fecha_actualizacion = NOW() WHERE id = NEW.id_objetivo;
     END IF;

     SET NEW.tipo_nombre = (SELECT nombre FROM crm_configuracion_actividades WHERE id = NEW.tipo );
     SET NEW.icono = (SELECT icono FROM crm_configuracion_actividades WHERE id = NEW.tipo );

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado);
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.fecha = NOW();
     SET NEW.cliente = (SELECT nombre_comercial FROM terceros  WHERE id = NEW.id_cliente); 

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivos_actividadesUPDATE`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivos_actividadesUPDATE` BEFORE UPDATE ON `crm_objetivos_actividades` FOR EACH ROW BEGIN
     
     if(NEW.id_objetivo != 0)THEN 
          SET NEW.tipo_objetivo = (SELECT tipo FROM crm_objetivos WHERE id = NEW.id_objetivo);
          SET NEW.referencia = (SELECT referencia FROM crm_objetivos WHERE id = NEW.id_objetivo);
          SET NEW.id_cliente = (SELECT id_cliente FROM crm_objetivos WHERE id = NEW.id_objetivo); 
          SET NEW.objetivo = (SELECT objetivo FROM crm_objetivos WHERE id = NEW.id_objetivo);
          UPDATE crm_objetivos SET fecha_actualizacion = NOW() WHERE id = NEW.id_objetivo;
     END IF;

     SET NEW.tipo_nombre = (SELECT nombre FROM crm_configuracion_actividades WHERE id = NEW.tipo );
     SET NEW.icono = (SELECT icono FROM crm_configuracion_actividades WHERE id = NEW.tipo );

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado);
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
     SET NEW.cliente = (SELECT nombre_comercial FROM terceros  WHERE id = NEW.id_cliente); 


     IF(NEW.id_usuario_finaliza IS NOT  NULL) THEN
              SET NEW.usuario_finaliza = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario_finaliza);
     END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivos_actividades_accionesINSERT`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivos_actividades_accionesINSERT` BEFORE INSERT ON `crm_objetivos_actividades_acciones` FOR EACH ROW BEGIN

     SET NEW.fecha = NOW();
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario) ;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_obejtivos_actividades_accionesINSERT2`;
DELIMITER ;;
CREATE TRIGGER `crm_obejtivos_actividades_accionesINSERT2` AFTER INSERT ON `crm_objetivos_actividades_acciones` FOR EACH ROW BEGIN


     UPDATE crm_objetivos_actividades SET acciones = (SELECT count(id) FROM crm_objetivos_actividades_acciones WHERE id_actividad = NEW.id_actividad AND activo = 1) WHERE id = NEW.id_actividad;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivos_actividades_personasINSERT`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivos_actividades_personasINSERT` BEFORE INSERT ON `crm_objetivos_actividades_personas` FOR EACH ROW BEGIN

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `crm_objetivos_adjuntos_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `crm_objetivos_adjuntos_INSERT_copy` BEFORE INSERT ON `crm_objetivos_adjuntos` FOR EACH ROW BEGIN

     SET NEW.fecha_creacion=NOW();
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario );    

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `cuentas_default_activos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `cuentas_default_activos_INSERT` BEFORE INSERT ON `cuentas_default_activos_fijos` FOR EACH ROW BEGIN

SET NEW.codigo_grupo = (SELECT codigo_grupo FROM inventario_grupo WHERE id=NEW.id_grupo);
SET NEW.grupo               = (SELECT nombre_grupo FROM inventario_grupo WHERE id=NEW.id_grupo);

SET NEW.cuenta_depreciacion_colgaap_debito                        = (SELECT cuenta FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_debito);
SET NEW.descripcion_cuenta_depreciacion_colgaap_debito  = (SELECT descripcion FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_debito);
SET NEW.cuenta_depreciacion_colgaap_credito                       = (SELECT cuenta FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_credito);
SET NEW.descripcion_cuenta_depreciacion_colgaap_credito = (SELECT descripcion FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_credito);

SET NEW.cuenta_depreciacion_niif_debito                           = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_debito);
SET NEW.descripcion_cuenta_depreciacion_niif_debito     = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_debito);
SET NEW.cuenta_depreciacion_niif_credito                          = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_credito);
SET NEW.descripcion_cuenta_depreciacion_niif_credito    = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_credito);

SET NEW.cuenta_deterioro_debito                         = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_debito); 
SET NEW.descripcion_cuenta_deterioro_debito   = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_debito); 
SET NEW.cuenta_deterioro_credito                        = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_credito); 
SET NEW.descripcion_cuenta_deterioro_credito  = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_credito); 

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `cuentas_default_activos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `cuentas_default_activos_UPDATE` BEFORE UPDATE ON `cuentas_default_activos_fijos` FOR EACH ROW BEGIN

SET NEW.codigo_grupo = (SELECT codigo_grupo FROM inventario_grupo WHERE id=NEW.id_grupo);
SET NEW.grupo               = (SELECT nombre_grupo FROM inventario_grupo WHERE id=NEW.id_grupo);

SET NEW.cuenta_depreciacion_colgaap_debito                        = (SELECT cuenta FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_debito);
SET NEW.descripcion_cuenta_depreciacion_colgaap_debito  = (SELECT descripcion FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_debito);
SET NEW.cuenta_depreciacion_colgaap_credito                       = (SELECT cuenta FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_credito);
SET NEW.descripcion_cuenta_depreciacion_colgaap_credito = (SELECT descripcion FROM puc WHERE id= NEW.id_cuenta_depreciacion_colgaap_credito);

SET NEW.cuenta_depreciacion_niif_debito                           = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_debito);
SET NEW.descripcion_cuenta_depreciacion_niif_debito     = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_debito);
SET NEW.cuenta_depreciacion_niif_credito                          = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_credito);
SET NEW.descripcion_cuenta_depreciacion_niif_credito    = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_depreciacion_niif_credito);

SET NEW.cuenta_deterioro_debito                         = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_debito); 
SET NEW.descripcion_cuenta_deterioro_debito   = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_debito); 
SET NEW.cuenta_deterioro_credito                        = (SELECT cuenta FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_credito); 
SET NEW.descripcion_cuenta_deterioro_credito  = (SELECT descripcion FROM puc_niif WHERE id= NEW.id_cuenta_deterioro_credito); 

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_INSERT`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_INSERT` BEFORE INSERT ON `deterioro_cartera_clientes` FOR EACH ROW BEGIN

SET NEW.documento_usuario = (SELECT documento FROM empleados WHERE activo=1 AND id=NEW.id_usuario);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE activo=1 AND id=NEW.id_usuario);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_UPDATE` BEFORE UPDATE ON `deterioro_cartera_clientes` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal);
IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET @consecutivo=(SELECT consecutivo FROM deterioro_cartera_clientes WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_cuentas_INSERT` BEFORE INSERT ON `deterioro_cartera_clientes_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta = (SELECT cuenta FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.descripcion = (SELECT descripcion FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.centro_costo = (SELECT centro_costo FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_cuentas_UPDATE` BEFORE UPDATE ON `deterioro_cartera_clientes_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta = (SELECT cuenta FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.descripcion = (SELECT descripcion FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.centro_costo = (SELECT centro_costo FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_facturas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_facturas_INSERT` BEFORE INSERT ON `deterioro_cartera_clientes_facturas` FOR EACH ROW BEGIN

SET NEW.id_tercero                    = (SELECT id_cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.documento_tercero  = (SELECT nit FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.tercero                        = (SELECT cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.fecha_factura        = (SELECT fecha_vencimiento FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.numero_factura         = (SELECT numero_factura_completo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.valor_factura              = (SELECT total_factura_sin_abono FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_sucursal_factura   = (SELECT id_sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.sucursal_factura        = (SELECT sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_centro_costo        = (SELECT id_centro_costo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_facturas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_facturas_UPDATE` BEFORE UPDATE ON `deterioro_cartera_clientes_facturas` FOR EACH ROW BEGIN

SET NEW.id_tercero                    = (SELECT id_cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.documento_tercero  = (SELECT nit FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.tercero                        = (SELECT cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.fecha_factura        = (SELECT fecha_vencimiento FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.numero_factura         = (SELECT numero_factura_completo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.valor_factura              = (SELECT total_factura_sin_abono FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_sucursal_factura   = (SELECT id_sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.sucursal_factura        = (SELECT sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_centro_costo        = (SELECT id_centro_costo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_INSERT_copy` BEFORE INSERT ON `deterioro_cartera_proveedores` FOR EACH ROW BEGIN

SET NEW.documento_usuario = (SELECT documento FROM empleados WHERE activo=1 AND id=NEW.id_usuario);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE activo=1 AND id=NEW.id_usuario);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_UPDATE_copy` BEFORE UPDATE ON `deterioro_cartera_proveedores` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id=NEW.id_sucursal);
IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET @consecutivo=(SELECT consecutivo FROM deterioro_cartera_clientes WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_cuentas_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_cuentas_INSERT_copy` BEFORE INSERT ON `deterioro_cartera_proveedores_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta = (SELECT cuenta FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.descripcion = (SELECT descripcion FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.centro_costo = (SELECT centro_costo FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_cuentas_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_cuentas_UPDATE_copy` BEFORE UPDATE ON `deterioro_cartera_proveedores_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta = (SELECT cuenta FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.descripcion = (SELECT descripcion FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.centro_costo = (SELECT centro_costo FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_facturas_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_facturas_INSERT_copy` BEFORE INSERT ON `deterioro_cartera_proveedores_facturas` FOR EACH ROW BEGIN

SET NEW.id_tercero                    = (SELECT id_cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.documento_tercero  = (SELECT nit FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.tercero                        = (SELECT cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.fecha_factura        = (SELECT fecha_vencimiento FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.numero_factura         = (SELECT numero_factura_completo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.valor_factura              = (SELECT total_factura_sin_abono FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_sucursal_factura   = (SELECT id_sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.sucursal_factura        = (SELECT sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_centro_costo        = (SELECT id_centro_costo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `deterioro_cartera_clientes_facturas_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `deterioro_cartera_clientes_facturas_UPDATE_copy` BEFORE UPDATE ON `deterioro_cartera_proveedores_facturas` FOR EACH ROW BEGIN

SET NEW.id_tercero                    = (SELECT id_cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.documento_tercero  = (SELECT nit FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.tercero                        = (SELECT cliente FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.fecha_factura        = (SELECT fecha_vencimiento FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.numero_factura         = (SELECT numero_factura_completo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.valor_factura              = (SELECT total_factura_sin_abono FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_sucursal_factura   = (SELECT id_sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.sucursal_factura        = (SELECT sucursal FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
SET NEW.id_centro_costo        = (SELECT id_centro_costo FROM ventas_facturas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_factura);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `devoluciones_compra_INSERT`;
DELIMITER ;;
CREATE TRIGGER `devoluciones_compra_INSERT` BEFORE INSERT ON `devoluciones_compra` FOR EACH ROW BEGIN

SET NEW.fecha_registro =NOW();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `devoluciones_compra_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `devoluciones_compra_UPDATE` BEFORE UPDATE ON `devoluciones_compra` FOR EACH ROW BEGIN

SET NEW.proveedor = (SELECT nombre FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET NEW.cod_proveedor =(SELECT codigo FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_proveedor LIMIT 0,1);

#ASIGNACION DE CONSECUTIVOS
IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='devolucion' AND modulo='compra' LIMIT 0,1);
    UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='devolucion' AND modulo='compra';
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `devoluciones_venta_INSERT`;
DELIMITER ;;
CREATE TRIGGER `devoluciones_venta_INSERT` BEFORE INSERT ON `devoluciones_venta` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `devoluciones_venta_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `devoluciones_venta_UPDATE` BEFORE UPDATE ON `devoluciones_venta` FOR EACH ROW BEGIN

#ASIGNACION DE CONSECUTIVOS
IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='devolucion' AND modulo='venta' LIMIT 0,1);
    UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='devolucion' AND modulo='venta';
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `devolucion_venta_invertarioINSERT`;
DELIMITER ;;
CREATE TRIGGER `devolucion_venta_invertarioINSERT` BEFORE INSERT ON `devoluciones_venta_inventario` FOR EACH ROW BEGIN

#SET NEW.codigo= (SELECT codigo FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.codigo_proveedor= (SELECT codigo_asignado_por_proveedor FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.nombre= (SELECT nombre_equipo FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.id_impuesto=(SELECT id_impuesto FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
#SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

#SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
#SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

#SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `devolucion_venta_invertarioUPDATE`;
DELIMITER ;;
CREATE TRIGGER `devolucion_venta_invertarioUPDATE` BEFORE UPDATE ON `devoluciones_venta_inventario` FOR EACH ROW BEGIN
#SET NEW.codigo= (SELECT codigo FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.codigo_proveedor= (SELECT codigo_asignado_por_proveedor FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.nombre= (SELECT nombre_equipo FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.id_impuesto=(SELECT id_impuesto FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
#SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

#SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM inventarios WHERE id=NEW.id_inventario LIMIT 0,1);
#SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
#SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

#SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `diferidos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `diferidos_INSERT` BEFORE INSERT ON `diferidos` FOR EACH ROW BEGIN

SET NEW.centro_costos_debito  = (SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_debito);
SET NEW.centro_costos_credito = (SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_credito);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `diferidos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `diferidos_UPDATE` BEFORE UPDATE ON `diferidos` FOR EACH ROW BEGIN

SET NEW.centro_costos_debito  = (SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_debito);
SET NEW.centro_costos_credito = (SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_credito);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `Empleados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `Empleados_INSERT` BEFORE INSERT ON `empleados` FOR EACH ROW BEGIN

DECLARE myconsecutivo  INT;

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.pais = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad);

SET NEW.pais_documento = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais_documento);
SET NEW.departamento_documento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento_documento);
SET NEW.ciudad_documento = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad_documento);

SET NEW.pais_nacimiento = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais_nacimiento);
SET NEW.departamento_nacimiento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento_nacimiento);
SET NEW.ciudad_nacimiento = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad_nacimiento);

SET NEW.cargo = (SELECT nombre FROM empleados_cargos WHERE id = NEW.id_cargo AND id_empresa=NEW.id_empresa);
SET NEW.nombre = CONCAT(NEW.nombre1,' ',NEW.nombre2,' ',NEW.apellido1,' ',NEW.apellido2);
SET NEW.rol = (SELECT nombre FROM empleados_roles WHERE id = NEW.id_rol AND id_empresa=NEW.id_empresa);
SET NEW.tipo_documento_nombre = (SELECT nombre FROM tipo_documento WHERE codigo = NEW.tipo_documento AND id_empresa=NEW.id_empresa);

#SET myconsecutivo =( SELECT consecutivo FROM  movil_sinc_contactos  WHERE id = 1) +1 ;
#UPDATE movil_sinc_contactos SET consecutivo = myconsecutivo WHERE id = 1;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `Empleados_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `Empleados_UPDATE` BEFORE UPDATE ON `empleados` FOR EACH ROW BEGIN

DECLARE myconsecutivo  INT;

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);

SET NEW.pais = (SELECT pais FROM empresas WHERE id = NEW.id_empresa);
IF NEW.id_departamento>0 THEN
SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento);
END IF;
IF NEW.id_ciudad>0 THEN
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad);
END IF;

SET NEW.pais_documento = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais_documento);
SET NEW.departamento_documento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento_documento);
SET NEW.ciudad_documento = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad_documento);

SET NEW.pais_nacimiento = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais_nacimiento);
SET NEW.departamento_nacimiento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento_nacimiento);
SET NEW.ciudad_nacimiento = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad_nacimiento);

SET NEW.cargo = (SELECT nombre FROM empleados_cargos WHERE id = NEW.id_cargo);
SET NEW.nombre = CONCAT(NEW.nombre1,' ',NEW.nombre2,' ',NEW.apellido1,' ',NEW.apellido2);
SET NEW.rol = (SELECT nombre FROM empleados_roles WHERE id = NEW.id_rol);
SET NEW.tipo_documento_nombre = (SELECT nombre FROM tipo_documento WHERE codigo = NEW.tipo_documento AND id_empresa=NEW.id_empresa);

/*
IF(
    OLD.nombre != NEW.nombre
    OR OLD.empresa != NEW.empresa
    OR OLD.cargo  !=  NEW.cargo
    OR OLD.telefono1  !=  NEW.telefono1
    OR OLD.telefono2  !=  NEW.telefono2
    OR OLD.activo  !=  NEW.activo
    OR OLD.email_empresa  !=  NEW.email_empresa
)
THEN
    SET myconsecutivo =( SELECT consecutivo FROM  movil_sinc_contactos  WHERE id = 1) +1 ;
    UPDATE movil_sinc_contactos SET consecutivo = myconsecutivo WHERE id = 1;
END IF;*/

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contratos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_contratos_INSERT` BEFORE INSERT ON `empleados_contratos` FOR EACH ROW BEGIN
   
   SET NEW.cargo=(SELECT nombre FROM empleados_cargos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cargo);   

   SET NEW.tipo_contrato=(SELECT descripcion FROM nomina_tipo_contrato WHERE id=NEW.id_tipo_contrato);
   SET NEW.grupo_trabajo=(SELECT nombre FROM nomina_grupos_trabajo WHERE id=NEW.id_grupo_trabajo);

   SET NEW.tipo_documento_empleado=(SELECT tipo_documento_nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
   SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
   SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);

   SET NEW.codigo_centro_costos=(SELECT codigo FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costos);
   SET NEW.nombre_centro_costos=(SELECT nombre FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costos);

   SET NEW.nivel_riesgo_laboral =(SELECT nombre FROM nomina_niveles_riesgos_laborales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nivel_riesgo_laboral);
   SET NEW.valor_nivel_riesgo_laboral =(SELECT porcentaje FROM nomina_niveles_riesgos_laborales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nivel_riesgo_laboral);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contratos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `empleados_contratos_UPDATE` BEFORE UPDATE ON `empleados_contratos` FOR EACH ROW BEGIN
      
   SET NEW.cargo=(SELECT nombre FROM empleados_cargos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cargo);   

   SET NEW.tipo_contrato=(SELECT descripcion FROM nomina_tipo_contrato WHERE id=NEW.id_tipo_contrato);
   SET NEW.grupo_trabajo=(SELECT nombre FROM nomina_grupos_trabajo WHERE id=NEW.id_grupo_trabajo);

   SET NEW.tipo_documento_empleado=(SELECT tipo_documento_nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
   SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
   SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);

   SET NEW.codigo_centro_costos=(SELECT codigo FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costos);
   SET NEW.nombre_centro_costos=(SELECT nombre FROM centro_costos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_centro_costos);

   SET NEW.nivel_riesgo_laboral =(SELECT nombre FROM nomina_niveles_riesgos_laborales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nivel_riesgo_laboral);
   SET NEW.valor_nivel_riesgo_laboral =(SELECT porcentaje FROM nomina_niveles_riesgos_laborales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nivel_riesgo_laboral);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contratos_entidades_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_contratos_entidades_INSERT` BEFORE INSERT ON `empleados_contratos_entidades` FOR EACH ROW BEGIN
   SET NEW.entidad=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_entidad);
   SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contratos_entidades_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `empleados_contratos_entidades_UPDATE` BEFORE UPDATE ON `empleados_contratos_entidades` FOR EACH ROW BEGIN
   SET NEW.entidad=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_entidad);
   SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contraos_entidades_traslados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_contraos_entidades_traslados_INSERT` BEFORE INSERT ON `empleados_contratos_entidades_traslados` FOR EACH ROW BEGIN

SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);

SET NEW.documento_entidad=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_entidad);
SET NEW.nombre_entidad=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_entidad);

SET NEW.concepto = (SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contraos_entidades_traslados_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `empleados_contraos_entidades_traslados_UPDATE` BEFORE UPDATE ON `empleados_contratos_entidades_traslados` FOR EACH ROW BEGIN

SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);

SET NEW.documento_entidad=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_entidad);
SET NEW.nombre_entidad=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_entidad);


SET NEW.concepto = (SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_contratos_modificacion_salarios_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_contratos_modificacion_salarios_INSERT` BEFORE INSERT ON `empleados_contratos_modificacion_salarios` FOR EACH ROW BEGIN

   SET NEW.tipo_documento_empleado=(SELECT tipo_documento_nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
   SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
   SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_documentosINSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_documentosINSERT` BEFORE INSERT ON `empleados_documentos` FOR EACH ROW BEGIN
SET NEW.tipo_documento_nombre = (SELECT nombre FROM empleados_tipo_documento WHERE id = NEW.tipo_documento);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_historial_vinculacionINSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_historial_vinculacionINSERT` BEFORE INSERT ON `empleados_historial_vinculacion` FOR EACH ROW BEGIN
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `EmpleadosHorasExtrasUPDATE`;
DELIMITER ;;
CREATE TRIGGER `EmpleadosHorasExtrasUPDATE` BEFORE UPDATE ON `empleados_horas_extras` FOR EACH ROW BEGIN

#################### PARAMETROS GENERALES
DECLARE el_id_pais		INT;
DECLARE el_ano		INT;
DECLARE SALARIO		FLOAT;
DECLARE INICIADIURNO	TIME;
DECLARE INICIANOCTURNO	TIME;
DECLARE fechaiFESTIVA	INT DEFAULT(0);
DECLARE fechaSFESTIVA	INT DEFAULT(0);
############### CONTROL NUMERO DE MINUTOS
DECLARE MED		FLOAT DEFAULT(0); 		
DECLARE MEN		FLOAT DEFAULT(0);
DECLARE MEDF		FLOAT DEFAULT(0);
DECLARE MENF		FLOAT DEFAULT(0); 
DECLARE MEDdia2		FLOAT DEFAULT(0); 		
DECLARE MENdia2		FLOAT DEFAULT(0);
DECLARE MEDFdia2		FLOAT DEFAULT(0);
DECLARE MENFdia2		FLOAT DEFAULT(0);
################# CONTROL VALOR DE MINUTOS
DECLARE MEDVALOR	FLOAT DEFAULT(0); 		
DECLARE MENVALOR	FLOAT DEFAULT(0);
DECLARE MEDFVALOR	FLOAT DEFAULT(0);
DECLARE MENFVALOR	FLOAT DEFAULT(0);
DECLARE MEDVALORdia2	FLOAT DEFAULT(0); 		
DECLARE MENVALORdia2	FLOAT DEFAULT(0);
DECLARE MEDFVALORdia2	FLOAT DEFAULT(0);
DECLARE MENFVALORdia2	FLOAT DEFAULT(0);
################ CONTROL FACTOR DE CALCULO	
DECLARE factorHED		FLOAT DEFAULT(1); 		
DECLARE factorHEN		FLOAT DEFAULT(1);
DECLARE factorHEDF	FLOAT DEFAULT(1);
DECLARE factorHENF	FLOAT DEFAULT(1);

SET el_ano = YEAR(NEW.fechai);
SET SALARIO=(SELECT salario_base FROM empleados WHERE documento = NEW.cedula LIMIT 1)/30/8/60;
SET el_id_pais = (SELECT id_pais FROM empleados WHERE documento = NEW.cedula LIMIT 1);
SET factorHED = (SELECT hed FROM configuracion_horas_extras WHERE id_pais = el_id_pais AND ano = el_ano   LIMIT 1);
SET factorHEN = (SELECT hen FROM configuracion_horas_extras WHERE id_pais = el_id_pais  AND ano = el_ano  LIMIT 1);
SET factorHEDF = (SELECT hedf FROM configuracion_horas_extras WHERE id_pais = el_id_pais  AND ano = el_ano  LIMIT 1);
SET factorHENF = (SELECT henf FROM configuracion_horas_extras WHERE id_pais = el_id_pais AND ano = el_ano  LIMIT 1);

SET INICIADIURNO = (SELECT inicia_diurno FROM configuracion_horas_extras WHERE id = 1 LIMIT 1);
SET INICIANOCTURNO = (SELECT inicia_nocturno FROM configuracion_horas_extras WHERE id = 1 LIMIT 1);

IF((SELECT COUNT(fecha) FROM configuracion_festivos WHERE id_pais = el_id_pais AND fecha = NEW.fechai)>0) THEN
	SET fechaiFESTIVA = 1;
ELSE
	IF(weekday(NEW.fechai) = 6) THEN
		SET fechaiFESTIVA = 1;
	END IF;
END if;

IF((SELECT COUNT(fecha) FROM configuracion_festivos WHERE id_pais = el_id_pais AND fecha = NEW.fechas)>0) THEN
	SET fechasFESTIVA = 1;
ELSE
	IF(weekday(NEW.fechas) = 6) THEN
		SET fechasFESTIVA = 1;
	END IF;	
END if;

IF(NEW.fechas != '0000-00-00')
THEN
	################################### SI LAS HORAS EXTRAS SON EL EL MISMO DIA DEL INGRESO ##########################
	IF(NEW.fechai = NEW.fechas) THEN
		############ SI HAY EXTRAS - HORA DE SALIDA ES MAYOR QUE LA HORA DE INICIO DE LAS EXTRAS DIURNA #############
		if(NEW.horas > INICIADIURNO) THEN
			#############   HORA DE SALIDA ES MENOR O IGUAL QUE EL INICIO DE LAS EXTRAS NOCTURNAS  #############
			if(NEW.horas <= INICIANOCTURNO) THEN
				SET MED = Retorna_Minutos(INICIADIURNO,NEW.horas);
				############## SI ES ORDINAL O FESTIVO ###############
				IF(fechaiFESTIVA=0) THEN
					SET NEW.med = MED;
					SET NEW.men = 0;
					SET NEW.medf = 0;
					SET NEW.menf = 0;
					SET NEW.med_valor = MED * (SALARIO * factorHED);
					SET NEW.men_valor = 0;
					SET NEW.medf_valor = 0;
					SET NEW.menf_valor = 0;	
					SET NEW.total = NEW.med_valor + NEW.men_valor + NEW.medf_valor + NEW.menf_valor;					
				ELSE
					SET NEW.med = 0;
					SET NEW.men = 0;		
					SET NEW.medf = MED;
					SET NEW.menf = 0;
					SET NEW.med_valor = 0;
					SET NEW.men_valor = 0;					
					SET NEW.medf_valor = MED * (SALARIO * factorHEDF) ;
					SET NEW.menf_valor = 0;	
					SET NEW.total = NEW.med_valor + NEW.men_valor + NEW.medf_valor + NEW.menf_valor;					
				END IF;
			END IF;
			
			##############  HORA DE SALIDA ES MAYOR QUE EL INICIO DE LAS EXTRAS NOCTURNAS   ###################
			if(NEW.horas > INICIANOCTURNO) THEN
				SET MED = Retorna_Minutos(INICIADIURNO,INICIANOCTURNO);		
				SET MEN = Retorna_Minutos(INICIANOCTURNO,NEW.horas);
				############## SI ES ORDINAL O FESTIVO ###############
				IF(fechaiFESTIVA=0) THEN
					SET NEW.med = MED;
					SET NEW.men = MEN;
					SET NEW.medf = 0;
					SET NEW.menf = 0;
					SET NEW.med_valor = MED * (SALARIO * factorHED);
					SET NEW.men_valor = MEN * (SALARIO * factorHEN);					
					SET NEW.medf_valor = 0;
					SET NEW.menf_valor = 0;
					SET NEW.total = NEW.med_valor + NEW.men_valor + NEW.medf_valor + NEW.menf_valor;
				ELSE
					SET NEW.med = 0;
					SET NEW.men = 0;
					SET NEW.medf = MED;
					SET NEW.menf = MEN;				
					SET NEW.med_valor = 0;
					SET NEW.men_valor = 0;
					SET NEW.medf_valor = MED * (SALARIO * factorHEDF);
					SET NEW.menf_valor = MEN * (SALARIO * factorHENF);	
					SET NEW.total = NEW.med_valor + NEW.men_valor + NEW.medf_valor + NEW.menf_valor;					
				END IF;				
			END IF;	
		END IF;
	END IF;
	
	################################### SI LAS HORAS EXTRAS SON EL DIA DESPUES DEL INGRESO ############################
	IF(NEW.fechai < NEW.fechas) THEN
		############## SI ES ORDINAL O FESTIVO EL DIA DE INGRESO ###############
		IF(fechaiFESTIVA=0) THEN		
			SET MED = Retorna_Minutos(INICIADIURNO,INICIANOCTURNO);
			SET MEN = Retorna_Minutos(INICIANOCTURNO,'24:00:00');
			SET MEDF = 0;
			SET MENf = 0;
			SET MEDVALOR = MED * (SALARIO * factorHED);
			SET MENVALOR = MEN * (SALARIO * factorHEN);
			SET MEDFVALOR = 0;
			SET MENFVALOR = 0;			
		ELSE
			SET MED = 0;
			SET MEN = 0;
			SET MEDF = Retorna_Minutos(INICIADIURNO,INICIANOCTURNO);
			SET MENF = Retorna_Minutos(INICIANOCTURNO,'24:00:00');	
			SET MEDVALOR = 0;
			SET MENVALOR = 0;
			SET MEDFVALOR = MEDF * (SALARIO * factorHEDF);
			SET MENFVALOR = MENF * (SALARIO * factorHENF);
		END IF;
		
		############## SI ES ORDINAL O FESTIVO EL DIA DE SALIDA ###############
		IF(fechasFESTIVA=0) THEN
			###### SI LA FECHA DEL DIA DE SALIDA NO ES FESTIVA PERO EL DE ENTRAD SI, DEBE ASUMIR QUE EL DE SALIDA TAMBIEN ES FESTIVO ######
			IF(fechaiFESTIVA=0)
			THEN
				SET MEDdia2 = 0;
				SET MENdia2 = Retorna_Minutos('00:00:00',NEW.horas);
				SET MEDFdia2 = 0;
				SET MENFdia2 = 0;
				SET MEDVALORdia2 = 0;
				SET MENVALORdia2 = MENdia2 * (SALARIO * factorHEN);
				SET MEDFVALORdia2 = 0;
				SET MENFVALORdia2 = 0;
			ELSE
				SET MEDdia2 = 0;
				SET MENdia2 = 0;
				SET MEDFdia2 = 0;
				SET MENFdia2 = Retorna_Minutos('00:00:00',NEW.horas);
				SET MEDVALORdia2 = 0;
				SET MENVALORdia2 = 0;
				SET MEDFVALORdia2 = 0;
				SET MENFVALORdia2 = MENFdia2 * (SALARIO * factorHENF);				
			END IF;
		ELSE
			SET MEDdia2 = 0;
			SET MENdia2 = 0;
			SET MEDFdia2 = 0;
			SET MENFdia2 = Retorna_Minutos('00:00:00',NEW.horas);
			SET MEDVALORdia2 = 0;
			SET MENVALORdia2 = 0;
			SET MEDFVALORdia2 = 0;
			SET MENFVALORdia2 = MENFdia2 * (SALARIO * factorHENF);			
		END IF;		
		
		SET NEW.med = MED + MEDdia2;
		SET NEW.men = MEN + MENdia2;
		SET NEW.medf = MEDF + MEDFdia2;
		SET NEW.menf = MENF + MENFdia2;
		SET NEW.med_valor = MEDVALOR + MEDVALORdia2;
		SET NEW.men_valor = MENVALOR + MENVALORdia2;
		SET NEW.medf_valor = MEDFVALOR + MEDFVALORdia2;
		SET NEW.menf_valor = MENFVALOR + MENFVALORdia2;	
		SET NEW.total = NEW.med_valor + NEW.men_valor + NEW.medf_valor + NEW.menf_valor;
		
	END IF;	
ELSE
	SET NEW.med = 0;
	SET NEW.men =0;
	SET NEW.medf = 0;
	SET NEW.menf = 0;
	SET NEW.med_valor =0;
	SET NEW.men_valor = 0;
	SET NEW.medf_valor = 0;
	SET NEW.menf_valor = 0;	
	SET NEW.total =0;	
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_info_contacto_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_info_contacto_INSERT` BEFORE INSERT ON `empleados_informacion_contacto` FOR EACH ROW BEGIN

SET NEW.nombre_completo = CONCAT(NEW.nombres,' ',NEW.apellidos);
SET NEW.parentesco = (SELECT nombre FROM configuracion_tipos_contacto WHERE id = NEW.id_parentesco);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_info_contacto_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `empleados_info_contacto_UPDATE` BEFORE UPDATE ON `empleados_informacion_contacto` FOR EACH ROW BEGIN

SET NEW.nombre_completo = CONCAT(NEW.nombres,' ',NEW.apellidos);
SET NEW.parentesco = (SELECT nombre FROM configuracion_tipos_contacto WHERE id = NEW.id_parentesco);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_registrosINSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_registrosINSERT` BEFORE INSERT ON `empleados_registro` FOR EACH ROW BEGIN

DECLARE DiaAnterior DATE;

SET DiaAnterior = DATE_SUB(NEW.fecha,INTERVAL 1 DAY);

SET NEW.nombre = (SELECT nombre FROM empleados WHERE documento = NEW.cedula);

IF(NEW.tipo = 'in') THEN
	IF((SELECT count(fechai) FROM empleados_horas_extras WHERE cedula=NEW.cedula AND fechai = NEW.fecha )<1) THEN
		INSERT INTO empleados_horas_extras (cedula,fechai,horai) VALUES (NEW.cedula,NEW.fecha,NEW.hora);
	END IF;
END IF;

IF(NEW.tipo = 'out') THEN
	IF((SELECT count(fechai) FROM empleados_horas_extras WHERE cedula = NEW.cedula AND fechai = NEW.fecha )>0) THEN
		UPDATE empleados_horas_extras SET cedula = NEW.cedula, fechas = NEW.fecha, horas = NEW.hora WHERE cedula = NEW.cedula AND fechai = NEW.fecha;
	ELSE
		IF((SELECT count(fechai) FROM empleados_horas_extras WHERE cedula=NEW.cedula AND fechai = DiaAnterior )>0) THEN
			UPDATE empleados_horas_extras SET cedula = NEW.cedula, fechas = NEW.fecha, horas = NEW.hora WHERE cedula = NEW.cedula AND fechai = DiaAnterior;
		END IF;		
	END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_roles_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_roles_INSERT` BEFORE INSERT ON `empleados_roles` FOR EACH ROW BEGIN

    SET @NEW_codigo = (SELECT codigo FROM empleados_roles WHERE id_empresa=NEW.id_empresa AND activo=1 ORDER BY codigo DESC LIMIT 0,1);

    IF @NEW_codigo > 0 THEN
         SET NEW.codigo = @NEW_codigo + 1;

    ELSEIF NEW.valor=0 THEN
         SET NEW.codigo=0; 
   
   ELSE
          SET NEW.codigo=1; 
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_sucursales_traslados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `empleados_sucursales_traslados_INSERT` BEFORE INSERT ON `empleados_sucursales_traslados` FOR EACH ROW BEGIN

SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);


SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empleados_sucursales_traslados_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `empleados_sucursales_traslados_UPDATE` BEFORE UPDATE ON `empleados_sucursales_traslados` FOR EACH ROW BEGIN

SET NEW.documento_empleado=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);


SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empresasINSERT`;
DELIMITER ;;
CREATE TRIGGER `empresasINSERT` BEFORE INSERT ON `empresas` FOR EACH ROW BEGIN

IF NEW.tipo_documento > 0  THEN
SET NEW.tipo_documento_nombre = (SELECT nombre FROM tipo_documento WHERE id = NEW.tipo_documento AND id_empresa=NEW.id);
END IF;

IF NEW.pais='' THEN
SET NEW.pais = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
ELSE
SET NEW.id_pais = (SELECT id FROM ubicacion_pais WHERE pais = NEW.pais);
END IF;

SET NEW.departamento=(SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento);
SET NEW.ciudad=(SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad);
SET NEW.simbolo_moneda = (SELECT simbolo FROM configuracion_moneda WHERE id = NEW.id_moneda);
SET NEW.decimales_moneda = (SELECT decimales FROM configuracion_moneda WHERE id = NEW.id_moneda);
SET NEW.descripcion_moneda = (SELECT descripcion FROM configuracion_moneda WHERE id = NEW.id_moneda);

SET NEW.fecha=now();
SET NEW.hora=now();

#NIT completo
IF NEW.digito_verificacion <> '' OR  NEW.digito_verificacion <> NULL THEN
    SET NEW.nit_completo=CONCAT( NEW.documento,'-', NEW.digito_verificacion);
ELSE
    SET NEW.nit_completo=NEW.documento;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empresasUPDATE`;
DELIMITER ;;
CREATE TRIGGER `empresasUPDATE` BEFORE UPDATE ON `empresas` FOR EACH ROW BEGIN

IF NEW.tipo_documento > 0  THEN
SET NEW.tipo_documento_nombre = (SELECT nombre FROM tipo_documento WHERE id = NEW.tipo_documento AND id_empresa=NEW.id);
END IF;

SET NEW.pais = (SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.simbolo_moneda = (SELECT simbolo FROM configuracion_moneda WHERE id = NEW.id_moneda);
SET NEW.descripcion_moneda = (SELECT descripcion FROM configuracion_moneda WHERE id = NEW.id_moneda);

#NIT completo
IF NEW.digito_verificacion <> '' OR  NEW.digito_verificacion <> NULL THEN
    SET NEW.nit_completo=CONCAT( NEW.documento,'-', NEW.digito_verificacion);
ELSE
    SET NEW.nit_completo=NEW.documento;
END IF;

SET NEW.departamento=(SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento);
SET NEW.ciudad=(SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empresas_sucursalesINSERT`;
DELIMITER ;;
CREATE TRIGGER `empresas_sucursalesINSERT` BEFORE INSERT ON `empresas_sucursales` FOR EACH ROW BEGIN

DECLARE contSucursal INT;
SET contSucursal = (SELECT codigo FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa ORDER BY codigo DESC LIMIT 0,1);
IF contSucursal > 0 THEN SET NEW.codigo= contSucursal+1;
ELSE SET NEW.codigo=1;
END IF;

SET NEW.departamento=(SELECT departamento FROM ubicacion_departamento WHERE activo=1 AND id=NEW.id_departamento);
SET NEW.ciudad=(SELECT ciudad FROM ubicacion_ciudad WHERE activo=1 AND id=NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `EmpresasSucursalesINSERT2`;
DELIMITER ;;
CREATE TRIGGER `EmpresasSucursalesINSERT2` AFTER INSERT ON `empresas_sucursales` FOR EACH ROW BEGIN

UPDATE empresas SET sucursales = (SELECT count(id) FROM empresas_sucursales WHERE id_empresa = NEW.id_empresa AND activo = 1) WHERE id = NEW.id_empresa;

#INSERTAR LOS CONSECUTIVOS DE CADA DOCUMENTO
INSERT INTO configuracion_consecutivos_documentos (documento,consecutivo,modulo,id_empresa,id_sucursal) VALUES
('cotizacion',1,'venta',NEW.id_empresa,NEW.id),
('pedido',1,'venta',NEW.id_empresa,NEW.id),
('remision',1,'venta',NEW.id_empresa,NEW.id),
('factura',1,'venta',NEW.id_empresa,NEW.id),
('recibo_de_caja',1,'venta',NEW.id_empresa,NEW.id),
('orden_de_compra',1,'compra',NEW.id_empresa,NEW.id),
('requisicion',1,'compra',NEW.id_empresa,NEW.id),
('entrada_de_almacen',1,'compra',NEW.id_empresa,NEW.id),
('factura',1,'compra',NEW.id_empresa,NEW.id),
('comprobante_de_egreso',1,'compra',NEW.id_empresa,NEW.id),
('planilla_de_nomina',1,'nomina',NEW.id_empresa,NEW.id),
('planilla_de_liquidacion',1,'nomina',NEW.id_empresa,NEW.id),
('pos_venta',1,'venta',NEW.id_empresa,NEW.id),
('depreciacion',1,'activos_fijos',NEW.id_empresa,NEW.id);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `empresas_sucursalesUPDATE`;
DELIMITER ;;
CREATE TRIGGER `empresas_sucursalesUPDATE` BEFORE UPDATE ON `empresas_sucursales` FOR EACH ROW BEGIN

SET NEW.departamento=(SELECT departamento FROM ubicacion_departamento WHERE activo=1 AND id=NEW.id_departamento);
SET NEW.ciudad=(SELECT ciudad FROM ubicacion_ciudad WHERE activo=1 AND id=NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `EmpresasSucursalesUPDATE2`;
DELIMITER ;;
CREATE TRIGGER `EmpresasSucursalesUPDATE2` AFTER UPDATE ON `empresas_sucursales` FOR EACH ROW BEGIN

UPDATE empresas SET sucursales = (SELECT count(id) FROM empresas_sucursales WHERE id_empresa = NEW.id_empresa AND activo = 1) WHERE id = NEW.id_empresa;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `BodegaINSERT`;
DELIMITER ;;
CREATE TRIGGER `BodegaINSERT` BEFORE INSERT ON `empresas_sucursales_bodegas` FOR EACH ROW BEGIN
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `BodegaINSERT2`;
DELIMITER ;;
CREATE TRIGGER `BodegaINSERT2` AFTER INSERT ON `empresas_sucursales_bodegas` FOR EACH ROW BEGIN

UPDATE empresas_sucursales SET bodegas = (SELECT count(id) FROM empresas_sucursales_bodegas WHERE id_sucursal = NEW.id_sucursal AND activo = 1) WHERE id = NEW.id_sucursal;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `BodegaUPDATE`;
DELIMITER ;;
CREATE TRIGGER `BodegaUPDATE` BEFORE UPDATE ON `empresas_sucursales_bodegas` FOR EACH ROW BEGIN
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `BodegaUPDATE2`;
DELIMITER ;;
CREATE TRIGGER `BodegaUPDATE2` AFTER UPDATE ON `empresas_sucursales_bodegas` FOR EACH ROW BEGIN

UPDATE empresas_sucursales SET bodegas = (SELECT count(id) FROM empresas_sucursales_bodegas WHERE id_sucursal = NEW.id_sucursal AND activo = 1) WHERE id = NEW.id_sucursal;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `equipos_registroUPDATE`;
DELIMITER ;;
CREATE TRIGGER `equipos_registroUPDATE` BEFORE UPDATE ON `equipos_registro` FOR EACH ROW BEGIN
   SET NEW.sucursal_nombre =CONCAT( (SELECT empresa FROM vista_sucursales_empresas WHERE id_sucursal = NEW.sucursal),' - ',(SELECT sucursal FROM vista_sucursales_empresas WHERE id_sucursal = NEW.sucursal));
   SET NEW.usuario =(SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `conciliaciones_extractos`;
DELIMITER ;;
CREATE TRIGGER `conciliaciones_extractos` BEFORE UPDATE ON `extractos` FOR EACH ROW IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
   SET @consecutivo=(SELECT consecutivo FROM extractos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
    IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
          SET NEW.consecutivo=1;
    ELSE
          SET NEW.consecutivo=@consecutivo+1;
    END IF;
END IF
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `facturas_saldos_iniciales_INSERT`;
DELIMITER ;;
CREATE TRIGGER `facturas_saldos_iniciales_INSERT` BEFORE INSERT ON `facturas_saldos_iniciales` FOR EACH ROW BEGIN

   SET NEW.sucursal = (SELECT  nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa AND activo=1);
   SET NEW.cuenta_pago = (SELECT nombre FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_pago);
   SET NEW.cuenta_colgaap = (SELECT cuenta FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_pago);
   SET NEW.cuenta_niif = (SELECT cuenta_niif FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_pago);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `facturas_saldos_iniciales_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `facturas_saldos_iniciales_UPDATE` BEFORE UPDATE ON `facturas_saldos_iniciales` FOR EACH ROW BEGIN

   SET NEW.sucursal = (SELECT  nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa AND activo=1);
   SET NEW.cuenta_pago = (SELECT nombre FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_pago);
   SET NEW.cuenta_colgaap = (SELECT cuenta FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_pago);
   SET NEW.cuenta_niif = (SELECT cuenta_niif FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_pago);

     IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
           SET @consecutivo = (SELECT consecutivo FROM facturas_saldos_iniciales  WHERE id_sucursal=NEW.id_sucursal AND id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
           IF  ISNULL(@consecutivo) THEN
                    SET @consecutivo=0;
           END IF;
           SET NEW.consecutivo=@consecutivo+1;
     END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `historico_equiposINSERT`;
DELIMITER ;;
CREATE TRIGGER `historico_equiposINSERT` BEFORE INSERT ON `historico_equipos` FOR EACH ROW BEGIN

IF(NEW.tipo_user = 0) THEN
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
END IF;

IF(NEW.tipo_user = 1) THEN
     SET NEW.usuario = (SELECT nombre_comercial FROM terceros WHERE id = NEW.id_usuario);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `medios_magneticos_formatos_columnas_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `medios_magneticos_formatos_columnas_INSERT_copy` BEFORE INSERT ON `informes_formatos_secciones_columnas` FOR EACH ROW BEGIN



END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_INSERT_copy` BEFORE INSERT ON `informes_formatos_secciones_filas_centro_costos` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_UPDATE_copy` BEFORE UPDATE ON `informes_formatos_secciones_filas_centro_costos` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_INSERT` BEFORE INSERT ON `informes_formatos_secciones_filas_cuentas` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

SET NEW.cuenta_inicial = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );
SET NEW.descripcion_cuenta_inicial = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );

SET NEW.cuenta_final = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );
SET NEW.descripcion_cuenta_final = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_UPDATE` BEFORE UPDATE ON `informes_formatos_secciones_filas_cuentas` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

SET NEW.cuenta_inicial = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );
SET NEW.descripcion_cuenta_inicial = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );

SET NEW.cuenta_final = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );
SET NEW.descripcion_cuenta_final = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_INSERT_copy_copy`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_INSERT_copy_copy` BEFORE INSERT ON `informes_formatos_secciones_filas_documentos` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_UPDATE_copy_copy`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_UPDATE_copy_copy` BEFORE UPDATE ON `informes_formatos_secciones_filas_documentos` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_INSERT_copy_copy1`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_INSERT_copy_copy1` BEFORE INSERT ON `informes_formatos_secciones_filas_terceros` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_formatos_secciones_filas_cuentas_UPDATE_copy_copy1`;
DELIMITER ;;
CREATE TRIGGER `informes_formatos_secciones_filas_cuentas_UPDATE_copy_copy1` BEFORE UPDATE ON `informes_formatos_secciones_filas_terceros` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.seccion = (SELECT nombre FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_seccion );
SET NEW.fila = (SELECT nombre FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_fila );
SET NEW.columna = (SELECT nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `informes_niif_formatos_secciones`;
DELIMITER ;;
CREATE TRIGGER `informes_niif_formatos_secciones` BEFORE INSERT ON `informes_niif_formatos_secciones` FOR EACH ROW BEGIN

SET @padding = (SELECT padding FROM informes_niif_formatos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_formato=NEW.id_formato AND codigo_seccion=NEW.codigo_seccion_padre);


IF ISNULL(@padding) THEN
    SET @padding = 0;
END IF;

SET NEW.padding =@padding+10;

#SET NEW.descripcion_tipo = CONCAT("SELECT padding FROM informes_niif_formatos_secciones WHERE activo=1 AND id_empresa=",NEW.id_empresa," AND id_formato=",NEW.id_formato," AND codigo_seccion=",NEW.codigo_seccion_padre);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_ajuste_INSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_ajuste_INSERT` BEFORE INSERT ON `inventario_ajuste` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_ajuste_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `inventario_ajuste_UPDATE` BEFORE UPDATE ON `inventario_ajuste` FOR EACH ROW BEGIN

SET NEW.tercero = (SELECT nombre FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);
SET NEW.cod_tercero =(SELECT codigo FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero LIMIT 0,1);

SET NEW.codigo_centro_costo = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.centro_costo = (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

#CONSECUTIVO DOCUMENTO
IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET @consecutivo=(SELECT consecutivo FROM inventario_ajuste WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_ajuste_detalle_INSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_ajuste_detalle_INSERT` BEFORE INSERT ON `inventario_ajuste_detalle` FOR EACH ROW BEGIN

    DECLARE id_empresa INTEGER;
    DECLARE id_sucursal INTEGER;
    DECLARE id_bodega INTEGER;

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        
    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_ajuste_detalle_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `inventario_ajuste_detalle_UPDATE` BEFORE UPDATE ON `inventario_ajuste_detalle` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    #SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=id_empresa_db AND id_sucursal=id_sucursal_db AND id_ubicacion=id_bodega_db AND activo=1 GROUP BY id LIMIT 0,1);
  
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventarios_documentosINSERT`;
DELIMITER ;;
CREATE TRIGGER `inventarios_documentosINSERT` BEFORE INSERT ON `inventario_documentos` FOR EACH ROW BEGIN
      IF NEW.tipo_documento = 1 THEN
              SET  NEW.tipo_documento_nombre="Imagen";
     END IF;
     IF  NEW.tipo_documento = 2 THEN
              SET  NEW.tipo_documento_nombre="Carta";
     END IF;
     IF NEW.tipo_documento = 3 THEN
              SET  NEW.tipo_documento_nombre="Documento General";

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_estadisticas_usoINSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_estadisticas_usoINSERT` BEFORE INSERT ON `inventario_estadisticas_uso` FOR EACH ROW BEGIN

INSERT INTO historico_equipos (tipo,tipo_nombre,id_equipo,fecha,tipo_user,id_usuario,id_evento)VALUES(5,'Alquiler',NEW.id_equipo,NEW.desde,1,NEW.id_cliente,NEW.id_pedido);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_notas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_notas_INSERT` BEFORE INSERT ON `inventario_movimiento_notas` FOR EACH ROW BEGIN
IF NEW.tipo='baja_activo_fijo' THEN
      SET NEW.codigo_item = (SELECT code_bar FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
      SET NEW.nombre      = (SELECT nombre_equipo FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
      SET NEW.costo      = (SELECT costo FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
      SET NEW.sucursal    = (SELECT sucursal  FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
      SET NEW.bodega      = (SELECT bodega  FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
ELSE
      SET NEW.codigo_item  = (SELECT codigo FROM inventario_totales  WHERE id_item=NEW.id_item AND id_ubicacion=NEW.id_bodega AND id_sucursal=NEW.id_sucursal AND id_empresa=NEW.id_empresa);
      SET NEW.nombre  = (SELECT nombre_equipo FROM inventario_totales  WHERE id_item=NEW.id_item AND id_ubicacion=NEW.id_bodega AND id_sucursal=NEW.id_sucursal AND id_empresa=NEW.id_empresa);
      SET NEW.sucursal = (SELECT sucursal  FROM inventario_totales  WHERE id_item=NEW.id_item AND id_ubicacion=NEW.id_bodega AND id_sucursal=NEW.id_sucursal AND id_empresa=NEW.id_empresa);
      SET NEW.bodega  = (SELECT ubicacion  FROM inventario_totales  WHERE id_item=NEW.id_item AND id_ubicacion=NEW.id_bodega AND id_sucursal=NEW.id_sucursal AND id_empresa=NEW.id_empresa);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_prestamosINSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_prestamosINSERT` BEFORE INSERT ON `inventario_prestamos` FOR EACH ROW BEGIN

SET NEW.nombre_empresa_origen = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa_origen);
SET NEW.nombre_sucursal_origen = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_origen);
SET NEW.nombre_bodega_origen = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_origen);
SET NEW.nombre_empresa_destino = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa_destino);
SET NEW.nombre_sucursal_destino = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_destino);
SET NEW.nombre_bodega_destino = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_destino);
SET NEW.nombre_equipo = (SELECT nombre_equipo FROM inventarios WHERE id = NEW.id_equipo);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
SET NEW.codigo = (SELECT codigo FROM inventarios WHERE id = NEW.id_equipo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_prestamosINSERTafter`;
DELIMITER ;;
CREATE TRIGGER `inventario_prestamosINSERTafter` AFTER INSERT ON `inventario_prestamos` FOR EACH ROW BEGIN
     IF(NEW.prestamos_devolucion = 'true')THEN
            INSERT INTO historico_equipos  (tipo,tipo_nombre,id_equipo,fecha,tipo_user,id_usuario,id_evento)VALUES(2,'Prestamo',NEW.id_equipo,NEW.fecha,0,NEW.id_usuario,NEW.id);
      ELSE
             INSERT INTO historico_equipos  (tipo,tipo_nombre,id_equipo,fecha,tipo_user,id_usuario,id_evento)VALUES(2,'Devolucion de prestamo',NEW.id_equipo,NEW.fecha,0,NEW.id_usuario,NEW.id);
      END IF;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_prestamosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `inventario_prestamosUPDATE` BEFORE UPDATE ON `inventario_prestamos` FOR EACH ROW BEGIN

SET NEW.nombre_empresa_origen = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa_origen);
SET NEW.nombre_sucursal_origen = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_origen);
SET NEW.nombre_bodega_origen = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_origen);
SET NEW.nombre_empresa_destino = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa_destino);
SET NEW.nombre_sucursal_destino = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_destino);
SET NEW.nombre_bodega_destino = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_destino);
SET NEW.nombre_equipo = (SELECT nombre_equipo FROM inventarios WHERE id = NEW.id_equipo);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
SET NEW.codigo = (SELECT codigo FROM inventarios WHERE id = NEW.id_equipo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `InventarioProcesoINSERT`;
DELIMITER ;;
CREATE TRIGGER `InventarioProcesoINSERT` BEFORE INSERT ON `inventario_proceso` FOR EACH ROW BEGIN
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `InventarioProcesoUPDATE`;
DELIMITER ;;
CREATE TRIGGER `InventarioProcesoUPDATE` BEFORE UPDATE ON `inventario_proceso` FOR EACH ROW BEGIN

DECLARE cont INT(11);
SET cont= (SELECT COUNT(*) FROM empleados WHERE username = NEW.usuario_inventario);

IF cont =1 THEN
SET NEW.id_usuario_inventario = (SELECT id FROM empleados WHERE username = NEW.usuario_inventario);
SET NEW.nombre_usuario_inventario = (SELECT nombre FROM empleados WHERE username = NEW.usuario_inventario);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventarios_proceso_items_outINSERT`;
DELIMITER ;;
CREATE TRIGGER `inventarios_proceso_items_outINSERT` BEFORE INSERT ON `inventario_proceso_items_out` FOR EACH ROW BEGIN

DECLARE cont INT(11);
DECLARE prestamo VARCHAR(20);
DECLARE es_prestado VARCHAR(20);


SET cont= (SELECT COUNT(*) FROM inventarios WHERE codigo = NEW.codigo);

IF cont > 0 THEN
SET NEW.id_equipo  = (SELECT id FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.equipo  = (SELECT nombre_equipo FROM inventarios WHERE codigo = NEW.codigo);

SET NEW.id_empresa  = (SELECT id_empresa FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.empresa  = (SELECT empresa FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.id_sucursal  = (SELECT id_sucursal FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.sucursal  = (SELECT sucursal FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.id_bodega  = (SELECT id_ubicacion FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.bodega  = (SELECT ubicacion FROM inventarios WHERE codigo = NEW.codigo);

SET NEW.id_empresa_prestamo  = (SELECT id_empresa_prestamo FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.empresa_prestamo  = (SELECT empresa_prestamo FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.id_sucursal_prestamo  = (SELECT id_sucursal_prestamo FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.sucursal_prestamo  = (SELECT sucursal_prestamo FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.id_bodega_prestamo  = (SELECT id_bodega_prestamo FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.bodega_prestamo  = (SELECT bodega_prestamo FROM inventarios WHERE codigo = NEW.codigo);

SET NEW.prestado  = (SELECT prestado FROM inventarios WHERE codigo = NEW.codigo);
SET NEW.pertenece_inventario_global  = 'true';
ELSE
SET NEW.pertenece_inventario_global  = 'false';
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_totales_INSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_totales_INSERT` BEFORE INSERT ON `inventario_totales` FOR EACH ROW BEGIN
    IF ISNULL(NEW.costos) THEN SET NEW.costos=0; END IF;

    SET NEW.codigo=(SELECT codigo FROM items WHERE id=NEW.id_item);
    SET NEW.code_bar=(SELECT code_bar FROM items WHERE id=NEW.id_item);
    SET NEW.nombre_equipo=(SELECT nombre_equipo FROM items WHERE id=NEW.id_item);
    SET NEW.unidad_medida=(SELECT unidad_medida FROM items WHERE id=NEW.id_item);
    SET NEW.cantidad_unidades=(SELECT cantidad_unidades FROM items WHERE id=NEW.id_item);
    SET NEW.costos =(SELECT costos FROM items WHERE id=NEW.id_item);
    SET NEW.precio_venta=(SELECT precio_venta FROM items WHERE id=NEW.id_item);
    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_item);

    SET NEW.id_empresa=(SELECT id_empresa FROM items WHERE id=NEW.id_item);
    SET NEW.empresa=(SELECT empresa FROM items WHERE id=NEW.id_item);

    SET NEW.ubicacion = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_ubicacion);
    SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);

    SET NEW.id_familia= (SELECT id_familia FROM items WHERE id = NEW.id_item);
    SET NEW.familia = (SELECT familia FROM items WHERE id = NEW.id_item);

    SET NEW.id_grupo= (SELECT id_grupo FROM items WHERE id = NEW.id_item);
    SET NEW.grupo= (SELECT grupo FROM items WHERE id = NEW.id_item);

    SET NEW.id_subgrupo=(SELECT id_subgrupo FROM items WHERE id=NEW.id_item);
    SET NEW.subgrupo= (SELECT subgrupo FROM items WHERE id = NEW.id_item);

    SET NEW.cantidad_minima_stock=(SELECT cantidad_minima_stock FROM items WHERE id=NEW.id_item);
    SET NEW.cantidad_maxima_stock=(SELECT cantidad_maxima_stock FROM items WHERE id=NEW.id_item);

    SET NEW.inventariable=(SELECT inventariable FROM items WHERE id=NEW.id_item);
    SET NEW.estado_compra=(SELECT estado_compra FROM items WHERE id=NEW.id_item);
    SET NEW.estado_venta=(SELECT estado_venta FROM items WHERE id=NEW.id_item);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_totales_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `inventario_totales_UPDATE` BEFORE UPDATE ON `inventario_totales` FOR EACH ROW BEGIN
IF NEW.cantidad <> OLD.cantidad OR  NEW.costos<>OLD.costos THEN
	SET time_zone = 'America/Bogota';
	#SELECT  @@SESSION.time_zone;
	INSERT INTO inventario_totales_log 
		(
			id_item,
			codigo,
			code_bar,
			nombre_equipo,
			unidad_medida,
			cantidad_unidades,
			costo_anterior,
			costo_nuevo,
			id_empresa,
			empresa,
			id_sucursal,
			sucursal,
			id_ubicacion,
			ubicacion,
			cantidad_anterior,
			cantidad_nueva,
			fecha,
			id_documento,
			tipo_documento,
			consecutivo_documento

		)
		VALUES(
			NEW.id_item,
			NEW.codigo,
			NEW.code_bar,
			NEW.nombre_equipo,
			NEW.unidad_medida,
			NEW.cantidad_unidades,
			OLD.costos,
			NEW.costos,
			NEW.id_empresa,
			NEW.empresa,
			NEW.id_sucursal,
			NEW.sucursal,
			NEW.id_ubicacion,
			NEW.ubicacion,
			OLD.cantidad,
			NEW.cantidad,
			NOW(),
			NEW.id_documento_update,
			NEW.tipo_documento_update,
			NEW.consecutivo_documento_update
		);

END IF;

IF ISNULL(NEW.costos) THEN SET NEW.costos=0; END IF;
IF NEW.cantidad = OLD.cantidad THEN
    SET NEW.codigo=(SELECT codigo FROM items WHERE id=NEW.id_item);
    SET NEW.code_bar=(SELECT code_bar FROM items WHERE id=NEW.id_item);
    SET NEW.nombre_equipo=(SELECT nombre_equipo FROM items WHERE id=NEW.id_item);
    SET NEW.unidad_medida=(SELECT unidad_medida FROM items WHERE id=NEW.id_item);
    SET NEW.cantidad_unidades=(SELECT cantidad_unidades FROM items WHERE id=NEW.id_item);
    SET NEW.precio_venta=(SELECT precio_venta FROM items WHERE id=NEW.id_item);
    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_item);

    SET NEW.id_empresa=(SELECT id_empresa FROM items WHERE id=NEW.id_item);
    SET NEW.empresa=(SELECT empresa FROM items WHERE id=NEW.id_item);

    SET NEW.ubicacion = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_ubicacion);
    SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);

    SET NEW.id_familia= (SELECT id_familia FROM items WHERE id = NEW.id_item);
    SET NEW.familia = (SELECT familia FROM items WHERE id = NEW.id_item);

    SET NEW.id_grupo= (SELECT id_grupo FROM items WHERE id = NEW.id_item);
    SET NEW.grupo= (SELECT grupo FROM items WHERE id = NEW.id_item);

    SET NEW.id_subgrupo=(SELECT id_subgrupo FROM items WHERE id=NEW.id_item);
    SET NEW.subgrupo= (SELECT subgrupo FROM items WHERE id = NEW.id_item);

    SET NEW.inventariable=(SELECT inventariable FROM items WHERE id=NEW.id_item);
    SET NEW.estado_compra=(SELECT estado_compra FROM items WHERE id=NEW.id_item);
    SET NEW.estado_venta=(SELECT estado_venta FROM items WHERE id=NEW.id_item);
    SET NEW.activo=(SELECT activo FROM items WHERE id=NEW.id_item);
END IF;



END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_totales_traslados_manual_INSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_totales_traslados_manual_INSERT` BEFORE INSERT ON `inventario_totales_traslados_manual` FOR EACH ROW BEGIN

SET NEW.nombre_empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.nombre_sucursal_origen = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_origen);
SET NEW.nombre_bodega_origen = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_origen);
SET NEW.nombre_sucursal_destino = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_destino);
SET NEW.nombre_bodega_destino = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_destino);
SET NEW.nombre_equipo = (SELECT nombre_equipo FROM items WHERE id = NEW.id_equipo);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
SET NEW.codigo = (SELECT codigo FROM items WHERE id = NEW.id_equipo);

SET NEW.consecutivo=(SELECT IF(COUNT(id)>0 AND consecutivo>0,consecutivo+1,1) FROM inventario_totales_traslados WHERE id_empresa=NEW.id_empresa AND id_sucursal_origen=NEW.id_sucursal_origen GROUP BY id ORDER BY consecutivo DESC LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_totales_traslados_manual_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `inventario_totales_traslados_manual_UPDATE` BEFORE UPDATE ON `inventario_totales_traslados_manual` FOR EACH ROW BEGIN

SET NEW.nombre_empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.nombre_sucursal_origen = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_origen);
SET NEW.nombre_bodega_origen = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_origen);
SET NEW.nombre_sucursal_destino = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal_destino);
SET NEW.nombre_bodega_destino = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega_destino);
SET NEW.nombre_equipo = (SELECT nombre_equipo FROM items WHERE id = NEW.id_equipo);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
SET NEW.codigo = (SELECT codigo FROM items WHERE id = NEW.id_equipo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_trasladosINSERT`;
DELIMITER ;;
CREATE TRIGGER `inventario_trasladosINSERT` BEFORE INSERT ON `inventario_traslados` FOR EACH ROW BEGIN

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.sucursal_traslado=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal_traslado );
SET NEW.bodega_traslado=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega_traslado );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `inventario_trasladosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `inventario_trasladosUPDATE` BEFORE UPDATE ON `inventario_traslados` FOR EACH ROW BEGIN

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.sucursal_traslado=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal_traslado );
SET NEW.bodega_traslado=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega_traslado );

#CONSECUTIVO DOCUMENTO
IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET @consecutivo=(SELECT consecutivo FROM inventario_traslados WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `invertario_traslado_unidades_INSERT`;
DELIMITER ;;
CREATE TRIGGER `invertario_traslado_unidades_INSERT` BEFORE INSERT ON `inventario_traslados_unidades` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `invertario_traslados_unidades_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `invertario_traslados_unidades_UPDATE` BEFORE UPDATE ON `inventario_traslados_unidades` FOR EACH ROW BEGIN

    IF NEW.id_inventario <>OLD.id_inventario THEN

        SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

        SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
        SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `itemsINSERTbefore`;
DELIMITER ;;
CREATE TRIGGER `itemsINSERTbefore` BEFORE INSERT ON `items` FOR EACH ROW BEGIN

SET NEW.empresa=(SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

#CODIGO AUTOMATICO
IF NEW.codigo_auto = 'true' OR NEW.codigo = '' THEN
    SET @codigo=(SELECT codigo FROM items WHERE id_empresa=NEW.id_empresa AND activo=1 ORDER BY CAST(codigo AS SIGNED) DESC LIMIT 0,1);

    IF  (@codigo IS NULL) THEN
        SET NEW.codigo=1;
    ELSE
         SET NEW.codigo=@codigo+1;
   END IF;
END IF;

SET NEW.fecha_creacion_en_inventario=(SELECT DATE_FORMAT(NOW(),GET_FORMAT(DATE,'JIS')));
SET NEW.unidad_medida = (SELECT nombre FROM inventario_unidades WHERE  id=NEW.id_unidad_medida);
SET NEW.cantidad_unidades = (SELECT unidades FROM inventario_unidades WHERE  id=NEW.id_unidad_medida);

SET NEW.familia = (SELECT nombre FROM items_familia WHERE id = NEW.id_familia);
SET NEW.grupo = (SELECT nombre FROM items_familia_grupo WHERE id = NEW.id_grupo);
SET NEW.subgrupo = (SELECT nombre FROM items_familia_grupo_subgrupo WHERE id = NEW.id_subgrupo);
SET NEW.centro_costos=(SELECT nombre FROM centro_costos WHERE id = NEW.id_centro_costos);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `itemsINSERTafter`;
DELIMITER ;;
CREATE TRIGGER `itemsINSERTafter` AFTER INSERT ON `items` FOR EACH ROW BEGIN

INSERT INTO historico_equipos (tipo,tipo_nombre,id_equipo,fecha,tipo_user,id_usuario)VALUES(0,'Creacion',NEW.id,NEW.fecha_creacion_en_inventario,0,NEW.id_usuario_creacion);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `itemsUPDATEbefore`;
DELIMITER ;;
CREATE TRIGGER `itemsUPDATEbefore` BEFORE UPDATE ON `items` FOR EACH ROW BEGIN

SET NEW.empresa=(SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.familia = (SELECT nombre FROM items_familia WHERE id = NEW.id_familia);
SET NEW.grupo = (SELECT nombre FROM items_familia_grupo WHERE id = NEW.id_grupo);
SET NEW.subgrupo = (SELECT nombre FROM items_familia_grupo_subgrupo WHERE id = NEW.id_subgrupo);
SET NEW.unidad_medida=(SELECT nombre FROM inventario_unidades WHERE  id=NEW.id_unidad_medida); 
SET NEW.cantidad_unidades=(SELECT unidades FROM inventario_unidades WHERE  id=NEW.id_unidad_medida);
SET NEW.centro_costos=(SELECT nombre FROM centro_costos WHERE id = NEW.id_centro_costos);
#SET NEW.valor_impuesto =  (SELECT valor FROM impuestos WHERE  id=NEW.id_impuesto);

#=============== actualizamos la tabla inventario totales =============================#
UPDATE inventario_totales SET nombre_equipo=NEW.nombre_equipo, id_familia=NEW.id_familia, id_grupo =NEW. id_grupo , id_subgrupo=NEW. id_subgrupo,codigo=NEW.codigo  WHERE id_item=NEW.id;

UPDATE items_recetas SET nombre_item=NEW.nombre_equipo,codigo_item=NEW.codigo  WHERE id_item=NEW.id;
UPDATE items_recetas SET nombre_item_materia_prima=NEW.nombre_equipo,codigo_item_materia_prima=NEW.codigo  WHERE id_item_materia_prima=NEW.id;

IF OLD.cantidad_minima_stock <>NEW.cantidad_minima_stock THEN
#Actualizar la cantidad minima en stock mientras en inventarios totales sea igual al anterior stock del item
UPDATE inventario_totales SET cantidad_minima_stock=NEW.cantidad_minima_stock WHERE cantidad_minima_stock=OLD.cantidad_minima_stock AND id_item=NEW.id  AND id_empresa=NEW.id_empresa;
END IF;

IF OLD.cantidad_maxima_stock<> NEW.cantidad_maxima_stock THEN
#Actualizar la cantidad maxima en stock mientras en inventarios totales sea igual al anterior stock del item
UPDATE inventario_totales SET cantidad_maxima_stock=NEW.cantidad_maxima_stock WHERE cantidad_maxima_stock=OLD.cantidad_maxima_stock AND id_item=NEW.id   AND id_empresa=NEW.id_empresa;
END IF;

#CODIGO AUTOMATICO
IF NEW.codigo=0 AND OLD.codigo>0 AND  NEW.codigo_auto = 'false' THEN
    SET NEW.codigo=OLD.codigo;
ELSEIF  NEW.codigo_auto = 'true'  AND OLD.codigo_auto='false' THEN
    SET @codigo=(SELECT MAX(codigo) AS valor FROM items WHERE id_empresa=NEW.id_empresa AND id<>NEW.id);

    IF  (@codigo IS NULL) THEN
        SET NEW.codigo=1;
    ELSE
         SET NEW.codigo=@codigo+1;
   END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `itemsUPDATEafter`;
DELIMITER ;;
CREATE TRIGGER `itemsUPDATEafter` AFTER UPDATE ON `items` FOR EACH ROW BEGIN

#por ultimo actualizamos la tabla inventario totales
UPDATE inventario_totales SET codigo=OLD.codigo WHERE id_item=OLD.id;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_cuentasINSERT`;
DELIMITER ;;
CREATE TRIGGER `items_cuentasINSERT` BEFORE INSERT ON `items_cuentas` FOR EACH ROW BEGIN

SET NEW.id_puc = (SELECT id FROM puc WHERE cuenta=NEW.puc AND activo=1 AND id_empresa=NEW.id_empresa);
SET NEW.cuenta = (SELECT descripcion FROM puc WHERE id=NEW.id_puc AND activo=1);
SET NEW.codigo_items = (SELECT codigo FROM items WHERE id=NEW.id_items AND activo=1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_cuentasUPDATE`;
DELIMITER ;;
CREATE TRIGGER `items_cuentasUPDATE` BEFORE UPDATE ON `items_cuentas` FOR EACH ROW BEGIN

SET NEW.id_puc = (SELECT id FROM puc WHERE cuenta=NEW.puc AND activo=1 AND id_empresa=NEW.id_empresa);
SET NEW.cuenta = (SELECT descripcion FROM puc WHERE id=NEW.id_puc AND activo=1);
SET NEW.codigo_items = (SELECT codigo FROM items WHERE id=NEW.id_items AND activo=1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_cuentas_niif_INSERT`;
DELIMITER ;;
CREATE TRIGGER `items_cuentas_niif_INSERT` BEFORE INSERT ON `items_cuentas_niif` FOR EACH ROW BEGIN

SET NEW.id_puc = (SELECT id FROM puc_niif WHERE cuenta=NEW.puc AND activo=1 AND id_empresa=NEW.id_empresa);
SET NEW.cuenta = (SELECT descripcion FROM puc_niif WHERE id=NEW.id_puc AND activo=1);
SET NEW.codigo_items = (SELECT codigo FROM items WHERE id=NEW.id_items AND activo=1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_cuentas_niif_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `items_cuentas_niif_UPDATE` BEFORE UPDATE ON `items_cuentas_niif` FOR EACH ROW BEGIN

IF NEW.debug=3 THEN
   SET NEW.puc = NEW.cuenta_colgaap;
END IF;

SET NEW.id_puc = (SELECT id FROM puc_niif WHERE cuenta=NEW.puc AND activo=1 AND id_empresa=NEW.id_empresa);
SET NEW.cuenta = (SELECT descripcion FROM puc_niif WHERE id=NEW.id_puc AND activo=1);
SET NEW.codigo_items = (SELECT codigo FROM items WHERE id=NEW.id_items AND activo=1);

IF NEW.debug=1 THEN
   SET NEW.cuenta_colgaap = (SELECT puc FROM items_cuentas WHERE id_empresa=NEW.id_empresa AND estado=NEW.estado AND id_items=NEW.id_items AND descripcion=NEW.descripcion AND activo=NEW.activo);
END IF;




END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_documentos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `items_documentos_INSERT` BEFORE INSERT ON `items_documentos` FOR EACH ROW BEGIN
      IF NEW.tipo_documento = 1 THEN
              SET  NEW.tipo_documento_nombre="Imagen";

     ELSEIF  NEW.tipo_documento = 2 THEN
              SET  NEW.tipo_documento_nombre="Carta";

     ELSEIF  NEW.tipo_documento = 3 THEN
              SET  NEW.tipo_documento_nombre="Documento General";

     ELSEIF NEW.tipo_documento = 4 THEN
              SET  NEW.tipo_documento_nombre="Imagen Logo";
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_familia_grupoINSERT`;
DELIMITER ;;
CREATE TRIGGER `items_familia_grupoINSERT` BEFORE INSERT ON `items_familia_grupo` FOR EACH ROW BEGIN

     SET NEW.familia = (SELECT nombre FROM items_familia WHERE id = NEW.id_familia AND id_empresa=NEW.id_empresa);
     SET NEW.cod_familia = (SELECT codigo FROM items_familia WHERE id = NEW.id_familia AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_familia_grupoUPDATE`;
DELIMITER ;;
CREATE TRIGGER `items_familia_grupoUPDATE` BEFORE UPDATE ON `items_familia_grupo` FOR EACH ROW BEGIN

     SET NEW.familia = (SELECT nombre FROM items_familia WHERE id = NEW.id_familia AND id_empresa=NEW.id_empresa);
     SET NEW.cod_familia = (SELECT codigo FROM items_familia WHERE id = NEW.id_familia AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_familia_grupo_subgrupoINSERT`;
DELIMITER ;;
CREATE TRIGGER `items_familia_grupo_subgrupoINSERT` BEFORE INSERT ON `items_familia_grupo_subgrupo` FOR EACH ROW BEGIN
        
     SET NEW.grupo = (SELECT nombre FROM items_familia_grupo WHERE id = NEW.id_grupo AND id_empresa=NEW.id_empresa);
     SET NEW.cod_grupo = (SELECT codigo FROM items_familia_grupo WHERE id = NEW.id_grupo AND id_empresa=NEW.id_empresa);
     SET NEW.cod_familia = (SELECT codigo FROM items_familia WHERE id = NEW.id_familia  AND id_empresa=NEW.id_empresa);
     SET NEW.familia = (SELECT nombre FROM items_familia WHERE id = NEW.id_familia  AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_familia_grupo_subgrupoUPDATE`;
DELIMITER ;;
CREATE TRIGGER `items_familia_grupo_subgrupoUPDATE` BEFORE UPDATE ON `items_familia_grupo_subgrupo` FOR EACH ROW BEGIN

     SET NEW.grupo = (SELECT nombre FROM items_familia_grupo WHERE id = NEW.id_grupo AND id_empresa=NEW.id_empresa);
     SET NEW.cod_grupo = (SELECT codigo FROM items_familia_grupo WHERE id = NEW.id_grupo AND id_empresa=NEW.id_empresa);
     SET NEW.cod_familia = (SELECT cod_familia FROM items_familia_grupo WHERE id = NEW.id_grupo AND id_empresa=NEW.id_empresa);
     SET NEW.familia = (SELECT familia FROM items_familia_grupo WHERE id = NEW.id_grupo AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_recetas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `items_recetas_INSERT` BEFORE INSERT ON `items_recetas` FOR EACH ROW BEGIN

SET NEW.codigo_item=(SELECT codigo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item);
SET NEW.code_bar_item=(SELECT code_bar FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item);
SET NEW.nombre_item=(SELECT nombre_equipo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item);

SET NEW.codigo_item_materia_prima=(SELECT codigo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);
SET NEW.code_bar_item_materia_prima=(SELECT code_bar FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);
SET NEW.nombre_item_materia_prima=(SELECT nombre_equipo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);

SET NEW.unidad_medida=(SELECT unidad_medida FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `items_recetas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `items_recetas_UPDATE` BEFORE UPDATE ON `items_recetas` FOR EACH ROW BEGIN

SET NEW.codigo_item=(SELECT codigo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item);
SET NEW.code_bar_item=(SELECT code_bar FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item);
SET NEW.nombre_item=(SELECT nombre_equipo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item);

SET NEW.codigo_item_materia_prima=(SELECT codigo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);
SET NEW.code_bar_item_materia_prima=(SELECT code_bar FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);
SET NEW.nombre_item_materia_prima=(SELECT nombre_equipo FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);

SET NEW.unidad_medida=(SELECT unidad_medida FROM items WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_item_materia_prima);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `logINSERT`;
DELIMITER ;;
CREATE TRIGGER `logINSERT` BEFORE INSERT ON `log` FOR EACH ROW BEGIN

SET NEW.username = (SELECT username FROM empleados WHERE id = NEW.user);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `logs_mysqlINSERT`;
DELIMITER ;;
CREATE TRIGGER `logs_mysqlINSERT` BEFORE INSERT ON `logs_mysql` FOR EACH ROW BEGIN
     SET NEW.usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `log_INSERT`;
DELIMITER ;;
CREATE TRIGGER `log_INSERT` BEFORE INSERT ON `log_documentos_contables` FOR EACH ROW BEGIN

SET NEW.fecha=CURDATE();
SET NEW.hora=CURTIME();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa );
SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.nombre_usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `mantenimientoINSERT`;
DELIMITER ;;
CREATE TRIGGER `mantenimientoINSERT` BEFORE INSERT ON `mantenimiento` FOR EACH ROW BEGIN

SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.equipo = (SELECT nombre_equipo FROM inventarios WHERE id = NEW.id_inventario);
SET NEW.cod_equipo = (SELECT codigo FROM inventarios WHERE id = NEW.id_inventario);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `mantenimientoINSERTafter`;
DELIMITER ;;
CREATE TRIGGER `mantenimientoINSERTafter` AFTER INSERT ON `mantenimiento` FOR EACH ROW BEGIN

INSERT INTO historico_equipos (tipo,tipo_nombre,id_equipo,fecha,tipo_user,id_usuario,id_evento)VALUES(3,'Informe mantenimiento',NEW.id_inventario,NEW.fecha_hora_mantenimiento,0,NEW.id_usuario,NEW.id);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `mantenimientoUPDATE`;
DELIMITER ;;
CREATE TRIGGER `mantenimientoUPDATE` BEFORE UPDATE ON `mantenimiento` FOR EACH ROW BEGIN

SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.equipo = (SELECT nombre_equipo FROM inventarios WHERE id = NEW.id_inventario);
SET NEW.cod_equipo = (SELECT codigo FROM inventarios WHERE id = NEW.id_inventario);
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `mantenimiento_datosINSERT`;
DELIMITER ;;
CREATE TRIGGER `mantenimiento_datosINSERT` BEFORE INSERT ON `mantenimiento_datos` FOR EACH ROW BEGIN
SET NEW.checklist = (SELECT nombre FROM configuracion_mantenimiento_checklist WHERE id = NEW.id_checklist);
SET NEW.checklist_detalle = (SELECT nombre FROM configuracion_mantenimiento_checklist_detalles WHERE id = NEW.id_checklist_detalle);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `mantenimiento_datosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `mantenimiento_datosUPDATE` BEFORE UPDATE ON `mantenimiento_datos` FOR EACH ROW BEGIN
SET NEW.checklist = (SELECT nombre FROM configuracion_mantenimiento_checklist WHERE id = NEW.id_checklist);
SET NEW.checklist_detalle = (SELECT nombre FROM configuracion_mantenimiento_checklist_detalles WHERE id = NEW.id_checklist_detalle);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `medios_magneticos_formatos_columnas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `medios_magneticos_formatos_columnas_INSERT` BEFORE INSERT ON `medios_magneticos_formatos_columnas` FOR EACH ROW BEGIN



END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `medios_magneticos_formatos_conceptos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `medios_magneticos_formatos_conceptos_INSERT` BEFORE INSERT ON `medios_magneticos_formatos_conceptos_cuentas` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.concepto = (SELECT concepto FROM medios_magneticos_formatos_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto );
SET NEW.descripcion_concepto = (SELECT descripcion FROM medios_magneticos_formatos_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto );

SET NEW.cuenta_inicial = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );
SET NEW.descripcion_cuenta_inicial = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );

SET NEW.cuenta_final = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );
SET NEW.descripcion_cuenta_final = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );

SET NEW.nombre_columna_formato = (SELECT nombre FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna_formato);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `medios_magneticos_formatos_conceptos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `medios_magneticos_formatos_conceptos_UPDATE` BEFORE UPDATE ON `medios_magneticos_formatos_conceptos_cuentas` FOR EACH ROW BEGIN

SET NEW.codigo_formato = (SELECT codigo FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );
SET NEW.nombre_formato = (SELECT nombre FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_formato );

SET NEW.concepto = (SELECT concepto FROM medios_magneticos_formatos_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto );
SET NEW.descripcion_concepto = (SELECT descripcion FROM medios_magneticos_formatos_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto );

SET NEW.cuenta_inicial = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );
SET NEW.descripcion_cuenta_inicial = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_inicial );

SET NEW.cuenta_final = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );
SET NEW.descripcion_cuenta_final = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_final );

SET NEW.nombre_columna_formato = (SELECT nombre FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_columna_formato);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_INSERT` BEFORE INSERT ON `nomina_conceptos` FOR EACH ROW BEGIN
   SET NEW.id_cuenta_colgaap=(SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_colgaap);
   SET NEW.id_cuenta_contrapartida_colgaap=(SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa  AND cuenta=NEW.cuenta_contrapartida_colgaap);

   SET NEW.descripcion_cuenta_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);

   SET NEW.id_cuenta_niif=(SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_niif);
   SET NEW.id_cuenta_contrapartida_niif=(SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa  AND cuenta=NEW.cuenta_contrapartida_niif);

   SET NEW.descripcion_cuenta_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_contrapartida_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif);

    SET NEW.id_cuenta_colgaap_liquidacion=(SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_colgaap_liquidacion);
   SET NEW.id_cuenta_niif_liquidacion=(SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa  AND cuenta=NEW.cuenta_niif_liquidacion);

   SET NEW.descripcion_cuenta_colgaap_liquidacion=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.descripcion_cuenta_niif_liquidacion=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);

   SET NEW.descripcion_cuenta_colgaap_ajuste=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_ajuste);
   SET NEW.descripcion_cuenta_niif_ajuste=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_ajuste);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_UPDATE` BEFORE UPDATE ON `nomina_conceptos` FOR EACH ROW BEGIN
   SET NEW.descripcion_cuenta_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);

   SET NEW.descripcion_cuenta_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_contrapartida_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif);

   SET NEW.descripcion_cuenta_colgaap_liquidacion=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.descripcion_cuenta_niif_liquidacion=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);

   SET NEW.descripcion_cuenta_colgaap_ajuste=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_ajuste);
   SET NEW.descripcion_cuenta_niif_ajuste=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_ajuste);

   #ACTUALIZAR EL NIVEL DE LOS CONCEPTOS POR CADA GRUPO DE TRABAJO
   UPDATE nomina_conceptos_grupos_trabajo SET nivel_formula=NEW.nivel_formula WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id;
   UPDATE nomina_conceptos_grupos_trabajo SET nivel_formula_liquidacion=NEW.nivel_formula_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_base_liquidacion_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_base_liquidacion_INSERT` BEFORE INSERT ON `nomina_conceptos_base_liquidacion` FOR EACH ROW BEGIN 

    SET NEW.codigo_concepto=(SELECT  codigo FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.concepto=(SELECT  descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.naturaleza=(SELECT  naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);

    SET NEW.codigo_concepto_base=(SELECT  codigo FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_base);
    SET NEW.concepto_base=(SELECT  descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_base);
    SET NEW.naturaleza_base=(SELECT  naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_base);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_base_liquidacion_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_base_liquidacion_UPDATE` BEFORE UPDATE ON `nomina_conceptos_base_liquidacion` FOR EACH ROW BEGIN 

    SET NEW.codigo_concepto=(SELECT  codigo FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.concepto=(SELECT  descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    SET NEW.naturaleza=(SELECT  naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);

    SET NEW.codigo_concepto_base=(SELECT  codigo FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_base);
    SET NEW.concepto_base=(SELECT  descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_base);
    SET NEW.naturaleza_base=(SELECT  naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_base);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_cargo_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_cargo_INSERT` BEFORE INSERT ON `nomina_conceptos_cargo` FOR EACH ROW BEGIN

SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE id=NEW.id_concepto);
SET NEW.cargo=(SELECT nombre FROM empleados_cargos WHERE id=NEW.id_cargo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_cargo_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_cargo_UPDATE` BEFORE UPDATE ON `nomina_conceptos_cargo` FOR EACH ROW BEGIN

SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE id=NEW.id_concepto);
SET NEW.cargo=(SELECT nombre FROM empleados_cargos WHERE id=NEW.id_cargo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_empleados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_empleados_INSERT` BEFORE INSERT ON `nomina_conceptos_empleados` FOR EACH ROW BEGIN

SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE id=NEW.id_concepto);
SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE id=NEW.id_empleado);
SET NEW.id_contrato=(SELECT id FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND estado=0);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_empleados_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_empleados_UPDATE` BEFORE UPDATE ON `nomina_conceptos_empleados` FOR EACH ROW BEGIN

SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE id=NEW.id_concepto);
SET NEW.nombre_empleado=(SELECT nombre FROM empleados WHERE id=NEW.id_empleado);
SET NEW.id_contrato=(SELECT id FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND estado=0);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_grupos_trabajo_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_grupos_trabajo_INSERT` BEFORE INSERT ON `nomina_conceptos_grupos_trabajo` FOR EACH ROW BEGIN
   SET NEW.descripcion_cuenta_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);

   SET NEW.descripcion_cuenta_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_contrapartida_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif);

   SET NEW.descripcion_cuenta_colgaap_liquidacion=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.descripcion_cuenta_niif_liquidacion=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);

   SET NEW.descripcion_cuenta_colgaap_ajuste=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_ajuste);
   SET NEW.descripcion_cuenta_niif_ajuste=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_ajuste);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_conceptos_grupos_trabajo_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_conceptos_grupos_trabajo_UPDATE` BEFORE UPDATE ON `nomina_conceptos_grupos_trabajo` FOR EACH ROW BEGIN
   SET NEW.descripcion_cuenta_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);

   SET NEW.descripcion_cuenta_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_contrapartida_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif);

   SET NEW.descripcion_cuenta_colgaap_liquidacion=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.descripcion_cuenta_niif_liquidacion=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);

   SET NEW.descripcion_cuenta_colgaap_ajuste=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_ajuste);
   SET NEW.descripcion_cuenta_niif_ajuste=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_ajuste);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_liquidacion_provision_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_liquidacion_provision_INSERT` BEFORE INSERT ON `nomina_consolidacion_provision` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.fecha_nota=now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE id=NEW. id_concepto AND id_empresa=NEW.id_empresa);



END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_liquidacion_provision_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_liquidacion_provision_UPDATE` BEFORE UPDATE ON `nomina_consolidacion_provision` FOR EACH ROW BEGIN

SET NEW.concepto=(SELECT descripcion FROM nomina_conceptos WHERE id=NEW. id_concepto AND id_empresa=NEW.id_empresa);

#SET NEW.cuenta_colgaap_cruce=(SELECT cuenta FROM puc WHERE activo=1 AND id=NEW.id_cuenta_colgaap_cruce AND id_empresa=NEW.id_empresa);
#SET NEW.descripcion_cuenta_colgaap_cruce=(SELECT descripcion FROM puc WHERE activo=1 AND id=NEW.id_cuenta_colgaap_cruce AND id_empresa=NEW.id_empresa);

SET NEW.id_cuenta_niif_cruce=(SELECT id FROM puc_niif WHERE activo=1 AND cuenta=NEW.cuenta_niif_cruce AND id_empresa=NEW.id_empresa);
#SET NEW.cuenta_niif_cruce=(SELECT cuenta FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta_niif_cruce AND id_empresa=NEW.id_empresa);
SET NEW.descripcion_cuenta_niif_cruce=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id=NEW.id_cuenta_niif_cruce AND id_empresa=NEW.id_empresa);

#validar que no tenga ya un consecutivo el documento

      IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
           #CONSECUTIVO PARA COLGAAP
           SET @consecutivo=(SELECT consecutivo FROM nomina_liquidacion_provision WHERE id_concepto=NEW.id_concepto AND id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
           IF  ISNULL(@consecutivo) THEN
                   SET @consecutivo=0;
           END IF;
           SET NEW.consecutivo=@consecutivo+1;
           #UPDATE tipo_nota_contable SET consecutivo =  NEW.consecutivo + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;

           #CONSECUTIVO PARA NIIF
           #SET NEW.consecutivo_niif=(SELECT consecutivo_niif FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
           #UPDATE tipo_nota_contable SET consecutivo_niif =  NEW.consecutivo_niif + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
     END IF;



END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_INSERT_copy` BEFORE INSERT ON `nomina_consolidacion_provision_cuentas` FOR EACH ROW BEGIN


    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    
    SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
    SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
     
    SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
    SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
   
# IF @sinc_nota <> 'colgaap'  THEN
    #	IF NEW.id_niif >0 THEN
    #	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	ELSE
    #	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	END IF;
    #END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_UPDATE_copy` BEFORE UPDATE ON `nomina_consolidacion_provision_cuentas` FOR EACH ROW BEGIN

    #SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general);

#     IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
  #   END IF;

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

    SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
    SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);

#    IF @sinc_nota <> 'colgaap'  THEN
  #  	IF NEW.id_niif >0 THEN
    #	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
    #	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
    #	ELSE
    #	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
    #	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    #	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    #	END IF;
    #END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_cuentas_pago_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `configuracion_cuentas_pago_INSERT_copy` BEFORE INSERT ON `nomina_cuentas_pago` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.id_sucursal = (SELECT id_sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.sucursal = (SELECT sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `configuracion_cuentas_pago_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `configuracion_cuentas_pago_UPDATE_copy` BEFORE UPDATE ON `nomina_cuentas_pago` FOR EACH ROW BEGIN

SET NEW.id_cuenta = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.id_sucursal = (SELECT id_sucursal FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);
SET NEW.nombre_cuenta = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_INSERT` BEFORE INSERT ON `nomina_planillas` FOR EACH ROW BEGIN
   SET NEW.fecha_creacion=NOW();
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_UPDATE` BEFORE UPDATE ON `nomina_planillas` FOR EACH ROW BEGIN
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='planilla_de_nomina' AND modulo='nomina' LIMIT 0,1);
    UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='planilla_de_nomina' AND modulo='nomina';
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_ajuste_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_ajuste_INSERT` BEFORE INSERT ON `nomina_planillas_ajuste` FOR EACH ROW BEGIN
   SET NEW.fecha_creacion=NOW();
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_ajuste_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_ajuste_UPDATE` BEFORE UPDATE ON `nomina_planillas_ajuste` FOR EACH ROW BEGIN
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET @consecutivo=(SELECT consecutivo FROM nomina_planillas_ajuste WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_ajuste_empleados_conceptos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_ajuste_empleados_conceptos_INSERT` BEFORE INSERT ON `nomina_planillas_ajuste_empleados_conceptos` FOR EACH ROW BEGIN
   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 
   
   IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    

   IF NEW.centro_costos_ajuste='true' THEN
         SET NEW.id_centro_costos_ajuste             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_ajuste    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_ajuste  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;  
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
        SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
        END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN AJUSTE
    SET @tercero_ajuste = (SELECT tercero_ajuste FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_ajuste ='Entidad' THEN
        
         SET @id_tercero_ajuste = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_ajuste)  OR @id_tercero_ajuste=''  THEN
              SET NEW.id_tercero_ajuste = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_ajuste=@id_tercero_ajuste;
         END IF;
        
    ELSE
       SET NEW.id_tercero_ajuste = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_ajuste_empleados_conceptos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_ajuste_empleados_conceptos_UPDATE` BEFORE UPDATE ON `nomina_planillas_ajuste_empleados_conceptos` FOR EACH ROW BEGIN

   SET NEW.cuenta_colgaap                                                 =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_colgaap                           =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.cuenta_niif                                                         =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_niif                                   =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.cuenta_contrapartida_colgaap                       =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.cuenta_contrapartida_niif                               =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );
   SET NEW.descripcion_cuenta_contrapartida_niif         =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );

   SET NEW.cuenta_colgaap_ajuste                            = (SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_ajuste);
   SET NEW.descripcion_cuenta_colgaap_ajuste      = (SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_ajuste);
   SET NEW.cuenta_niif_ajuste                                    = (SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_ajuste);
   SET NEW.descripcion_cuenta_niif_ajuste              = (SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_ajuste);

   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 
   
   IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    

   IF NEW.centro_costos_ajuste='true' THEN
         SET NEW.id_centro_costos_ajuste             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_ajuste    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_ajuste  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;   
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN AJUSTE
    SET @tercero_ajuste = (SELECT tercero_ajuste FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_ajuste ='Entidad' THEN
        
         SET @id_tercero_ajuste = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_ajuste)  OR @id_tercero_ajuste=''  THEN
              SET NEW.id_tercero_ajuste = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_ajuste=@id_tercero_ajuste;
         END IF;
        
    ELSE
       SET NEW.id_tercero_ajuste = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_INSERT_copy_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_INSERT_copy_copy` BEFORE INSERT ON `nomina_planillas_consolidacion_provision` FOR EACH ROW BEGIN
   SET NEW.fecha_creacion=NOW();
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_UPDATE_copy_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_UPDATE_copy_copy` BEFORE UPDATE ON `nomina_planillas_consolidacion_provision` FOR EACH ROW BEGIN
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET @consecutivo=(SELECT consecutivo FROM nomina_planillas_consolidacion_provision WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
     IF @consecutivo=0 OR ISNULL(@consecutivo) THEN
           SET NEW.consecutivo=1;
     ELSE
           SET NEW.consecutivo=@consecutivo+1;
     END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_conceptos_INSERT_copy_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_conceptos_INSERT_copy_copy` BEFORE INSERT ON `nomina_planillas_consolidacion_provision_empleados_conceptos` FOR EACH ROW BEGIN
   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 
   
   IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN LIQUIDACION
    SET @tercero_liquidacion = (SELECT tercero_cruce_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_liquidacion ='Entidad' THEN
        
         SET @id_tercero_cruce_liquidacion = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_cruce_liquidacion)  OR @id_tercero_cruce_liquidacion=''  THEN
              SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              #SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_cruce_liquidacion=@id_tercero_cruce_liquidacion;
              #SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       #SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_conceptos_UPDATE_copy_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_conceptos_UPDATE_copy_copy` BEFORE UPDATE ON `nomina_planillas_consolidacion_provision_empleados_conceptos` FOR EACH ROW BEGIN

   SET NEW.cuenta_colgaap                                                 =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_colgaap                           =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
  SET NEW.cuenta_niif                                                         =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_niif                                   =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.cuenta_contrapartida_colgaap                       =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.cuenta_contrapartida_niif                               =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );
   SET NEW.descripcion_cuenta_contrapartida_niif         =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );

   SET NEW.cuenta_colgaap_liquidacion                             =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.descripcion_cuenta_colgaap_liquidacion        =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.cuenta_niif_liquidacion                             =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);
   SET NEW.descripcion_cuenta_niif_liquidacion        =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);

   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 

     IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN LIQUIDACION
    SET @tercero_liquidacion = (SELECT tercero_cruce_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_liquidacion ='Entidad' THEN
        
         SET @id_tercero_cruce_liquidacion = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_cruce_liquidacion)  OR @id_tercero_cruce_liquidacion=''  THEN
              SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_cruce_liquidacion=@id_tercero_cruce_liquidacion;
              SET NEW.id_empleado_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_conceptos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_conceptos_INSERT` BEFORE INSERT ON `nomina_planillas_empleados_conceptos` FOR EACH ROW BEGIN
   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 


   IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad'  THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad'  THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_conceptos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_conceptos_UPDATE` BEFORE UPDATE ON `nomina_planillas_empleados_conceptos` FOR EACH ROW BEGIN

IF (OLD.id_planilla_cruce = NEW.id_planilla_cruce) AND (OLD.tipo_planilla_cruce=NEW.tipo_planilla_cruce) THEN

   SET NEW.cuenta_colgaap                                                 =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_colgaap                           =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
  SET NEW.cuenta_niif                                                         =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_niif                                   =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.cuenta_contrapartida_colgaap                       =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.cuenta_contrapartida_niif                               =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );
   SET NEW.descripcion_cuenta_contrapartida_niif         =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );

 IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 

     IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_contabilizacion_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_contabilizacion_INSERT` BEFORE INSERT ON `nomina_planillas_empleados_contabilizacion` FOR EACH ROW BEGIN
   SET NEW.id_cuenta_colgaap=(SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_colgaap LIMIT 0,1);
   SET NEW.descripcion_cuenta_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_colgaap LIMIT 0,1);
   SET NEW.id_cuenta_niif=(SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_niif LIMIT 0,1);
   SET NEW.descripcion_cuenta_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_niif LIMIT 0,1);
   SET NEW.documento_tercero=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tercero);
   SET NEW.tercero=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tercero);
   SET NEW.documento_empleado_cruce=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado_cruce);
   SET NEW.empleado_cruce=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado_cruce);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_contabilizacion_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_contabilizacion_UPDATE` BEFORE UPDATE ON `nomina_planillas_empleados_contabilizacion` FOR EACH ROW BEGIN
   SET NEW.id_cuenta_colgaap=(SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_colgaap LIMIT 0,1);
   SET NEW.descripcion_cuenta_colgaap=(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_colgaap LIMIT 0,1);
   SET NEW.id_cuenta_niif=(SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_niif LIMIT 0,1);
   SET NEW.descripcion_cuenta_niif=(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_niif LIMIT 0,1);
   SET NEW.documento_tercero=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tercero);
   SET NEW.tercero=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tercero);
   SET NEW.documento_empleado_cruce=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado_cruce);
   SET NEW.empleado_cruce=(SELECT nombre FROM terceros WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado_cruce);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_INSERT_copy` BEFORE INSERT ON `nomina_planillas_liquidacion` FOR EACH ROW BEGIN
   SET NEW.fecha_creacion=NOW();
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_UPDATE_copy` BEFORE UPDATE ON `nomina_planillas_liquidacion` FOR EACH ROW BEGIN
   SET NEW.tipo_liquidacion=(SELECT nombre FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
   SET NEW.dias_liquidacion=(SELECT dias FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_tipo_liquidacion);
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='planilla_de_liquidacion' AND modulo='nomina' LIMIT 0,1);
    UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='planilla_de_liquidacion' AND modulo='nomina';
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_liquidacion_conceptos_deducir_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_liquidacion_conceptos_deducir_INSERT` BEFORE INSERT ON `nomina_planillas_liquidacion_conceptos_deducir` FOR EACH ROW BEGIN

SET NEW.concepto =(SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
SET NEW.concepto_deducir =(SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_deducir);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_liquidacion_conceptos_deducir_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_liquidacion_conceptos_deducir_UPDATE` BEFORE UPDATE ON `nomina_planillas_liquidacion_conceptos_deducir` FOR EACH ROW BEGIN

SET NEW.concepto =(SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
SET NEW.concepto_deducir =(SELECT descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto_deducir);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_conceptos_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_conceptos_INSERT_copy` BEFORE INSERT ON `nomina_planillas_liquidacion_empleados_conceptos` FOR EACH ROW BEGIN
   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 
   
   IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN LIQUIDACION
    SET @tercero_liquidacion = (SELECT tercero_cruce_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_liquidacion ='Entidad' THEN
        
         SET @id_tercero_cruce_liquidacion = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_cruce_liquidacion)  OR @id_tercero_cruce_liquidacion=''  THEN
              SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              #SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_cruce_liquidacion=@id_tercero_cruce_liquidacion;
              #SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       #SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_planillas_empleados_conceptos_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `nomina_planillas_empleados_conceptos_UPDATE_copy` BEFORE UPDATE ON `nomina_planillas_liquidacion_empleados_conceptos` FOR EACH ROW BEGIN

   SET NEW.cuenta_colgaap                                                 =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
   SET NEW.descripcion_cuenta_colgaap                           =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap);
  SET NEW.cuenta_niif                                                         =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.descripcion_cuenta_niif                                   =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif);
   SET NEW.cuenta_contrapartida_colgaap                       =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.descripcion_cuenta_contrapartida_colgaap =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_colgaap);
   SET NEW.cuenta_contrapartida_niif                               =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );
   SET NEW.descripcion_cuenta_contrapartida_niif         =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_contrapartida_niif );

   SET NEW.cuenta_colgaap_liquidacion                             =(SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.descripcion_cuenta_colgaap_liquidacion        =(SELECT descripcion FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_colgaap_liquidacion);
   SET NEW.cuenta_niif_liquidacion                             =(SELECT cuenta FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);
   SET NEW.descripcion_cuenta_niif_liquidacion        =(SELECT descripcion FROM puc_niif WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_cuenta_niif_liquidacion);

   IF NEW.centro_costos='true' THEN               
               SET NEW.id_centro_costos             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.codigo_centro_costos    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
               SET NEW.nombre_centro_costos  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);                  
    END IF; 

     IF NEW.centro_costos_contrapartida='true' THEN
         SET NEW.id_centro_costos_contrapartida             =(SELECT id_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.codigo_centro_costos_contrapartida    =(SELECT codigo_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
         SET NEW.nombre_centro_costos_contrapartida  =(SELECT nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empleado=NEW.id_empleado AND id=NEW.id_contrato);
    END IF;    
   
    #VERIFICAR EL TERCERO
    SET @tercero = (SELECT tercero FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero ='Entidad' THEN
        
         SET @id_tercero = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado AND id_contrato=NEW.id_contrato);
         SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         IF ISNULL(@id_tercero)  OR @id_tercero=''  THEN
              SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero=@id_tercero;
              SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN CONTRAPARTIDA
    SET @tercero_contrapartida = (SELECT tercero_cruce FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_contrapartida ='Entidad' THEN
        
         SET @id_tercero_contrapartida = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_contrapartida)  OR @id_tercero_contrapartida=''  THEN
              SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_contrapartida=@id_tercero_contrapartida;
              SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_contrapartida = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    #VERIFICAR EL TERCERO EN LIQUIDACION
    SET @tercero_liquidacion = (SELECT tercero_cruce_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_concepto);
    IF @tercero_liquidacion ='Entidad' THEN
        
         SET @id_tercero_cruce_liquidacion = (SELECT id_entidad FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_concepto=NEW.id_concepto AND id_empleado=NEW.id_empleado  AND id_contrato=NEW.id_contrato);

         IF ISNULL(@id_tercero_cruce_liquidacion)  OR @id_tercero_cruce_liquidacion=''  THEN
              SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
              SET NEW.id_empleado_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         ELSE
              SET NEW.id_tercero_cruce_liquidacion=@id_tercero_cruce_liquidacion;
              SET NEW.id_empleado_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
         END IF;
        
    ELSE
       SET NEW.id_tercero_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
       SET NEW.id_empleado_cruce_liquidacion = (SELECT id_tercero FROM empleados WHERE id=NEW.id_empleado);
    END IF;

    # SI TIENE PRESTAMO
    IF NEW.id_prestamo>0 THEN
        SET @id_tercero_prestamo=(SELECT id_tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_empleado=NEW.id_empleado AND id=NEW.id_prestamo);
        IF @tercero ='Entidad' AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL  )  THEN
             SET NEW.id_tercero=@id_tercero_prestamo;
        END IF;
        IF @tercero_contrapartida ='Entidad'  AND (@id_tercero_prestamo>0  OR @id_tercero_prestamo IS NOT NULL ) THEN
              SET NEW.id_tercero_contrapartida=@id_tercero_prestamo;
        END IF;

    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_prestamos_empleados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_prestamos_empleados_INSERT` BEFORE INSERT ON `nomina_prestamos_empleados` FOR EACH ROW BEGIN

SET NEW.valor_prestamo_restante=NEW.valor_prestamo;
SET NEW.cuotas_restantes=NEW.cuotas;
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal);

SET NEW.documento_tercero=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE activo=1 AND id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

SET @consecutivo=(SELECT consecutivo FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal ORDER BY consecutivo DESC LIMIT 0,1);
IF ISNULL(@consecutivo) THEN
     SET NEW.consecutivo=1;
ELSE
     SET NEW.consecutivo=@consecutivo+1;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_prestamos_empleados_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_prestamos_empleados_UPDATE` BEFORE UPDATE ON `nomina_prestamos_empleados` FOR EACH ROW BEGIN

SET NEW.documento_tercero=(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE activo=1 AND id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_prestamos_empleados_pagos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_prestamos_empleados_pagos_INSERT` BEFORE INSERT ON `nomina_prestamos_empleados_pagos` FOR EACH ROW BEGIN

SET NEW.tipo_documento_extendido=IF(NEW.tipo_documento='RC','Recibo de Caja','Nota Contable' );
SET NEW.fecha=NOW();

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_prestamos_empleados_pagos_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nomina_prestamos_empleados_pagos_UPDATE` BEFORE UPDATE ON `nomina_prestamos_empleados_pagos` FOR EACH ROW BEGIN

SET NEW.tipo_documento_extendido=IF(NEW.tipo_documento='RC','Recibo de Caja','Nota Contable' );
SET NEW.fecha=NOW();

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nomina_vacaciones_empleados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nomina_vacaciones_empleados_INSERT` BEFORE INSERT ON `nomina_vacaciones_empleados` FOR EACH ROW BEGIN

SET NEW.documento_empleado =  (SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
SET NEW.tipo_documento =  (SELECT tipo_documento_nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);
SET NEW.nombre_empleado =  (SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_empleado);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_cierre_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nota_cierre_INSERT` BEFORE INSERT ON `nota_cierre` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_cierre_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nota_cierre_UPDATE` BEFORE UPDATE ON `nota_cierre` FOR EACH ROW BEGIN

 IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
          SET @consecutivo=(SELECT consecutivo FROM nota_cierre WHERE id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
          IF ISNULL(@consecutivo) THEN
               SET NEW.consecutivo=1;
           ELSE   
               SET NEW.observacion = (SELECT consecutivo FROM nota_cierre WHERE id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
               SET NEW.consecutivo=@consecutivo+1;
          END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_INSERT_copy1`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_INSERT_copy1` BEFORE INSERT ON `nota_cierre_cuentas` FOR EACH ROW BEGIN

    #SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general LIMIT 0,1);

     #IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     #ELSE  
            # SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc_niif WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     #END IF;


    #SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
    #SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    
    #IF @sinc_nota <> 'colgaap'  THEN
    #	IF NEW.id_niif >0 THEN
    #	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	ELSE
    	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	END IF;
    #END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_UPDATE_copy1`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_UPDATE_copy1` BEFORE UPDATE ON `nota_cierre_cuentas` FOR EACH ROW BEGIN

    #SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general);

#     IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
  #   ELSE  
    #         SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc_niif WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     #END IF;

#    IF NEW.tiene_centro_costo='No' THEN
 #            SET NEW.id_centro_costos = 0;
  #           SET NEW.codigo_centro_costos = 0;
   #          SET NEW.centro_costos = '';
   # ELSE
     #        SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
      #       SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
    #END IF;

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

#    IF @sinc_nota <> 'colgaap'  THEN
  #  	IF NEW.id_niif >0 THEN
    #	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
   # 	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
   # 	ELSE
    	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
    	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
#    	END IF;
 #   END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_cierre_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `nota_cierre_INSERT_copy` BEFORE INSERT ON `nota_cierre_niif` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_cierre_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `nota_cierre_UPDATE_copy` BEFORE UPDATE ON `nota_cierre_niif` FOR EACH ROW BEGIN

 IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
          SET @consecutivo=(SELECT consecutivo FROM nota_cierre WHERE id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
          IF ISNULL(@consecutivo) THEN
               SET NEW.consecutivo=1;
           ELSE   
               SET NEW.observacion = (SELECT consecutivo FROM nota_cierre WHERE id_empresa=NEW.id_empresa  AND activo=1 ORDER BY consecutivo DESC LIMIT 0,1);
               SET NEW.consecutivo=@consecutivo+1;
          END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_INSERT_copy1_copy`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_INSERT_copy1_copy` BEFORE INSERT ON `nota_cierre_niif_cuentas` FOR EACH ROW BEGIN

    #SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general LIMIT 0,1);

     #IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     #ELSE  
            # SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc_niif WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     #END IF;


    #SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
    #SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    
    #IF @sinc_nota <> 'colgaap'  THEN
    #	IF NEW.id_niif >0 THEN
    #	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	ELSE
    	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    #	END IF;
    #END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_UPDATE_copy1_copy`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_UPDATE_copy1_copy` BEFORE UPDATE ON `nota_cierre_niif_cuentas` FOR EACH ROW BEGIN

    #SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general);

#     IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
  #   ELSE  
    #         SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc_niif WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     #END IF;

#    IF NEW.tiene_centro_costo='No' THEN
 #            SET NEW.id_centro_costos = 0;
  #           SET NEW.codigo_centro_costos = 0;
   #          SET NEW.centro_costos = '';
   # ELSE
     #        SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
      #       SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
    #END IF;

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

#    IF @sinc_nota <> 'colgaap'  THEN
  #  	IF NEW.id_niif >0 THEN
    #	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
   # 	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
   # 	ELSE
    	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
    	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
#    	END IF;
 #   END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_general_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nota_general_INSERT` BEFORE INSERT ON `nota_contable_general` FOR EACH ROW BEGIN
SET NEW.fecha_registro =now();
IF ISNULL(NEW.fecha_nota) OR NEW.fecha_nota='' THEN
     SET NEW.fecha_nota=now();
END IF;

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );

SET NEW.tipo_nota=(SELECT descripcion FROM tipo_nota_contable WHERE id=NEW. id_tipo_nota AND id_empresa=NEW.id_empresa);

#validar que no tenga ya un consecutivo el documento
     IF NEW.estado = 1 AND (NEW.consecutivo<1 OR ISNULL(NEW.consecutivo)) THEN
          
           IF NEW.sinc_nota='colgaap_niif' THEN
                  SET NEW.consecutivo=(SELECT consecutivo FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
                 UPDATE tipo_nota_contable SET consecutivo =  NEW.consecutivo + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;

                SET NEW.consecutivo_niif=(SELECT consecutivo_niif FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
                UPDATE tipo_nota_contable SET consecutivo_niif =  NEW.consecutivo_niif + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
         
          ELSEIF NEW.sinc_nota='colgaap' THEN
                  SET NEW.consecutivo=(SELECT consecutivo FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
                 UPDATE tipo_nota_contable SET consecutivo =  NEW.consecutivo + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
          
          ELSEIF NEW.sinc_nota='niif' THEN
                 SET NEW.consecutivo_niif=(SELECT consecutivo_niif FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
                UPDATE tipo_nota_contable SET consecutivo_niif =  NEW.consecutivo_niif + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
          END IF;

     END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_general_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nota_general_UPDATE` BEFORE UPDATE ON `nota_contable_general` FOR EACH ROW BEGIN

SET NEW.tipo_nota=(SELECT descripcion FROM tipo_nota_contable WHERE id=NEW. id_tipo_nota AND id_empresa=NEW.id_empresa);

#validar que no tenga ya un consecutivo el documento

IF NEW.sinc_nota='colgaap' THEN
     IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
           SET NEW.consecutivo=(SELECT consecutivo FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
           UPDATE tipo_nota_contable SET consecutivo =  NEW.consecutivo + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
     END IF;

ELSEIF NEW.sinc_nota='niif' THEN
     IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo_niif<1 OR ISNULL(OLD.consecutivo_niif)) THEN
           SET NEW.consecutivo_niif=(SELECT consecutivo_niif FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
           UPDATE tipo_nota_contable SET consecutivo_niif =  NEW.consecutivo_niif + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
     END IF;

ELSE
      IF OLD.estado = 0 AND NEW.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
           #CONSECUTIVO PARA COLGAAP
           SET NEW.consecutivo=(SELECT consecutivo FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
           UPDATE tipo_nota_contable SET consecutivo =  NEW.consecutivo + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;

           #CONSECUTIVO PARA NIIF
           SET NEW.consecutivo_niif=(SELECT consecutivo_niif FROM tipo_nota_contable WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1 LIMIT 0,1);
           UPDATE tipo_nota_contable SET consecutivo_niif =  NEW.consecutivo_niif + 1 WHERE id=NEW.id_tipo_nota AND id_empresa=NEW.id_empresa  AND activo=1;
     END IF;

END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_INSERT` BEFORE INSERT ON `nota_contable_general_cuentas` FOR EACH ROW BEGIN

    SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general LIMIT 0,1);

     IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     ELSE  
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
     END IF;


    SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
    SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    
    IF @sinc_nota <> 'colgaap'  THEN
    	IF NEW.id_niif >0 THEN
    	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	ELSE
    	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    	END IF;
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `nota_contable_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `nota_contable_cuentas_UPDATE` BEFORE UPDATE ON `nota_contable_general_cuentas` FOR EACH ROW BEGIN

    SET @sinc_nota=(SELECT  sinc_nota FROM nota_contable_general WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_nota_general);

     IF @sinc_nota <> 'niif'  THEN
             SET NEW.cuenta_puc=(SELECT cuenta FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     ELSE  
             SET NEW.tiene_centro_costo=(SELECT centro_costo FROM puc_niif WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
     END IF;

    IF NEW.tiene_centro_costo='No' THEN
             SET NEW.id_centro_costos = 0;
             SET NEW.codigo_centro_costos = 0;
             SET NEW.centro_costos = '';
    ELSE
             SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
             SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
    END IF;

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.nit_tercero=(SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);

    IF @sinc_nota <> 'colgaap'  THEN
    	IF NEW.id_niif >0 THEN
    	        SET NEW.cuenta_niif=(SELECT cuenta FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE id=NEW.id_niif AND id_empresa=NEW.id_empresa);
    	ELSE
    	       SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa);
    	       SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    	       SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa);
    	END IF;
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `plantilla_configuracionINSERT`;
DELIMITER ;;
CREATE TRIGGER `plantilla_configuracionINSERT` BEFORE INSERT ON `plantillas_configuracion` FOR EACH ROW BEGIN

SET NEW.cuenta=(SELECT puc.descripcion FROM plantillas, puc WHERE plantillas.id=NEW.plantillas_id AND plantillas.id_empresa=puc.id_empresa AND NEW.codigo_puc = puc.cuenta LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `plantilla_configuracionUPDATE`;
DELIMITER ;;
CREATE TRIGGER `plantilla_configuracionUPDATE` BEFORE UPDATE ON `plantillas_configuracion` FOR EACH ROW BEGIN

SET NEW.cuenta=(SELECT puc.descripcion FROM plantillas, puc WHERE plantillas.id=NEW.plantillas_id AND plantillas.id_empresa=puc.id_empresa AND NEW.codigo_puc = puc.cuenta LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `prospectos_upload_registro_INSERT`;
DELIMITER ;;
CREATE TRIGGER `prospectos_upload_registro_INSERT` BEFORE INSERT ON `prospectos_upload_registro` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.ciudad =(SELECT ciudad FROM ubicacion_ciudad WHERE id = NEW.id_ciudad);
SET NEW.iso2 =(SELECT iso2 FROM ubicacion_pais WHERE id = NEW.id_pais);

SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `puc_INSERT`;
DELIMITER ;;
CREATE TRIGGER `puc_INSERT` BEFORE INSERT ON `puc` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento AND activo=1 LIMIT 0,1);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `puc_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `puc_UPDATE` BEFORE UPDATE ON `puc` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento AND activo=1 LIMIT 0,1);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `puc_niif_INSERT`;
DELIMITER ;;
CREATE TRIGGER `puc_niif_INSERT` BEFORE INSERT ON `puc_niif` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento AND activo=1 LIMIT 0,1);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `puc_niif_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `puc_niif_UPDATE` BEFORE UPDATE ON `puc_niif` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento AND activo=1 LIMIT 0,1);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad AND activo=1 LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `recibo_caja_INSERT`;
DELIMITER ;;
CREATE TRIGGER `recibo_caja_INSERT` BEFORE INSERT ON `recibo_caja` FOR EACH ROW BEGIN

SET NEW.fecha_inicial=NOW();

IF NEW.fecha_recibo = '' OR ISNULL(NEW.fecha_recibo) THEN
    SET NEW.fecha_recibo=NOW();
END IF;

SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `recibo_caja_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `recibo_caja_UPDATE` BEFORE UPDATE ON `recibo_caja` FOR EACH ROW BEGIN

SET NEW.configuracion_cuenta = (SELECT nombre FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.cuenta = (SELECT cuenta FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.cuenta_niif=(SELECT cuenta_niif FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);
SET NEW.descripcion_cuenta= (SELECT nombre_cuenta FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta AND id_empresa=NEW.id_empresa);

#CONSECUTIVO DOCUMENTO
IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
     SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='recibo_de_caja' AND modulo='venta' LIMIT 0,1);
     UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='recibo_de_caja' AND modulo='venta';
END IF;

IF NEW.debug=2 THEN
     SET NEW.id_tercero = (SELECT id FROM terceros WHERE id_empresa=NEW.id_empresa AND activo=1 AND numero_identificacion=NEW.nit_tercero AND codigo=NEW.codigo_tercero);
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `recibo_caja_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `recibo_caja_cuentas_INSERT` BEFORE INSERT ON `recibo_caja_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta=(SELECT  cuenta FROM puc WHERE id=NEW.id_puc);
SET NEW.descripcion=(SELECT descripcion FROM puc WHERE id=NEW.id_puc);

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

SET @id_empresa = (SELECT id_empresa FROM recibo_caja WHERE id=NEW.id_recibo_caja AND activo=1);
SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE cuenta=NEW.cuenta AND id_empresa=@id_empresa AND activo=1);

SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND activo=1);
SET NEW.nit_tercero = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND activo=1);
SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND activo=1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `recibo_caja_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `recibo_caja_cuentas_UPDATE` BEFORE UPDATE ON `recibo_caja_cuentas` FOR EACH ROW BEGIN

SET NEW.cuenta=(SELECT  cuenta FROM puc WHERE id=NEW.id_puc);
SET NEW.descripcion=(SELECT descripcion FROM puc WHERE id=NEW.id_puc);

SET NEW.codigo_centro_costos = (SELECT codigo FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);
SET NEW.centro_costos = (SELECT nombre FROM centro_costos WHERE activo=1 AND id=NEW.id_centro_costos);

SET @id_empresa = (SELECT id_empresa FROM recibo_caja WHERE id=NEW.id_recibo_caja AND activo=1);
SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE cuenta=NEW.cuenta AND id_empresa=@id_empresa AND activo=1);

SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND activo=1);
SET NEW.nit_tercero = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND activo=1);
SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND activo=1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reparacionINSERT`;
DELIMITER ;;
CREATE TRIGGER `reparacionINSERT` BEFORE INSERT ON `reparacion` FOR EACH ROW BEGIN

SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);
SET NEW.cod_equipo = (SELECT codigo FROM inventarios WHERE id = NEW.id_inventario);
SET NEW.equipo = (SELECT nombre_equipo FROM inventarios WHERE id = NEW.id_inventario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reparacionINSERTafter`;
DELIMITER ;;
CREATE TRIGGER `reparacionINSERTafter` AFTER INSERT ON `reparacion` FOR EACH ROW BEGIN

INSERT INTO historico_equipos (tipo,tipo_nombre,id_equipo,fecha,tipo_user,id_usuario,id_evento)VALUES(4,'Informe reparacion',NEW.id_inventario,NEW.fecha_registro,0,NEW.id_usuario,NEW.id);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reparacionUPDATE`;
DELIMITER ;;
CREATE TRIGGER `reparacionUPDATE` BEFORE UPDATE ON `reparacion` FOR EACH ROW BEGIN
SET NEW.nombre_usuario = (SELECT nombre FROM empleados WHERE id = NEW.id_usuario);
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.bodega = (SELECT nombre FROM empresas_sucursales_bodegas WHERE id = NEW.id_bodega);
SET NEW.cod_equipo = (SELECT codigo FROM inventarios WHERE id = NEW.id_inventario);
SET NEW.equipo = (SELECT nombre_equipo FROM inventarios WHERE id = NEW.id_inventario);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `retencionesINSERT`;
DELIMITER ;;
CREATE TRIGGER `retencionesINSERT` BEFORE INSERT ON `retenciones` FOR EACH ROW BEGIN

SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `retencionesUPDATE`;
DELIMITER ;;
CREATE TRIGGER `retencionesUPDATE` BEFORE UPDATE ON `retenciones` FOR EACH ROW BEGIN

IF OLD.activo =1 AND NEW.activo = 0 THEN
  UPDATE terceros_retenciones SET activo=0 WHERE id_retencion=NEW.id AND id_empresa=NEW.id_empresa;
END IF;

SET NEW.departamento = (SELECT departamento FROM ubicacion_departamento WHERE id=NEW.id_departamento);
SET NEW.ciudad = (SELECT ciudad FROM ubicacion_ciudad WHERE id=NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reunion_coopeINSERT`;
DELIMITER ;;
CREATE TRIGGER `reunion_coopeINSERT` BEFORE INSERT ON `reunion_coope` FOR EACH ROW BEGIN
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
SET NEW.fecha = NOW();
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reunion_coopeUPDATE`;
DELIMITER ;;
CREATE TRIGGER `reunion_coopeUPDATE` BEFORE UPDATE ON `reunion_coope` FOR EACH ROW BEGIN
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);
SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id = NEW.id_sucursal);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reunion_coope_datosINSERT`;
DELIMITER ;;
CREATE TRIGGER `reunion_coope_datosINSERT` BEFORE INSERT ON `reunion_coope_datos` FOR EACH ROW BEGIN
SET NEW.checklist_detalle = (SELECT nombre FROM configuracion_reunion_coope_checklist_detalles  WHERE id = NEW.id_checklist_detalle);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `reunion_coope_datosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `reunion_coope_datosUPDATE` BEFORE UPDATE ON `reunion_coope_datos` FOR EACH ROW BEGIN
SET NEW.checklist_detalle = (SELECT nombre FROM configuracion_reunion_coope_checklist_detalles  WHERE id = NEW.id_checklist_detalle);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_INSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_INSERT` BEFORE INSERT ON `terceros` FOR EACH ROW BEGIN

IF((SELECT tipo FROM tipo_documento WHERE id = NEW.id_tipo_identificacion AND id_empresa=New.id_empresa LIMIT 0,1) ='Persona') THEN
     SET NEW.contactos = 1;
END IF;

IF NEW.codigo IS NULL THEN
   SET @consulta=(SELECT codigo FROM terceros WHERE id_empresa=NEW.id_empresa ORDER BY codigo DESC LIMIT 0,1);
        IF(ISNULL(@consulta) ) THEN
              SET NEW.codigo=1;
       ELSE
                 SET NEW.codigo=(@consulta+1);
       END IF;
END IF;

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.ciudad =(SELECT ciudad FROM ubicacion_ciudad WHERE id = NEW.id_ciudad);
SET NEW.iso2 =(SELECT iso2 FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.tipo_identificacion = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion AND id_empresa=NEW.id_empresa);
SET NEW.tipo_identificacion_representante = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion_representante AND id_empresa=NEW.id_empresa);
SET NEW.tipo = (SELECT tipo FROM tipo_documento WHERE id = NEW.id_tipo_identificacion AND id_empresa=NEW.id_empresa);
SET NEW.sector_empresarial = (SELECT nombre FROM configuracion_sector_empresarial WHERE id = NEW.id_sector_empresarial AND id_empresa=NEW.id_empresa);
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

SET NEW.tercero_tributario =  (SELECT nombre FROM terceros_tributario WHERE id = NEW.id_tercero_tributario);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `persona_natural_INSERT`;
DELIMITER ;;
CREATE TRIGGER `persona_natural_INSERT` AFTER INSERT ON `terceros` FOR EACH ROW BEGIN

                  ######ACTUALIZACION DE TERCEROS_DIRECCIONES DESPUES DEL UPDATE
	INSERT DELAYED INTO terceros_direcciones(
		id_tercero, 
		direccion, 
		id_departamento, 
		id_ciudad, 
		nombre, 
		direccion_principal, 
		telefono1,
		telefono2,
		celular1,
		celular2,
		id_pais,
		pais
	)VALUES(
		NEW.id, 
		NEW.direccion, 
		NEW.id_departamento, 
		NEW.id_ciudad, 
		NEW.nombre_comercial,
		1,
		NEW.telefono1,
		NEW.telefono2,
		NEW.celular1,
		NEW.celular2,
		NEW.id_pais,
		NEW.pais		
	);
	######

	IF((SELECT tipo FROM tipo_documento WHERE id = NEW.id_tipo_identificacion  LIMIT 0,1) = 'Persona') THEN

		INSERT DELAYED INTO terceros_contactos(
			id_tercero,
			nombre,
			identificacion,
			id_tipo_identificacion,
			ContactoAuto,
			telefono1,
			telefono2,
			celular1,
			celular2,
			direccion,
			id_empresa
		)VALUES(
			NEW.id,
			NEW.nombre,
			NEW.numero_identificacion,
			NEW.id_tipo_identificacion,
			1,
			NEW.telefono1,
			NEW.telefono2,
			NEW.celular1,
			NEW.celular2,
			NEW.direccion,
			NEW.id_empresa
		);
	END IF;

                #INSERTA EN EL LOG DE TERCEROS SI SE CREO UN PROSPECTO O UN TERCERO

                IF(NEW.tercero = 0) THEN
                         INSERT INTO terceros_log(id_tercero,fecha,hora,id_usuario,accion)  values(NEW.id,NOW(),NOW(),NEW.UserIdLog,'prospecto');
                END IF;

                IF(NEW.tercero = 1) THEN
                        INSERT INTO terceros_log(id_tercero,fecha,hora,id_usuario,accion)  values(NEW.id,NOW(),NOW(),NEW.UserIdLog,'tercero');
                END IF;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `terceros_UPDATE` BEFORE UPDATE ON `terceros` FOR EACH ROW BEGIN

#INSERTA EN EL LOG DE TERCEROS

#IF(OLD.tercero = 0 AND NEW.tercero = 1) THEN
      # INSERT INTO terceros_log(id_tercero,fecha,hora,id_usuario,accion)  values(NEW.id,NOW(),NOW(),NEW.UserIdLog,'prospecto-tercero');
#END IF;

IF NEW.update=1 THEN
      ######ACTUALIZACION DE TERCEROS_DIRECCIONES DESPUES DEL UPDATE
	INSERT INTO terceros_direcciones(
		id_tercero, 
		direccion, 
		id_departamento, 
		id_ciudad, 
		nombre, 
		direccion_principal, 
		telefono1,
		telefono2,
		celular1,
		celular2,
		id_pais,
		pais
	)VALUES(
		NEW.id, 
		NEW.direccion, 
		NEW.id_departamento, 
		NEW.id_ciudad, 
		NEW.nombre_comercial,
		1,
		NEW.telefono1,
		NEW.telefono2,
		NEW.celular1,
		NEW.celular2,
		NEW.id_pais,
		NEW.pais		
	);
END IF;

######ACTUALIZACION DE TERCEROS_DIRECCIONES DESPUES DEL UPDATE

	UPDATE  
		terceros_direcciones 
	SET 
		nombre = NEW.nombre_comercial,
		direccion= NEW.direccion,  
		id_departamento=NEW.id_departamento,  
		id_ciudad= NEW.id_ciudad, 
		telefono1=NEW.telefono1 
	WHERE 
		id_tercero=NEW.id 
		AND direccion_principal=1;

######


SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.ciudad =(SELECT ciudad FROM ubicacion_ciudad WHERE id = NEW.id_ciudad);
SET NEW.iso2 =(SELECT iso2 FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.tipo_identificacion = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion );
SET NEW.tipo_identificacion_representante = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion_representante );
SET NEW.tipo = (SELECT tipo FROM tipo_documento WHERE id = NEW.id_tipo_identificacion );
SET NEW.sector_empresarial = (SELECT nombre FROM configuracion_sector_empresarial WHERE id = NEW.id_sector_empresarial );
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

SET NEW.tercero_tributario =  (SELECT nombre FROM terceros_tributario WHERE id = NEW.id_tercero_tributario);

########  LOGS  #########
#IF(OLD.tipo_identificacion != NEW.tipo_identificacion)  THEN  call InsertLogs( 'terceros', 'tipo_identificacion', NEW.id, 'UPDATE', OLD.tipo_identificacion, NEW.tipo_identificacion, NEW.UserIdLog);  END IF;
#IF(OLD.numero_identificacion != NEW.numero_identificacion)  THEN  call InsertLogs( 'terceros', 'numero_identificacion', NEW.id, 'UPDATE', OLD.numero_identificacion, NEW.numero_identificacion, NEW.UserIdLog);  END IF;
#IF(OLD.pais != NEW.pais)  THEN  call InsertLogs( 'terceros', 'pais', NEW.id, 'UPDATE', OLD.pais, NEW.pais, NEW.UserIdLog);  END IF;
#IF(OLD.departamento != NEW.departamento)  THEN  call InsertLogs( 'terceros', 'departamento', NEW.id, 'UPDATE', OLD.departamento, NEW.departamento, NEW.UserIdLog);  END IF;
#IF(OLD.ciudad != NEW.ciudad)  THEN  call InsertLogs( 'terceros', 'ciudad', NEW.id, 'UPDATE', OLD.ciudad, NEW.ciudad, NEW.UserIdLog);  END IF;
#IF(OLD.nombre != NEW.nombre)  THEN  call InsertLogs( 'terceros', 'nombre', NEW.id, 'UPDATE', OLD.nombre, NEW.nombre, NEW.UserIdLog);  END IF;
#IF(OLD.sector_empresarial != NEW.sector_empresarial)  THEN  call InsertLogs( 'terceros', 'sector_empresarial', NEW.id, 'UPDATE', OLD.sector_empresarial, NEW.sector_empresarial, NEW.UserIdLog);  END IF;
#IF(OLD.telefono1 != NEW.telefono1)  THEN  call InsertLogs( 'terceros', 'telefono1', NEW.id, 'UPDATE', OLD.telefono1, NEW.telefono1, NEW.UserIdLog);  END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tercerosasignadosINSERT`;
DELIMITER ;;
CREATE TRIGGER `tercerosasignadosINSERT` BEFORE INSERT ON `terceros_asignados` FOR EACH ROW BEGIN

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tercerosasignadosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `tercerosasignadosUPDATE` BEFORE UPDATE ON `terceros_asignados` FOR EACH ROW BEGIN

     SET NEW.asignado = (SELECT nombre FROM empleados WHERE id = NEW.id_asignado );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosContactosINSERT`;
DELIMITER ;;
CREATE TRIGGER `TercerosContactosINSERT` BEFORE INSERT ON `terceros_contactos` FOR EACH ROW BEGIN

SET NEW.tratamiento = (SELECT nombre FROM terceros_tratamiento WHERE id = NEW.id_tratamiento);
SET NEW.tipo_identificacion = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosContactosINSERT2`;
DELIMITER ;;
CREATE TRIGGER `TercerosContactosINSERT2` AFTER INSERT ON `terceros_contactos` FOR EACH ROW BEGIN

IF(NEW.ContactoAuto = 0) THEN
     UPDATE terceros SET contactos = (SELECT count(id) FROM terceros_contactos WHERE id_tercero = NEW.id_tercero AND activo = 1) WHERE id = NEW.id_tercero;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosContactosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `TercerosContactosUPDATE` BEFORE UPDATE ON `terceros_contactos` FOR EACH ROW BEGIN

SET NEW.tratamiento = (SELECT nombre FROM terceros_tratamiento WHERE id = NEW.id_tratamiento);
SET NEW.tipo_identificacion = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion );

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosContactosUPDATE2`;
DELIMITER ;;
CREATE TRIGGER `TercerosContactosUPDATE2` AFTER UPDATE ON `terceros_contactos` FOR EACH ROW BEGIN

UPDATE terceros SET contactos = (SELECT count(id) FROM terceros_contactos WHERE id_tercero = NEW.id_tercero AND activo = 1) WHERE id = NEW.id_tercero;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosContactosEmailINSERT`;
DELIMITER ;;
CREATE TRIGGER `TercerosContactosEmailINSERT` AFTER INSERT ON `terceros_contactos_email` FOR EACH ROW BEGIN
   UPDATE terceros_contactos SET emails = (SELECT count(id) FROM terceros_contactos_email WHERE id_contacto = NEW.id_contacto AND activo = 1) WHERE id = NEW.id_contacto;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosContactosEmailUpdate`;
DELIMITER ;;
CREATE TRIGGER `TercerosContactosEmailUpdate` AFTER UPDATE ON `terceros_contactos_email` FOR EACH ROW BEGIN
   UPDATE terceros_contactos SET emails = (SELECT count(id) FROM terceros_contactos_email WHERE id_contacto = NEW.id_contacto AND activo = 1) WHERE id = NEW.id_contacto;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_direccionesINSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_direccionesINSERT` BEFORE INSERT ON `terceros_direcciones` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.ciudad =(SELECT ciudad FROM ubicacion_ciudad WHERE id = NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_direccionesUPDATE`;
DELIMITER ;;
CREATE TRIGGER `terceros_direccionesUPDATE` BEFORE UPDATE ON `terceros_direcciones` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.ciudad =(SELECT ciudad FROM ubicacion_ciudad WHERE id = NEW.id_ciudad);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosDireccionesEmailINSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `TercerosDireccionesEmailINSERT_copy` AFTER INSERT ON `terceros_direcciones_email` FOR EACH ROW BEGIN
   UPDATE terceros_direcciones SET emails = (SELECT count(id) FROM terceros_direcciones_email WHERE id_direccion = NEW.id_direccion AND activo = 1) WHERE id = NEW.id_direccion;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `TercerosDireccionesEmailUpdate_copy`;
DELIMITER ;;
CREATE TRIGGER `TercerosDireccionesEmailUpdate_copy` AFTER UPDATE ON `terceros_direcciones_email` FOR EACH ROW BEGIN
    UPDATE terceros_direcciones SET emails = (SELECT count(id) FROM terceros_direcciones_email WHERE id_direccion = NEW.id_direccion AND activo = 1) WHERE id = NEW.id_direccion;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_documentosINSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_documentosINSERT` BEFORE INSERT ON `terceros_documentos` FOR EACH ROW BEGIN
SET NEW.tipo_documento_nombre = (SELECT nombre FROM terceros_tipo_documento WHERE id = NEW.tipo_documento);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_documentosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `terceros_documentosUPDATE` BEFORE UPDATE ON `terceros_documentos` FOR EACH ROW BEGIN
SET NEW.tipo_documento_nombre = (SELECT nombre FROM terceros_tipo_documento WHERE id = NEW.tipo_documento);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_ficha_tecnicaINSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_ficha_tecnicaINSERT` BEFORE INSERT ON `terceros_ficha_tecnica` FOR EACH ROW BEGIN

SET NEW.forma_pago =  (SELECT nombre FROM configuracion_formas_pago WHERE id = NEW.id_forma_pago LIMIT 0,1);

UPDATE terceros SET ficha_tecnica = 'true' where id = NEW.id_tercero ;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_ficha_tecnicaUPDATE`;
DELIMITER ;;
CREATE TRIGGER `terceros_ficha_tecnicaUPDATE` BEFORE UPDATE ON `terceros_ficha_tecnica` FOR EACH ROW BEGIN

SET NEW.forma_pago =  (SELECT nombre FROM configuracion_formas_pago WHERE id = NEW.id_forma_pago LIMIT 0,1);

UPDATE terceros SET ficha_tecnica = 'true' where id = NEW.id_tercero ;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_logINSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_logINSERT` BEFORE INSERT ON `terceros_log` FOR EACH ROW BEGIN

      SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id=NEW.id_usuario);
	
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_logUPDATE`;
DELIMITER ;;
CREATE TRIGGER `terceros_logUPDATE` BEFORE UPDATE ON `terceros_log` FOR EACH ROW BEGIN

      SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id=NEW.id_usuario);
	
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_retenciones_INSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_retenciones_INSERT` BEFORE INSERT ON `terceros_retenciones` FOR EACH ROW BEGIN

SET NEW.retencion = (SELECT retencion FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.id_departamento = (SELECT id_departamento FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.departamento = (SELECT departamento FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.id_ciudad = (SELECT id_ciudad FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.ciudad = (SELECT ciudad FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.valor = (SELECT valor FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.modulo = (SELECT modulo FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_retenciones_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `terceros_retenciones_UPDATE` BEFORE UPDATE ON `terceros_retenciones` FOR EACH ROW BEGIN

SET NEW.retencion = (SELECT retencion FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.id_departamento = (SELECT id_departamento FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.departamento = (SELECT departamento FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.id_ciudad = (SELECT id_ciudad FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.ciudad = (SELECT ciudad FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.valor = (SELECT valor FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);
SET NEW.modulo = (SELECT modulo FROM retenciones WHERE id=NEW.id_retencion LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tercero_tratamiento_INSERT`;
DELIMITER ;;
CREATE TRIGGER `tercero_tratamiento_INSERT` BEFORE INSERT ON `terceros_tratamiento` FOR EACH ROW BEGIN

    SET @new_codigo_tratamiento = (SELECT codigo FROM terceros_tratamiento WHERE id_empresa=NEW.id_empresa AND activo=1 ORDER BY codigo DESC LIMIT 0,1);

    IF @new_codigo_tratamiento > 0 THEN
         SET NEW.codigo = @new_codigo_tratamiento + 1;
    ELSE
         SET NEW.codigo=1; 
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tercero_tributario_INSERT`;
DELIMITER ;;
CREATE TRIGGER `tercero_tributario_INSERT` BEFORE INSERT ON `terceros_tributario` FOR EACH ROW BEGIN

DECLARE contRegimen CHAR;

SET contRegimen = (SELECT codigo FROM terceros_tributario WHERE activo=1 AND codigo>0 AND id_pais=NEW.id_pais ORDER BY codigo DESC LIMIT 1);

IF contRegimen > 0 THEN SET NEW.codigo = contRegimen+1;
ELSE SET NEW.codigo = 1;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tercero_tributario_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `tercero_tributario_UPDATE` BEFORE UPDATE ON `terceros_tributario` FOR EACH ROW BEGIN

DECLARE contRegimen INT;

IF(NEW.codigo = '' OR ISNULL(OLD.codigo)) THEN

    SET contRegimen = (SELECT codigo FROM terceros_tributario WHERE activo=1 AND codigo>0 AND id_pais=NEW.id_pais ORDER BY codigo DESC LIMIT 1);

    IF contRegimen > 0 THEN SET NEW.codigo = contRegimen+1;
    ELSE SET NEW.codigo = 1;
    END IF;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_upload_INSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_upload_INSERT` BEFORE INSERT ON `terceros_upload` FOR EACH ROW BEGIN

DECLARE cont_upload INT;

SET cont_upload = (SELECT consecutivo FROM terceros_upload WHERE consecutivo > 0 AND id_empresa=NEW.id_empresa ORDER BY consecutivo DESC LIMIT 0,1);

IF cont_upload > 0 THEN SET NEW.consecutivo = cont_upload+1;
ELSE SET NEW.consecutivo=1;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `terceros_upload_registro_INSERT`;
DELIMITER ;;
CREATE TRIGGER `terceros_upload_registro_INSERT` BEFORE INSERT ON `terceros_upload_registro` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.ciudad =(SELECT ciudad FROM ubicacion_ciudad WHERE id = NEW.id_ciudad);
SET NEW.iso2 =(SELECT iso2 FROM ubicacion_pais WHERE id = NEW.id_pais);

IF NEW.id_tipo_identificacion > 0 THEN SET NEW.tipo = (SELECT tipo FROM tipo_documento WHERE id = NEW.id_tipo_identificacion AND id_empresa=NEW.id_empresa);
END IF;

SET NEW.tipo_identificacion = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion AND id_empresa=NEW.id_empresa);
SET NEW.tipo_identificacion_representante = (SELECT nombre FROM tipo_documento WHERE id = NEW.id_tipo_identificacion_representante AND id_empresa=NEW.id_empresa);
SET NEW.sector_empresarial = (SELECT nombre FROM configuracion_sector_empresarial WHERE id = NEW.id_sector_empresarial AND id_empresa=NEW.id_empresa);
SET NEW.empresa = (SELECT nombre FROM empresas WHERE id = NEW.id_empresa);

SET NEW.tercero_tributario =  (SELECT nombre FROM terceros_tributario WHERE id = NEW.id_tercero_tributario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tipo_documento_INSERT`;
DELIMITER ;;
CREATE TRIGGER `tipo_documento_INSERT` BEFORE INSERT ON `tipo_documento` FOR EACH ROW BEGIN

    SET @NEW_codigo_tipo_documento = (SELECT codigo FROM tipo_documento WHERE id_empresa=NEW.id_empresa AND activo=1 ORDER BY codigo DESC LIMIT 0,1);

    IF @NEW_codigo_tipo_documento > 0 THEN
         SET NEW.codigo = @NEW_codigo_tipo_documento+1;
    ELSE
         SET NEW.codigo=1; 
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tipo_nota_contable_INSERT`;
DELIMITER ;;
CREATE TRIGGER `tipo_nota_contable_INSERT` BEFORE INSERT ON `tipo_nota_contable` FOR EACH ROW BEGIN

   DECLARE cont INT;
   SET cont = (SELECT codigo FROM tipo_nota_contable WHERE activo=1 AND id_empresa=NEW.id_empresa ORDER BY codigo DESC LIMIT 0,1);

   IF cont > 0 THEN SET NEW.codigo=cont+1;
   ELSE SET NEW.codigo=1;
   END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tipo_nota_contable_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `tipo_nota_contable_UPDATE` BEFORE UPDATE ON `tipo_nota_contable` FOR EACH ROW BEGIN

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `UbicacionCiudadINSERT`;
DELIMITER ;;
CREATE TRIGGER `UbicacionCiudadINSERT` BEFORE INSERT ON `ubicacion_ciudad` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.codigo_departamento =(SELECT codigo_departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `UbicacionCiudadUPDATE`;
DELIMITER ;;
CREATE TRIGGER `UbicacionCiudadUPDATE` BEFORE UPDATE ON `ubicacion_ciudad` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);
SET NEW.departamento =(SELECT departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);
SET NEW.codigo_departamento =(SELECT codigo_departamento FROM ubicacion_departamento WHERE id = NEW.id_departamento);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `UbicacionDepartamentoINSERT`;
DELIMITER ;;
CREATE TRIGGER `UbicacionDepartamentoINSERT` BEFORE INSERT ON `ubicacion_departamento` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `UbicacionDepartamentoUPDATE`;
DELIMITER ;;
CREATE TRIGGER `UbicacionDepartamentoUPDATE` BEFORE UPDATE ON `ubicacion_departamento` FOR EACH ROW BEGIN

SET NEW.pais =(SELECT pais FROM ubicacion_pais WHERE id = NEW.id_pais);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `variables_INSERT`;
DELIMITER ;;
CREATE TRIGGER `variables_INSERT` BEFORE INSERT ON `variables` FOR EACH ROW BEGIN

SET NEW.grupo = (SELECT nombre FROM variables_grupos WHERE id = NEW.id_grupo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `variables_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `variables_UPDATE` BEFORE UPDATE ON `variables` FOR EACH ROW BEGIN

SET NEW.grupo = (SELECT nombre FROM variables_grupos WHERE id = NEW.id_grupo);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `variables_grupos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `variables_grupos_INSERT` BEFORE INSERT ON `variables_grupos` FOR EACH ROW BEGIN

SET NEW.codigo=(SELECT codigo FROM variables_grupos WHERE id_empresa=NEW.id_empresa AND activo=1 ORDER BY codigo DESC LIMIT 0,1); 

IF NEW.codigo>0 THEN SET NEW.codigo =  NEW.codigo+1;
ELSE  SET NEW.codigo=1;
END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_cotizacionesINSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_cotizacionesINSERT` BEFORE INSERT ON `ventas_cotizaciones` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

SET NEW.id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_cotizacionesUPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_cotizacionesUPDATE` BEFORE UPDATE ON `ventas_cotizaciones` FOR EACH ROW BEGIN

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET new.cliente = (SELECT nombre FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET new.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
    SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='cotizacion' AND modulo='venta' LIMIT 0,1);
    UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='cotizacion' AND modulo='venta';
END IF;

#ACTUALIZA CANTIDAD EN INVENTARIO
SET NEW.total_unidades = (SELECT SUM(cantidad) FROM ventas_cotizaciones_inventario WHERE activo=1 AND id_cotizacion_venta=NEW.id);

SET NEW.id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_cotizaciones_invertarioINSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_cotizaciones_invertarioINSERT` BEFORE INSERT ON `ventas_cotizaciones_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    #IF ISNULL(NEW.valor_impuesto) THEN
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    #END IF;

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_cotizaciones_invertarioUPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_cotizaciones_invertarioUPDATE` BEFORE UPDATE ON `ventas_cotizaciones_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.saldo_cantidad=NEW.cantidad;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturasINSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturasINSERT` BEFORE INSERT ON `ventas_facturas` FOR EACH ROW BEGIN

SET NEW.cliente = (SELECT nombre FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.cod_cliente = (SELECT codigo FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

SET NEW.fecha_creacion = NOW();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

SET NEW.id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturasUPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturasUPDATE` BEFORE UPDATE ON `ventas_facturas` FOR EACH ROW BEGIN

SET NEW.cliente = (SELECT nombre FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.cod_cliente = (SELECT codigo FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);
SET NEW.sucursal_cliente=(SELECT nombre FROM terceros_direcciones WHERE activo=1 AND id=NEW.id_sucursal_cliente);

SET NEW.codigo_centro_costo = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.centro_costo = (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

SET NEW.forma_pago=(SELECT nombre FROM configuracion_formas_pago WHERE id=NEW.id_forma_pago LIMIT 0,1);
SET NEW.dias_pago = (SELECT plazo FROM configuracion_formas_pago WHERE id=NEW.id_forma_pago AND activo=1 LIMIT 0,1);

IF NEW.dias_pago >= 0 THEN
    SET NEW.fecha_vencimiento = (DATE_ADD(NEW.fecha_inicio, INTERVAL NEW.dias_pago DAY));
END IF;

SET NEW.configuracion_cuenta_pago = (SELECT nombre FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta_pago);
IF ISNULL(NEW.plantillas_id) OR NEW.plantillas_id=0 THEN
     SET NEW.cuenta_pago = (SELECT cuenta FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta_pago);
     SET NEW.cuenta_pago_niif = (SELECT cuenta_niif FROM configuracion_cuentas_pago WHERE id=NEW.id_configuracion_cuenta_pago);
END IF;
SET NEW.id_cuenta_pago = (SELECT id FROM puc WHERE activo=1 AND id_empresa=NEW.id_empresa AND cuenta=NEW.cuenta_pago LIMIT 0,1);

#CONSECUTIVO DOCUMENTO
IF OLD.estado = 0 AND NEW.estado = 1 AND NEW.id_saldo_inicial=0 AND (OLD.numero_factura<1 OR ISNULL(OLD.numero_factura) AND NEW.tipo<>'Ws') THEN
     SET NEW.prefijo=(SELECT prefijo FROM ventas_facturas_configuracion WHERE id_empresa=NEW.id_empresa  AND activo=1 AND id=NEW.id_configuracion_resolucion);
     SET NEW.numero_factura=(SELECT consecutivo_factura FROM ventas_facturas_configuracion WHERE id_empresa=NEW.id_empresa  AND activo=1 AND id=NEW.id_configuracion_resolucion);
     UPDATE ventas_facturas_configuracion SET consecutivo_factura =  NEW.numero_factura + 1 WHERE id_empresa=NEW.id_empresa AND activo=1 AND  id=NEW.id_configuracion_resolucion;
END IF;

IF OLD.estado = 0 AND NEW.estado = 1 THEN
     SET NEW.hora_inicio=NOW();
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_configuracion_sucursales_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_configuracion_sucursales_INSERT` BEFORE INSERT ON `ventas_facturas_configuracion_sucursales` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_configuracion_sucursales_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_configuracion_sucursales_UPDATE` BEFORE UPDATE ON `ventas_facturas_configuracion_sucursales` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_cuentas_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_cuentas_INSERT` BEFORE INSERT ON `ventas_facturas_cuentas` FOR EACH ROW BEGIN

     SET NEW.id_puc=(SELECT id FROM puc WHERE cuenta=NEW.cuenta_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);    
     SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.nit_tercero = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa LIMIT 0,1);

    SET NEW.codigo_centro_costos=(SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.centro_costos=(SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costos AND id_empresa=NEW.id_empresa LIMIT 0,1);
 
    SET NEW.id_niif=(SELECT cuenta_niif FROM puc WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
    SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_cuentas_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_cuentas_UPDATE` BEFORE UPDATE ON `ventas_facturas_cuentas` FOR EACH ROW BEGIN

    SET NEW.codigo_tercero=(SELECT codigo FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.nit_tercero = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    SET NEW.tercero=(SELECT nombre_comercial FROM terceros WHERE id=NEW.id_tercero AND id_empresa=NEW.id_empresa);
    
     IF NEW.cuenta_puc<>OLD.cuenta_puc THEN
        SET NEW.id_puc=(SELECT id FROM puc WHERE cuenta=NEW.cuenta_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);  
        SET NEW.descripcion_puc=(SELECT descripcion FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.cuenta_niif=(SELECT cuenta_niif FROM puc WHERE id=NEW.id_puc AND id_empresa=NEW.id_empresa LIMIT 0,1);

        SET NEW.id_niif=(SELECT id FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
        SET NEW.descripcion_niif=(SELECT descripcion FROM puc_niif WHERE cuenta=NEW.cuenta_niif AND id_empresa=NEW.id_empresa LIMIT 0,1);
   END IF;


END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_inventario_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_inventario_INSERT` BEFORE INSERT ON `ventas_facturas_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    

    IF ISNULL(NEW.id_impuesto) OR NEW.id_impuesto='' THEN
        SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
     ELSE
        SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    END IF;

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.id_empresa = (SELECT id_empresa FROM ventas_facturas WHERE id=NEW.id_factura_venta);
    SET NEW.id_sucursal = (SELECT id_sucursal FROM ventas_facturas WHERE id=NEW.id_factura_venta);
    SET NEW.id_bodega = (SELECT id_bodega FROM ventas_facturas WHERE id=NEW.id_factura_venta);

    SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id_ubicacion=NEW.id_bodega AND activo=1 GROUP BY id LIMIT 0,1);
    
   SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
   SET @estado=(SELECT estado FROM ventas_facturas WHERE id=NEW.id_factura_venta);

   IF @estado<1 THEN
       SET NEW.saldo_cantidad=NEW.cantidad;
   END IF;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_inventario_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_inventario_UPDATE` BEFORE UPDATE ON `ventas_facturas_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    IF ISNULL(NEW.id_impuesto) OR NEW.id_impuesto='' THEN
        SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
        SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
     ELSE
        SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    END IF;

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND id_ubicacion=NEW.id_bodega AND activo=1 GROUP BY id LIMIT 0,1);
    
    IF OLD.id_inventario <> NEW.id_inventario THEN
         SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_facturas_retenciones_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_facturas_retenciones_INSERT` BEFORE INSERT ON `ventas_facturas_retenciones` FOR EACH ROW BEGIN

SET NEW.tipo_retencion  = (SELECT tipo_retencion FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.retencion  = (SELECT retencion FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.valor          = (SELECT valor FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.base          = (SELECT base FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.codigo_cuenta  = (SELECT cuenta FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.codigo_cuenta_niif  = (SELECT cuenta_niif FROM retenciones WHERE id=NEW.id_retencion);

SET NEW.cuenta_autoretencion  = (SELECT cuenta_autoretencion FROM retenciones WHERE id=NEW.id_retencion);
SET NEW.cuenta_autoretencion_niif  = (SELECT cuenta_autoretencion_niif FROM retenciones WHERE id=NEW.id_retencion);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pedidosINSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pedidosINSERT` BEFORE INSERT ON `ventas_pedidos` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

SET NEW.id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pedidosUPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_pedidosUPDATE` BEFORE UPDATE ON `ventas_pedidos` FOR EACH ROW BEGIN

SET new.cliente = (SELECT nombre FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET new.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
     SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='pedido' AND modulo='venta' LIMIT 0,1);
     UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='pedido' AND modulo='venta';
END IF;

#ACTUALIZA CANTIDAD EN INVENTARIO
#SET NEW.unidades_pendientes = (SELECT SUM(saldo_cantidad) FROM ventas_pedidos_inventario WHERE activo=1 AND id_pedido_venta=NEW.id);

SET NEW.id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pedidos_invertarioINSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pedidos_invertarioINSERT` BEFORE INSERT ON `ventas_pedidos_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    #IF ISNULL(NEW.valor_impuesto) THEN
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    #END IF;

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pedidos_invertarioUPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_pedidos_invertarioUPDATE` BEFORE UPDATE ON `ventas_pedidos_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    #SET NEW.saldo_cantidad=NEW.cantidad;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_INSERT` BEFORE INSERT ON `ventas_pos` FOR EACH ROW BEGIN

     IF NEW.tipo<>'restaurantes ' THEN
         UPDATE ventas_pos_consecutivos_caja SET estado='true'  WHERE activo=1 AND id_resolucion=NEW.id_configuracion_resolucion AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND consecutivo_final=NEW.consecutivo;
         UPDATE ventas_pos_configuracion SET estado='block'  WHERE activo=1 AND id=NEW.id_configuracion_resolucion AND id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND numero_final=NEW.consecutivo;
     END IF;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_configuracion_sucursales_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_configuracion_sucursales_INSERT` BEFORE INSERT ON `ventas_pos_configuracion_sucursales` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_configuracion_sucursales_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_configuracion_sucursales_UPDATE` BEFORE UPDATE ON `ventas_pos_configuracion_sucursales` FOR EACH ROW BEGIN

SET NEW.sucursal = (SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_consecutivos_caja_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_consecutivos_caja_INSERT` BEFORE INSERT ON `ventas_pos_consecutivos_caja` FOR EACH ROW BEGIN
   SET NEW.fecha=NOW();
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_consecutivos_liberados_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_consecutivos_liberados_INSERT` BEFORE INSERT ON `ventas_pos_consecutivos_liberados` FOR EACH ROW BEGIN

SET  NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_sucursal);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_inventarioINSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_inventarioINSERT` BEFORE INSERT ON `ventas_pos_inventario` FOR EACH ROW BEGIN

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_item LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_item LIMIT 0,1);
     SET NEW.codigo_barras=(SELECT code_bar FROM items WHERE id=NEW.id_item);
    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_item LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    IF ISNULL(NEW.valor_impuesto) THEN
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    END IF;
    
#    SET NEW.precio_venta = (SELECT precio_venta FROM items WHERE id=NEW.id_item LIMIT 0,1);
#    SET NEW.costo_inventario=(SELECT costos FROM items WHERE id=NEW.id_item LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_item LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET NEW.id_empresa = (SELECT id_empresa FROM ventas_pos WHERE id=NEW.id_pos);
    SET NEW.id_sucursal = (SELECT id_sucursal FROM ventas_pos WHERE id=NEW.id_pos);
    
   SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_item LIMIT 0,1);
 #  SET NEW.saldo_cantidad=NEW.cantidad;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_pos_secciones`;
DELIMITER ;;
CREATE TRIGGER `ventas_pos_secciones` BEFORE INSERT ON `ventas_pos_secciones` FOR EACH ROW BEGIN

SET @padding = (SELECT padding FROM ventas_pos_secciones WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_padre);


IF ISNULL(@padding) THEN
    SET @padding = 0;
END IF;

SET NEW.padding =@padding+10;

#SET NEW.descripcion_tipo = CONCAT("SELECT padding FROM informes_niif_formatos_secciones WHERE activo=1 AND id_empresa=",NEW.id_empresa," AND id_formato=",NEW.id_formato," AND codigo_seccion=",NEW.codigo_seccion_padre);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_remisiones_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_remisiones_INSERT` BEFORE INSERT ON `ventas_remisiones` FOR EACH ROW BEGIN

SET NEW.fecha_registro =now();
SET NEW.sucursal=(SELECT nombre FROM empresas_sucursales WHERE id=NEW.id_sucursal );
SET NEW.bodega=(SELECT nombre FROM empresas_sucursales_bodegas WHERE id=NEW.id_bodega );

SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

SET @id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);
IF @id_vendedor = '' OR ISNULL( @id_vendedor ) THEN
       SET NEW.id_vendedor=0;
ELSE
       SET NEW.id_vendedor=@id_vendedor ;
END IF;

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_remisiones_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_remisiones_UPDATE` BEFORE UPDATE ON `ventas_remisiones` FOR EACH ROW BEGIN

SET NEW.cliente = (SELECT nombre FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.cod_cliente =(SELECT codigo FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);
SET NEW.exento_iva = (SELECT exento_iva FROM terceros WHERE id=NEW.id_cliente LIMIT 0,1);

SET NEW.codigo_centro_costo = (SELECT codigo FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);
SET NEW.centro_costo = (SELECT nombre FROM centro_costos WHERE id=NEW.id_centro_costo AND id_empresa=NEW.id_empresa AND activo=1 LIMIT 0,1);

SET NEW.documento_usuario=(SELECT documento FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);
SET NEW.usuario=(SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND id=NEW.id_usuario);

#CONSECUTIVO DOCUMENTO
IF OLD.estado = 0 AND new.estado = 1 AND (OLD.consecutivo<1 OR ISNULL(OLD.consecutivo)) THEN
     SET NEW.consecutivo=(SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='remision' AND modulo='venta' LIMIT 0,1);
     UPDATE configuracion_consecutivos_documentos SET consecutivo =  NEW.consecutivo + 1 WHERE id_empresa=NEW.id_empresa AND id_sucursal=NEW.id_sucursal AND activo=1 AND documento='remision' AND modulo='venta';
END IF;

#ACTUALIZA CANTIDAD EN INVENTARIO
#SET NEW.pendientes_facturar = (SELECT SUM(saldo_cantidad) FROM ventas_remisiones_inventario WHERE activo=1 AND id_remision_venta=NEW.id);

SET @id_vendedor=(SELECT id FROM empleados WHERE activo=1 AND id_empresa=NEW.id_empresa AND documento=NEW.documento_vendedor);
IF @id_vendedor = '' OR ISNULL( @id_vendedor ) THEN
       SET NEW.id_vendedor=0;
ELSE
       SET NEW.id_vendedor=@id_vendedor ;
END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_remisiones_invertario_INSERT`;
DELIMITER ;;
CREATE TRIGGER `ventas_remisiones_invertario_INSERT` BEFORE INSERT ON `ventas_remisiones_inventario` FOR EACH ROW BEGIN

    DECLARE id_empresa_db INT;
    DECLARE id_sucursal_db INT;
    DECLARE id_bodega_db INT;
    
    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    #IF ISNULL(NEW.valor_impuesto) THEN
        SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    #END IF;
        
    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    IF NEW.tipo <> 'SIHO' THEN
           SET id_empresa_db = (SELECT id_empresa FROM ventas_remisiones WHERE id=NEW.id_remision_venta);
           SET id_sucursal_db = (SELECT id_sucursal FROM ventas_remisiones WHERE id=NEW.id_remision_venta);
           SET id_bodega_db = (SELECT id_bodega FROM ventas_remisiones WHERE id=NEW.id_remision_venta);
      
        SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=id_empresa_db AND id_sucursal=id_sucursal_db AND id_ubicacion=id_bodega_db AND activo=1 GROUP BY id LIMIT 0,1);

   END IF;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `ventas_remisiones_invertario_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `ventas_remisiones_invertario_UPDATE` BEFORE UPDATE ON `ventas_remisiones_inventario` FOR EACH ROW BEGIN

    DECLARE id_empresa_db INT;
    DECLARE id_sucursal_db INT;
    DECLARE id_bodega_db INT;

    SET NEW.codigo= (SELECT codigo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre= (SELECT nombre_equipo FROM items WHERE id=NEW.id_inventario LIMIT 0,1);

    SET NEW.id_impuesto=(SELECT id_impuesto FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.impuesto= (SELECT impuesto FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);
    SET NEW.valor_impuesto= (SELECT valor FROM impuestos WHERE id=NEW.id_impuesto LIMIT 0,1);

    SET NEW.inventariable = (SELECT inventariable FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.id_unidad_medida=(SELECT id_unidad_medida FROM items WHERE id=NEW.id_inventario LIMIT 0,1);
    SET NEW.nombre_unidad_medida=(SELECT nombre FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);
    SET NEW.cantidad_unidad_medida=(SELECT unidades FROM inventario_unidades WHERE id=NEW.id_unidad_medida LIMIT 0,1);

    SET id_empresa_db = (SELECT id_empresa FROM ventas_remisiones WHERE id=NEW.id_remision_venta);
    SET id_sucursal_db = (SELECT id_sucursal FROM ventas_remisiones WHERE id=NEW.id_remision_venta);
    SET id_bodega_db = (SELECT id_bodega FROM ventas_remisiones WHERE id=NEW.id_remision_venta);

    SET NEW.costo_inventario=(SELECT costos FROM inventario_totales WHERE id_item=NEW.id_inventario  AND id_empresa=id_empresa_db AND id_sucursal=id_sucursal_db AND id_ubicacion=id_bodega_db AND activo=1 GROUP BY id LIMIT 0,1);
  
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `web_service_metodo_INSERT_copy`;
DELIMITER ;;
CREATE TRIGGER `web_service_metodo_INSERT_copy` BEFORE INSERT ON `web_service_metodos` FOR EACH ROW BEGIN

SET NEW.software = (SELECT software FROM web_service_software WHERE id=NEW.id_software);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `web_service_metodo_UPDATE_copy`;
DELIMITER ;;
CREATE TRIGGER `web_service_metodo_UPDATE_copy` BEFORE UPDATE ON `web_service_metodos` FOR EACH ROW BEGIN

SET NEW.software = (SELECT software FROM web_service_software WHERE id=NEW.id_software);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `web_service_tercero_INSERT`;
DELIMITER ;;
CREATE TRIGGER `web_service_tercero_INSERT` BEFORE INSERT ON `web_service_tercero_causacion` FOR EACH ROW BEGIN

SET NEW.codigo = (SELECT codigo FROM terceros WHERE activo=1 AND id=NEW.id_tercero);
SET NEW.tipo_documento= (SELECT tipo_identificacion FROM terceros WHERE activo=1 AND id=NEW.id_tercero);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NEW.id_tercero);
SET NEW.tercero = (SELECT nombre FROM terceros WHERE activo=1 AND id=NEW.id_tercero);

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `web_service_tercero_UPDATE`;
DELIMITER ;;
CREATE TRIGGER `web_service_tercero_UPDATE` BEFORE UPDATE ON `web_service_tercero_causacion` FOR EACH ROW BEGIN

SET NEW.codigo = (SELECT codigo FROM terceros WHERE activo=1 AND id=NEW.id_tercero);
SET NEW.tipo_documento= (SELECT tipo_identificacion FROM terceros WHERE activo=1 AND id=NEW.id_tercero);
SET NEW.nit = (SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NEW.id_tercero);
SET NEW.tercero = (SELECT nombre FROM terceros WHERE activo=1 AND id=NEW.id_tercero);

END
;;
DELIMITER ;
