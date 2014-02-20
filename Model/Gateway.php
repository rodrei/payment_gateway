<?php
App::uses('PaymentGatewayAppModel', 'PaymentGateway.Model');

abstract class Gateway extends PaymentGatewayAppModel {

	public $name = 'Gateway';

	public $useTable = false;

	public $hasMany = array(
		'Payment' => array(
			'className' => 'PaymentGateway.Payment'
		)
	);
}