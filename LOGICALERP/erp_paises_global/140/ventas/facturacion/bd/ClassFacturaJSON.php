<?php
	// include("../../../../configuracion/conectar.php");
	// include("../../../../configuracion/define_variables.php");
	// include("../../../web_service/nuSoap/nusoap.php");

	/**
	*@class ClassFacturaJSON
	*/

	class ClassFacturaJSON{
    public $mysql;

  	function __construct($mysql){
  		$this->mysql = $mysql;
  	}

		public function obtenerDatos($codigoFactura,$id_empresa){
      //----------------- DATOS DE LA CABECERA DE LA FACTURA -----------------//
  		$sqlVentasFacturas = "SELECT
  														ventas_facturas.id,
															ventas_facturas.fecha_inicio,
															ventas_facturas.prefijo,
  														ventas_facturas.numero_factura,
  														ventas_facturas.nit,
															ventas_facturas.fecha_vencimiento,
															ventas_facturas.observacion,
															ventas_facturas.orden_compra,
															ventas_facturas.sucursal_cliente,
															ventas_facturas.sucursal,
															ventas_facturas.id_sucursal_cliente,
															ventas_facturas.nombre_vendedor,
															ventas_facturas.documento_vendedor,
															configuracion_metodos_pago.codigo_metodo_pago_dian
  													FROM
  														ventas_facturas
  													LEFT JOIN
  														ventas_facturas_configuracion
  													ON
  														ventas_facturas.id_configuracion_resolucion = ventas_facturas_configuracion.id
														LEFT JOIN
															configuracion_metodos_pago
														ON
															ventas_facturas.id_metodo_pago = configuracion_metodos_pago.id
  													WHERE
  														ventas_facturas.activo = 1
  													AND
  														ventas_facturas.estado = 1
  													AND
  														ventas_facturas.id = $codigoFactura
														AND
															ventas_facturas.id_empresa = '$id_empresa'";

      $queryVentasFacturas 		       			   = $this->mysql->query($sqlVentasFacturas,$this->mysql->link);

			if(!$queryVentasFacturas){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos principales de la factura.");' . '</script>';
				exit;
			}

      $this->idVF   											   = $this->mysql->result($queryVentasFacturas,0,'id');
			$this->fecha_inicioVF								   = $this->mysql->result($queryVentasFacturas,0,'fecha_inicio');
			$this->prefijoVF								       = $this->mysql->result($queryVentasFacturas,0,'prefijo');
      $this->numero_facturaVF 	 					   = $this->mysql->result($queryVentasFacturas,0,'numero_factura');
      $this->nitVF 												   = $this->mysql->result($queryVentasFacturas,0,'nit');
			$this->fecha_vencimientoVF						 = $this->mysql->result($queryVentasFacturas,0,'fecha_vencimiento');
			$this->observacionVF									 = $this->mysql->result($queryVentasFacturas,0,'observacion');
			$this->orden_compraVF									 = $this->mysql->result($queryVentasFacturas,0,'orden_compra');
			$this->sucursal_clienteVF							 = $this->mysql->result($queryVentasFacturas,0,'sucursal_cliente');
			$this->sucursalVF							 				 = $this->mysql->result($queryVentasFacturas,0,'sucursal');
			$this->id_sucursal_clienteVF  				 = $this->mysql->result($queryVentasFacturas,0,'id_sucursal_cliente');
			$this->nombre_vendedorVF			 				 = $this->mysql->result($queryVentasFacturas,0,'nombre_vendedor');
			$this->documento_vendedorVF						 = $this->mysql->result($queryVentasFacturas,0,'documento_vendedor');
			$this->codigo_metodo_pago_dianVF			 = $this->mysql->result($queryVentasFacturas,0,'codigo_metodo_pago_dian');

      //------------------- DATOS DEL EMISOR O LA EMPRESA --------------------//
      $sqlEmpresa =	 "SELECT
	                    	empresas.documento,
												empresas.tipo_regimen,
	                    	empresas.razon_social,
	                    	empresas.nombre,
	                    	empresas.direccion,
	                    	empresas.email,
	                    	empresas.departamento,
	                    	empresas.ciudad,
	                    	tipo_documento.codigo_tipo_documento_dian,
	                      ubicacion_pais.iso2 AS pais,
												configuracion_moneda.moneda
	                    FROM
	                    	empresas
	                    LEFT JOIN
	                    	tipo_documento
	                    ON
	                    	empresas.tipo_documento = tipo_documento.codigo
	                    LEFT JOIN
	                    	ubicacion_pais
	                    ON
	                    	empresas.id_pais = ubicacion_pais.id
											LEFT JOIN
												configuracion_moneda
											ON
												empresas.id_moneda = configuracion_moneda.id
	                    WHERE
	                    	empresas.id = '$id_empresa'
	                    GROUP BY
	                    	empresas.id";

      $queryEmpresa                          = $this->mysql->query($sqlEmpresa,$this->mysql->link);

			if(!$queryEmpresa){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos del emisor.");' . '</script>';
				exit;
			}

      $this->documentoE                      = $this->mysql->result($queryEmpresa,0,'documento');
			$this->tipo_regimenE									 = $this->mysql->result($queryEmpresa,0,'tipo_regimen');
      $this->razon_socialE                   = $this->mysql->result($queryEmpresa,0,'razon_social');
      $this->nombreE                         = $this->mysql->result($queryEmpresa,0,'nombre');
      $this->direccionE                      = $this->mysql->result($queryEmpresa,0,'direccion');
      $this->emailE                          = $this->mysql->result($queryEmpresa,0,'email');
      $this->departamentoE                   = $this->mysql->result($queryEmpresa,0,'departamento');
      $this->ciudadE                         = $this->mysql->result($queryEmpresa,0,'ciudad');
      $this->codigo_tipo_documento_dianE     = $this->mysql->result($queryEmpresa,0,'codigo_tipo_documento_dian');
      $this->paisE                           = $this->mysql->result($queryEmpresa,0,'pais');
			$this->monedaE												 = $this->mysql->result($queryEmpresa,0,'moneda');

      //--------------------- DATOS DEL TERCERO O CLIENTE --------------------//
      $sqlTerceros = "SELECT
												terceros.id,
                        terceros.id_tipo_persona_dian,
                        terceros.numero_identificacion,
                        terceros.nombre,
												terceros.nombre_comercial,
												terceros.email,
                        terceros.iso2 AS pais,
												terceros.sector_empresarial,
												terceros.exento_iva,
                        terceros_tributario.codigo_regimen_dian,
                        tipo_documento.codigo_tipo_documento_dian
                      FROM
                        terceros
                      LEFT JOIN
                        terceros_tributario
                      ON
                        terceros.id_tercero_tributario = terceros_tributario.id
                      LEFT JOIN
                        tipo_documento
                      ON
                        terceros.id_tipo_identificacion = tipo_documento.id
                      WHERE
                        terceros.activo = 1
                      AND
                        terceros.numero_identificacion = '$this->nitVF'
                      AND
                        terceros.id_empresa = '$id_empresa'";

      $queryTerceros           			         = $this->mysql->query($sqlTerceros,$this->mysql->link);

			if(!$queryTerceros){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos del cliente.");' . '</script>';
				exit;
			}

			$this->idT														 = $this->mysql->result($queryTerceros,0,'id');
      $this->id_tipo_persona_dianT   			   = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
      $this->numero_identificacionT  			   = $this->mysql->result($queryTerceros,0,'numero_identificacion');
			$this->nombreT											   = $this->mysql->result($queryTerceros,0,'nombre');
      $this->nombre_comercialT						   = $this->mysql->result($queryTerceros,0,'nombre_comercial');
      $this->emailT                 			   = $this->mysql->result($queryTerceros,0,'email');
      $this->paisT                   			   = $this->mysql->result($queryTerceros,0,'pais');
			$this->sector_empresarialT						 = $this->mysql->result($queryTerceros,0,'sector_empresarial');
			$this->exento_ivaT										 = $this->mysql->result($queryTerceros,0,'exento_iva');
      $this->codigo_regimen_dianT    			   = $this->mysql->result($queryTerceros,0,'codigo_regimen_dian');
      $this->codigo_tipo_documento_dianT 	   = $this->mysql->result($queryTerceros,0,'codigo_tipo_documento_dian');

			$sqlTercerosDireccion  = "SELECT
																	id,
																	direccion,
																	ciudad,
																	departamento,
																	telefono1
																FROM
																	terceros_direcciones
																WHERE
																	id = '$this->id_sucursal_clienteVF'
																AND
																	id_tercero = '$this->idT'
																AND
																	activo = 1
																LIMIT
																	0,1";

			$queryTercerosDireccion   = $this->mysql->query($sqlTercerosDireccion,$this->mysql->link);

			if(!$queryTercerosDireccion){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron las direcciones del cliente.");' . '</script>';
				exit;
			}

			$this->idTD								= $this->mysql->result($queryTercerosDireccion,0,'id');
			$this->ciudadTD    				= $this->mysql->result($queryTercerosDireccion,0,'ciudad');
			$this->departamentoTD 		= $this->mysql->result($queryTercerosDireccion,0,'departamento');
			$this->direccionTD 				= $this->mysql->result($queryTercerosDireccion,0,'direccion');
			$this->telefono1TD  			= $this->mysql->result($queryTercerosDireccion,0,'telefono1');

			$sqlTercerosDireccionesEmail = "SELECT
																				terceros_direcciones_email.email
																			FROM
																				terceros_direcciones_email
																			LEFT JOIN
																				terceros_direcciones
																			ON
																				terceros_direcciones.id = terceros_direcciones_email.id_direccion
																			LEFT JOIN
																				terceros
																			ON
																				terceros.id = terceros_direcciones.id_tercero
																			WHERE
																				terceros_direcciones_email.activo = 1
																			AND
																				terceros_direcciones_email.id_direccion = '$this->idTD'
																			AND
																				terceros.activo = 1
																			AND
																				terceros.id = $this->idT";

			$queryTercerosDireccionesEmail				 = $this->mysql->query($sqlTercerosDireccionesEmail,$this->mysql->link);

			if(!$queryTercerosDireccionesEmail){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los correos electronicos del cliente.");' . '</script>';
				exit;
			}

			$this->contTercerosDireccionesEmail 	 = $this->mysql->num_rows($queryTercerosDireccionesEmail);
			for($i = 0; $i < $this->contTercerosDireccionesEmail; $i++){
				$this->emailTDE[$i] = $this->mysql->result($queryTercerosDireccionesEmail,$i,'email');
			}

			//---------------------- DATOS DE LAS RETENCIONES ----------------------//
			$sqlVentasFacturasRetenciones =  "SELECT
																					ventas_facturas_retenciones.valor,
																					ventas_facturas_retenciones.base,
																					ventas_facturas_retenciones.tipo_retencion
																				FROM
																					ventas_facturas_retenciones
																				LEFT JOIN
																					ventas_facturas
																				ON
																					ventas_facturas_retenciones.id_factura_venta = ventas_facturas.id
																				WHERE
																					ventas_facturas_retenciones.activo = 1
																				AND
																					ventas_facturas_retenciones.id_factura_venta = $this->idVF";

	    $queryVentasFacturasRetenciones = $this->mysql->query($sqlVentasFacturasRetenciones,$this->mysql->link);

			if(!$queryVentasFacturasRetenciones){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron las retenciones de la factura.");' . '</script>';
				exit;
			}

			//Contamos el numero de retenciones que posee la factura
			$this->contRetenciones = $this->mysql->num_rows($queryVentasFacturasRetenciones);
			for($i = 0; $i < $this->contRetenciones; $i++){
				$this->valorVFR[$i] 					= $this->mysql->result($queryVentasFacturasRetenciones,$i,'valor');
				$this->baseVFR[$i]  					= $this->mysql->result($queryVentasFacturasRetenciones,$i,'base');
				$this->tipo_retencionVFR[$i]  = $this->mysql->result($queryVentasFacturasRetenciones,$i,'tipo_retencion');
			}

			//----------------------- DATOS DE lOS ARTICULOS -----------------------//
			$sqlVentasFacturasInventario = "SELECT
																				ventas_facturas_inventario.codigo,
																				ventas_facturas_inventario.cantidad,
																				ventas_facturas_inventario.nombre,
																				ventas_facturas_inventario.costo_unitario,
																				ventas_facturas_inventario.observaciones,
																				ventas_facturas_inventario.tipo_descuento,
																				ventas_facturas_inventario.descuento,
																				ventas_facturas_inventario.impuesto,
																				ventas_facturas_inventario.valor_impuesto,
																				impuestos.codigo_impuesto_dian
																			FROM
																				ventas_facturas_inventario
																			LEFT JOIN
																				ventas_facturas
																			ON
																				ventas_facturas_inventario.id_factura_venta = ventas_facturas.id
																			LEFT JOIN
																				impuestos
																			ON
																				impuestos.id = ventas_facturas_inventario.id_impuesto
																			LEFT JOIN
																			 	ventas_facturas_inventario_grupos
																			ON
																				ventas_facturas_inventario_grupos.id_inventario_factura_venta = ventas_facturas_inventario.id
																			WHERE
																				ventas_facturas_inventario.activo = 1
																			AND
																				ventas_facturas_inventario.id_factura_venta = $this->idVF
																			AND
																				ventas_facturas_inventario_grupos.id_inventario_factura_venta IS NULL
																			AND
																				ventas_facturas_inventario.id_empresa = '$id_empresa'";

			$queryVentasFacturasInventario = $this->mysql->query($sqlVentasFacturasInventario,$this->mysql->link);

			if(!$queryVentasFacturasInventario){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los articulos de la factura.");' . '</script>';
				exit;
			}

			//Contamos el numero de articulos que posee la factura
			$this->contArticulos = $this->mysql->num_rows($queryVentasFacturasInventario);
			for($i = 0; $i < $this->contArticulos; $i++){
				$this->codigoVFI[$i]     						 = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo');
				$this->cantidadVFI[$i] 							 = $this->mysql->result($queryVentasFacturasInventario,$i,'cantidad');
				$this->nombreVFI[$i] 								 = $this->mysql->result($queryVentasFacturasInventario,$i,'nombre');
				$this->costo_unitarioVFI[$i] 				 = $this->mysql->result($queryVentasFacturasInventario,$i,'costo_unitario');
				$this->observacionesVFI[$i]					 = $this->mysql->result($queryVentasFacturasInventario,$i,'observaciones');
				$this->tipo_descuentoVFI[$i] 				 = $this->mysql->result($queryVentasFacturasInventario,$i,'tipo_descuento');
				$this->descuentoVFI[$i] 		 				 = $this->mysql->result($queryVentasFacturasInventario,$i,'descuento');
				$this->impuestoVFI[$i] 				       = $this->mysql->result($queryVentasFacturasInventario,$i,'impuesto');
				$this->valor_impuestoVFI[$i] 				 = $this->mysql->result($queryVentasFacturasInventario,$i,'valor_impuesto');
				$this->codigo_impuesto_dianVFI[$i] 	 = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo_impuesto_dian');
			}

			for($i = 0; $i < $this->contArticulos; $i++){
				//Buscamos primero si el articulo tiene o no descuento
				if($this->descuentoVFI[$i] != 0){
					if($this->tipo_descuentoVFI[$i] == "porcentaje"){
						$this->costo_subtotalVFI[$i] = ($this->cantidadVFI[$i] * $this->costo_unitarioVFI[$i]) - ($this->cantidadVFI[$i] * $this->costo_unitarioVFI[$i] * $this->descuentoVFI[$i] / 100);
						$this->descuento_itemVFI[$i] = ($this->cantidadVFI[$i] * $this->costo_unitarioVFI[$i] * $this->descuentoVFI[$i] / 100);
					} else if($this->tipo_descuentoVFI[$i] == "pesos"){
						$this->costo_subtotalVFI[$i] = ($this->cantidadVFI[$i] * $this->costo_unitarioVFI[$i]) - $this->descuentoVFI[$i];
						$this->descuento_itemVFI[$i] = $this->descuentoVFI[$i];
					}
				} else{
					$this->costo_subtotalVFI[$i] = $this->cantidadVFI[$i] * $this->costo_unitarioVFI[$i];
					$this->descuento_itemVFI[$i] = 0.00;
				}
				//Buscamos si el tercero esta o no exento de IVA
				if($this->exento_ivaT == "Si"){
					$this->costo_impuestoVFI[$i] = 0;
				}
				else{
					if($this->valor_impuestoVFI[$i] != null && ($this->impuestoVFI[$i] != "" || $this->impuestoVFI[$i] != null)){
						$this->costo_impuestoVFI[$i] = $this->costo_subtotalVFI[$i] * $this->valor_impuestoVFI[$i] / 100;
					}
				}
			}

			for($i = 0; $i < $this->contArticulos; $i++){
				if($this->valor_impuestoVFI[$i] != null && ($this->impuestoVFI[$i] != "" || $this->impuestoVFI[$i] != null)){
					$this->arrayImpuestos[$this->codigo_impuesto_dianVFI[$i]][$this->valor_impuestoVFI[$i]] += $this->costo_impuestoVFI[$i];
				}
			}

			//---------------- DATOS DE LOS GRUPOS DE LOS ARTICULOS ----------------//
			$sqlVentasFacturasInventarioGrupos = "SELECT
																							ventas_facturas_grupos.codigo,
																							ventas_facturas_grupos.cantidad,
																							ventas_facturas_grupos.nombre,
																							ventas_facturas_grupos.costo_unitario,
																							ventas_facturas_grupos.observaciones,
																							ventas_facturas_grupos.descuento,
																							ventas_facturas_grupos.nombre_impuesto,
																							ventas_facturas_grupos.porcentaje_impuesto,
																							impuestos.codigo_impuesto_dian
																						FROM
																							ventas_facturas_grupos
																						LEFT JOIN
																							impuestos
																						ON
																							impuestos.id = ventas_facturas_grupos.id_impuesto
																						WHERE
																							ventas_facturas_grupos.activo = 1
																						AND
																							ventas_facturas_grupos.id_empresa = '$id_empresa'
																						AND
																							ventas_facturas_grupos.id_factura_venta = $this->idVF";

			$queryVentasFacturasInventarioGrupos = $this->mysql->query($sqlVentasFacturasInventarioGrupos,$this->mysql->link);

			if(!$queryVentasFacturasInventarioGrupos){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los grupos de articulos de la factura.");' . '</script>';
				exit;
			}

			//Contamos el numero de grupos que posee la factura
			$this->contGruposArticulos = $this->mysql->num_rows($queryVentasFacturasInventarioGrupos);

			for($i = 0; $i < $this->contGruposArticulos; $i++){
				$this->codigoVFIG[$i]								= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'codigo');
				$this->cantidadVFIG[$i]							= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'cantidad');
				$this->nombreVFIG[$i]								= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'nombre');
				$this->costo_unitarioVFIG[$i]				= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'costo_unitario');
				$this->observacionesVFIG[$i]				= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'observaciones');
				$this->descuentoVFIG[$i]						= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'descuento');
				$this->nombre_impuestoVFIG[$i]			= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'nombre_impuesto');
				$this->porcentaje_impuestoVFIG[$i]	= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'porcentaje_impuesto');
				$this->codigo_impuesto_dianVFIG[$i] = $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'codigo_impuesto_dian');
			}

			//Buscamos primero si el articulo tiene o no descuento
			for($i = 0; $i < $this->contGruposArticulos; $i++){
				if($this->descuentoVFIG[$i] != 0){
					$this->costo_subtotalVFIG[$i] = ($this->cantidadVFIG[$i] * $this->costo_unitarioVFIG[$i]) - $this->descuentoVFIG[$i];
				} else{
					$this->costo_subtotalVFIG[$i] = $this->cantidadVFIG[$i] * $this->costo_unitarioVFIG[$i];
				}
				//Buscamos si el tercero esta o no exento de IVA
        if($this->exento_ivaT == "Si"){
          $this->costo_impuestoVFIG[$i] = 0;
        }
        else{
					if($this->porcentaje_impuestoVFIG[$i] != null && ($this->nombre_impuestoVFIG[$i] != "" || $this->nombre_impuestoVFIG[$i] != null)){
					  $this->costo_impuestoVFIG[$i] = $this->costo_subtotalVFIG[$i] * $this->porcentaje_impuestoVFIG[$i] / 100;
					}
        }
			}

			for($i = 0; $i < $this->contGruposArticulos; $i++){
				if($this->porcentaje_impuestoVFIG[$i] != null && ($this->nombre_impuestoVFIG[$i] != "" || $this->nombre_impuestoVFIG[$i] != null)){
					$this->arrayImpuestos[$this->codigo_impuesto_dianVFIG[$i]][$this->porcentaje_impuestoVFIG[$i]] += $this->costo_impuestoVFIG[$i];
				}
			}
		}

		public function quitarTildes($cadena){
			$caracterEspecial = array("\t","\r","\n",chr(160));
			$originales  = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ°ª&º/';
	    $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo-';
	    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
			$cadena = str_replace($caracterEspecial,"",$cadena);
	    return utf8_encode($cadena);
		}

    public function construirJSON(){
			$arrayDetalle 					= Array(); 		//Se crea un arreglo que contenga todos los articulos y grupos de articulos
			$arrayImpuesto 					= Array();		//Se crea un arreglo que contenga todos los impuestos y retenciones
			$arrayTercerosEmail 		= Array();		//Se crea un arreglo que contenga los email de los terceros
			$subTotal 							= array_sum($this->costo_subtotalVFI)
															+ array_sum($this->costo_subtotalVFIG); //Variable que contendra el costo del subtotal de toda la factura

			//------------------- ARTICULOS Y GRUPOS DE ARTICULOS ------------------//
			for($i = 0; $i < $this->contArticulos; $i++){
				if($this->impuestoVFI[$i] != null && $this->exento_ivaT == "No"){
					$arrayImpuestoItem[$i] =  [array(
																					"Impuesto"    => $this->codigo_impuesto_dianVFI[$i],
																					"Porcentaje"  => ($this->valor_impuestoVFI[$i] != null)? (float) $this->valor_impuestoVFI[$i] : "",
																					"TotalImp"    => round($this->costo_impuestoVFI[$i],$_SESSION['DECIMALESMONEDA'])
																			 )];
				}

				//Creamos una variable que contenga el total del item
				$this->totalItemVFI[$i] = $this->costo_subtotalVFI[$i] + $this->costo_impuestoVFI[$i];

				$arrayDetalle[] =	 array(
																	"Nombre"    		=> $this->quitarTildes($this->nombreVFI[$i]),
																	"Cantidad"  		=> $this->cantidadVFI[$i],
																	"ValorUnitario"	=> (float) $this->costo_unitarioVFI[$i],
																	"Subtotal"  		=> $this->costo_subtotalVFI[$i],
																	"Total"     		=> round($this->totalItemVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"Codigo"    		=> (int) $this->codigoVFI[$i],
																	"Impuestos" 		=> ($arrayImpuestoItem[$i] != null)? $arrayImpuestoItem[$i] : "",
																	"Descripcion" 	=> [array(
																															"Nombre"  => "Descuento",
																															"Valor" 	=> round($this->descuento_itemVFI[$i],$_SESSION['DECIMALESMONEDA'])
																													 ),
																											array(
																															"Nombre"	=> "Observaciones",
																															"Valor"		=> ($this->observacionesVFI[$i] != null)? $this->quitarTildes($this->observacionesVFI[$i]) : ""
																											     )]
																);
			}

			for($i = 0; $i < $this->contGruposArticulos; $i++){
				if($this->nombre_impuestoVFIG[$i] != null && $this->exento_ivaT == "No"){
					$arrayImpuestoItemGrupo[$i] = [array(
																							"Impuesto"    => $this->codigo_impuesto_dianVFIG[$i],
																							"Porcentaje"  => ($this->porcentaje_impuestoVFIG[$i] != null)? (float) $this->porcentaje_impuestoVFIG[$i] : "",
																							"TotalImp"    => round($this->costo_impuestoVFIG[$i],$_SESSION['DECIMALESMONEDA'])
																					 )];
				}

				//Creamos una variable que contenga el total del item
				$this->totalItemVFIG[$i] = $this->costo_subtotalVFIG[$i] + $this->costo_impuestoVFIG[$i];

				$arrayDetalle[] =	 array(
																	"Nombre"    		=> $this->quitarTildes($this->nombreVFIG[$i]),
																	"Cantidad"  		=> $this->cantidadVFIG[$i],
																	"ValorUnitario"	=> (float) $this->costo_unitarioVFIG[$i],
																	"Subtotal"  		=> $this->costo_subtotalVFIG[$i],
																	"Total"     		=> round($this->totalItemVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Codigo"    		=> (int) $this->codigoVFIG[$i],
																	"Impuestos" 		=> ($arrayImpuestoItemGrupo[$i] != null)? $arrayImpuestoItemGrupo[$i] : "",
																	"Descripcion" 	=> [array(
																															"Nombre"  => "Descuento",
																															"Valor"  	=> round($this->descuentoVFIG[$i],$_SESSION['DECIMALESMONEDA'])
																													 ),
																											array(
																															"Nombre"  => "Observaciones",
																															"Valor"  	=> ($this->observacionesVFIG[$i] != null)? $this->quitarTildes($this->observacionesVFIG[$i]) : ""
																													 )]
																);
			}

			//------------------------------ IMPUESTOS -----------------------------//
			if($this->exento_ivaT == "No" && ($arrayImpuestoItem != null || $arrayImpuestoItemGrupo != null)){
				foreach($this->arrayImpuestos as $codigoDian => $resultCodigoDian){
					foreach($resultCodigoDian as $porcentajeImpuesto => $resultPorcentajeImpuesto){
							$arrayImpuesto[] = array(
																				"Impuesto" 		=> $codigoDian,
																				"Porcentaje" 	=> (float) $porcentajeImpuesto / 1,
																				"TotalImp"    => round($resultPorcentajeImpuesto,$_SESSION['DECIMALESMONEDA'])
																			);
							if($codigoDian == "01"){
								$codigoIVA[] = $resultPorcentajeImpuesto;
							}
					}
				}
			}
      else{
        $arrayImpuesto[] = array(
                                  "Impuesto"    => "03",
                                  "Porcentaje"  => 0,
                                  "TotalImp"    => 0
                                );
      }

			//------------------------------ TOTAL IVA -----------------------------//
			$totalIVA = array_sum($codigoIVA);

			//----------------------------- RETENCIONES ----------------------------//
			for($i = 0; $i < $this->contRetenciones; $i++){
				if($subTotal > $this->baseVFR[$i]){
					if($this->tipo_retencionVFR[$i] == "ReteFuente"){
						$arrayImpuesto[] = array(
																			"Impuesto" 		=> "05",
																			"Porcentaje" 	=> (float) $this->valorVFR[$i] / 1,
																			"TotalImp"    => round(($subTotal * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		);
						$totalRetencionesVF += ($subTotal * $this->valorVFR[$i] / 100);
					} else if($this->tipo_retencionVFR[$i] == "ReteIva"){
						if($totalIVA > $this->baseVFR[$i]){
							$arrayImpuesto[] = array(
																				"Impuesto" 		=> "06",
																				"Porcentaje" 	=> (float) $this->valorVFR[$i] / 1,
																				"TotalImp"    => round(($totalIVA * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		  );
							$totalRetencionesVF += ($totalIVA * $this->valorVFR[$i] / 100);
						}
					} else if($this->tipo_retencionVFR[$i] == "ReteIca"){
						$arrayImpuesto[] = array(
																			"Impuesto" 		=> "07",
																			"Porcentaje" 	=> (float) $this->valorVFR[$i] / 1,
																			"TotalImp"    => round(($subTotal * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																	  );
						$totalRetencionesVF += ($subTotal * $this->valorVFR[$i] / 100);
					}
				}
			}

			//---------------------------- TOTAL FACTURA ---------------------------//
			$totalVF = round((array_sum($this->totalItemVFI) + array_sum($this->totalItemVFIG)),$_SESSION['DECIMALESMONEDA']) - round($totalRetencionesVF,$_SESSION['DECIMALESMONEDA']);

			//--------------------------- TERCEROS EMAIL'S -------------------------//
			for($i = 0; $i < $this->contTercerosDireccionesEmail; $i++){
				$arrayTercerosEmail[$i] = $this->emailTDE[$i];
			}

			$emails = implode(',',$arrayTercerosEmail);

			//------------------------- PREFIJO FACTURACION ------------------------//
			if(strlen($this->prefijoVF) < 4){
				$prefijo_diferencia = (4 - strlen($this->prefijoVF));
				for($i = 0; $i < $prefijo_diferencia; $i++){
					$this->prefijoVF .= " ";
				}
			}

			//--------------------------- ARRAY PRINCIPAL --------------------------//
      $arrayPrincipal = array(
        "Comprobante" => array(
                                "TipoComprobante"		  => "01",
                                "Fecha"               => $this->fecha_inicioVF,
                                "Serie"               => ($this->prefijoVF != null)? $this->prefijoVF : "",
                                "Folio"               => $this->numero_facturaVF,
                                "Moneda"              => $this->monedaE,
                                "Referencia"          => "",
                                "ConceptoRef"         => "",
																"Observaciones"				=> ($this->observacionVF != null)? $this->quitarTildes($this->observacionVF) : "",
                                "Descripcion" 				=> [array(
																																	"Nombre"  => "Fecha Vencimiento",
																																	"Valor"   => $this->fecha_vencimientoVF
																																),
																													array(
				                                                        	"Nombre" => "Orden Compra Cliente",
				                                                        	"Valor"  => ($this->orden_compraVF != null)? $this->quitarTildes($this->orden_compraVF) : ""
				                                                       ),
																											 		array(
				                                                        	"Nombre" => "Sucursal Factura",
				                                                        	"Valor"  => ($this->sucursalVF != null)? $this->quitarTildes($this->sucursalVF) : ""
				                                                       ),
																												  array(
																																	"Nombre" => "Sucursal Cliente",
																																	"Valor"  => ($this->sucursal_clienteVF != null)? $this->quitarTildes($this->sucursal_clienteVF) : ""
																															 ),
																													array(
																																	"Nombre" => "Nombre Vendedor",
																																	"Valor"  => $this->quitarTildes($this->nombre_vendedorVF)
																															 ),
																													array(
																																	"Nombre" => "Documento Vendedor",
																																	"Valor"  => $this->documento_vendedorVF
																														   )],
                                "MetodoPago"  				=> [array(
				                                                        	"Codigo" => $this->codigo_metodo_pago_dianVF,
				                                                        	"Valor"  => $totalVF
				                                                       )]
                              ),
        "Emisor" =>  array(
                            "Identificacion"      => str_replace(array(".","-"),"",$this->documentoE),
                            "TipoIdentificacion"  => $this->codigo_tipo_documento_dianE,
                            "RazonSocial"         => $this->quitarTildes($this->razon_socialE),
                            "NombreComercial"     => $this->quitarTildes($this->nombreE),
                            "Direccion"           => $this->quitarTildes($this->direccionE),
                            "Pais"                => $this->quitarTildes($this->paisE),
                            "email"               => $this->quitarTildes($this->emailE),
                            "Department"          => $this->quitarTildes($this->departamentoE),
                            "CitySubdivisionName" => $this->quitarTildes($this->ciudadE),
                            "CityName"            => $this->quitarTildes($this->ciudadE),
				                    "Descripcion"         => [array(
					                                                    "Nombre" => "Tipo De Regimen",
					                                                    "Valor"  => $this->quitarTildes($this->tipo_regimenE)
				                                                   )]
                          ),
        "Receptor" =>  array(
                              "Identificacion"      => str_replace(array(".","-"),"",$this->numero_identificacionT),
                              "TipoIdentificacion"  => $this->codigo_tipo_documento_dianT,
                              "RazonSocial"         => $this->quitarTildes($this->nombreT),
                              "NombreComercial"     => $this->quitarTildes($this->nombre_comercialT),
                              "Direccion"           => $this->quitarTildes($this->direccionTD),
                              "Pais"                => $this->quitarTildes($this->paisT),
                              "email"               => $emails,
                              "Department"          => $this->quitarTildes($this->departamentoTD),
                              "CitySubdivisionName" => $this->quitarTildes($this->ciudadTD),
                              "CityName"            => $this->quitarTildes($this->ciudadTD),
                              "Descripcion" 				=> [array(
					                                                      "Nombre" => "Sector Empresarial",
					                                                      "Valor"  => ($this->sector_empresarialT != null)? $this->quitarTildes($this->sector_empresarialT) : ""
				                                                     ),
																											  array(
																																"Nombre" => "Telefono",
																																"Valor"	 => ($this->telefono1TD != null)? $this->telefono1TD : ""
																														 )]
                            ),
        "Detalles" => $arrayDetalle,
        "Totales" => array(
                            "Total"       => $totalVF,
                            "SubTotal"    => round($subTotal,$_SESSION['DECIMALESMONEDA']),
														"IVA"					=> ($totalIVA != null)? round($totalIVA,$_SESSION['DECIMALESMONEDA']) : 0,
                            "Impuestos"   => $arrayImpuesto
                          ),
        "DetallesComprobante" => [array(
	                                        "Nombre"  => "",
	                                        "Valor"   => ""
	                                     )]
      );

      $this->arrayFinal = json_encode($arrayPrincipal, JSON_PRETTY_PRINT);
			$this->envioJSON = "<JsonId xmlns='http://tempuri.org/'>
														<json>$this->arrayFinal</json>
													</JsonId>";
			// echo json_last_error_msg();
    }

		public function debugJSON($json){
			$debugJSON = json_decode($json,true);
			foreach($debugJSON as $primerIndice => $primerValor){ //Entra al primer grupo de tags
				foreach($primerValor as $segundoIndice => $segundoValor){ //Entra al segundo grupo de tags
					if(is_array($segundoValor)){ //Se recorre el segundo grupo de tags si es un array
						foreach($segundoValor as $tercerIndice => $tercerValor){ //Entra al tercer grupo de tags
							foreach ($tercerValor as $cuartoIndice => $cuartoValor){ //Entra al cuarto grupo de tags
								if($cuartoValor === null){ //Evaluamos si el contenido del tag esta vacio
									echo "<script>console.log('El valor contenido en $primerIndice => $segundoIndice => $cuartoIndice esta nulo');</script>";
								}
							}
						}
					} else{ //Sino es un array no lo recorremos
						if($segundoValor === null){ //Evaluamos si el contenido del tag esta vacio
							echo "<script>console.log('El valor contenido en $primerIndice => $segundoIndice esta nulo');</script>";
						}
					}
				}
			}
		}

    public function enviarJSON(){
			$server_name = $_SERVER['SERVER_NAME'];

			if($server_name == "logicalerp.localhost"){
				$url_web_service = "http://test.facse.net/conexion/comprobante.asmx?WSDL";
			}
			else{
				$url_web_service = "http://app.facse.net/conexion/comprobante.asmx?WSDL";
			}

			// $client = new nusoap_client("http://test.facse.net/conexion/comprobante.asmx?WSDL", TRUE);
			$client  = new nusoap_client($url_web_service, TRUE, FALSE, FALSE, FALSE, FALSE, 0, 600);
			$client->soap_defencoding = 'UTF-8';
			$success = $client->call('JsonId',$this->envioJSON);
			$error 	 = $client->getError();

			//SI EXISTE ALGUN ERROR EN EL ENVIO
			if($error){
				$this->debugJSON($this->arrayFinal);
				echo "<script>
								console.log('$error');
							</script>";
				return $error;
			}
			//SI NO EXISTE NINGUN ERROR EN EL ENVIO
			else{
				$recibidoJSON 	= $this->quitarTildes($success["JsonIdResult"]);
				$respuestaJSON 	= json_decode($recibidoJSON,true);
				//SI LA RESPUESTA DEL WEB SERVICE ES VACIA
				if($respuestaJSON["Data"]["SOAP-ENV:Envelope"]["SOAP-ENV:Body"]["ns2:EnvioFacturaElectronicaRespuesta"]["ns2:Comments"] == ""){
					echo "<script>
									console.log(JSON.stringify($recibidoJSON));
								</script>";
				}
				//SI LA RESPUESTA DEL WEB SERVICE NO ES VACIA
				else{
					//VERIFICAMOS SI LA FACTURA LLEGA POR PRIMERA VEZ Y GUARDAMOS SUS DATOS EN LA DB
					if(is_array($respuestaJSON["Data"])){
						$respuesta["id_factura"] = $respuestaJSON["ContentType"];
						$respuesta["comentario"] = $respuestaJSON["Data"]["SOAP-ENV:Envelope"]["SOAP-ENV:Body"]["ns2:EnvioFacturaElectronicaRespuesta"]["ns2:Comments"];
						return $respuesta;
					}
					//SI NO ES LA PRIMERA VEZ QUE SE ENVIA
					else{
						$cadena_de_texto = $respuestaJSON["Data"];
						$cadena_buscada  = 'ocumento ya Registrado';
						$posicion_coincidencia = strpos($cadena_de_texto,$cadena_buscada);

						//VERIFICAMOS SI EL DOCUMENTO YA HA SIDO REGISTRADO
						if($posicion_coincidencia == true){
							$id_factura_facse = substr($respuestaJSON["Data"],28);
							$respuesta["id_factura"] = $id_factura_facse;
							$respuesta["comentario"] = "Ejemplar recibido exitosamente pasara a verificacion";
						}
						//SI RECIBIMOS ALGUN ERROR
						else{
							$respuesta["id_factura"] = 0;
							$respuesta["comentario"] = $respuestaJSON["Data"];
						}
					}

					return $respuesta;
				}
			}
		}

		public function imprimirJSON(){
			return $this->arrayFinal;
		}
	}

	// Instanciamos el objeto y llamamos los metodos para enviar la factura
	// $facturaJSON = new ClassFacturaJSON($mysql);
	// $facturaJSON->obtenerDatos(146102,47);
	// $facturaJSON->construirJSON();
	// $facturaJSON->enviarJSON()
?>
