<h2>Autenticacion</h2>
<p>En las API de logicalsoft utilizamos Basic Access como método de autenticación, todos los request que se realicen deben incluir el header Authorization.
El acceso al API se realiza utilizando el nombre de usuario, el token generado a ese usuario desde el modulo de empleados en nuestro software y el nit
de la empresa sin digito de verificacion.</p>
<h2>Uso del metodo de autenticacion</h2>
<p>En el header Authorization se debe poner el usuario, el token del usuario separado por dos puntos (:), el nit de la empresa separado por dos puntos (:) todo en base64,
Por ejemplo, si el usuario es usuario@example.com, el token es tokenapi12345, y el nit de la empresa es 900467785 el header Authorization debe quedar así :</p>
<table>
	<tr>
        <td class="code">Authorization: dXN1YXJpb0BleGFtcGxlLmNvbTp0b2tlbmFwaTEyMzQ1OjkwMDQ2Nzc4NQ==</td>
  	</tr>
</table>
<p>Siendo <code>dXN1YXJpb0BleGFtcGxlLmNvbTp0b2tlbmFwaTEyMzQ1OjkwMDQ2Nzc4NQ==</code> el base_64("usuario@example.com:tokenapi12345:900467785")</p>
<p>El incorrecto envio de la autenticacion, o el no enviar la informacion completa, ocacionara algunos de los siguientes errores:</p>

<h2>Errores 4xx</h2>
<table>
	<thead>
		<tr>
			<th style="width: 30%">Status</th>
			<th style="width: 70%">Descripción</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="code">401 Unauthorized</td>
			<td><p>Datos de autenticacion incompletos</p></td>
		</tr>
		<tr>
			<td class="code">401 Unauthorized</td>
			<td><p>La empresa no existe en el sistema</p></td>
		</tr>
		<tr>
			<td class="code">401 Unauthorized</td>
			<td><p>El usuario no existe en el sistema</p></td>
		</tr>
		<tr>
			<td class="code">401 Unauthorized</td>
			<td><p>Error, token invalido</p></td>
		</tr>
	</tbody>
</table>