<?php
App::uses('Gateway', 'PaymentGateway.Model');

class MercadoPago extends Gateway {

	public $name = 'MercadoPago';

	public $actsAs = array(
		'PaymentGateway.MercadoPago'
	);
}