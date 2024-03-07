/*jshint esversion: 6 */
"use strict";
const controller = {};
const jwt        = require('jsonwebtoken');
const config     = require('../config');
const tokenList  = {};
controller.login = (req, res) => {
    const postData = req.body;
    const user = {
        "nit"  : postData.nit,
        "nombre": postData.nombre
    };
    req.getConnection((err, conn) => {
        console.log(req.body.nit);
        conn.query("SELECT bd FROM host WHERE nit = ? ", [req.body.nit],  (err, results) => {
            console.log(results);
            if (typeof results !== 'undefined' && results.length > 0) {
               const token        = jwt.sign(user, config.secret, { expiresIn: config.tokenLife});
                const refreshToken = jwt.sign(user, config.refreshTokenSecret, { expiresIn: config.refreshTokenLife});
                const response     = {
                    "status"       : "Logged in",
                    "token"        : token,
                    "refreshToken" : refreshToken
                };
                tokenList[refreshToken] = response;
                res.status(200).json(response);
            }else{
                const response     = {
                    "status"       : "ERROR NIT_EMPRESA",
                    "note"        : 'La empresa con el NIT: '+req.body.nit+' No existe en ERP'

                };
                res.status(404).json(response);
            }
        });
      });
};
module.exports = controller;