/*jshint esversion: 6 */
"use strict";
require('./config/config');//instantiate configuration variables
const express         = require('express'),
		mysql         = require('mysql'),
		fs            = require('fs'),
		myConnection  = require('express-myconnection'),
		bodyParser    = require('body-parser'),
		https         = require('https'),
		helmet        = require('helmet');
//:: mainexpress variable :://
const app = express();
// import routes
const CuentasRoutes = require('./routes/routes');
// settings
app.set('port',CONFIG.port);
// :::::::MIDDLEWARES::::::::::::
// app.use(function(req, res) {
//   res.status(404).send({url: req.originalUrl + ' not found'})
// });
app.use(helmet());
app.use(
myConnection(mysql,{
    host     : CONFIG.db_host,
	user     : CONFIG.db_user,
	password : CONFIG.db_password,
	port     : CONFIG.db_port,
  	database : CONFIG.db_name
},'single') //or single
);
var newConnection = mysql.createPool({
	host     : CONFIG.db_host,
	user     : CONFIG.db_user,
	password : CONFIG.db_password,
	port     : CONFIG.db_port,
	database : CONFIG.db_name1
});
app.set('connection_user',newConnection);//setting DB configuration in a variable
app.use(bodyParser.json()); // support json encoded bodies
app.use(bodyParser.urlencoded({ extended: false })); // support encoded bodies
app.use('/', CuentasRoutes);
const options = {
  key: fs.readFileSync("/opt/lampp/etc/ssl.key/servidor.key"),
  cert: fs.readFileSync("/opt/lampp/etc/ssl.crt/6e49b630919b83cd.crt")


};
app.listen(app.get('port'), () => {
  console.log(`server on port ${app.get('port')} now online`);
});
https.createServer(options, app).listen(app.get('port'));