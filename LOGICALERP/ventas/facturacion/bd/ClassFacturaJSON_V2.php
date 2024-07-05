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
								VF.exento_iva,
								VF.email_fe,
                              	VF.info_reserva,
								CMP.codigo_metodo_pago_dian,
								CMP.nombre AS nombre_metodo_pago_dian,
								VF.forma_pago,
								CCP.estado AS forma_pago_CCP,
								VF.valor_anticipo
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
							LEFT JOIN
								configuracion_cuentas_pago AS CCP
							ON
								VF.id_configuracion_cuenta_pago = CCP.id
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

      	$this->idVF						 = $this->mysql->result($queryVentasFacturas,0,'id');
		$this->fecha_inicioVF			 = $this->mysql->result($queryVentasFacturas,0,'fecha_inicio');
		$this->prefijoVF				 = $this->mysql->result($queryVentasFacturas,0,'prefijo');
		$this->numero_facturaVF 	 	 = $this->mysql->result($queryVentasFacturas,0,'numero_factura');
		$this->nitVF 					 = $this->mysql->result($queryVentasFacturas,0,'nit');
		$this->fecha_vencimientoVF		 = $this->mysql->result($queryVentasFacturas,0,'fecha_vencimiento');
		$this->observacionVF			 = $this->mysql->result($queryVentasFacturas,0,'observacion');
		$this->orden_compraVF			 = $this->mysql->result($queryVentasFacturas,0,'orden_compra');
		$this->sucursal_clienteVF		 = $this->mysql->result($queryVentasFacturas,0,'sucursal_cliente');
		$this->id_sucursalVF    		 = $this->mysql->result($queryVentasFacturas,0,'id_sucursal');
		$this->sucursalVF				 = $this->mysql->result($queryVentasFacturas,0,'sucursal');
		$this->id_sucursal_clienteVF  	 = $this->mysql->result($queryVentasFacturas,0,'id_sucursal_cliente');
		$this->nombre_vendedorVF		 = $this->mysql->result($queryVentasFacturas,0,'nombre_vendedor');
		$this->documento_vendedorVF		 = $this->mysql->result($queryVentasFacturas,0,'documento_vendedor');
		$this->dias_pagoVF   			 = $this->mysql->result($queryVentasFacturas,0,'dias_pago');
      	$this->email_feVF                = $this->mysql->result($queryVentasFacturas,0,'email_fe');
		$this->info_reservaVF			 = $this->mysql->result($queryVentasFacturas,0,'info_reserva');
		$this->exento_ivaVF              = $this->mysql->result($queryVentasFacturas,0,'exento_iva');
		$this->codigo_metodo_pago_dianVF = $this->mysql->result($queryVentasFacturas,0,'codigo_metodo_pago_dian');
		$this->nombre_metodo_pago_dianVF = $this->mysql->result($queryVentasFacturas,0,'nombre_metodo_pago_dian');
		$this->forma_pagoVF              = $this->mysql->result($queryVentasFacturas,0,'forma_pago');
		$this->forma_pago_CCP            = $this->mysql->result($queryVentasFacturas,0,'forma_pago_CCP');
		$this->valor_anticipo            = $this->mysql->result($queryVentasFacturas,0,'valor_anticipo');

		switch (true){
			case empty($this->forma_pagoVF):
				$this->forma_pagoVF = $this->forma_pago_CCP;
				break;

			case is_null($this->forma_pagoVF):
				$this->forma_pagoVF = $this->forma_pago_CCP;
				break;

			case $this->forma_pagoVF=="":
				$this->forma_pagoVF = $this->forma_pago_CCP;
				break;
			}

		//------------------- DATOS DEL EMISOR O LA EMPRESA --------------------//
		$sqlEmpresa = "SELECT
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
		$this->tipo_regimenE			   = $this->mysql->result($queryEmpresa,0,'tipo_regimen');
		$this->razon_socialE               = $this->mysql->result($queryEmpresa,0,'razon_social');
		$this->nombreE                     = $this->mysql->result($queryEmpresa,0,'nombre');
		$this->emailE                      = $this->mysql->result($queryEmpresa,0,'email');
		$this->client_tokenE	   		   = $this->mysql->result($queryEmpresa,0,'client_token');
		$this->access_tokenE	   		   = $this->mysql->result($queryEmpresa,0,'access_token');
		$this->tipo_persona_codigoE		   = $this->mysql->result($queryEmpresa,0,'tipo_persona_codigo');
		$this->codigo_tipo_documento_dianE = $this->mysql->result($queryEmpresa,0,'codigo_tipo_documento_dian');
		$this->paisE                       = $this->mysql->result($queryEmpresa,0,'pais');
		$this->iso2E                       = $this->mysql->result($queryEmpresa,0,'iso2');
		$this->monedaE					   = $this->mysql->result($queryEmpresa,0,'moneda');
      	$this->direccionE                  = $this->mysql->result($queryEmpresa,0,'direccion');
		$this->telefonoE                   = $this->mysql->result($queryEmpresa,0,'telefono');
		$this->codigo_postalE			   = $this->mysql->result($queryEmpresa,0,'codigo_postal');
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
							T.telefono1,
							T.direccion,
							T.iso2,
							T.id_pais,
							T.pais,
							T.sector_empresarial,
							T.dv,
							T.id_tipo_persona_dian,
							TT.codigo_regimen_dian,
							UD.departamento,
							UD.codigo_departamento,
							UC.ciudad,
							CONCAT(UD.codigo_departamento,UC.codigo_ciudad) AS codigo_ciudad,
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
						LEFT JOIN
							ubicacion_departamento AS UD
						ON
							T.id_departamento = UD.id
						LEFT JOIN
							ubicacion_ciudad AS UC
						ON
							T.id_ciudad = UC.id
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

		$this->idT						   = $this->mysql->result($queryTerceros,0,'id');
		$this->id_tipo_persona_dianT   	   = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
		$this->numero_identificacionT  	   = $this->mysql->result($queryTerceros,0,'numero_identificacion');
		$this->nombreT					   = $this->mysql->result($queryTerceros,0,'nombre');
		$this->nombre_comercialT		   = $this->mysql->result($queryTerceros,0,'nombre_comercial');
		$this->emailT                 	   = $this->mysql->result($queryTerceros,0,'email');
		$this->id_paisT                    = $this->mysql->result($queryTerceros,0,'id_pais');
		$this->paisT                   	   = $this->mysql->result($queryTerceros,0,'pais');
      	$this->iso2T                   	   = $this->mysql->result($queryTerceros,0,'iso2');
		$this->sector_empresarialT		   = $this->mysql->result($queryTerceros,0,'sector_empresarial');
		$this->dvT                         = $this->mysql->result($queryTerceros,0,'dv');
		$this->tipo_persona_codigoT        = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
		$this->codigo_regimen_dianT    	   = $this->mysql->result($queryTerceros,0,'codigo_regimen_dian');
		$this->nombre_departamentoT        = $this->mysql->result($queryTerceros,0,'departamento');
		$this->codigo_departamentoT        = $this->mysql->result($queryTerceros,0,'codigo_departamento');
		$this->nombre_ciudadT              = $this->mysql->result($queryTerceros,0,'ciudad');
		$this->codigo_ciudadT              = $this->mysql->result($queryTerceros,0,'codigo_ciudad');
		$this->codigo_tipo_documento_dianT = $this->mysql->result($queryTerceros,0,'codigo_tipo_documento_dian');
		$this->telefonoT                   = $this->mysql->result($queryTerceros,0,'telefono1');
		$this->direccionT                  = $this->mysql->result($queryTerceros,0,'direccion');

		$sqlTercerosDireccion = "SELECT
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
				exit;
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
				exit;
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
			$this->valorVFR[$i] 		 = $this->mysql->result($queryVentasFacturasRetenciones,$i,'valor');
			$this->baseVFR[$i]  		 = $this->mysql->result($queryVentasFacturasRetenciones,$i,'base');
			$this->retencionVFR[$i]		 = $this->mysql->result($queryVentasFacturasRetenciones,$i,'retencion');
			$this->tipo_retencionVFR[$i] = $this->mysql->result($queryVentasFacturasRetenciones,$i,'tipo_retencion');
		}

		//----------------------- DATOS DE lOS ARTICULOS -----------------------//
		$sqlVentasFacturasInventario = "SELECT
											VFI.codigo,
											SUM(VFI.cantidad) AS cantidad,
											VFI.nombre,
											VFI.costo_unitario,
											VFI.observaciones,
											VFI.tipo_descuento,
											VFI.descuento,
											VFI.impuesto,
											VFI.valor_impuesto,
											I.codigo_impuesto_dian,
											IU.codigo_dian AS codigo_unidad_medida,
											IC.puc AS cuenta
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
										LEFT JOIN 
											items_cuentas AS IC
										ON
											VFI.id_inventario = IC.id_items 
										WHERE
											VFI.activo = 1
										AND
											VFI.id_factura_venta = $this->idVF
										AND
											VFIG.id_inventario_factura_venta IS NULL
										AND
											VFI.id_empresa = '$id_empresa'
										AND
											IC.descripcion = 'precio'
										AND
											IC.estado = 'venta'
										GROUP BY
											VFI.codigo,VFI.costo_unitario,VFI.tipo_descuento,VFI.descuento,VFI.observaciones";

		$queryVentasFacturasInventario = $this->mysql->query($sqlVentasFacturasInventario,$this->mysql->link);

		if(!$queryVentasFacturasInventario){
			echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los articulos de la factura.");' . '</script>';
			exit;
		}

		//Contamos el numero de articulos que posee la factura
		$this->contArticulos = $this->mysql->num_rows($queryVentasFacturasInventario);

		for($i = 0; $i < $this->contArticulos; $i++){
			$this->numVFI[$i]                  = $i+1;
			$this->codigoVFI[$i]               = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo');
			$this->cantidadVFI[$i]             = $this->mysql->result($queryVentasFacturasInventario,$i,'cantidad');
			$this->nombreVFI[$i]               = $this->mysql->result($queryVentasFacturasInventario,$i,'nombre');
			$this->costo_unitarioVFI[$i]       = $this->mysql->result($queryVentasFacturasInventario,$i,'costo_unitario');
			$this->observacionesVFI[$i]        = $this->mysql->result($queryVentasFacturasInventario,$i,'observaciones');
			$this->tipo_descuentoVFI[$i]       = $this->mysql->result($queryVentasFacturasInventario,$i,'tipo_descuento');
			$this->descuentoVFI[$i]            = $this->mysql->result($queryVentasFacturasInventario,$i,'descuento');
			$this->impuestoVFI[$i]             = $this->mysql->result($queryVentasFacturasInventario,$i,'impuesto');
			$this->valor_impuestoVFI[$i]       = $this->mysql->result($queryVentasFacturasInventario,$i,'valor_impuesto');
			$this->codigo_impuesto_dianVFI[$i] = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo_impuesto_dian');
			$this->codigo_unidad_medidaVFI[$i] = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo_unidad_medida');
			$this->cuentaItem[$i]			   = $this->mysql->result($queryVentasFacturasInventario,$i,'cuenta');
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
			if($this->exento_ivaVF == "Si"){
				$this->costo_impuestoVFI[$i] = 0;
			}
			else{
				if($this->valor_impuestoVFI[$i] != null && ($this->impuestoVFI[$i] != "" || $this->impuestoVFI[$i] != null)){
					$this->costo_impuestoVFI[$i] = ROUND($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']) * $this->valor_impuestoVFI[$i] / 100;
				}
			}
			if($this->valor_impuestoVFI[$i] != null && ($this->impuestoVFI[$i] != "" || $this->impuestoVFI[$i] != null)){
				$this->arrayImpuestos[$this->codigo_impuesto_dianVFI[$i]][$this->valor_impuestoVFI[$i]]['costo'] += $this->costo_impuestoVFI[$i];
				$this->arrayImpuestos[$this->codigo_impuesto_dianVFI[$i]][$this->valor_impuestoVFI[$i]]['nombre'] = $this->impuestoVFI[$i];
			}
			$this->acumuladorBaseRetenciones += ($this->cuentaItem[$i][0]=="4")?$this->costo_subtotalVFI[$i]:0;

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
			$this->numVFIG[$i]                  = $i+1;
			$this->codigoVFIG[$i]				= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'codigo');
			$this->cantidadVFIG[$i]				= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'cantidad');
			$this->nombreVFIG[$i]				= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'nombre');
			$this->costo_unitarioVFIG[$i]		= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'costo_unitario');
			$this->observacionesVFIG[$i]		= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'observaciones');
			$this->descuentoVFIG[$i]			= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'descuento');
			$this->nombre_impuestoVFIG[$i]		= $this->mysql->result($queryVentasFacturasInventarioGrupos,$i,'nombre_impuesto');
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
			if($this->exento_ivaVF == "Si"){
			$this->costo_impuestoVFIG[$i] = 0;
			}
			else{
				if($this->porcentaje_impuestoVFIG[$i] != null && ($this->nombre_impuestoVFIG[$i] != "" || $this->nombre_impuestoVFIG[$i] != null)){
					$this->costo_impuestoVFIG[$i] = ROUND($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']) * $this->porcentaje_impuestoVFIG[$i] / 100;
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
	    $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo/';
	    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
		$cadena = str_replace($caracterEspecial,"",$cadena);
	    return utf8_encode($cadena);
	}

    public function construirJSON(){
			$arrayDetalle 		  = Array(); //Se crea un arreglo que contenga todos los articulos y grupos de articulos
			$arrayImpuesto 		  = Array(); //Se crea un arreglo que contenga todos los impuestos y retenciones
			$arrayTercerosEmail = Array(); //Se crea un arreglo que contenga los email de los terceros
			$numero_detalle     = 1;

			//------------------- ARTICULOS Y GRUPOS DE ARTICULOS ------------------//
			for($i = 0; $i < $this->contArticulos; $i++){
				$Porcentaje_Impuesto = 0;

				if($this->impuestoVFI[$i] != null && ($this->exento_ivaVF == "No" || $this->exento_ivaVF == null || $this->exento_ivaVF == "")){
					$Porcentaje_Impuesto = $this->valor_impuestoVFI[$i];
					$arrayImpuestoItem[$i] = [
																			array(
																							// "Base"                => (string) round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']),
																							"Base"                => $this->costo_subtotalVFI[$i],
																							"CodigoImpuesto"      => $this->codigo_impuesto_dianVFI[$i],
                                              												"Nombre"              => $this->impuestoVFI[$i],
																							"Porcentaje"          => round($this->valor_impuestoVFI[$i],$_SESSION['DECIMALESMONEDA']),
																							// "Impuesto"            => (string) round($this->costo_impuestoVFI[$i],$_SESSION['DECIMALESMONEDA'])
																							"Impuesto"            => $this->costo_impuestoVFI[$i]
																					  )
																	 ];

					$aplica_impuestoVFI[$i] = true;

					$totalBaseImpuesto[$this->codigo_impuesto_dianVFI[$i]] += (string) round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']);

					if(stripos($this->impuestoVFI[$i],"servicio") !== false){
						$descripcionIVA = "IVA SERVICIOS";
					}
					else if(stripos($this->impuestoVFI[$i],"compra") !== false){
						$descripcionIVA = "IVA COMPRAS";
					}
				}
				else{
					$arrayImpuestoItem[$i] = [
																			array(
																							"Base"                => "0",
																							"CodigoImpuesto"      => "ZY",
																							"Nombre"              => "0",
																							"Porcentaje"          => 0,
																							"Impuesto"            => "0"
																					  )
																	 ];

					$aplica_impuestoVFI[$i] = true;

					$totalBaseExentos += (string) round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']);
				}

				//AÑADIMOS LAS OBSERVACIONES DE CADA ITEM
				$descripcionVFI[$i][] = array(
																				"Nombre" => "detalleItem",
																				"Valor"  => ($this->observacionesVFI[$i] != "")? $this->quitarTildes($this->observacionesVFI[$i]) : ""
																			);

				//Creamos una variable que contenga el total del item
				$this->totalItemVFI[$i] = $this->costo_subtotalVFI[$i] + $this->costo_impuestoVFI[$i];

				$arrayDetalle[] =	 array(
																	"idDetalle"           => (string) $numero_detalle,
																	"Nombre"              => $this->quitarTildes($this->nombreVFI[$i]),
																	"UnidadCodigo"        => $this->codigo_unidad_medidaVFI[$i],
																	"Cantidad"            => (float) $this->cantidadVFI[$i],
																	// "ValorUnitario"       => (float) $this->costo_unitarioVFI[$i],
																	// "ValorUnitario"       => round($this->costo_unitarioVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"ValorUnitario"       => $this->costo_unitarioVFI[$i],
																	"Descuento"           => round($this->descuento_itemVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"Cargos"              => 0,
																	// "SubTotal"            => round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"SubTotal"            => $this->costo_subtotalVFI[$i],
																	"Total"               => round($this->totalItemVFI[$i],$_SESSION['DECIMALESMONEDA']),
																	"codigo"              => $this->codigoVFI[$i],
																	"Consecutivo"         => $this->numVFI[$i],
																	"AplicaImpuesto"      => $aplica_impuestoVFI[$i],
																	"Impuestos"           => $arrayImpuestoItem[$i],
																	"Porcentaje_Impuesto" => $Porcentaje_Impuesto,
																	"Descripcion"         => ($descripcionVFI[$i] == null)? null : $descripcionVFI[$i],
																	"AllowanceCharge"     => null,
																	"PricingReference"    => null
																);

				$numero_detalle++;

        		// $subTotal += round($this->costo_subtotalVFI[$i],$_SESSION['DECIMALESMONEDA']);
        		$subTotal += $this->costo_subtotalVFI[$i];
			}
			$Porcentaje_Impuesto = 0;
			for($i = 0; $i < $this->contGruposArticulos; $i++){
				if($this->nombre_impuestoVFIG[$i] != null && ($this->exento_ivaVF == "No" || $this->exento_ivaVF == null || $this->exento_ivaVF == "")){
					$Porcentaje_Impuesto = $this->porcentaje_impuestoVFIG[$i];
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

					$totalBaseImpuesto[$this->codigo_impuesto_dianVFIG[$i]] += (string) round($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']);
					
					if(stripos($this->nombre_impuestoVFIG[$i],"servicio") !== false){
						$descripcionIVA = "IVA SERVICIOS";
					}
					else if(stripos($this->nombre_impuestoVFIG[$i],"compra") !== false){
						$descripcionIVA = "IVA COMPRAS";
					}
				}
				else{
					$arrayImpuestoItemGrupo[$i] = [
																					array(
																									"Base"           => "0",
																									"CodigoImpuesto" => "ZY",
																									"Nombre"         => "0",
																									"Porcentaje"     => 0,
																									"Impuesto"       => "0"
																					      )
																				];
					
					$aplica_impuestoVFIG[$i] = true;
					
					$totalBaseExentos += (string) round($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']);
				}

				//AÑADIMOS LAS OBSERVACIONES DE CADA ITEM
				$descripcionVFIG[$i][] = array(
																				"Nombre" => "detalleItem",
																				"Valor"  => ($this->observacionesVFIG[$i] != "")? $this->quitarTildes($this->observacionesVFIG[$i]) : ""
																			);
				
				//Creamos una variable que contenga el total del item
				$this->totalItemVFIG[$i] = $this->costo_subtotalVFIG[$i] + $this->costo_impuestoVFIG[$i];
				
				$arrayDetalle[] =	 array(
																	"idDetalle"           => (string) $numero_detalle,
																	"Nombre"              => $this->quitarTildes($this->nombreVFIG[$i]),
																	"UnidadCodigo"        => "EA",
																	"Cantidad"            => (float) $this->cantidadVFIG[$i],
																	"ValorUnitario"       => (float) $this->costo_unitarioVFIG[$i],
																	"Descuento"           => round($this->descuentoVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Cargos"              => 0,
																	"SubTotal"            => round($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Total"               => round($this->totalItemVFIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"codigo"              => $this->codigoVFIG[$i],
																	"Consecutivo"         => $this->numVFIG[$i],
																	"AplicaImpuesto"      => $aplica_impuestoVFIG[$i],
																	"Impuestos"           => $arrayImpuestoItemGrupo[$i],
																	"Porcentaje_Impuesto" => $Porcentaje_Impuesto,
																	"Descripcion"         => ($descripcionVFIG[$i] == null)? null : $descripcionVFIG[$i],
																	"AllowanceCharge"     => null,
																	"PricingReference"    => null
																);
				
				$numero_detalle++;
				
				$subTotal += round($this->costo_subtotalVFIG[$i],$_SESSION['DECIMALESMONEDA']);
				
				
			}

			if($descripcionIVA != null){
				$descripcionGeneralIVA = $descripcionIVA;
			}
			else{
				$descripcionGeneralIVA = "";
			}

			//------------------------------ IMPUESTOS -----------------------------//
			if(($this->exento_ivaVF == "No" || $this->exento_ivaVF == null || $this->exento_ivaVF == "") && $this->arrayImpuestos != null){
				foreach($this->arrayImpuestos as $codigoDian => $resultCodigoDian){
					foreach($resultCodigoDian as $porcentajeImpuesto => $result){
							$arrayImpuesto[] = array(
																				"Base"           => (string) round($totalBaseImpuesto[$codigoDian],$_SESSION['DECIMALESMONEDA']),
																				"CodigoImpuesto" => $codigoDian,
																		    "Nombre"         => (string) ($codigoDian == "01")? "IVA SERVICIOS 19%" : $result['nombre'],
																		    "Porcentaje"     => (float) $porcentajeImpuesto / 1,
																		    "Impuesto"       => (string) round($result['costo'],$_SESSION['DECIMALESMONEDA'])
																			);
							// SI ES IVA O IPOCONSUMO
							if($codigoDian == "04"){
								$codigoICO[] = $result['costo'];
							}
							if($codigoDian == "01"){
								$codigoIVA[] = $result['costo'];
							}
					}
				}
			}
      else{
        $arrayImpuesto = [array(
																	"Base"                => "0",
																	"CodigoImpuesto"      => "ZY",
																	"Nombre"              => "0",
																	"Porcentaje"          => 0,
																	"Impuesto"            => "0"
                                ),
        										array(
																	"Base"                => "0",
																	"CodigoImpuesto"      => "01",
																	"Nombre"              => "IVA SERVICIOS 19%",
																	"Porcentaje"          => 19,
																	"Impuesto"            => "0"
                                )];
      }

			//------------------------------ TOTAL IVA -----------------------------//
			$totalIVA = array_sum($codigoIVA);
			$totalICO = array_sum($codigoICO);
	  		

			//----------------------------- RETENCIONES ----------------------------//
			for($i = 0; $i < $this->contRetenciones; $i++){
				if($this->acumuladorBaseRetenciones > $this->baseVFR[$i]){
					if($this->tipo_retencionVFR[$i] == "ReteFuente"){
						$arrayImpuesto[] = array(
                                      "Base"           => (string) $this->baseVFR[$i],
																			// "Base"           => "0",
																			"CodigoImpuesto" => "06",
																			"Nombre"         => (string) "ReteFuente",
																			"Porcentaje"     => (float) $this->valorVFR[$i],
																			"Impuesto"       => (string) round(($this->acumuladorBaseRetenciones * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		);
						$totalRetencionesVF += ($this->acumuladorBaseRetenciones * $this->valorVFR[$i] / 100);

						$completar_retencion[] = "ReteFuente";
					}
					else if($this->tipo_retencionVFR[$i] == "ReteIva"){
						if($totalIVA > $this->baseVFR[$i]){
							$arrayImpuesto[] = array(
																				"Base"           => (string) $this->baseVFR[$i],
                                        // "Base"           => "0",
																				"CodigoImpuesto" => "05",
																				"Nombre"         => (string) "ReteIVA",
																				"Porcentaje"     => (float) $this->valorVFR[$i],
																				"Impuesto"       => (string) round(($totalIVA * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		  );
							$totalRetencionesVF += ($totalIVA * $this->valorVFR[$i] / 100);
						}

						$completar_retencion[] = "ReteIVA";
					}
					else if($this->tipo_retencionVFR[$i] == "ReteIca"){
							$arrayImpuesto[] = array(
																			"Base"           => (string) $this->baseVFR[$i],
                                      // "Base"           => "0",
																			"CodigoImpuesto" => "07",
																			"Nombre"         => (string) "ReteICA",
																			"Porcentaje"     => (float) $this->valorVFR[$i],
																			"Impuesto"       => (string) round(($this->acumuladorBaseRetenciones * $this->valorVFR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																	  );
						$totalRetencionesVF += ($this->acumuladorBaseRetenciones * $this->valorVFR[$i] / 100);									  
						$completar_retencion[] = "ReteICA";
					}
				}
			}

			if(!in_array("ReteFuente",$completar_retencion)){
				$arrayImpuesto[] = array(
                                  "Base"           => "0",
																	"CodigoImpuesto" => "06",
																	"Nombre"         => (string) "ReteFuente",
																	"Porcentaje"     => (float) 0,
																	"Impuesto"       => (string) 0
																);
			}
			if(!in_array("ReteIVA",$completar_retencion)){
				$arrayImpuesto[] = array(
                                  "Base"           => "0",
																	"CodigoImpuesto" => "05",
																	"Nombre"         => (string) "ReteIVA",
																	"Porcentaje"     => (float) 0,
																	"Impuesto"       => (string) 0
																);
			}
			if(!in_array("ReteICA",$completar_retencion)){
				$arrayImpuesto[] = array(
                                  "Base"           => "0",
																	"CodigoImpuesto" => "07",
																	"Nombre"         => (string) "ReteICA",
																	"Porcentaje"     => (float) 0,
																	"Impuesto"       => (string) 0
																);
			}

			// ---------------------------- TOTAL FACTURA ---------------------------//
			$totalVF = round(($subTotal + round($totalIVA,$_SESSION['DECIMALESMONEDA']) + round($totalICO,$_SESSION['DECIMALESMONEDA'])),$_SESSION['DECIMALESMONEDA']);

			//--------------------------- TERCEROS EMAIL'S -------------------------//
			for($i = 0; $i < $this->contTercerosDireccionesEmail; $i++){
				$arrayTercerosEmail[$i] = $this->emailTDE[$i];
			}

			$emails = implode(',',$arrayTercerosEmail);

			// VERIFICAMOS SI LA EMPRESA TIENE EMAILS CONFIGURADOS
			if($emails == ""){
				$emails = $this->email_feVF;
			}

			//SI NO TIENE EMAILS EN LA PESTAÑA DE EMAILFE ENTONCES SE TOMA EL EMAIL DEL TERCERO
			if($emails == ""){
				$emails	= $this->emailT;
			}

			// SI LOS DATOS DE LA SUCURSAL ESTAN INCOMPLETOS TOMAMOS LOS PRINCIPALES DEL TERCERO
			if($this->departamentoTD != null || $this->ciudadTD != null){
				$direccion_tercero   = $this->direccionTD;
				$telefono_tercero    = $this->telefono1TD;
				$nombre_departamento = $this->departamentoTD;
				$codigo_departamento = $this->codigo_departamentoTD;
				$nombre_ciudad       = $this->ciudadTD;
				$codigo_ciudad       = $this->codigo_ciudadTD;
			}
			else{
				$direccion_tercero   = $this->direccionT;
				$telefono_tercero    = $this->telefonoT;
				$nombre_departamento = $this->nombre_departamentoT;
				$codigo_departamento = $this->codigo_departamentoT;
				$nombre_ciudad       = $this->nombre_ciudadT;
				$codigo_ciudad       = $this->codigo_ciudadT;
				
			}

			//---------------------------- TIPO PERSONA ----------------------------//
			if($this->tipo_persona_codigoT == null || $this->tipo_persona_codigoT == "" || $this->tipo_persona_codigoT == 0){
				$tipo_persona_tercero = ($this->codigo_tipo_documento_dianT == "31")? "1" : "2";
			}
			else{
				$tipo_persona_tercero = $this->tipo_persona_codigoT;
			}

		//------------------------ DETALLES COMPROBANTE ------------------------//
		$total_a_pagar = (($totalVF - $totalRetencionesVF - $this->valor_anticipo)>=0)? ($totalVF - $totalRetencionesVF - $this->valor_anticipo) : 0;
		$arrayDetallesComprobante = [
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
			),
			array(
				"Nombre" => "Total Factura",
				"Valor"  => round(($totalVF - $totalRetencionesVF),$_SESSION['DECIMALESMONEDA'])
			),
			array(
				"Nombre" => "Descripcion IVA",
				"Valor"  => ($descripcionGeneralIVA == null)? "" : $descripcionGeneralIVA
			),
			array(
				"Nombre" => "orden_compra",
				"Valor"  => ($this->orden_compraVF == null)? "" : $this->orden_compraVF
			),
			array(
				"Nombre" => "OC",
				"Valor"  => ($this->orden_compraVF == null)? "" : $this->orden_compraVF
			),
			array(
				"Nombre" => "Total anticipo",
				"Valor"  => ($this->valor_anticipo == null)? "" : $this->valor_anticipo
			),
			array(
				"Nombre" => "Total a pagar",
				"Valor"  => ($this->valor_anticipo == null)? ($totalVF - $totalRetencionesVF) : $total_a_pagar
			)
		];

      //---------------------------- INFO RESERVA ----------------------------//
      if($this->info_reservaVF != "" || $this->info_reservaVF != null){
        $datosReserva = json_decode($this->info_reservaVF,TRUE);

        foreach($datosReserva as $key => $value){
          if($key == "huesped"){
            $key = "Huesped";
          }
          else if($key == "numero_reserva"){
            $key = "No.Reserva";
          }
          else if($key == "fecha_llegada"){
            $key = "Llegada";
          }
          else if($key == "fecha_salida"){
            $key = "Salida";
          }
          else if($key == "habitacion"){
            $key = "Hab.No.";
          }
          else if($key == "tarifa"){
            $key = "Tarifa";
          }
          else if($key == "numero_personas"){
            $key = "No.Persona";
          }
          else if($key == "acompanantes"){
            $key = "Acompanantes";
          }
          else if($key == "numero_noches"){
            $key = "No.Noches";
          }
          else if($key == "forma_pago"){
            $key = "forma_pago";
          }
          else if($key == "creado_por"){
            $key = "creado_por";
          }

          $value = ($value == false)? "" : $value;
          $arrayDetallesComprobante[] = array(
                                                "Nombre" => "$key",
                                                "Valor" => "$value"
                                              );
        }
       
      }

	  	//AÑADIMOS LAS BASES PARA LOS HOTELES
		$arrayDetallesComprobante[] = array(
			"Nombre" => "Base gravada 19 %",
			"Valor" => ($totalBaseImpuesto["01"] != null)? $totalBaseImpuesto["01"] : 0
		);
		$arrayDetallesComprobante[] = array(
			"Nombre" => "Base ICO 8 %",
			"Valor" => ($totalBaseImpuesto["04"] != null)? $totalBaseImpuesto["04"] : 0
		);
		$arrayDetallesComprobante[] = array(
			"Nombre" => "Total Base Gravada",
			"Valor" => ($totalBaseImpuesto != null)? array_sum($totalBaseImpuesto) : 0
		);
		$arrayDetallesComprobante[] = array(
			"Nombre" => "Exentos",
			"Valor" => ($totalBaseExentos != null)? $totalBaseExentos : 0
		);
		$arrayDetallesComprobante[] = array(
			"Nombre" => "Excluidos",
			"Valor" => 0
		);
		$arrayDetallesComprobante[] = array(
			"Nombre" => "Total Base No Gravada",
			"Valor" => ($totalBaseExentos != null)? $totalBaseExentos : 0
		); 

			//--------------------------- ARRAY PRINCIPAL --------------------------//
		$subtotal = round($subTotal,$_SESSION['DECIMALESMONEDA']);
		$totalImpuestos = round($totalIVA+$totalICO,$_SESSION['DECIMALESMONEDA']);
		$totalFacturaVenta = $subtotal +  $totalImpuestos;
      $arrayPrincipal = array(
        "Comprobante" => array(
                                "TipoComprobante"               => "01",
                                // "Fecha"                         => date('Y-m-d'),
                                "Fecha"                         => $this->fecha_inicioVF,
                                "Prefijo"                       => ($this->prefijoVF != null)? $this->prefijoVF : "",
                                "Numero"                        => (int) $this->numero_facturaVF,
                                "Moneda"                        => $this->monedaE,
                                "Referencia"                    => "",
                                "ConceptoRef"                   => "",
																"Observaciones"		              => ($this->observacionVF != null)? $this->quitarTildes($this->observacionVF) : "",
																"Usuario"                       => ($this->quitarTildes($_SESSION['NOMBREFUNCIONARIO']) == "")? "Usuario Soporte" : $this->quitarTildes($_SESSION['NOMBREFUNCIONARIO']),
																"NumeroOrden"                   => ($this->orden_compraVF != null)? $this->quitarTildes($this->orden_compraVF) : "",
																"NumeroDespacho"                => "",
																"NumeroRecepcion"               => "",
																"DocumentoAdicionalNotaCredito" => "",
																"DocumentoReferenciaCodigo"     => "",
                                "Descripcion" 									=> [],
                                "MetodoPago"  									=> [
																																			array(
										                                                        	"FormaPago" => ($this->forma_pagoVF == "Contado")? "1" : "2",
																																							"MedioPago" => $this->codigo_metodo_pago_dianVF,
																																							"Fecha"     => $this->fecha_inicioVF
									                                                         )
																																		]
                              ),
        "Emisor" =>  array(
                            "Identificacion"           => str_replace(array(".","-"),"",$this->documentoE),
														"DigitoVerificador"        => $this->digito_verificacionE,
														"TipoPersona"              => $this->tipo_persona_codigoE,
                            "TipoIdentificacion"       => $this->codigo_tipo_documento_dianE,
														"TipoEmisor"               => "R-99-PN",
                            "RazonSocial"              => $this->quitarTildes($this->razon_socialE),
                            "NombreComercial"          => $this->quitarTildes($this->nombreE),
														"Sucursal"                 => ($_SERVER['SERVER_NAME'] == "logicalerp.localhost")? "PRIN" : $this->quitarTildes($this->sucursalVF),
                            "Direccion"                => $this->quitarTildes($this->direccionE),
														"Telefono"                 => $this->telefonoE,
														"email"                    => $this->quitarTildes($this->emailE),
                            "Pais"                     => $this->quitarTildes($this->paisE),
														"PaisCodigo"               => $this->iso2E,
														"Departamento"             => $this->quitarTildes($this->departamentoE),
														"DepartamentoCodigo"       => $this->codigo_departamentoE,
														"Ciudad"                   => $this->quitarTildes($this->ciudadE),
														"CiudadCodigo"             => $this->codigo_ciudadE,
														"CodigoPostal"             => ($this->codigo_postalE == null)? "" : $this->codigo_postalE,
														"NumeroMatriculaMercantil" => ($this->numero_matricula_mercantilE == null)? "" : $this->numero_matricula_mercantilE,
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
															"TipoPersona"              => $tipo_persona_tercero,
                              "TipoIdentificacion"       => $this->codigo_tipo_documento_dianT,
															"TipoReceptor"             => "R-99-PN",
                              "RazonSocial"              => $this->quitarTildes($this->nombreT),
                              "NombreComercial"          => $this->quitarTildes($this->nombre_comercialT),
                              "Direccion"                => ($direccion_tercero != "")? $this->quitarTildes($direccion_tercero) : "",
															"Telefono"                 => ($telefono_tercero != "")? $telefono_tercero : "",
															"email"                    => $emails,
                              "Pais"                     => $this->quitarTildes($this->paisT),
															"PaisCodigo"               => $this->iso2T,
															"Departamento"             => ($this->id_paisT == 49)? $this->quitarTildes($nombre_departamento) : "",
															"DepartamentoCodigo"       => ($this->id_paisT == 49)? $codigo_departamento : "",
															"Ciudad"                   => ($this->id_paisT == 49)? $this->quitarTildes($nombre_ciudad) : "",
															"CiudadCodigo"             => ($this->id_paisT == 49)? $codigo_ciudad : "",
															"CodigoPostal"             => ($this->codigo_postalTD == null)? "" : $this->codigo_postalTD,
															"NumeroMatriculaMercantil" => ($this->numero_matricula_mercantilTD == null)? "" : $this->numero_matricula_mercantilTD,
															"Descripcion" 				     => [
																															array(
								                                                      "Nombre" => "Sector Empresarial",
								                                                      "Valor"  => ($this->sector_empresarialT != null)? $this->quitarTildes($this->sector_empresarialT) : ""
							                                                      )
																												    ]
                            ),
        "Detalles" => $arrayDetalle,
        "Totales" => array(
                            "Total"                       => (string) $totalFacturaVenta,
														"TotalEnLetras"               => $this->quitarTildes($this->num2letras($totalVF)),
														"SubTotal"                    => (string) $subtotal,
														// "SubTotal"                    => (string) $subTotal,
														"Cargos"                      => "0",
														"Descuentos"                  => "0",
														"SubTotalSinCargosDescuentos" => (string) $subtotal,
														// "SubTotalSinCargosDescuentos" => (string) $subTotal,
														"IVA"					                => ($totalIVA != null || $totalICO != null)? (string) $totalImpuestos : "0"
                          ),
				"TotalImpuestos" => $arrayImpuesto,
        "DetallesComprobante" => $arrayDetallesComprobante,       													
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
																  "Duracion"     => ($this->dias_pagoVF != "")? $this->dias_pagoVF : "1"
																)
      );

	  $this->arrayFinal = $this->utf8_encode_recursive($arrayPrincipal);
      $this->arrayFinal = json_encode($this->arrayFinal, JSON_PRETTY_PRINT);
	  
			//echo json_last_error_msg();
    }

	public function utf8_encode_recursive($mixed) {
		if (is_array($mixed)) {
			foreach ($mixed as &$valor) {
				$valor = $this->utf8_encode_recursive($valor);
			}
		} elseif (is_string($mixed)) {
			$mixed = utf8_encode($mixed);
		}
		return $mixed;
	}

	public function enviarJSON(){

		if(str_contains($_SERVER['SERVER_NAME'],'localhost')){
			// API para enviar el JSON a la DIAN
			$url_api = "http://fst.facse.net/api/comunicacion/ComprobanteJson";

			// Cambiamos la url de validacion por la del envio
			$params                   = [];
			$params['request_url']    = $url_api;
			$params['request_method'] = "POST";
			$params['Authorization']  = "";
			$params['data']           = $this->arrayFinal;

			// Consumimos el API y obtenemos sus resultados
			$respuesta = $this->curlApi($params);
			$respuesta = json_decode($respuesta,true);

        	$validar = $respuesta['RespuestaFacse'];

			$respuestaFinal['validar']     = $this->quitarTildes($validar);
			$respuestaFinal['comprobante'] = "Se ejecuto el envio en desarrollo";
			$respuestaFinal['id_factura']  = $respuesta['IdDocumento']['Contenido'];
			$respuestaFinal['cufe']        = $respuesta['CufeDocumento']['Contenido'];

			return $respuestaFinal;
		}
		else{
			// API para enviar el JSON a la DIAN
			$url_api = "https://web.facse.net:444/api/Comunicacion/ComprobanteJson";

			// Creamos los parametros para consumir la API
			$params                   = [];
			$params['request_url']    = $url_api;
			$params['request_method'] = "POST";
			$params['Authorization']  = "";
			$params['data']           = $this->arrayFinal;

			// Consumimos el API y obtenemos sus resultados
			$respuesta = $this->curlApi($params);
			$respuesta = json_decode($respuesta,true);

			$validar = $respuesta['RespuestaFacse'];

			$respuestaFinal['validar']     = $this->quitarTildes($validar);
			$respuestaFinal['comprobante'] = "Se ejecuto el envio en produccion";
			$respuestaFinal['id_factura']  = $respuesta['IdDocumento']['Contenido'];
			$respuestaFinal['cufe']        = $respuesta['CufeDocumento']['Contenido'];

			return $respuestaFinal;
		}
	}

	public function imprimirJSON(){
		return $this->arrayFinal;
	}

	public function curlApi($params){
		$client = curl_init();
		$options = array(
			CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
			CURLOPT_URL            => "$params[request_url]",
			CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS     => $params['data'],
			CURLOPT_SSL_VERIFYPEER => false
		);
		curl_setopt_array($client,$options);
		$response    = curl_exec($client);
		$curl_errors = curl_error($client);

		if(!empty($curl_errors)){
			$response['status']               = 'failed';
			$response['errors'][0]['titulo']  = curl_getinfo($client);
			$response['errors'][0]['detalle'] = curl_error($client);
		}

		$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
		curl_close($client);
		return $response;
	}

	public function num2letras($num,$fem = false,$dec = false){
		$float = explode('.',$num);
		$num   = $float[0];
		$num2  = $float[1];

		$end_num  = $this->convertir($num, $fem = false, $dec = false);
		$end_num2 = $this->convertir($num2, $fem = false, $dec = false);
		if($end_num2 <> ''){
			return $end_num . ' ' . $_SESSION['DESCRIMONEDA'] . ' con ' . $end_num2 . ' centavos';
		}
		else{
			return $end_num . ' ' . $_SESSION['DESCRIMONEDA'];
		}
	}

	public function convertir($num,$fem = false,$dec = false){
		$matuni[2]  = "dos";
		$matuni[3]  = "tres";
		$matuni[4]  = "cuatro";
		$matuni[5]  = "cinco";
		$matuni[6]  = "seis";
		$matuni[7]  = "siete";
		$matuni[8]  = "ocho";
		$matuni[9]  = "nueve";
		$matuni[10] = "diez";
		$matuni[11] = "once";
		$matuni[12] = "doce";
		$matuni[13] = "trece";
		$matuni[14] = "catorce";
		$matuni[15] = "quince";
		$matuni[16] = "dieciseis";
		$matuni[17] = "diecisiete";
		$matuni[18] = "dieciocho";
		$matuni[19] = "diecinueve";
		$matuni[20] = "veinte";

		$matunisub[2] = "dos";
		$matunisub[3] = "tres";
		$matunisub[4] = "cuatro";
		$matunisub[5] = "quin";
		$matunisub[6] = "seis";
		$matunisub[7] = "sete";
		$matunisub[8] = "ocho";
		$matunisub[9] = "nove";

		$matdec[2] = "veint";
		$matdec[3] = "treinta";
		$matdec[4] = "cuarenta";
		$matdec[5] = "cincuenta";
		$matdec[6] = "sesenta";
		$matdec[7] = "setenta";
		$matdec[8] = "ochenta";
		$matdec[9] = "noventa";
		$matsub[3]  = 'mill';
		$matsub[5]  = 'bill';
		$matsub[7]  = 'mill';
		$matsub[9]  = 'trill';
		$matsub[11] = 'mill';
		$matsub[13] = 'bill';
		$matsub[15] = 'mill';

		$matmil[4]  = 'millones';
		$matmil[6]  = 'billones';
		$matmil[7]  = 'de billones';
		$matmil[8]  = 'millones de billones';
		$matmil[10] = 'trillones';
		$matmil[11] = 'de trillones';
		$matmil[12] = 'millones de trillones';
		$matmil[13] = 'de trillones';
		$matmil[14] = 'billones de trillones';
		$matmil[15] = 'de billones de trillones';
		$matmil[16] = 'millones de billones de trillones';

		$num = trim((string)@$num);

		if($num[0] == '-'){
		    $neg = 'menos ';
		    $num = substr($num, 1);
		}
		else{
		    $neg = '';
		    while($num[0] == '0') $num = substr($num, 1);
		    if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
		    $zeros = true;
		    $punt = false;
		    $ent = '';
		    $fra = '';

		    for($c = 0; $c < strlen($num); $c++){
		      $n = $num[$c];

		      if(!(strpos(".,'''", $n) === false)){
	          if($punt){
						  break;
						}
	          else{
	            $punt = true;
	            continue;
	          }
		      }
					elseif(!(strpos('0123456789', $n) === false)){
		        if($punt){
		          if($n != '0'){
								$zeros = false;
		            $fra .= $n;
							}
		        }
						else{
							$ent .= $n;
						}
		      }
					else{
						break;
					}
		    }

		    $ent = '     ' . $ent;
		    if($dec and $fra and ! $zeros){
		      $fin = ' coma';
		      for($n = 0; $n < strlen($fra); $n++){
		        if(($s = $fra[$n]) == '0'){
		          $fin .= ' cero';
						}
		        elseif($s == '1'){
							$fin .= $fem ? ' una' : ' un';
						}
		        else{
		          $fin .= ' ' . $matuni[$s];
						}
		      }
		    }
				else{
					$fin = '';
				}

				if((int)$ent === 0){ return 'Cero' . $fin; }
		    $tex = '';
		    $sub = 0;
		    $mils = 0;
		    $neutro = false;

		    while(($num = substr($ent, -3)) != '   '){
		      $ent = substr($ent, 0, -3);

		      if(++$sub < 3 and $fem){
		        $matuni[1] = 'una';
		        $subcent = 'as';
		      }
					else{
		        $matuni[1] = $neutro ? 'un' : 'uno';
		        $subcent = 'os';
		      }

		      $t = '';
		      $n2 = substr($num, 1);
		      if($n2 == '00'){
		      }
					elseif ($n2 < 21){
		        $t = ' ' . $matuni[(int)$n2];
					}
		      elseif($n2 < 30){
		        $n3 = $num[2];
		        if($n3 != 0){ $t = 'i' . $matuni[$n3]; }
		        $n2 = $num[1];
		        $t = ' ' . $matdec[$n2] . $t;
		      }
					else{
		        $n3 = $num[2];
		        if($n3 != 0){ $t = ' y ' . $matuni[$n3]; }
		        $n2 = $num[1];
		        $t = ' ' . $matdec[$n2] . $t;
		      }

		      $n = $num[0];
		      if($n == 1){
		        $t = ' ciento' . $t;
		      }
					elseif($n == 5){
		        $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
		      }
					elseif($n != 0){
		        $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
		      }

		      if($sub == 1){
		      }
					elseif(!isset($matsub[$sub])){
		        if($num == 1){
		          $t = ' mil';
		        }
						elseif($num > 1){
		          $t .= ' mil';
		        }
		      }
					elseif($num == 1){
		        $t .= ' ' . $matsub[$sub] . 'on';
		      }
					elseif($num > 1){
		        $t .= ' ' . $matsub[$sub] . 'ones';
		      }

		      if($num == '000'){
						$mils ++;
					}
		      elseif($mils != 0){
		        if(isset($matmil[$sub])){ $t .= ' ' . $matmil[$sub]; }
		        $mils = 0;
		      }
		      $neutro = true;
		      $tex = $t . $tex;
		    }

		    $tex = $neg . substr($tex, 1) . $fin;

		    $end_num = ucfirst($tex);
		    return $end_num;
			}
	}
}
?>
