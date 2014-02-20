<?php
/**
 * This file is loaded automatically with plugin autoload.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your plugin uses.
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
define('PRODUCTION', 'production');
define('DEVELOPMENT', 'development');
define('MERCADO_PAGO', 'MercadoPago');

CakeLog::config('gatewayNotify', array(
	'engine' => 'FileLog',
	'types' => array('gatewayNotice'),
	'file' => 'gatewayNotify',
));

Configure::load('PaymentGateway.gateways');
Configure::load('PaymentGateway.config');