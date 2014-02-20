<?php
App::uses('PaymentGatewayAppController', 'PaymentGateway.Controller');

abstract class GatewaysController extends PaymentGatewayAppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('process');
	}

/**
 * Process request from specific gateway.
 *
 * @return int On success 200 otherwise 404.
 */
	abstract public function process();

/**
 * Get direct payment.
 *
 * @param int $id
 * @return int On success 200 otherwise 404.
 */
	abstract public function getPayment($id);

/**
 * Create direct payment.
 *
 * @param array $data Specific array with conditional information from each gateway.
 * @return array $response Data created from gateway.
 */
	abstract public function createPayment($data);

/**
 * Get subscription payment.
 *
 * @param int $id
 * @return int On success 200 otherwise 404.
 */
	abstract public function getSubscription($id);

/**
 * Create subscription payment.
 *
 * @param array $data Specific information from each gateway.
 * @return array $response Data created from gateway.
 */
	abstract public function createSubscription($data);

/**
 * Get token used to authenticate.
 *
 * @return string $token
 */
	abstract protected function _getToken();

/**
 * Select which method type we will handle.
 *
 * @param array $request Instant Payment Request.
 * @return boolean True on success otherwise false.
 */
	abstract protected function _handleRequest($request);

/**
 * Save transaction extracted from request.
 *
 * @param array $collection Transaction data.
 * @param array $matches Variables from external reference (like our app).
 * @return boolean Save operation status.
 */
	abstract protected function _saveTransaction($collection, $matches);
}