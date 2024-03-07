/*jshint esversion: 6 */
"use strict";
const router            = require('express').Router();
const AuthController    = require('../controllers/AuthController');
const CuentasController = require('../controllers/CuentasController');
//Auth Routes
router.post('/login', AuthController.login);
// router.post('/token', AuthController.token);
//Auth Rules
router.use(require('../config/tokenChecker'));
// main
router.get('/:nit?', CuentasController.json);
module.exports = router;