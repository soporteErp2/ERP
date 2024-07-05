<?php
  /**
   *@class ClassDevolucionJSON_V2
   */
  class ClassDevolucionJSON_V2{
    public $mysql;

    function __construct($mysql){
      $this->mysql = $mysql;
    }

    public function obtenerDatos($codigoDevolucionVenta,$id_empresa){
      //---------------- DATOS DE LA CABECERA DE LA DEVOLUCION ---------------//
      $sqlDevolucionesVentas = "SELECT
                                  DV.id,
                                  DV.fecha_registro,
                                  DV.consecutivo,
                                  DV.nit,
                                  DV.observacion,
                                  DV.id_metodo_pago,
                                  DV.metodo_pago,
                                  DV.id_sucursal,
                                  DV.sucursal,
                                  DV.id_motivo_dian,
                                  DV.descripcion_motivo_dian,
                                  DV.id_documento_venta,
                                  DV.numero_documento_venta,
                                  VF.id_sucursal_cliente,
                                  VF.exento_iva,
                                  VF.dias_pago,
                                  VF.email_fe,
                                  VF.info_reserva,
                                  VF.fecha_vencimiento,
                                  VF.nombre_vendedor,
                                  VF.documento_vendedor,
                                  CMP.codigo_metodo_pago_dian,
                                  CMP.nombre AS nombre_metodo_pago_dian,
                                  CCP.estado
                                FROM
                                  devoluciones_venta AS DV
                                LEFT JOIN
                                  configuracion_metodos_pago AS CMP
                                ON
                                  DV.id_metodo_pago = CMP.id
                                LEFT JOIN
                                  ventas_facturas AS VF
                                ON
                                  DV.id_documento_venta = VF.id
                                LEFT JOIN
                                  configuracion_cuentas_pago AS CCP
                                ON
                                  VF.id_configuracion_cuenta_pago = CCP.id
                                WHERE
                                  DV.activo = 1
                                AND
                                  DV.estado = 1
                                AND
                                  DV.id_empresa = $id_empresa
                                AND
                                  DV.id = $codigoDevolucionVenta";

      $queryDevolucionesVentas = $this->mysql->query($sqlDevolucionesVentas,$this->mysql->link);

      if(!$queryDevolucionesVentas){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos principales de la devolucion.");' . '</script>';
				exit;
			}

      $this->idDV                      = $this->mysql->result($queryDevolucionesVentas,0,'id');
      $this->fecha_registroDV          = $this->mysql->result($queryDevolucionesVentas,0,'fecha_registro');
      $this->consecutivoDV             = $this->mysql->result($queryDevolucionesVentas,0,'consecutivo');
      $this->nitDV                     = $this->mysql->result($queryDevolucionesVentas,0,'nit');
      $this->observacionDV             = $this->mysql->result($queryDevolucionesVentas,0,'observacion');
      $this->id_metodo_pagoDV          = $this->mysql->result($queryDevolucionesVentas,0,'id_metodo_pago');
      $this->metodo_pagoDV             = $this->mysql->result($queryDevolucionesVentas,0,'metodo_pago');
      $this->id_sucursalDV             = $this->mysql->result($queryDevolucionesVentas,0,'id_sucursal');
      $this->sucursalDV                = $this->mysql->result($queryDevolucionesVentas,0,'sucursal');
      $this->id_motivo_dianDV          = $this->mysql->result($queryDevolucionesVentas,0,'id_motivo_dian');
      $this->descripcion_motivo_dianDV = $this->mysql->result($queryDevolucionesVentas,0,'descripcion_motivo_dian');
      $this->id_documento_ventaDV      = $this->mysql->result($queryDevolucionesVentas,0,'id_documento_venta');
      $this->numero_documento_ventaDV  = $this->mysql->result($queryDevolucionesVentas,0,'numero_documento_venta');
      $this->codigo_metodo_pago_dianDV = $this->mysql->result($queryDevolucionesVentas,0,'codigo_metodo_pago_dian');
      $this->nombre_metodo_pago_dianDV = $this->mysql->result($queryDevolucionesVentas,0,'nombre_metodo_pago_dian');
      $this->id_sucursal_clienteDV     = $this->mysql->result($queryDevolucionesVentas,0,'id_sucursal_cliente');
      $this->exento_ivaDV              = $this->mysql->result($queryDevolucionesVentas,0,'exento_iva');
      $this->forma_pagoDV              = $this->mysql->result($queryDevolucionesVentas,0,'estado');
      $this->dias_pagoDV               = $this->mysql->result($queryDevolucionesVentas,0,'dias_pago');
      $this->email_feDV                = $this->mysql->result($queryDevolucionesVentas,0,'email_fe');
      $this->info_reservaDV            = $this->mysql->result($queryDevolucionesVentas,0,'info_reserva');
      $this->fecha_vencimientoDV       = $this->mysql->result($queryDevolucionesVentas,0,'fecha_vencimiento');
      $this->nombre_vendedorDV         = $this->mysql->result($queryDevolucionesVentas,0,'nombre_vendedor');
      $this->documento_vendedorDV      = $this->mysql->result($queryDevolucionesVentas,0,'documento_vendedor');

      //------------------- DATOS DEL EMISOR O LA EMPRESA --------------------//
      $sqlEmpresa =  "SELECT
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
                      	E.id = $id_empresa
                      AND
												ES.id = '$this->id_sucursalDV'
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
                        T.numero_identificacion = '$this->nitDV'
                      AND
                        T.id_empresa = '$id_empresa'";

      $queryTerceros = $this->mysql->query($sqlTerceros,$this->mysql->link);

      if(!$queryTerceros){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos del cliente.");' . '</script>';
				exit;
			}

      $this->idT                         = $this->mysql->result($queryTerceros,0,'id');
      $this->id_tipo_persona_dianT       = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
      $this->numero_identificacionT      = $this->mysql->result($queryTerceros,0,'numero_identificacion');
      $this->nombreT                     = $this->mysql->result($queryTerceros,0,'nombre');
      $this->nombre_comercialT           = $this->mysql->result($queryTerceros,0,'nombre_comercial');
      $this->emailT                      = $this->mysql->result($queryTerceros,0,'email');
      $this->id_paisT                    = $this->mysql->result($queryTerceros,0,'id_pais');
      $this->paisT                       = $this->mysql->result($queryTerceros,0,'pais');
      $this->iso2T                       = $this->mysql->result($queryTerceros,0,'iso2');
      $this->sector_empresarialT         = $this->mysql->result($queryTerceros,0,'sector_empresarial');
      $this->dvT                         = $this->mysql->result($queryTerceros,0,'dv');
      $this->tipo_persona_codigoT        = $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
      $this->codigo_regimen_dianT        = $this->mysql->result($queryTerceros,0,'codigo_regimen_dian');
      $this->nombre_departamentoT        = $this->mysql->result($queryTerceros,0,'departamento');
      $this->codigo_departamentoT        = $this->mysql->result($queryTerceros,0,'codigo_departamento');
      $this->nombre_ciudadT              = $this->mysql->result($queryTerceros,0,'ciudad');
      $this->codigo_ciudadT              = $this->mysql->result($queryTerceros,0,'codigo_ciudad');
      $this->codigo_tipo_documento_dianT = $this->mysql->result($queryTerceros,0,'codigo_tipo_documento_dian');
      $this->telefonoT                   = $this->mysql->result($queryTerceros,0,'telefono1');
      $this->direccionT                  = $this->mysql->result($queryTerceros,0,'direccion');

      $sqlTercerosDireccion  = "SELECT
																	TD.id,
																	TD.direccion,
																	TD.ciudad,
																	TD.departamento,
																	TD.telefono1,
                                  TD.codigo_postal,
                                  UD.departamento,
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
																	TD.id = '$this->id_sucursal_clienteDV'
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
																				terceros_direcciones As TD
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
																					VFR.id_factura_venta = $this->id_documento_ventaDV";

	    $queryDevolucionesVentasRetenciones = $this->mysql->query($sqlVentasFacturasRetenciones,$this->mysql->link);

      if(!$queryDevolucionesVentasRetenciones){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron las retenciones de la factura.");' . '</script>';
				exit;
			}

      //Contamos el numero de retenciones que posee la factura
      $this->contRetenciones = $this->mysql->num_rows($queryDevolucionesVentasRetenciones);
			for($i = 0; $i < $this->contRetenciones; $i++){
				$this->valorDVR[$i] 				 = $this->mysql->result($queryDevolucionesVentasRetenciones,$i,'valor');
        $this->baseDVR[$i]  				 = $this->mysql->result($queryDevolucionesVentasRetenciones,$i,'base');
				$this->retencionDVR[$i]      = $this->mysql->result($queryDevolucionesVentasRetenciones,$i,'retencion');
				$this->tipo_retencionDVR[$i] = $this->mysql->result($queryDevolucionesVentasRetenciones,$i,'tipo_retencion');
			}

      //----------------------- DATOS DE lOS ARTICULOS -----------------------//
			$sqlDevolucionesInventario = "SELECT
																			DVI.codigo,
																			SUM(DVI.cantidad) AS cantidad,
																			DVI.nombre,
																			DVI.costo_unitario,
                                      DVI.observaciones,
																			DVI.tipo_descuento,
																			DVI.descuento,
																			DVI.impuesto,
																			DVI.valor_impuesto,
																			I.codigo_impuesto_dian,
                                      IU.codigo_dian AS codigo_unidad_medida
																		FROM
																			devoluciones_venta_inventario AS DVI
																		LEFT JOIN
																			devoluciones_venta AS DV
																		ON
																			DVI.id_devolucion_venta = DV.id
																		LEFT JOIN
																			impuestos AS I
																		ON
																			I.id = DVI.id_impuesto
                                    LEFT JOIN
                                      ventas_facturas_inventario_grupos AS VFIG
                                    ON
                                      DVI.id_fila_cargada = VFIG.id_inventario_factura_venta
                                    LEFT JOIN
                                      inventario_unidades AS IU
                                    ON
                                      DVI.id_unidad_medida = IU.id
																		WHERE
																			DVI.activo = 1
																		AND
																			DVI.id_devolucion_venta = $this->idDV
                                    AND
                                      VFIG.id_inventario_factura_venta IS NULL
                                    AND
                                      DV.id_empresa = '$id_empresa'
                                    GROUP BY
                                      DVI.codigo,DVI.costo_unitario,DVI.tipo_descuento,DVI.descuento,DVI.observaciones";

			$queryDevolucionesInventario = $this->mysql->query($sqlDevolucionesInventario,$this->mysql->link);

      if(!$queryDevolucionesInventario){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los articulos de la devolucion.");' . '</script>';
				exit;
			}

			//Contamos el numero de articulos que posee la factura
			$this->contArticulos = $this->mysql->num_rows($queryDevolucionesInventario);

      for($i = 0; $i < $this->contArticulos; $i++){
				$this->codigoDVI[$i]     				   = $this->mysql->result($queryDevolucionesInventario,$i,'codigo');
				$this->cantidadDVI[$i] 						 = $this->mysql->result($queryDevolucionesInventario,$i,'cantidad');
				$this->nombreDVI[$i] 							 = $this->mysql->result($queryDevolucionesInventario,$i,'nombre');
				$this->costo_unitarioDVI[$i] 			 = $this->mysql->result($queryDevolucionesInventario,$i,'costo_unitario');
        $this->observacionesDVI[$i]        = $this->mysql->result($queryDevolucionesInventario,$i,'observaciones');
				$this->tipo_descuentoDVI[$i] 			 = $this->mysql->result($queryDevolucionesInventario,$i,'tipo_descuento');
				$this->descuentoDVI[$i] 		 			 = $this->mysql->result($queryDevolucionesInventario,$i,'descuento');
				$this->impuestoDVI[$i] 				     = $this->mysql->result($queryDevolucionesInventario,$i,'impuesto');
				$this->valor_impuestoDVI[$i] 			 = $this->mysql->result($queryDevolucionesInventario,$i,'valor_impuesto');
        $this->codigo_impuesto_dianDVI[$i] = $this->mysql->result($queryDevolucionesInventario,$i,'codigo_impuesto_dian');
				$this->codigo_unidad_medidaDVI[$i] = $this->mysql->result($queryDevolucionesInventario,$i,'codigo_unidad_medida');
			}

      //Buscamos primero si el articulo tiene o no descuento
			for($i = 0; $i < $this->contArticulos; $i++){
				if($this->descuentoDVI[$i] != 0){
					if($this->tipo_descuentoDVI[$i] == "porcentaje"){
						$this->costo_subtotalDVI[$i] = ($this->cantidadDVI[$i] * $this->costo_unitarioDVI[$i]) - ($this->cantidadDVI[$i] * $this->costo_unitarioDVI[$i] * $this->descuentoDVI[$i] / 100);
            $this->descuento_itemDVI[$i] = ($this->cantidadDVI[$i] * $this->costo_unitarioDVI[$i] * $this->descuentoDVI[$i] / 100);
					} else if($this->tipo_descuentoDVI[$i] == "pesos"){
						$this->costo_subtotalDVI[$i] = ($this->cantidadDVI[$i] * $this->costo_unitarioDVI[$i]) - $this->descuentoDVI[$i];
            $this->descuento_itemDVI[$i] = $this->descuentoDVI[$i];
					}
				} else{
					$this->costo_subtotalDVI[$i] = $this->cantidadDVI[$i] * $this->costo_unitarioDVI[$i];
          $this->descuento_itemDVI[$i] = 0.00;
				}
				//Buscamos si el tercero esta o no exento de IVA
        if($this->exento_ivaDV == "Si"){
          $this->costo_impuestoDVI[$i] = 0;
        }
        else{
          if($this->valor_impuestoDVI[$i] != null && ($this->impuestoDVI[$i] != "" || $this->impuestoDVI[$i] != null)){
            $this->costo_impuestoDVI[$i] = $this->costo_subtotalDVI[$i] * $this->valor_impuestoDVI[$i] / 100;
          }
        }
			}

      for($i = 0; $i < $this->contArticulos; $i++){
        if($this->valor_impuestoDVI[$i] != null && ($this->impuestoDVI[$i] != "" || $this->impuestoDVI[$i] != null)){
          $this->arrayImpuestos[$this->codigo_impuesto_dianDVI[$i]][$this->valor_impuestoDVI[$i]]['costo'] += $this->costo_impuestoDVI[$i];
				  $this->arrayImpuestos[$this->codigo_impuesto_dianDVI[$i]][$this->valor_impuestoDVI[$i]]['nombre'] = $this->impuestoDVI[$i];
        }
      }

      //---------------- DATOS DE LOS GRUPOS DE LOS ARTICULOS ----------------//
      $sqlDevolucionesInventarioGrupos = "SELECT
																						DVG.codigo,
																						DVG.cantidad,
																						DVG.nombre,
																						DVG.costo_unitario,
                                            DVG.observaciones,
																						DVG.descuento,
																						DVG.nombre_impuesto,
																						DVG.porcentaje_impuesto,
																						I.codigo_impuesto_dian
																					FROM
																						devoluciones_venta_grupos AS DVG
																					LEFT JOIN
																						impuestos AS I
																					ON
																						I.id = DVG.id_impuesto
																					WHERE
																						DVG.activo = 1
																					AND
																						DVG.id_empresa = '$id_empresa'
																					AND
																						DVG.id_devolucion_venta = $this->idDV";

			$queryDevolucionesInventarioGrupos = $this->mysql->query($sqlDevolucionesInventarioGrupos,$this->mysql->link);

      if(!$queryDevolucionesInventarioGrupos){
        echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los grupos de articulos de la devolucion.");' . '</script>';
        exit;
      }

			//Contamos el numero de grupos que posee la factura
			$this->contGruposArticulos = $this->mysql->num_rows($queryDevolucionesInventarioGrupos);

			for($i = 0; $i < $this->contGruposArticulos; $i++){
				$this->codigoDVIG[$i]								= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'codigo');
				$this->cantidadDVIG[$i]							= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'cantidad');
				$this->nombreDVIG[$i]								= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'nombre');
				$this->costo_unitarioDVIG[$i]				= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'costo_unitario');
        $this->observacionesDVIG[$i]        = $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'observaciones');
				$this->descuentoDVIG[$i]						= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'descuento');
				$this->nombre_impuestoDVIG[$i]			= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'nombre_impuesto');
				$this->porcentaje_impuestoDVIG[$i]	= $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'porcentaje_impuesto');
				$this->codigo_impuesto_dianDVIG[$i] = $this->mysql->result($queryDevolucionesInventarioGrupos,$i,'codigo_impuesto_dian');
			}

			//Buscamos primero si el articulo tiene o no descuento
			for($i = 0; $i < $this->contGruposArticulos; $i++){
				if($this->descuentoDVIG[$i] != 0){
					$this->costo_subtotalDVIG[$i] = ($this->cantidadDVIG[$i] * $this->costo_unitarioDVIG[$i]) - $this->descuentoDVIG[$i];
				} else{
					$this->costo_subtotalDVIG[$i] = $this->cantidadDVIG[$i] * $this->costo_unitarioDVIG[$i];
				}
				//Buscamos si el tercero esta o no exento de IVA
        if($this->exento_ivaDV == "Si"){
          $this->costo_impuestoDVIG[$i] = 0;
        }
        else{
          if($this->porcentaje_impuestoDVIG[$i] != null && ($this->nombre_impuestoDVIG[$i] != "" || $this->nombre_impuestoDVIG[$i] != null)){
				    $this->costo_impuestoDVIG[$i] = $this->costo_subtotalDVIG[$i] * $this->porcentaje_impuestoDVIG[$i] / 100;
          }
        }
      }

      for($i = 0; $i < $this->contGruposArticulos; $i++){
        if($this->porcentaje_impuestoDVIG[$i] != null && ($this->nombre_impuestoDVIG[$i] != "" || $this->nombre_impuestoDVIG[$i] != null)){
          $this->arrayImpuestos[$this->codigo_impuesto_dianDVIG[$i]][$this->porcentaje_impuestoDVIG[$i]]['costo'] += $this->costo_impuestoDVIG[$i];
				  $this->arrayImpuestos[$this->codigo_impuesto_dianDVIG[$i]][$this->porcentaje_impuestoDVIG[$i]]['nombre']  = $this->nombre_impuestoDVIG[$i];
        }
      }
    }

    public function quitarTildes($cadena){
			$caracterEspecial = array("\t","\r","\n",chr(160));
			$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ°ª&º/';
	    $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo-';
	    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
			$cadena = str_replace($caracterEspecial,"",$cadena);
	    return utf8_encode($cadena);
		}

    public function construirJSON(){
      $arrayDetalle 			= Array(); //Se crea un arreglo que contenga todos los articulos y grupos de articulos
			$arrayImpuesto 			= Array(); //Se crea un arreglo que contenga todos los impuestos y retenciones
      $arrayTercerosEmail = Array(); //Se crea un arreglo que contenga los email de los terceros
      // $subTotal 					= array_sum($this->costo_subtotalDVI) + array_sum($this->costo_subtotalDVIG); //Variable que contendra el costo del subtotal de toda la factura
      $numero_detalle     = 1;

      //------------------- ARTICULOS Y GRUPOS DE ARTICULOS ------------------//
			for($i = 0; $i < $this->contArticulos; $i++){
        if($this->impuestoDVI[$i] != null && ($this->exento_ivaDV == "No" || $this->exento_ivaDV == null || $this->exento_ivaDV == "")){
					$arrayImpuestoItem[$i] = [
                                      array(
                                              "Base"           => (string) round($this->costo_subtotalDVI[$i],$_SESSION['DECIMALESMONEDA']),
                                              "CodigoImpuesto" => $this->codigo_impuesto_dianDVI[$i],
                                              "Nombre"         => $this->impuestoDVI[$i],
                                              // "Nombre"         => "IVA SERVICIOS 19%",
      																				"Porcentaje"     => round($this->valor_impuestoDVI[$i],$_SESSION['DECIMALESMONEDA']),
      																				"Impuesto"       => (string) round($this->costo_impuestoDVI[$i],$_SESSION['DECIMALESMONEDA'])
																		        )
                                   ];

          $aplica_impuestoDVI[$i] = true;

          $totalBaseImpuesto[$this->codigo_impuesto_dianDVI[$i]] += (string) round($this->costo_subtotalDVI[$i],$_SESSION['DECIMALESMONEDA']);

          if(stripos($this->impuestoDVI[$i],"servicio") !== false){
            $descripcionDVI[$i] = [
                                    array(
                                            "Nombre" => "Descripcion IVA",
                                            "Valor"  => "IVA SERVICIOS"
                                          )
                                  ];

            $descripcionGeneralIVA = "IVA SERVICIOS";
          }
          else if(stripos($this->impuestoDVI[$i],"compra") !== false){
            $descripcionDVI[$i] = [
                                    array(
                                            "Nombre" => "Descripcion IVA",
                                            "Valor"  => "IVA COMPRAS"
                                          )
                                  ];

            $descripcionGeneralIVA = "IVA COMPRAS";
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

          $aplica_impuestoDVI[$i] = true;
        }

        //AÑADIMOS LAS OBSERVACIONES DE CADA ITEM
        $descripcionDVI[$i][] = array(
                                        "Nombre" => "Observaciones",
                                        "Valor"  => ($this->observacionesDVI[$i] != "")? $this->quitarTildes($this->observacionesDVI[$i]) : ""
                                      );

        //Creamos una variable que contenga el total del item
        $this->totalItemDVI[$i] = $this->costo_subtotalDVI[$i] + $this->costo_impuestoDVI[$i];

				$arrayDetalle[] =	array(
                                  "idDetalle"        => (string) $numero_detalle,
																	"Nombre"           => $this->quitarTildes($this->nombreDVI[$i]),
                                  "UnidadCodigo"     => $this->codigo_unidad_medidaDVI[$i],
																	"Cantidad"         => (float) $this->cantidadDVI[$i],
                                  "ValorUnitario"    => (float) $this->costo_unitarioDVI[$i],
                                  "Descuento"        => round($this->descuento_itemDVI[$i],$_SESSION['DECIMALESMONEDA']),
																	"Cargos"           => 0,
                                  "SubTotal"         => $this->costo_subtotalDVI[$i],
																	"Total"            => round($this->totalItemDVI[$i],$_SESSION['DECIMALESMONEDA']),
																	"codigo"           => $this->codigoDVI[$i],
                                  "AplicaImpuesto"   => $aplica_impuestoDVI[$i],
																	"Impuestos"        => $arrayImpuestoItem[$i],
                                  "Descripcion"  	   => ($descripcionDVI[$i] == null)? null : $descripcionDVI[$i],
                                  "AllowanceCharge"  => null,
																	"PricingReference" => null
																);

        $numero_detalle++;

        $subTotal += $this->costo_subtotalDVI[$i];
			}

      for($i = 0; $i < $this->contGruposArticulos; $i++){
        if($this->nombre_impuestoDVIG[$i] != null && ($this->exento_ivaDV == "No" || $this->exento_ivaDV == null || $this->exento_ivaDV == "")){
					$arrayImpuestoItemGrupo[$i] = [
                                          array(
                                                  "Base"           => (string) round($this->costo_subtotalDVIG[$i],$_SESSION['DECIMALESMONEDA']),
                                                  "CodigoImpuesto" => $this->codigo_impuesto_dianDVIG[$i],
    																							"Nombre"         => $this->nombre_impuestoDVIG[$i],
                                                  // "Nombre"         => "IVA SERVICIOS 19%",
    																							"Porcentaje"     => round($this->porcentaje_impuestoDVIG[$i],$_SESSION['DECIMALESMONEDA']),
    																							"Impuesto"       => (string) round($this->costo_impuestoDVIG[$i],$_SESSION['DECIMALESMONEDA'])
    																					  )
                                        ];

          $aplica_impuestoDVIG[$i] = true;

          $totalBaseImpuesto[$this->codigo_impuesto_dianDVIG[$i]] += (string) round($this->costo_subtotalDVIG[$i],$_SESSION['DECIMALESMONEDA']);

          if(stripos($this->nombre_impuestoDVIG[$i],"servicio") !== false){
            $descripcionDVIG[$i] = [
                                    array(
                                            "Nombre" => "Descripcion IVA",
                                            "Valor"  => "IVA SERVICIOS"
                                          )
                                  ];

            $descripcionGeneralIVA = "IVA SERVICIOS";
          }
          else if(stripos($this->nombre_impuestoDVIG[$i],"compra") !== false){
            $descripcionDVIG[$i] = [
                                    array(
                                            "Nombre" => "Descripcion IVA",
                                            "Valor"  => "IVA COMPRAS"
                                          )
                                  ];

            $descripcionGeneralIVA = "IVA COMPRAS";
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

					$aplica_impuestoDVIG[$i] = true;
        }

        //AÑADIMOS LAS OBSERVACIONES DE CADA ITEM
        $descripcionDVIG[$i][] = array(
                                        "Nombre" => "Observaciones",
                                        "Valor"  => ($this->observacionesDVIG[$i] != "")? $this->quitarTildes($this->observacionesDVIG[$i]) : ""
                                      );

        //Creamos una variable que contenga el total del item
				$this->totalItemDVIG[$i] = $this->costo_subtotalDVIG[$i] + $this->costo_impuestoDVIG[$i];

				$arrayDetalle[] =	 array(
                                  "idDetalle"        => (string) $numero_detalle,
																	"Nombre"           => $this->quitarTildes($this->nombreDVIG[$i]),
                                  "UnidadCodigo"     => "EA",
																	"Cantidad"         => (float) $this->cantidadDVIG[$i],
                                  "ValorUnitario"    => (float) $this->costo_unitarioDVIG[$i],
                                  "Descuento"        => round($this->descuentoDVIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Cargos"           => 0,
                                  "SubTotal"         => round($this->costo_subtotalDVIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Total"            => round($this->totalItemDVIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"codigo"           => $this->codigoDVIG[$i],
                                  "AplicaImpuesto"   => $aplica_impuestoDVIG[$i],
																	"Impuestos"        => $arrayImpuestoItemGrupo[$i],
                                  "Descripcion" 	   => ($descripcionDVIG[$i] == null)? null : $descripcionDVIG[$i],
																	"AllowanceCharge"  => null,
																	"PricingReference" => null
																);

        $numero_detalle++;

        $subTotal += $this->costo_subtotalDVIG[$i];

        if($descripcionGeneralIVA == null){
          $descripcionGeneralIVA = array(
                                          "Nombre" => "Descripcion IVA",
                                          "Valor"  => $descripcionGeneralIVA
                                        );
        }
        else{
          $descripcionGeneralIVA = "";
        }
			}
      
      //------------------------------ IMPUESTOS -----------------------------//
      // if($this->exento_ivaDV == "No" && ($arrayImpuestoItem != null || $arrayImpuestoItemGrupo != null)){
      if(($this->exento_ivaDV == "No" || $this->exento_ivaDV == null || $this->exento_ivaDV == "") && $this->arrayImpuestos != null){
        foreach($this->arrayImpuestos as $codigoDian => $resultCodigoDian){
  				foreach($resultCodigoDian as $porcentajeImpuesto => $result){
  						$arrayImpuesto[] = array(
                                        "Base"           => (string) round($totalBaseImpuesto[$codigoDian],$_SESSION['DECIMALESMONEDA']),
                                        "CodigoImpuesto" => $codigoDian,
                                        // "Nombre"         => (string) ($codigoDian == "01")? "IVA SERVICIOS 19%" : $result['nombre'],
  																			"Nombre" 		     => (string) $result['nombre'],
  																			"Porcentaje"     => (float) $porcentajeImpuesto / 1,
  																			"Impuesto"  	   => (string) round($result['costo'],$_SESSION['DECIMALESMONEDA'])
  																		);
              if($codigoDian == "01" || $codigoDian == "04"){
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

			//----------------------------- RETENCIONES ----------------------------//
			for($i = 0; $i < $this->contRetenciones; $i++){
				if($subTotal > $this->baseDVR[$i]){
					if($this->tipo_retencionDVR[$i] == "ReteFuente"){
						$arrayImpuesto[] = array(
                                      "Base"           => (string) $this->baseDVR[$i],
                                      // "Base"           => "0",
                                      "CodigoImpuesto" => "06",
																			"Nombre"         => (string) "ReteFuente",
                                      "Porcentaje"     => (float) $this->valorDVR[$i],
																			"Impuesto"       => (string) round(($subTotal * $this->valorDVR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		);
            $totalRetencionesDV += ($subTotal * $this->valorDVR[$i] / 100);
					}
          else if($this->tipo_retencionDVR[$i] == "ReteIva"){
            if($totalIVA > $this->baseDVR[$i]){
  						$arrayImpuesto[] = array(
                                        "Base"           => (string) $this->baseDVR[$i],
                                        // "Base"           => "0",
                                        "CodigoImpuesto" => "05",
  																			"Nombre"         => (string) "ReteIVA",
                                        "Porcentaje"     => (float) $this->valorDVR[$i],
  																			"Impuesto"       => (string) round(($totalIVA * $this->valorDVR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
  																	  );
              $totalRetencionesDV += ($totalIVA * $this->valorDVR[$i] / 100);
            }
					}
          else if($this->tipo_retencionDVR[$i] == "ReteIca"){
						$arrayImpuesto[] = array(
                                      "Base"           => (string) $this->baseDVR[$i],
                                      // "Base"           => "0",
                                      "CodigoImpuesto" => "07",
																			"Nombre"         => (string) "ReteICA",
                                      "Porcentaje"     => (float) $this->valorDVR[$i],
																			"Impuesto"       => (string) round(($subTotal * $this->valorDVR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																	  );
            $totalRetencionesDV += ($subTotal * $this->valorDVR[$i] / 100);
					}
				}
			}

      //-------------------------- TOTAL DEVOLUCION --------------------------//
      // $totalDV = round((array_sum($this->totalItemDVI) + array_sum($this->totalItemDVIG)),$_SESSION['DECIMALESMONEDA']);
      $totalDV = round(($subTotal + $totalIVA),$_SESSION['DECIMALESMONEDA']);

			//--------------------------- TERCEROS EMAIL'S -------------------------//
			for($i = 0; $i < $this->contTercerosDireccionesEmail; $i++){
				$arrayTercerosEmail[$i] = $this->emailTDE[$i];
			}

			$emails = implode(',',$arrayTercerosEmail);

      // VERIFICAMOS SI LA EMPRESA TIENE EMAILS CONFIGURADOS
      if($emails == ""){
        $emails = $this->email_feDV;
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
        $telefono_tercero    = $this->telefono1T;
        $nombre_departamento = $this->nombre_departamentoT;
        $codigo_departamento = $this->codigo_departamentoT;
        $nombre_ciudad       = $this->nombre_ciudadT;
        $codigo_ciudad       = $this->codigo_ciudadT;
      }

      //---------------------------- TIPO PERSONA ----------------------------//
      if($this->tipo_persona_codigoT == null || $this->tipo_persona_codigoT == ""){
        $tipo_persona_tercero = ($this->codigo_tipo_documento_dianT == "31")? "1" : "2";
      }
      else{
        $tipo_persona_tercero = $this->tipo_persona_codigoT;
      }

      //------------------------ DETALLES COMPROBANTE ------------------------//
      $arrayDetallesComprobante = [
                                    array(
                                            "Nombre"  => "Fecha Vencimiento",
                                            "Valor"   => $this->fecha_vencimientoDV
                                          ),
                                    array(
                                            "Nombre" => "Sucursal Factura",
                                            "Valor"  => ($this->sucursalDV != null)? $this->quitarTildes($this->sucursalDV) : ""
                                         ),
                                    array(
                                            "Nombre" => "Sucursal Cliente",
                                            "Valor"  => ($this->sucursal_clienteDV != null)? $this->quitarTildes($this->sucursal_clienteDV) : ""
                                         ),
                                    array(
                                            "Nombre" => "Nombre Vendedor",
                                            "Valor"  => $this->quitarTildes($this->nombre_vendedorDV)
                                         ),
                                    array(
                                            "Nombre" => "Documento Vendedor",
                                            "Valor"  => $this->documento_vendedorDV
                                         ),
                                    array(
                                            "Nombre" => "Total Factura",
                                            "Valor"  => ($totalDV - $totalRetencionesDV)
                                          ),
                                    array(
                                            "Nombre" => "Descripcion IVA",
                                            "Valor"  => ($descripcionGeneralIVA == null)? "" : $descripcionGeneralIVA
                                          )
                                  ];

      //---------------------------- INFO RESERVA ----------------------------//
      if($this->info_reservaDV != "" || $this->info_reservaDV != null){
        $datosReserva = json_decode($this->info_reservaDV,TRUE);

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
            $key = "Forma Pago";
          }

          $value = ($value == false)? "" : $value;
          $arrayDetallesComprobante[] = array(
                                                "Nombre" => "$key",
                                                "Valor" => "$value"
                                              );
        }
      }

      //------------------------- PREFIJO DEVOLUCION -------------------------//
      if(strlen($this->id_sucursalDV) == 1){
        $this->idES = "0" . $this->id_sucursalDV;
      }
      else{
        $this->idES = $this->id_sucursalDV;
      }

      //--------------------------- ARRAY PRINCIPAL --------------------------//
      $arrayPrincipal = array(
        "Comprobante" => array(
                                "TipoComprobante"               => "91",
                                "Fecha"                         => date('Y-m-d'),
                                "Prefijo"                       => ($_SERVER['SERVER_NAME'] == "logicalerp.localhost")? "NCNC" : "DV".$this->idES,
                                "Numero"                        => (int) $this->consecutivoDV,
                                "Moneda"                        => $this->monedaE,
                                "Referencia"                    => str_replace(" ", "", $this->numero_documento_ventaDV),
                                "ConceptoRef"                   => $this->id_motivo_dianDV,
                                "Observaciones"                 => ($this->observacionDV != null)? $this->quitarTildes($this->observacionDV) : "",
                                "Usuario"                       => ($this->quitarTildes($_SESSION['NOMBREFUNCIONARIO']) == "")? "Usuario Soporte" : $this->quitarTildes($_SESSION['NOMBREFUNCIONARIO']),
                                "NumeroOrden"                   => "",
																"NumeroDespacho"                => "",
																"NumeroRecepcion"               => "",
																"DocumentoAdicionalNotaCredito" => "",
																"DocumentoReferenciaCodigo"     => "",
                                "Descripcion" 				          => [],
                                "MetodoPago"  				          => [
                                                                      array(
                                                                              "FormaPago" => ($this->forma_pagoDV == "Contado")? "1" : "2",
                                                                              "MedioPago" => $this->codigo_metodo_pago_dianDV,
                                                                              "Fecha"     => $this->fecha_registroDV
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
                            "Sucursal"                 => ($_SERVER['SERVER_NAME'] == "logicalerp.localhost")? "PRIN" : $this->quitarTildes($this->sucursalDV),
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
                              "TipoReceptor"             => "O-99",
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
                            "Total"                       => ($totalIVA != null)? (string) ($subTotal + round($totalIVA,$_SESSION['DECIMALESMONEDA'])) : $subTotal,
                            "TotalEnLetras"               => $this->quitarTildes($this->num2letras($totalDV)),
                            "SubTotal"                    => (float) $subTotal,
                            "Cargos"                      => "0",
                            "Descuentos"                  => "0",
                            "SubTotalSinCargosDescuentos" => (string) round($subTotal,$_SESSION['DECIMALESMONEDA']),
                            "IVA"					                => ($totalIVA != null)? (string) round($totalIVA,$_SESSION['DECIMALESMONEDA']) : "0",
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
																  "Duracion"     => ($this->dias_pagoDV != "")? $this->dias_pagoDV : "1"
																)
      );

      $this->arrayFinal = json_encode($arrayPrincipal, JSON_PRETTY_PRINT);

      // echo json_last_error_msg();
    }

    public function enviarJSON(){
    	$server_name = $_SERVER['SERVER_NAME'];

			if($server_name == "logicalerp.localhost"){
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

				$respuestaFinal['validar']       = $this->quitarTildes($validar);
				$respuestaFinal['comprobante']   = "Se ejecuto el envio en desarrollo";
        $respuestaFinal['id_devolucion'] = $respuesta['IdDocumento']['Contenido'];
        $respuestaFinal['cufe']          = $respuesta['CufeDocumento']['Contenido'];

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

        $respuestaFinal['validar']       = $this->quitarTildes($validar);
        $respuestaFinal['comprobante']   = "Se ejecuto el envio en produccion";
        $respuestaFinal['id_devolucion'] = $respuesta['IdDocumento']['Contenido'];
        $respuestaFinal['cufe']          = $respuesta['CufeDocumento']['Contenido'];

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
