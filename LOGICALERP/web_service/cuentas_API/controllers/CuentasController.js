/*jshint esversion: 6 */
"use strict";
const controller = {};
controller.json = (req, res) => {
  	let nit   = req.params.nit;
  	// fecha = req.params.fecha;
	var connection_user = req.app.get('connection_user');

	connection_user.getConnection((err, conn) => {
    conn.query("SELEC fecha,debe,haber,id_cuenta,codigo_cuenta,cuenta FROM asientos_colgaap WHERE nit_tercero = ? ", [nit],  (err, cuentas) => {//RECIBO UNO SOLO


    	// conn.query("SELEC fecha,debe,haber,id_cuenta,codigo_cuenta,cuenta FROM asientos_colgaap WHERE nit_tercero = ? AND fecha= ? ", [nit,fecha],  (err, cuentas) => { RECIBO DOS PARAMETROS
      res.send(cuentas);//envio como json
    });
  });

  // req.getConnection((err, conn) => {
  //   conn.query("SELEC fecha,debe,haber,id_cuenta,codigo_cuenta,cuenta FROM asientos_colgaap WHERE nit_tercero = ? ", [nit],  (err, cuentas) => {//RECIBO UNO SOLO


  //   	// conn.query("SELEC fecha,debe,haber,id_cuenta,codigo_cuenta,cuenta FROM asientos_colgaap WHERE nit_tercero = ? AND fecha= ? ", [nit,fecha],  (err, cuentas) => { RECIBO DOS PARAMETROS
  //     res.send(cuentas);//envio como json
  //   });
  // });
};
module.exports = controller;
