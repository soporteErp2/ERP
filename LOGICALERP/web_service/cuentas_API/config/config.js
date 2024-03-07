require('dotenv').config();//instatiate environment variables

CONFIG = {} ;//Make this global to use all over the application

CONFIG.app          = process.env.APP   || 'prod';
CONFIG.port         = process.env.PORT  || '3000';

CONFIG.db_host      = process.env.DB_HOST       || 'localhost';
CONFIG.db_port      = process.env.DB_PORT       || 3306;
CONFIG.db_user      = process.env.DB_USER       || 'root';
CONFIG.db_name      = process.env.DB_NAME       || 'erp_acceso';
CONFIG.db_name1     = process.env.DB_NAME1      || '';
CONFIG.db_password  = process.env.DB_PASSWORD   || 'root';
//
CONFIG.jwt_encryption  = process.env.JWT_ENCRYPTION || 'A128GCM';
CONFIG.jwt_expiration  = process.env.JWT_EXPIRATION || '10000';
CONFIG.jwt_secret      = process.env.JWT_SECRET     || '9crtgnege9ppv$S!dt!hu3REuvg!L96';//ran


