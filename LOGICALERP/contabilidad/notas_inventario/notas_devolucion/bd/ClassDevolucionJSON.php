<?php
  // include("../../../../../configuracion/conectar.php");
  // include("../../../../../configuracion/define_variables.php");
  // include("../../../../web_service/nuSoap/nusoap.php");

  /**
   *@class ClassDevolucionJSON
   */

  class ClassDevolucionJSON{
    public $mysql;

    function __construct($mysql){
      $this->mysql = $mysql;
    }

    public function obtenerDatos($codigoDevolucionVenta,$id_empresa,$id_sucursal){
      //---------------- DATOS DE LA CABECERA DE LA DEVOLUCION ---------------//
      $sqlDevolucionesVentas = "SELECT
                                  devoluciones_venta.id,
                                  devoluciones_venta.id_documento_venta,
                                  devoluciones_venta.consecutivo,
                                  devoluciones_venta.numero_documento_venta,
                                  devoluciones_venta.id_motivo_dian,
                                  devoluciones_venta.descripcion_motivo_dian,
                                  devoluciones_venta.nit,
                                  devoluciones_venta.observacion,
                                  devoluciones_venta.id_metodo_pago,
                                  devoluciones_venta.metodo_pago,
                                  devoluciones_venta.sucursal,
                                  devoluciones_venta.fecha_registro,
                                  configuracion_metodos_pago.codigo_metodo_pago_dian,
                                  ventas_facturas.id_sucursal_cliente
                                FROM
                                  devoluciones_venta
                                LEFT JOIN
                                  configuracion_metodos_pago
                                ON
                                  devoluciones_venta.id_metodo_pago = configuracion_metodos_pago.id
                                LEFT JOIN
                                  ventas_facturas
                                ON
                                  devoluciones_venta.id_documento_venta = ventas_facturas.id
                                WHERE
                                  devoluciones_venta.activo = 1
                                AND
                                  devoluciones_venta.estado = 1
                                AND
                                  devoluciones_venta.id_empresa = $id_empresa
                                AND
                                  devoluciones_venta.id = $codigoDevolucionVenta";

      $queryDevolucionesVentas                = $this->mysql->query($sqlDevolucionesVentas,$this->mysql->link);

      if(!$queryDevolucionesVentas){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos principales de la devolucion.");' . '</script>';
				exit;
			}

      $this->idDV                             = $this->mysql->result($queryDevolucionesVentas,0,'id');
      $this->id_documento_ventaDV             = $this->mysql->result($queryDevolucionesVentas,0,'id_documento_venta');
      $this->consecutivoDV                    = $this->mysql->result($queryDevolucionesVentas,0,'consecutivo');
      $this->numero_documento_ventaDV         = $this->mysql->result($queryDevolucionesVentas,0,'numero_documento_venta');
      $this->id_motivo_dianDV                 = $this->mysql->result($queryDevolucionesVentas,0,'id_motivo_dian');
      $this->descripcion_motivo_dianDV        = $this->mysql->result($queryDevolucionesVentas,0,'descripcion_motivo_dian');
      $this->nitDV                            = $this->mysql->result($queryDevolucionesVentas,0,'nit');
      $this->observacionDV                    = $this->mysql->result($queryDevolucionesVentas,0,'observacion');
      $this->id_metodo_pagoDV                 = $this->mysql->result($queryDevolucionesVentas,0,'id_metodo_pago');
      $this->metodo_pagoDV                    = $this->mysql->result($queryDevolucionesVentas,0,'metodo_pago');
      $this->sucursalDV                       = $this->mysql->result($queryDevolucionesVentas,0,'sucursal');
      $this->fecha_registroDV                 = $this->mysql->result($queryDevolucionesVentas,0,'fecha_registro');
      $this->codigo_metodo_pago_dianDV        = $this->mysql->result($queryDevolucionesVentas,0,'codigo_metodo_pago_dian');
      $this->id_sucursal_clienteDV            = $this->mysql->result($queryDevolucionesVentas,0,'id_sucursal_cliente');

      //------------------- DATOS DEL EMISOR O LA EMPRESA --------------------//
      $sqlEmpresa =  "SELECT
                        empresas.id,
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
                      	empresas.id = $id_empresa
                      GROUP BY
                      	empresas.id";

      $queryEmpresa                          = $this->mysql->query($sqlEmpresa,$this->mysql->link);

      if(!$queryEmpresa){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos del emisor.");' . '</script>';
				exit;
			}

      $this->idE                             = $this->mysql->result($queryEmpresa,0,'id');
      $this->documentoE                      = $this->mysql->result($queryEmpresa,0,'documento');
      $this->tipo_regimenE                   = $this->mysql->result($queryEmpresa,0,'tipo_regimen');
      $this->razon_socialE                   = $this->mysql->result($queryEmpresa,0,'razon_social');
      $this->nombreE                         = $this->mysql->result($queryEmpresa,0,'nombre');
      $this->direccionE                      = $this->mysql->result($queryEmpresa,0,'direccion');
      $this->emailE                          = $this->mysql->result($queryEmpresa,0,'email');
      $this->departamentoE                   = $this->mysql->result($queryEmpresa,0,'departamento');
      $this->ciudadE                         = $this->mysql->result($queryEmpresa,0,'ciudad');
      $this->codigo_tipo_documento_dianE     = $this->mysql->result($queryEmpresa,0,'codigo_tipo_documento_dian');
      $this->paisE                           = $this->mysql->result($queryEmpresa,0,'pais');
			$this->monedaE												 = $this->mysql->result($queryEmpresa,0,'moneda');

      $sqlEmpresaSucursal =  "SELECT
                                empresas_sucursales.id
                              FROM
                                empresas_sucursales
                              WHERE
                                empresas_sucursales.id_empresa = $this->idE
                              AND
                                empresas_sucursales.id = $id_sucursal";

      $queryEmpresaSucursal                  = $this->mysql->query($sqlEmpresaSucursal,$this->mysql->link);

      if(!$queryEmpresaSucursal){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los datos de sucursales del emisor.");' . '</script>';
				exit;
			}

      $this->idES                            = $this->mysql->result($queryEmpresaSucursal,0,'id');

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
                        terceros.numero_identificacion = '$this->nitDV'
                      AND
                        terceros.id_empresa = $id_empresa";

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
      $this->paisT                           = $this->mysql->result($queryTerceros,0,'pais');
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
																	id = '$this->id_sucursal_clienteDV'
																AND
																	id_tercero = '$this->idT'
																AND
																	activo = 1
																LIMIT
																	0,1";

			$queryTercerosDireccion                = $this->mysql->query($sqlTercerosDireccion,$this->mysql->link);

      if(!$queryTercerosDireccion){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron las direcciones del cliente.");' . '</script>';
				exit;
			}

      $this->idTD								             = $this->mysql->result($queryTercerosDireccion,0,'id');
			$this->ciudadTD    				             = $this->mysql->result($queryTercerosDireccion,0,'ciudad');
			$this->departamentoTD 		             = $this->mysql->result($queryTercerosDireccion,0,'departamento');
			$this->direccionTD 				             = $this->mysql->result($queryTercerosDireccion,0,'direccion');
			$this->telefono1TD  			             = $this->mysql->result($queryTercerosDireccion,0,'telefono1');

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
																					ventas_facturas_retenciones.id_factura_venta = $this->id_documento_ventaDV";

	    $queryVentasFacturasRetenciones        = $this->mysql->query($sqlVentasFacturasRetenciones,$this->mysql->link);

      if(!$queryVentasFacturasRetenciones){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron las retenciones de la factura.");' . '</script>';
				exit;
			}

      //Contamos el numero de retenciones que posee la factura
      $this->contRetenciones = $this->mysql->num_rows($queryVentasFacturasRetenciones);
			for($i = 0; $i < $this->contRetenciones; $i++){
				$this->valorDVR[$i] 					     = $this->mysql->result($queryVentasFacturasRetenciones,$i,'valor');
				$this->baseDVR[$i]  					     = $this->mysql->result($queryVentasFacturasRetenciones,$i,'base');
				$this->tipo_retencionDVR[$i]       = $this->mysql->result($queryVentasFacturasRetenciones,$i,'tipo_retencion');
			}

      //----------------------- DATOS DE lOS ARTICULOS -----------------------//
			$sqlDevolucionesInventario = "SELECT
																			devoluciones_venta_inventario.codigo,
																			devoluciones_venta_inventario.cantidad,
																			devoluciones_venta_inventario.nombre,
																			devoluciones_venta_inventario.costo_unitario,
                                      devoluciones_venta_inventario.observaciones,
																			devoluciones_venta_inventario.tipo_descuento,
																			devoluciones_venta_inventario.descuento,
																			devoluciones_venta_inventario.impuesto,
																			devoluciones_venta_inventario.valor_impuesto,
																			impuestos.codigo_impuesto_dian,
                                      ventas_facturas_inventario_grupos.id_inventario_factura_venta
																		FROM
																			devoluciones_venta_inventario
																		LEFT JOIN
																			devoluciones_venta
																		ON
																			devoluciones_venta_inventario.id_devolucion_venta = devoluciones_venta.id
																		LEFT JOIN
																			impuestos
																		ON
																			impuestos.id = devoluciones_venta_inventario.id_impuesto
                                    LEFT JOIN
                                      ventas_facturas_inventario_grupos
                                    ON
                                      devoluciones_venta_inventario.id_fila_cargada = ventas_facturas_inventario_grupos.id_inventario_factura_venta
																		WHERE
																			devoluciones_venta_inventario.activo = 1
																		AND
																			devoluciones_venta_inventario.id_devolucion_venta = $this->idDV
                                    AND
                                      ventas_facturas_inventario_grupos.id_inventario_factura_venta IS NULL
                                    AND
                                      devoluciones_venta.id_empresa = '$id_empresa'";

			$queryDevolucionesInventario = $this->mysql->query($sqlDevolucionesInventario,$this->mysql->link);

      if(!$queryDevolucionesInventario){
				echo '<script>' . 'alert("\u00A1Error!\nNo se consultaron los articulos de la devolucion.");' . '</script>';
				exit;
			}

			//Contamos el numero de articulos que posee la factura
			$this->contArticulos = $this->mysql->num_rows($queryDevolucionesInventario);
      for($i = 0; $i < $this->contArticulos; $i++){
				$this->codigoDVI[$i]     							= $this->mysql->result($queryDevolucionesInventario,$i,'codigo');
				$this->cantidadDVI[$i] 							  = $this->mysql->result($queryDevolucionesInventario,$i,'cantidad');
				$this->nombreDVI[$i] 								  = $this->mysql->result($queryDevolucionesInventario,$i,'nombre');
				$this->costo_unitarioDVI[$i] 				  = $this->mysql->result($queryDevolucionesInventario,$i,'costo_unitario');
				$this->tipo_descuentoDVI[$i] 				  = $this->mysql->result($queryDevolucionesInventario,$i,'tipo_descuento');
				$this->descuentoDVI[$i] 		 				  = $this->mysql->result($queryDevolucionesInventario,$i,'descuento');
				$this->impuestoDVI[$i] 				      	= $this->mysql->result($queryDevolucionesInventario,$i,'impuesto');
				$this->valor_impuestoDVI[$i] 				  = $this->mysql->result($queryDevolucionesInventario,$i,'valor_impuesto');
        $this->observacionesDVI[$i]           = $this->mysql->result($queryDevolucionesInventario,$i,'observaciones');
				$this->codigo_impuesto_dianDVI[$i] 		= $this->mysql->result($queryDevolucionesInventario,$i,'codigo_impuesto_dian');
			}

			for($i = 0; $i < $this->contArticulos; $i++){
        //Buscamos primero si el articulo tiene o no descuento
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
        if($this->exento_ivaT == "Si"){
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
				  $this->arrayImpuestos[$this->codigo_impuesto_dianDVI[$i]][$this->valor_impuestoDVI[$i]] += $this->costo_impuestoDVI[$i];
        }
      }

      //---------------- DATOS DE LOS GRUPOS DE LOS ARTICULOS ----------------//
      $sqlDevolucionesInventarioGrupos = "SELECT
																						devoluciones_venta_grupos.codigo,
																						devoluciones_venta_grupos.cantidad,
																						devoluciones_venta_grupos.nombre,
																						devoluciones_venta_grupos.costo_unitario,
                                            devoluciones_venta_grupos.observaciones,
																						devoluciones_venta_grupos.descuento,
																						devoluciones_venta_grupos.nombre_impuesto,
																						devoluciones_venta_grupos.porcentaje_impuesto,
																						impuestos.codigo_impuesto_dian
																					FROM
																						devoluciones_venta_grupos
																					LEFT JOIN
																						impuestos
																					ON
																						impuestos.id = devoluciones_venta_grupos.id_impuesto
																					WHERE
																						devoluciones_venta_grupos.activo = 1
																					AND
																						devoluciones_venta_grupos.id_empresa = $id_empresa
																					AND
																						devoluciones_venta_grupos.id_devolucion_venta = $this->idDV";

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
        if($this->exento_ivaT == "Si"){
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
				  $this->arrayImpuestos[$this->codigo_impuesto_dianDVIG[$i]][$this->porcentaje_impuestoDVIG[$i]] += $this->costo_impuestoDVIG[$i];
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
      $arrayDetalle 			     = Array(); 		//Se crea un arreglo que contenga todos los articulos y grupos de articulos
			$arrayImpuesto 			     = Array(); 		//Se crea un arreglo que contenga todos los impuestos y retenciones
      $arrayTercerosEmail      = Array();		  //Se crea un arreglo que contenga los email de los terceros
			$subTotal 					     = array_sum($this->costo_subtotalDVI)
                               + array_sum($this->costo_subtotalDVIG); //Variable que contendra el costo del subtotal de toda la factura

      //------------------- ARTICULOS Y GRUPOS DE ARTICULOS ------------------//
			for($i = 0; $i < $this->contArticulos; $i++){
        if($this->impuestoDVI[$i] != null && $this->exento_ivaT == "No"){
					$arrayImpuestoItem[$i] = [array(
  																				"Impuesto"    => $this->codigo_impuesto_dianDVI[$i],
  																				"Porcentaje"  => ($this->valor_impuestoDVI[$i] != null)? (float) $this->valor_impuestoDVI[$i] : "",
  																				"TotalImp"    => round($this->costo_impuestoDVI[$i],$_SESSION['DECIMALESMONEDA'])
																		   )];
				}

        //Creamos una variable que contenga el total del item
        $this->totalItemDVI[$i] = $this->costo_subtotalDVI[$i] + $this->costo_impuestoDVI[$i];

				$arrayDetalle[] =	array(
																	"Nombre"        => $this->quitarTildes($this->nombreDVI[$i]),
																	"Cantidad"      => $this->cantidadDVI[$i],
                                  "ValorUnitario" => (float) $this->costo_unitarioDVI[$i],
																	"Subtotal"      => $this->costo_subtotalDVI[$i],
																	"Total"         => round($this->totalItemDVI[$i],$_SESSION['DECIMALESMONEDA']),
																	"Codigo"        => (int) $this->codigoDVI[$i],
																	"Impuestos"     => ($arrayImpuestoItem[$i] != null)? $arrayImpuestoItem[$i] : "",
                                  "Descripcion" 	  => [array(
																															"Nombre"  => "Descuento",
																															"Valor" 	=> round($this->descuento_itemDVI[$i],$_SESSION['DECIMALESMONEDA'])
																													 ),
																											array(
																															"Nombre"	=> "Observaciones",
																															"Valor"		=> ($this->observacionesDVI[$i] != null)? $this->quitarTildes($this->observacionesDVI[$i]) : ""
																											     )]
																);
			}

      for($i = 0; $i < $this->contGruposArticulos; $i++){
        if($this->nombre_impuestoDVIG[$i] != null && $this->exento_ivaT == "No"){
					$arrayImpuestoItemGrupo[$i] = [array(
																							"Impuesto"    => $this->codigo_impuesto_dianDVIG[$i],
																							"Porcentaje"  => ($this->porcentaje_impuestoDVIG[$i] != null)? (float) $this->porcentaje_impuestoDVIG[$i] : "",
																							"TotalImp"    => round($this->costo_impuestoDVIG[$i],$_SESSION['DECIMALESMONEDA'])
																					 )];
				}

        //Creamos una variable que contenga el total del item
				$this->totalItemDVIG[$i] = $this->costo_subtotalDVIG[$i] + $this->costo_impuestoDVIG[$i];

				$arrayDetalle[] =	 array(
																	"Nombre"        => $this->quitarTildes($this->nombreDVIG[$i]),
																	"Cantidad"      => $this->cantidadDVIG[$i],
                                  "ValorUnitario" => (float) $this->costo_unitarioDVIG[$i],
																	"Subtotal"      => $this->costo_subtotalDVIG[$i],
																	"Total"         => round($this->totalItemDVIG[$i],$_SESSION['DECIMALESMONEDA']),
																	"Codigo"        => (int) $this->codigoDVIG[$i],
																	"Impuestos"     => ($arrayImpuestoItemGrupo[$i] != null)? $arrayImpuestoItemGrupo[$i] : "",
                                  "Descripcion" 	=> [array(
																															"Nombre"  => "Descuento",
																															"Valor" 	=> round($this->descuentoDVIG[$i],$_SESSION['DECIMALESMONEDA'])
																													 ),
																											array(
																															"Nombre"	=> "Observaciones",
																															"Valor"		=> ($this->observacionesDVIG[$i] != null)? $this->quitarTildes($this->observacionesDVIG[$i]) : ""
																											     )]
																);
			}

      //------------------------------ IMPUESTOS -----------------------------//
      if($this->exento_ivaT == "No" && ($arrayImpuestoItem != null || $arrayImpuestoItemGrupo != null)){
        foreach($this->arrayImpuestos as $codigoDian => $resultCodigoDian){
  				foreach($resultCodigoDian as $porcentajeImpuesto => $resultPorcentajeImpuesto){
  						$arrayImpuesto[] = array(
  																			"Impuesto" 		=> $codigoDian,
  																			"Porcentaje"  => (float) $porcentajeImpuesto / 1,
  																			"TotalImp"  	=> round($resultPorcentajeImpuesto,$_SESSION['DECIMALESMONEDA'])
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
				if($subTotal > $this->baseDVR[$i]){
					if($this->tipo_retencionDVR[$i] == "ReteFuente"){
						$arrayImpuesto[] = array(
																			"Impuesto"    => "05",
                                      "Porcentaje"  => (float) $this->valorDVR[$i] / 1,
																			"TotalImp"    => round(($subTotal * $this->valorDVR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																		);
            $totalRetencionesDV += ($subTotal * $this->valorDVR[$i] / 100);
					} else if($this->tipo_retencionDVR[$i] == "ReteIva"){
            if($totalIVA > $this->baseDVR[$i]){
  						$arrayImpuesto[] = array(
  																			"Impuesto"    => "06",
                                        "Porcentaje"  => (float) $this->valorDVR[$i] / 1,
  																			"TotalImp"    => round(($totalIVA * $this->valorDVR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
  																	  );
              $totalRetencionesDV += ($totalIVA * $this->valorDVR[$i] / 100);
            }
					} else if($this->tipo_retencionDVR[$i] == "ReteIca"){
						$arrayImpuesto[] = array(
																			"Impuesto"    => "07",
                                      "Porcentaje"  => (float) $this->valorDVR[$i] / 1,
																			"TotalImp"    => round(($subTotal * $this->valorDVR[$i] / 100),$_SESSION['DECIMALESMONEDA'])
																	  );
            $totalRetencionesDV += ($subTotal * $this->valorDVR[$i] / 100);
					}
				}
			}

      //-------------------------- TOTAL DEVOLUCION --------------------------//
      $totalDV = round((array_sum($this->totalItemDVI) + array_sum($this->totalItemDVIG)),$_SESSION['DECIMALESMONEDA']) - round($totalRetencionesDV,$_SESSION['DECIMALESMONEDA']);

			//--------------------------- TERCEROS EMAIL'S -------------------------//
			for($i = 0; $i < $this->contTercerosDireccionesEmail; $i++){
				$arrayTercerosEmail[$i] = $this->emailTDE[$i];
			}

			$emails = implode(',',$arrayTercerosEmail);

      //------------------------- PREFIJO DEVOLUCION -------------------------//
      if(strlen($this->idES) == 1){
        $this->idES = "0".$this->idES;
      }

      //--------------------------- ARRAY PRINCIPAL --------------------------//
      $arrayPrincipal = array(
        "Comprobante" => array(
                                "TipoComprobante"     => "04",
                                "Fecha"               => $this->fecha_registroDV,
                                "Serie"               => "DV" . $this->idES,
                                "Folio"               => $this->consecutivoDV,
                                "Moneda"              => $this->monedaE,
                                "Referencia"          => str_replace(" ", "-", $this->numero_documento_ventaDV),
                                "ConceptoRef"         => $this->id_motivo_dianDV,
                                "Observaciones"       => ($this->observacionDV != null)? $this->quitarTildes($this->observacionDV) : "",
                                "Descripcion" 				=> [array(
  				                                                        "Nombre" => "Sucursal Devolucion",
  				                                                        "Valor"  => $this->sucursalDV
				                                                       )],
                                "MetodoPago"  				=> [array(
  				                                                        "Codigo" => $this->codigo_metodo_pago_dianDV,
  				                                                        "Valor"  => $totalDV
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
                            "Total"       => $totalDV,
                            "SubTotal"    => round($subTotal,$_SESSION['DECIMALESMONEDA']),
                            "IVA"					=> ($totalIVA != null)? round($totalIVA,$_SESSION['DECIMALESMONEDA']) : 0,
                            "Impuestos"   => $arrayImpuesto
                          ),
        "DetallesComprobante" => [array(
                                          "Nombre"  => "Motivo Devolucion",
                                          "Valor"   => $this->descripcion_motivo_dianDV
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

  // Instanciamos el objeto y llamamos los metodos para enviar la devolucion
  // $devolucionJSON = new ClassDevolucionJSON($mysql);
  // $devolucionJSON->obtenerDatos(401,1);
  // $devolucionJSON->construirJSON();
  // $devolucionJSON->enviarJSON();
?>
