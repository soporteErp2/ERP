<?php
	/**
	*@class ClassFacturaJSON_V2
	*/
	class ClassFacturaJSON_V2{
    public $mysql;

  	function __construct($mysql){
  		$this->mysql = $mysql;
  	}

		public function obtenerDatos($codigoFactura,$id_empresa){
      //----------------- DATOS DE LA CABECERA DE LA FACTURA -----------------//
  		$sqlVentasFacturas = "SELECT
  														VF.id,
															VF.fecha_inicio,
															VF.prefijo,
  														VF.numero_factura,
  														VF.nit,
															VF.fecha_vencimiento,
															VF.observacion,
															VF.orden_compra,
															VF.sucursal_cliente,
															VF.id_sucursal,
															VF.sucursal,
															VF.id_sucursal_cliente,
															VF.nombre_vendedor,
															VF.documento_vendedor,
															VF.dias_pago,
															CMP.codigo_metodo_pago_dian,
															CMP.nombre AS nombre_metodo_pago_dian
  													FROM
  														ventas_facturas AS VF
  													LEFT JOIN
  														ventas_facturas_configuracion AS VFC
  													ON
  														VF.id_configuracion_resolucion = VFC.id
														LEFT JOIN
															configuracion_metodos_pago AS CMP
														ON
															VF.id_metodo_pago = CMP.id
  													WHERE
  														VF.activo = 1
  													AND
  														VF.estado = 1
  													AND
  														VF.id = $codigoFactura
														AND
															VF.id_empresa = '$id_empresa'";

      $queryVentasFacturas = $this->mysql->query($sqlVentasFacturas,$this->mysql->link);

			if(!$queryVentasFacturas){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos principales de la factura.");' . '</script>';
				exit;
			}

      $this->idVF   									 = $this->mysql->result($queryVentasFacturas,0,'id');
			$this->fecha_inicioVF						 = $this->mysql->result($queryVentasFacturas,0,'fecha_inicio');
			$this->prefijoVF								 = $this->mysql->result($queryVentasFacturas,0,'prefijo');
      $this->numero_facturaVF 	 			 = $this->mysql->result($queryVentasFacturas,0,'numero_factura');
      $this->nitVF 										 = $this->mysql->result($queryVentasFacturas,0,'nit');
			$this->fecha_vencimientoVF			 = $this->mysql->result($queryVentasFacturas,0,'fecha_vencimiento');
			$this->observacionVF						 = $this->mysql->result($queryVentasFacturas,0,'observacion');
			$this->orden_compraVF						 = $this->mysql->result($queryVentasFacturas,0,'orden_compra');
			$this->sucursal_clienteVF				 = $this->mysql->result($queryVentasFacturas,0,'sucursal_cliente');
			$this->id_sucursalVF    				 = $this->mysql->result($queryVentasFacturas,0,'id_sucursal');
			$this->sucursalVF							 	 = $this->mysql->result($queryVentasFacturas,0,'sucursal');
			$this->id_sucursal_clienteVF  	 = $this->mysql->result($queryVentasFacturas,0,'id_sucursal_cliente');
			$this->nombre_vendedorVF			 	 = $this->mysql->result($queryVentasFacturas,0,'nombre_vendedor');
			$this->documento_vendedorVF			 = $this->mysql->result($queryVentasFacturas,0,'documento_vendedor');
			$this->dias_pagoVF   			       = $this->mysql->result($queryVentasFacturas,0,'dias_pago');
			$this->codigo_metodo_pago_dianVF = $this->mysql->result($queryVentasFacturas,0,'codigo_metodo_pago_dian');
			$this->nombre_metodo_pago_dianVF = $this->mysql->result($queryVentasFacturas,0,'nombre_metodo_pago_dian');

      //------------------- DATOS DEL EMISOR O LA EMPRESA --------------------//
      $sqlEmpresa =	 "SELECT
	                    	E.documento,
												E.digito_verificacion,
												E.tipo_regimen,
	                    	E.razon_social,
	                    	E.nombre,
	                    	E.email,
												E.client_token,
												E.access_token,
												E.tipo_persona_codigo,
	                    	TD.codigo_tipo_documento_dian,
												UP.pais,
	                      UP.iso2,
												CM.moneda,
												ES.direccion,
												ES.telefono,
												ES.codigo_postal,
												ES.numero_matricula_mercantil,
												UD.departamento,
												UD.codigo_departamento,
												UC.ciudad,
												CONCAT(UD.codigo_departamento,UC.codigo_ciudad) AS codigo_ciudad
	                    FROM
	                    	empresas AS E
	                    LEFT JOIN
	                    	tipo_documento AS TD
	                    ON
	                    	E.tipo_documento = TD.id
	                    LEFT JOIN
	                    	ubicacion_pais AS UP
	                    ON
	                    	E.id_pais = UP.id
											LEFT JOIN
												configuracion_moneda AS CM
											ON
												E.id_moneda = CM.id
											LEFT JOIN
												empresas_sucursales AS ES
											ON
												E.id = ES.id_empresa
											LEFT JOIN
												ubicacion_departamento AS UD
											ON
												ES.id_departamento = UD.id
											LEFT JOIN
												ubicacion_ciudad AS UC
											ON
												ES.id_ciudad = UC.id
	                    WHERE
	                    	E.id = '$id_empresa'
											AND
												ES.id = '$this->id_sucursalVF'
	                    GROUP BY
	                    	E.id";

      $queryEmpresa = $this->mysql->query($sqlEmpresa,$this->mysql->link);

			if(!$queryEmpresa){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos del emisor.");' . '</script>';
				exit;
			}

			$this->documentoE                  = $this->mysql->result($queryEmpresa,0,'documento');
      $this->digito_verificacionE        = $this->mysql->result($queryEmpresa,0,'digito_verificacion');
			$this->tipo_regimenE						   = $this->mysql->result($queryEmpresa,0,'tipo_regimen');
      $this->razon_socialE               = $this->mysql->result($queryEmpresa,0,'razon_social');
      $this->nombreE                     = $this->mysql->result($queryEmpresa,0,'nombre');
			$this->emailE                      = $this->mysql->result($queryEmpresa,0,'email');
			$this->client_tokenE	   		       = $this->mysql->result($queryEmpresa,0,'client_token');
			$this->access_tokenE	   		       = $this->mysql->result($queryEmpresa,0,'access_token');
			$this->tipo_persona_codigoE				 = $this->mysql->result($queryEmpresa,0,'tipo_persona_codigo');
			$this->codigo_tipo_documento_dianE = $this->mysql->result($queryEmpresa,0,'codigo_tipo_documento_dian');
			$this->paisE                       = $this->mysql->result($queryEmpresa,0,'pais');
			$this->iso2E                       = $this->mysql->result($queryEmpresa,0,'iso2');
			$this->monedaE										 = $this->mysql->result($queryEmpresa,0,'moneda');
      $this->direccionE                  = $this->mysql->result($queryEmpresa,0,'direccion');
			$this->telefonoE                   = $this->mysql->result($queryEmpresa,0,'telefono');
			$this->codigo_postalE							 = $this->mysql->result($queryEmpresa,0,'codigo_postal');
			$this->numero_matricula_mercantilE = $this->mysql->result($queryEmpresa,0,'numero_matricula_mercantil');
      $this->departamentoE               = $this->mysql->result($queryEmpresa,0,'departamento');
			$this->codigo_departamentoE        = $this->mysql->result($queryEmpresa,0,'codigo_departamento');
      $this->ciudadE                     = $this->mysql->result($queryEmpresa,0,'ciudad');
			$this->codigo_ciudadE              = $this->mysql->result($queryEmpresa,0,'codigo_ciudad');

      //--------------------- DATOS DEL TERCERO O CLIENTE --------------------//
      $sqlTerceros = "SELECT
												T.id,
                        T.id_tipo_persona_dian,
                        T.numero_identificacion,
                        T.nombre,
												T.nombre_comercial,
												T.email,
                        T.iso2,
												T.pais,
												T.sector_empresarial,
												T.exento_iva,
												T.dv,
												T.id_tipo_persona_dian,
                        TT.codigo_regimen_dian,
                        TD.codigo_tipo_documento_dian
                      FROM
                        terceros AS T
                      LEFT JOIN
                        terceros_tributario AS TT
                      ON
                        T.id_tercero_tributario = TT.id
                      LEFT JOIN
                        tipo_documento AS TD
                      ON
                        T.id_tipo_identificacion = TD.id
                      WHERE
                        T.activo = 1
                      AND
                        T.numero_identificacion = '$this->nitVF'
                      AND
                        T.id_empresa = '$id_empresa'";

      $queryTerceros = $this->mysql->query($sqlTerceros,$this->mysql->link);

			if(!$queryTerceros){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos del cliente.");' . '</script>';
				exit;
			}

			$this->idT											   = $this->mysql->result($queryTerceros,0,'id');
      $this->id_tipo_persona_dianT   		 = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
      $this->numero_identificacionT  		 = $this->mysql->result($queryTerceros,0,'numero_identificacion');
			$this->nombreT										 = $this->mysql->result($queryTerceros,0,'nombre');
      $this->nombre_comercialT					 = $this->mysql->result($queryTerceros,0,'nombre_comercial');
      $this->emailT                 		 = $this->mysql->result($queryTerceros,0,'email');
			$this->paisT                   		 = $this->mysql->result($queryTerceros,0,'pais');
      $this->iso2T                   		 = $this->mysql->result($queryTerceros,0,'iso2');
			$this->sector_empresarialT			   = $this->mysql->result($queryTerceros,0,'sector_empresarial');
			$this->exento_ivaT							   = $this->mysql->result($queryTerceros,0,'exento_iva');
			$this->dvT                         = $this->mysql->result($queryTerceros,0,'dv');
			$this->tipo_persona_codigoT        = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
      $this->codigo_regimen_dianT    		 = $this->mysql->result($queryTerceros,0,'codigo_regimen_dian');
      $this->codigo_tipo_documento_dianT = $this->mysql->result($queryTerceros,0,'codigo_tipo_documento_dian');

			$sqlTercerosDireccion  = "SELECT
																	TD.id,
																	TD.direccion,
																	TD.ciudad,
																	TD.departamento,
																	TD.telefono1,
																	TD.codigo_postal,
																	UD.codigo_departamento,
																	CONCAT(UD.codigo_departamento,UC.codigo_ciudad) AS codigo_ciudad,
																	TD.numero_matricula_mercantil
																FROM
																	terceros_direcciones AS TD
																LEFT JOIN
																	ubicacion_departamento AS UD
																ON
																	TD.id_departamento = UD.id
																LEFT JOIN
																	ubicacion_ciudad AS UC
																ON
																	TD.id_ciudad = UC.id
																WHERE
																	TD.id = '$this->id_sucursal_clienteVF'
																AND
																	TD.id_tercero = '$this->idT'
																AND
																	TD.activo = 1
																LIMIT
																	0,1";

			$queryTercerosDireccion = $this->mysql->query($sqlTercerosDireccion,$this->mysql->link);

			if(!$queryTercerosDireccion){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron las direcciones del cliente.");' . '</script>';
				// exit;
			}

			$this->idTD						              = $this->mysql->result($queryTercerosDireccion,0,'id');
			$this->direccionTD 		              = $this->mysql->result($queryTercerosDireccion,0,'direccion');
			$this->ciudadTD    		              = $this->mysql->result($queryTercerosDireccion,0,'ciudad');
			$this->departamentoTD               = $this->mysql->result($queryTercerosDireccion,0,'departamento');
			$this->telefono1TD  	              = $this->mysql->result($queryTercerosDireccion,0,'telefono1');
			$this->codigo_postalTD              = $this->mysql->result($queryTercerosDireccion,0,'codigo_postal');
			$this->codigo_departamentoTD        = $this->mysql->result($queryTercerosDireccion,0,'codigo_departamento');
			$this->codigo_ciudadTD              = $this->mysql->result($queryTercerosDireccion,0,'codigo_ciudad');
			$this->numero_matricula_mercantilTD = $this->mysql->result($queryTercerosDireccion,0,'numero_matricula_mercantil');

			$sqlTercerosDireccionesEmail = "SELECT
																				TDE.email
																			FROM
																				terceros_direcciones_email AS TDE
																			LEFT JOIN
																				terceros_direcciones AS TD
																			ON
																				TD.id = TDE.id_direccion
																			LEFT JOIN
																				terceros AS T
																			ON
																				T.id = TD.id_tercero
																			WHERE
																				TDE.activo = 1
																			AND
																				TDE.id_direccion = '$this->idTD'
																			AND
																				T.activo = 1
																			AND
																				T.id = $this->idT";

			$queryTercerosDireccionesEmail = $this->mysql->query($sqlTercerosDireccionesEmail,$this->mysql->link);

			if(!$queryTercerosDireccionesEmail){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los correos electronicos del cliente.");' . '</script>';
				// exit;
			}

			$this->contTercerosDireccionesEmail = $this->mysql->num_rows($queryTercerosDireccionesEmail);
			for($i = 0; $i < $this->contTercerosDireccionesEmail; $i++){
				$this->emailTDE[$i] = $this->mysql->result($queryTercerosDireccionesEmail,$i,'email');
			}

			//---------------------- DATOS DE LAS RETENCIONES ----------------------//
			$sqlVentasFacturasRetenciones =  "SELECT
																					VFR.valor,
																					VFR.base,
																					VFR.retencion,
																					VFR.tipo_retencion
																				FROM
																					ventas_facturas_retenciones AS VFR
																				LEFT JOIN
																					ventas_facturas AS VF
																				ON
																					VFR.id_factura_venta = VF.id
																				WHERE
																					VFR.activo = 1
																				AND
																					VFR.id_factura_venta = $this->idVF";

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
				$this->retencionVFR[$i]				= $this->mysql->result($queryVentasFacturasRetenciones,$i,'retencion');
				$this->tipo_retencionVFR[$i]  = $this->mysql->result($queryVentasFacturasRetenciones,$i,'tipo_retencion');
			}

			//----------------------- DATOS DE lOS ARTICULOS -----------------------//
			$sqlVentasFacturasInventario = "SELECT
																				VFI.codigo,
																				VFI.cantidad,
																				VFI.nombre,
																				VFI.costo_unitario,
																				VFI.observaciones,
																				VFI.tipo_descuento,
																				VFI.descuento,
																				VFI.impuesto,
																				VFI.valor_impuesto,
																				I.codigo_impuesto_dian,
																				IU.codigo_dian AS codigo_unidad_medida
																			FROM
																				ventas_facturas_inventario AS VFI
																			LEFT JOIN
																				ventas_facturas AS VF
																			ON
																				VFI.id_factura_venta = VF.id
																			LEFT JOIN
																				impuestos AS I
																			ON
																				I.id = VFI.id_impuesto
																			LEFT JOIN
																			 	ventas_facturas_inventario_grupos AS VFIG
																			ON
																				VFIG.id_inventario_factura_venta = VFI.id
																			LEFT JOIN
																				inventario_unidades AS IU
																			ON
																				VFI.id_unidad_medida = IU.id
																			WHERE
																				VFI.activo = 1
																			AND
																				VFI.id_factura_venta = $this->idVF
																			AND
																				VFIG.id_inventario_factura_venta IS NULL
																			AND
																				VFI.id_empresa = '$id_empresa'";

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
				$this->codigo_unidad_medidaVFI[$i] 	 = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo_unidad_medida');
			}

			//Buscamos primero si el articulo tiene o no descuento
			for($i = 0; $i < $this->contArticulos; $i++){
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
					$this->arrayImpuestos[$this->codigo_impuesto_dianVFI[$i]][$this->valor_impuestoVFI[$i]]['costo'] += $this->costo_impuestoVFI[$i];
					$this->arrayImpuestos[$this->codigo_impuesto_dianVFI[$i]][$this->valor_impuestoVFI[$i]]['nombre'] = $this->impuestoVFI[$i];
				}
			}

			//---------------- DATOS DE LOS GRUPOS DE LOS ARTICULOS ----------------//
			$sqlVentasFacturasInventarioGrupos = "SELECT
																							VFG.codigo,
																							VFG.cantidad,
																							VFG.nombre,
																							VFG.costo_unitario,
																							VFG.observaciones,
																							VFG.descuento,
																							VFG.nombre_impuesto,
																							VFG.porcentaje_impuesto,
																							I.codigo_impuesto_dian
																						FROM
																							ventas_facturas_grupos AS VFG
																						LEFT JOIN
																							impuestos AS I
																						ON
																							I.id = VFG.id_impuesto
																						WHERE
																							VFG.activo = 1
																						AND
																							VFG.id_empresa = '$id_empresa'
																						AND
																							VFG.id_factura_venta = $this->idVF";

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
					$this->arrayImpuestos[$this->codigo_impuesto_dianVFIG[$i]][$this->porcentaje_impuestoVFIG[$i]]['costo'] += $this->costo_impuestoVFIG[$i];
					$this->arrayImpuestos[$this->codigo_impuesto_dianVFIG[$i]][$this->porcentaje_impuestoVFIG[$i]]['nombre'] = $this->nombre_impuestoVFIG[$i];
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
			$arrayDetalle 		  = Array(); //Se crea un arreglo que contenga todos los articulos y grupos de articulos
			$arrayImpuesto 		  = Array(); //Se crea un arreglo que contenga todos los impuestos y retenciones
			$arrayTercerosEmail = Array(); //Se crea un arreglo que contenga los email de los terceros
			$subTotal 				  = array_sum($this->costo_subtotalVFI) + array_sum($this->costo_subtotalVFIG); //Variable que contendra el costo del subtotal de toda la factura
			$descuento          = array_sum($this->descuento_itemVFI) + array_sum($this->descuentoVFIG);
			$numero_detalle     = 1;

			//------------------- ARTICULOS Y GRUPOS DE ARTICULOS ------------------//
			for($i = 0; $i < $this->contArticulos; $i++){
				if($this->impuestoVFI[$i] != null && $this->exento_ivaT == "No"){
					$arrayImpuestoItem[$i] = [
																			array(
																							"Base"                => (string) round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']),
																							"CodigoImpuesto"      => $this->codigo_impuesto_dianVFI[$i],
																							"Nombre"              => $this->impuestoVFI[$i],
																							"Porcentaje"          => round($this->valor_impuestoVFI[$i],$_SESSION['DECIMALESMONEDA']),
																							"Impuesto"            => (string) round($this->costo_impuestoVFI[$i],$_SESSION['DECIMALESMONEDA'])
																					  )
																	 ];

					$aplica_impuestoVFI[$i] = true;
				}
				else{
					$arrayImpuestoItem[$i] = [
																			array(
																							"Base"                => "",
																							"CodigoImpuesto"      => "",
																							"Nombre"              => "",
																							"Porcentaje"          => 0,
																							"Impuesto"            => ""
																					  )
																	 ];

					$aplica_impuestoVFI[$i] = false;
				}

				//Creamos una variable que contenga el total del item
				$this->totalItemVFI[$i] = $this->costo_subtotalVFI[$i] + $this->costo_impuestoVFI[$i];

				$arrayDetalle[] =	 array(
																	"idDetalle"        => (string) $numero_detalle,
																	"Nombre"    		   => $this->quitarTildes($this->nombreVFI[$i]),
																	"UnidadCodigo"     => $this->codigo_unidad_medidaVFI[$i],
																	"Cantidad"  		   => (float) $this->cantidadVFI[$i],
																	"ValorUnitario"	   => (float) $this->costo_unitarioVFI[$i],
																	"Descuento"        => round($this->descuento_itemVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"SubTotal"  		   => round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"Total"     		   => round($this->totalItemVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"codigo"    		   => $this->codigoVFI[$i],
																	"AplicaImpuesto"   => $aplica_impuestoVFI[$i],
																	"Impuestos" 		   => $arrayImpuestoItem[$i],
																	"Descripcion" 	   => null,
																	"AllowanceCharge"  => null,
																	"PricingReference" => null
																);

				$numero_detalle++;
			}

			for($i = 0; $i < $this->contGruposArticulos; $i++){
				if($this->nombre_impuestoVFIG[$i] != null && $this->exento_ivaT == "No"){
					$arrayImpuestoItemGrupo[$i] = [
																					array(
																									"Base"           => (string) round($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																									"CodigoImpuesto" => $this->codigo_impuesto_dianVFIG[$i],
																									"Nombre"         => $this->nombre_impuestoVFIG[$i],
																									"Porcentaje"     => round($this->porcentaje_impuestoVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																									"Impuesto"       => (string) round($this->costo_impuestoVFIG[$i],$_SESSION['DECIMALESMONEDA'])
																					      )
																				];
					$aplica_impuestoVFIG[$i] = true;
				}
				else{
					$arrayImpuestoItemGrupo[$i] = [
																					array(
																									"Base"           => "",
																									"CodigoImpuesto" => "",
																									"Nombre"         => "",
																									"Porcentaje"     => 0,
																									"Impuesto"       => ""
																					      )
																				];
					$aplica_impuestoVFIG[$i] = false;
				}

				//Creamos una variable que contenga el total del item
				$this->totalItemVFIG[$i] = $this->costo_subtotalVFIG[$i] + $this->costo_impuestoVFIG[$i];

				$arrayDetalle[] =	 array(
																	"idDetalle"        => (string) $numero_detalle,
																	"Nombre"    		   => $this->quitarTildes($this->nombreVFIG[$i]),
																	"UnidadCodigo"     => "EA",
																	"Cantidad"  		   => (float) $this->cantidadVFIG[$i],
																	"ValorUnitario"	   => (float) $this->costo_unitarioVFIG[$i],
																	"Descuento"        => round($this->descuentoVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"SubTotal"  		   => round($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Total"     		   => round($this->totalItemVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"codigo"    		   => $this->codigoVFIG[$i],
																	"AplicaImpuesto"   => $aplica_impuestoVFIG[$i],
																	"Impuestos" 		   => $arrayImpuestoItemGrupo[$i],
																	"Descripcion" 	   => null,
																	"AllowanceCharge"  => null,
																	"PricingReference" => null
																);

				$numero_detalle++;
			}

			//------------------------------ IMPUESTOS -----------------------------//
			if($this->exento_ivaT == "No" && ($arrayImpuestoItem != null || $arrayImpuestoItemGrupo != null)){
				foreach($this->arrayImpuestos as $codigoDian => $resultCodigoDian){
					foreach($resultCodigoDian as $porcentajeImpuesto => $result){
							$arrayImpuesto[] = array(
																				"Base"                => (string) round($subTotal,$_SESSION['DECIMALESMONEDA']),
																				"CodigoImpuesto"      => $codigoDian,
																		    "Nombre"              => (string) $result['nombre'],
																		    "Porcentaje"          => (float) $porcentajeImpuesto / 1,
																		    "Impuesto"            => (string) round($result['costo'],$_SESSION['DECIMALESMONEDA'])
																			);
							if($codigoDian == "01"){
								$codigoIVA[] = $result['costo'];
							}
					}
				}
			}
      else{
        $arrayImpuesto[] = array(
																	"Base"                => "",
																	"CodigoImpuesto"      => "03",
																	"Nombre"              => "",
																	"Porcentaje"          => 0,
																	"Impuesto"            => "0"
                                );
      }

			//------------------------------ TOTAL IVA -----------------------------//
			$totalIVA = array_sum($codigoIVA);

			//----------------------------- RETENCIONES ----------------------------//
			for($i = 0; $i < $this->contRetenciones; $i++){
				if($subTotal > $this->baseVFR[$i]){
					if($this->tipo_retencionVFR[$i] == "ReteFuente"){
						$arrayImpuesto[] = array(
																			"Base"           => (string) $this->baseVFR[$i],
																			"CodigoImpuesto" => "05",
																			"Nombre"         => (string) $this->retencionVFR[$i],
																			"Porcentaje"     => (float) $this->valorVFR[$i],
																			"Impuesto"       => (string) round(($subTotal * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		);
						$totalRetencionesVF += ($subTotal * $this->valorVFR[$i] / 100);
					}
					else if($this->tipo_retencionVFR[$i] == "ReteIva"){
						if($totalIVA > $this->baseVFR[$i]){
							$arrayImpuesto[] = array(
																				"Base"           => (string) $this->baseVFR[$i],
																				"CodigoImpuesto" => "06",
																				"Nombre"         => (string) $this->retencionVFR[$i],
																				"Porcentaje"     => (float) $this->valorVFR[$i],
																				"Impuesto"       => (string) round(($subTotal * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		  );
							$totalRetencionesVF += ($totalIVA * $this->valorVFR[$i] / 100);
						}
					}
					else if($this->tipo_retencionVFR[$i] == "ReteIca"){
						$arrayImpuesto[] = array(
																			"Base"           => (string) $this->baseVFR[$i],
																			"CodigoImpuesto" => "07",
																			"Nombre"         => (string) $this->retencionVFR[$i],
																			"Porcentaje"     => (float) $this->valorVFR[$i],
																			"Impuesto"       => (string) round(($subTotal * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
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
                                "TipoComprobante"               => "01",
                                "Fecha"                         => date('Y-m-d'),
                                "Prefijo"                       => ($this->prefijoVF != null)? $this->prefijoVF : "",
                                "Numero"                        => (int) $this->numero_facturaVF,
                                "Moneda"                        => $this->monedaE,
                                "Referencia"                    => "",
                                "ConceptoRef"                   => "",
																"Observaciones"		              => ($this->observacionVF != null)? $this->quitarTildes($this->observacionVF) : "",
																"Usuario"                       => "Test",
																"NumeroOrden"                   => ($this->orden_compraVF != null)? $this->quitarTildes($this->orden_compraVF) : "",
																"NumeroDespacho"                => "",
																"NumeroRecepcion"               => "",
																"DocumentoAdicionalNotaCredito" => "",
																"DocumentoReferenciaCodigo"     => "",
                                "Descripcion" 									=> [
																																			array(
																																							"Nombre"  => "Fecha Vencimiento",
																																							"Valor"   => $this->fecha_vencimientoVF
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
																																				   )
																																		],
                                "MetodoPago"  									=> [
																																			array(
										                                                        	"FormaPago" => $this->codigo_metodo_pago_dianVF,
																																							"MedioPago" => (string) $totalVF,
																																							"Fecha"     => $this->fecha_inicioVF
									                                                         )
																																		]
                              ),
        "Emisor" =>  array(
                            "Identificacion"           => str_replace(array(".","-"),"",$this->documentoE),
														"DigitoVerificador"        => $this->digito_verificacionE,
														"TipoPersona"              => $this->tipo_persona_codigoE,
                            "TipoIdentificacion"       => $this->codigo_tipo_documento_dianE,
														"TipoEmisor"               => "O-99",
                            "RazonSocial"              => $this->quitarTildes($this->razon_socialE),
                            "NombreComercial"          => $this->quitarTildes($this->nombreE),
														"Sucursal"                 => "PRIN",
                            "Direccion"                => $this->quitarTildes($this->direccionE),
														"Telefono"                 => $this->telefonoE,
														"email"                    => $this->quitarTildes($this->emailE),
                            "Pais"                     => $this->quitarTildes($this->paisE),
														"PaisCodigo"               => $this->iso2E,
														"Departamento"             => $this->quitarTildes($this->departamentoE),
														"DepartamentoCodigo"       => $this->codigo_departamentoE,
														"Ciudad"                   => $this->quitarTildes($this->ciudadE),
														"CiudadCodigo"             => $this->codigo_ciudadE,
														"CodigoPostal"             => $this->codigo_postalE,
														"NumeroMatriculaMercantil" => $this->numero_matricula_mercantilE,
				                    "Descripcion"              => [
																														array(
								                                                    "Nombre" => "Tipo De Regimen",
								                                                    "Valor"  => $this->quitarTildes($this->tipo_regimenE)
						                                                   		)
																													]
                          ),
        "Receptor" =>  array(
                              "Identificacion"           => str_replace(array(".","-"),"",$this->numero_identificacionT),
															"DigitoVerificador"        => $this->dvT,
															"TipoPersona"              => $this->tipo_persona_codigoT,
                              "TipoIdentificacion"       => $this->codigo_tipo_documento_dianT,
															"TipoReceptor"             => "O-99",
                              "RazonSocial"              => $this->quitarTildes($this->nombreT),
                              "NombreComercial"          => $this->quitarTildes($this->nombre_comercialT),
                              "Direccion"                => $this->quitarTildes($this->direccionTD),
															"Telefono"                 => $this->telefono1TD,
															"email"                    => $emails,
                              "Pais"                     => $this->quitarTildes($this->paisT),
															"PaisCodigo"               => $this->iso2T,
															"Departamento"             => $this->quitarTildes($this->departamentoTD),
															"DepartamentoCodigo"       => $this->codigo_departamentoTD,
															"Ciudad"                   => $this->quitarTildes($this->ciudadTD),
															"CiudadCodigo"             => $this->codigo_ciudadTD,
															"CodigoPostal"             => $this->codigo_postalTD,
															"NumeroMatriculaMercantil" => $this->numero_matricula_mercantilTD,
															"Descripcion" 				     => [
																															array(
								                                                      "Nombre" => "Sector Empresarial",
								                                                      "Valor"  => ($this->sector_empresarialT != null)? $this->quitarTildes($this->sector_empresarialT) : ""
							                                                      )
																												    ]
                            ),
        "Detalles" => $arrayDetalle,
        "Totales" => array(
                            "Total"                       => (string) $totalVF,
                            "SubTotal"                    => (string) round($subTotal,$_SESSION['DECIMALESMONEDA']),
														"Cargos"                      => "0",
														"Descuentos"                  => ($descuento != null)? (string) round($descuento,$_SESSION['DECIMALESMONEDA']) : "0",
														"SubTotalSinCargosDescuentos" => (string) round(($subTotal + $descuento),$_SESSION['DECIMALESMONEDA']),
														"IVA"					                => ($totalIVA != null)? (string) round($totalIVA,$_SESSION['DECIMALESMONEDA']) : "0"
                          ),
				"TotalImpuestos" => $arrayImpuesto,
        "DetallesComprobante" => [],
				"QR" => "QEA = ",
				"Credenciales" => array(
																	"ClientToken" => $this->client_tokenE,
																	"AccessToken" => $this->access_tokenE,
																),
				"AllowanceCharge" => null,
				"PaymentExchangeRate" => null,
				"TerminosPago" => array(
																  "Codigo"       => "2",
																  "UnidadCodigo" => "DAY",
																  "Duracion"     => $this->dias_pagoVF
																)
      );

      $this->arrayFinal = json_encode($arrayPrincipal, JSON_PRETTY_PRINT);

			// echo json_last_error_msg();
    }

    public function enviarJSON(){
			$server_name = $_SERVER['SERVER_NAME'];

			// Incluimos la clase maestra para consumir API's
			include_once("../../../external_apis/LOGICALHOTELS/backend/ClassExternalApis.php");

			// Validamos si estamos desarrollo o produccion
			if($server_name == "logicalerp.localhost"){
				$url_api = "http://api.facsep.com/api/Comunicacion/ValidarJson";
			}
			else{
				$url_api = "http://api.facsep.com/api/Comunicacion/ValidarJson";
			}

			// Creamos los parametros para consumir la API
			$params                   = [];
			$params['request_url']    = $url_api;
			$params['request_method'] = "POST";
			$params['Authorization']  = "";
			$params['data']           = $this->arrayFinal;

			// Consumimos el API y obtenemos sus resultados
			$envioValidar     = new ClassExternalApis($_SESSION['SUCURSAL'],$_SESSION['EMPRESA'],$this->mysql);
			$respuestaValidar = $envioValidar->curlApi($params);
			$arrayValidar     = json_decode($respuestaValidar,true);

			// Si se valida el Json exitosamente podemos enviarlo a la DIAN
			if($arrayValidar['respuesta'] == "Json sin inconsistencias"){

				// Validamos si estamos desarrollo o produccion
				if($server_name == "logicalerp.localhost"){
					$url_api = "http://api.facsep.com/api/Comunicacion/Comprobante";
				}
				else{
					$url_api = "http://api.facsep.com/api/Comunicacion/Comprobante";
				}

				// Cambiamos la url de validacion por la del envio
				$params['request_url'] = $url_api;

				// Consumimos el API y obtenemos sus resultados
				$envioComprobante     = new ClassExternalApis($_SESSION['SUCURSAL'],$_SESSION['EMPRESA'],$this->mysql);
				$respuestaComprobante = $envioComprobante->curlApi($params);
				$arrayComprobante     = json_decode($respuestaComprobante,true);

				$respuestaFinal['validar']     = $arrayValidar['respuesta'];
				$respuestaFinal['comprobante'] = $respuestaComprobante;

				return $respuestaFinal;
			}
			// De lo contrario si el Json presenta inconsistencias
			else{
				$respuestaFinal['validar']     = $arrayValidar['respuesta'];
				$respuestaFinal['comprobante'] = "No se logro enviar porque el Json presenta inconsistencia o existe algun problema en la conexion con Facse.";

				return $respuestaFinal;
			}
		}

		public function imprimirJSON(){
			return $this->arrayFinal;
		}
	}
?>
