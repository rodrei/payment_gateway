<?php
class PaymentGatewaySchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $payments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'item_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'product_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'transaction_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'gateway' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'ascii_general_ci', 'charset' => 'ascii'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'ascii_general_ci', 'charset' => 'ascii'),
		'status' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'u_transaction_id_status' => array('column' => array('transaction_id', 'status'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

}
