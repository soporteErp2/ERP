<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$objAudit = new AuditDocs($mysql);

	switch($opc){
		case 'getDOMDocsCruce':
			$objAudit->getDOMDocsCruce($opcGrillaContable,$documento_cruce);
			break;
		case 'getDates':
			$objAudit->getDates();
			break;
		case 'getState':
			$objAudit->getState();
			break;
		case 'getAuditDocs':
			$objAudit->getAuditDocs($id_sucursal,$tipo_documento,$jsonId);
			break;
		case 'setAuditDoc':
			$objAudit->setAuditDoc($id_sucursal,$tipo_documento,$consecutivo,$id_documento,$checkValue);
			break;
	}

	/**
	 * AuditDocs Clase para manejar la auditoria de documentos
	 */
	class AuditDocs
	{
		private $id_sucursal_session;
		private $id_empresa;
		private $mysql;

		function __construct($mysql){
			$this->id_empresa          = $_SESSION['EMPRESA'];
			$this->id_sucursal_session = $_SESSION['SUCURSAL'];
			$this->mysql               = $mysql;
		}

		public function getDOMDocsCruce($opcGrillaContable,$documento_cruce){
			?>
	    		<select id="filtro_tipo_documento" style="width:135px;float: left;margin: 8px 5px;" onChange="carga_filtro_tipo_documento(this.value)">
	                <optgroup label="Compras">
	                    <option value="FC">FC - Factura</option>
	                    <option value="CE">CE - Comprobante Egreso</option>
	                </optgroup>
	                <optgroup label="Ventas">
	                    <option value="RV">RV - Remision</option>
	                    <option value="FV">FV - Factura</option>
	                    <option value="RC">RC - Recibo Caja</option>
	                </optgroup>
	                <optgroup label="Nomina">
	                    <option value="LN">LN - Planilla de Nomina</option>
	                    <option value="LE">LE - Planilla de Liquidacion</option>
	                    <option value="PA">PA - Planilla de Ajuste</option>
	                </optgroup>
	                <optgroup label="Contabilidad">
	                    <option value="NCG">NCG - Nota Contable General</option>
	                </optgroup>
	            </select>;
	    		<script>
					function carga_filtro_tipo_documento(tipo_documento_cruce){
						if(document.getElementById("filtro_sucursal_")){
							filtro_sucursal = document.getElementById("filtro_sucursal_").value;
						}
						else{
							filtro_sucursal = "<?php echo $this->id_sucursal_session; ?>";
						}

						fecha_inicial = document.getElementById('MyInformeFiltroFechaInicio').value;
						fecha_final   = document.getElementById('MyInformeFiltroFechaFinal').value;
						filtro_estado = document.getElementById("filtro_estado").value;

						Ext.get("contenedor_Win_Panel_Global").load({
							url     : "auditoria_documentos/consulta_documentos.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_sucursal      : filtro_sucursal,
								tipo_documento_cruce : tipo_documento_cruce,
								fecha_inicial        : fecha_inicial,
								fecha_final          : fecha_final,
								filtro_estado        : filtro_estado,
								opcGrillaContable    : "<?php echo $opcGrillaContable ?>",
							}
						});
					}
					</script>
			<?php
		}

		public function getDates(){
			?>
			<table>
        <tr>
          <td>Fecha Inicial</td>
          <td><input type="text" id="MyInformeFiltroFechaInicio"/></td>
        </tr>
        <tr>
          <td>Fecha Final</td>
          <td><input type="text" on id="MyInformeFiltroFechaFinal"/></td>
        </tr>
      </table>
			<script>
				new Ext.form.DateField({
					format     : "Y-m-d",
					width      : 120,
					id         : "cmpFechaInicio",
					allowBlank : false,
					showToday  : false,
					applyTo    : "MyInformeFiltroFechaInicio",
					editable   : false,
					listeners  : { select: function(){ carga_filtro_fecha() } }
				});

				new Ext.form.DateField({
					format     : "Y-m-d",
					width      : 120,
					allowBlank : false,
					showToday  : false,
					applyTo    : "MyInformeFiltroFechaFinal",
					editable   : false,
					listeners  : { select: function(){ carga_filtro_fecha() } }
				});

				function carga_filtro_fecha(){
					fecha_inicial         = document.getElementById('MyInformeFiltroFechaInicio').value;
					fecha_final           = document.getElementById('MyInformeFiltroFechaFinal').value;
					filtro_sucursal       = document.getElementById("filtro_sucursal_").value;
					filtro_tipo_documento = document.getElementById("filtro_tipo_documento").value;
					filtro_estado         = document.getElementById("filtro_estado").value;

					if(fecha_inicial != '' && fecha_final != ''){
						Ext.get("contenedor_Win_Panel_Global").load({
							url     : "auditoria_documentos/consulta_documentos.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_sucursal      : filtro_sucursal,
								tipo_documento_cruce : filtro_tipo_documento,
								fecha_inicial        : fecha_inicial,
								fecha_final          : fecha_final,
								filtro_estado        : filtro_estado,
								opcGrillaContable    : "<?php echo $opcGrillaContable ?>",
							}
						});
					}
				}
			</script>
			<?php
		}

		public function getState(){
			?>
  		<select id="filtro_estado" style="width:135px;float: left;margin: 8px 5px;" onChange="carga_filtro_estado(this.value)">
        <option value="global">Todos</option>
				<option value="auditados">Auditados</option>
        <option value="no_auditados">No Auditados</option>
      </select>;
  		<script>
				function carga_filtro_estado(filtro_estado){
					if(document.getElementById("filtro_sucursal_")){
						filtro_sucursal = document.getElementById("filtro_sucursal_").value;
					}
					else{
						filtro_sucursal = "<?php echo $this->id_sucursal_session; ?>";
					}

					fecha_inicial 			 = document.getElementById('MyInformeFiltroFechaInicio').value;
					fecha_final   			 = document.getElementById('MyInformeFiltroFechaFinal').value;
					tipo_documento_cruce = document.getElementById('filtro_tipo_documento').value;

					Ext.get("contenedor_Win_Panel_Global").load({
						url     : "auditoria_documentos/consulta_documentos.php",
						scripts : true,
						nocache : true,
						params  :	{
												filtro_sucursal      : filtro_sucursal,
												tipo_documento_cruce : tipo_documento_cruce,
												fecha_inicial        : fecha_inicial,
												fecha_final          : fecha_final,
												filtro_estado        : filtro_estado,
												opcGrillaContable    : "<?php echo $opcGrillaContable ?>",
											}
					});
				}
			</script>
			<?php
		}

		/**
		 * getAuditDocs Consultar los documentos para validar si ya fueron auditados
		 * @param  String $jsonId String con estructura Json que contiene los id de documentos y tipo de documentos
		 * @return Json    		  Json con los documentos que han sido auditados
		 */
		public function getAuditDocs($id_sucursal,$tipo_documento,$jsonId){
			$json=json_decode($jsonId,true);
			foreach ($json as $key => $id_documento) {
				$whereId .= ($whereId=="")? " id_documento=$id_documento " : " OR id_documento=$id_documento " ;
			}

			if($id_sucursal == "global"){
				$sql = "SELECT id FROM empresas_sucursales WHERE activo = 1 AND id_empresa = $this->id_empresa";
				$query = $this->mysql->query($sql);

				$whereSucursal = "";
				while($row = $this->mysql->fetch_array($query)){
					if($whereSucursal == ""){
						$whereSucursal .= " id_sucursal = $row[id]";
					} else{
						$whereSucursal .= " OR id_sucursal = $row[id]";
					}
				}

				$whereSucursal = " AND ($whereSucursal)";
			} else{
				$whereSucursal = " AND id_sucursal = $id_sucursal";
			}

			$sql = "SELECT
						id_usuario,
						documento_usuario,
						usuario,
						id_documento,
						consecutivo,
						tipo_documento,
						fecha,
						hora
					FROM documentos_auditados
					WHERE activo = 1
					$whereSucursal
					AND id_empresa=$this->id_empresa
					AND tipo_documento='$tipo_documento'
					AND ($whereId)";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayTemp[]= array(
									'id_usuario'        => $row['id_usuario'],
									'documento_usuario' => $row['documento_usuario'],
									'usuario'           => $row['usuario'],
									'id_documento'      => $row['id_documento'],
									'consecutivo'       => $row['consecutivo'],
									'tipo_documento'    => $row['tipo_documento'],
									'fecha'             => $row['fecha'],
									'hora'              => $row['hora'],
									);
			}

			echo json_encode(array('status' => "success", "auditDocs" => $arrayTemp));
		}

		/**
		 * setAuditDoc Autorizar o auditar documentos
		 * @param Int $id_sucursal    Id de la sucursal del documento
		 * @param String $tipo_documento Tipo de documento a auditar
		 * @param Int $id_documento   Id del documento a auditar
		 * @param String $checkValue   Valor a auditar o quitar  del documento
		 */
		public function setAuditDoc($id_sucursal,$tipo_documento,$consecutivo,$id_documento,$checkValue){
			if ($checkValue=='true') {
				$sql = "DELETE FROM documentos_auditados
						WHERE activo=1
						AND id_documento=$id_documento
						AND id_sucursal = $id_sucursal
						AND id_empresa=$this->id_empresa
						AND tipo_documento='$tipo_documento'";
			}
			else{
				$sql = "INSERT INTO documentos_auditados
							(
								id_usuario,
								documento_usuario,
								usuario,
								id_documento,
								consecutivo,
								tipo_documento,
								id_sucursal,
								id_empresa,
								fecha,
								hora
							)
						VALUES (
								'$_SESSION[IDUSUARIO]',
								'$_SESSION[DOCUMENTOUSUARIO]',
								'$_SESSION[USUARIO]',
								'$id_documento',
								'$consecutivo',
								'$tipo_documento',
								'$id_sucursal',
								'$this->id_empresa',
								'".date("Y-m-d")."',
								'".date("H:i:s")."'
							)";
			}

			$query = $this->mysql->query($sql);
			if ($query) {
				echo json_encode(array('status' => "success", ));
			}
			else{
				echo json_encode(array('status' => "failed", "sql"=>$sql));
			}
		}
	}
?>
