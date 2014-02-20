<?php
App::uses('GatewaysController', 'PaymentGateway.Controller');

class MercadoPagosController extends GatewaysController {

	public $name = 'MercadoPagos';

	public $uses = array('PaymentGateway.MercadoPago');

	public $paymentType;

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('process');
	}

/**
 * Process request from specific gateway.
 *
 * @return int On success 200 otherwise 404.
 */
	public function process() {
		if (!$this->request->is('POST')) {
			throw new ForbiddenException();
		}
		$this->autoRender = false;

		if (!$this->_handleRequest($this->request->query)) {
			throw new NotFoundException();
		}

		return $this->response->statusCode(200);
	}

/**
 * Get direct payment.
 *
 * @param int $id
 * @return int On success 200 otherwise 404.
 */
	public function getPayment($id) {
		return $this->MercadoPago->getPayment($id);
	}

/**
 * Create direct payment.
 *
 * @param array $data Specific array with conditional information from each gateway.
 * @return array $response Data created from gateway.
 */
	public function createPayment($data) {
		if (empty($data)) {
			throw new BadRequestException();
		}
		return $this->MercadoPago->createPayment($preferenceData);
	}

/**
 * Get subscription payment.
 *
 * @param int $id
 * @return int On success 200 otherwise 404.
 */
	public function getSubscription($id) {
		return $this->MercadoPago->getSubscription($id);
	}

/**
 * Create subscription payment.
 *
 * @param array $data Specific information from each gateway.
 * @return array $response Data created from gateway.
 */
	public function createSubscription($data) {
		if (empty($data)) {
			throw new BadRequestException();
		}
		return $this->MercadoPago->createSubscription($data);
	}

/**
 * Create sandbox users for each countries.
 *
 * @return string $siteId Decide in which country create users.
 * @link http://developers.mercadopago.com/documentacion/crear-usuarios-de-prueba
 */
	protected function _createUsers($siteId) {
		return $this->MercadoPago->createUsers($siteId);
	}

/**
 * Get token used to authenticate.
 *
 * @return string $token
 */
	protected function _getToken() {
		return $this->MercadoPago->getToken();
	}

/**
 * Select which method type we will handle.
 *
 * @link https://developers.mercadopago.com/documentacion/notificaciones-de-pago
 * @param array $request Instant Payment Request.
 * @return boolean True on success otherwise false.
 */
	protected function _handleRequest($request) {
		if (isset($request['sandbox'])) {
			//TODO
		}
		$this->log(print_r($request, true), 'gatewayNotice');

		if (empty($request['topic']) || empty($request['id'])) {
			throw new BadRequestException();
		}
		$transactionId = $request['id'];

		$this->paymentType = $request['topic'];
		switch ($this->paymentType) {
			case 'payment':
				$response = $this->getPayment($transactionId);
				break;
			case 'preapproval':
				$response = $this->getSubscription($transactionId);
				break;
		}

		if (empty($response['collection'])) {
			return false;
		}

		$collection = $response['collection'];
		if (!$matches = $this->_parseVariables($collection['external_reference'])) {
			return false;
		}

		$matches['transaction_id'] = $transactionId;
		$matches['gateway'] = $this->MercadoPago->alias;

		switch ($collection['status']) {
			case 'approved':
				if (!$this->_saveTransaction($collection, $matches) || !$this->_afterPaymentGateway($matches, true)) {
					return false;
				}
				break;
			case 'pending':
				$this->_saveTransaction($collection, $matches);
				break;
			case 'in_process':
				$this->_saveTransaction($collection, $matches);
				break;
			case 'rejected':
				$this->_saveTransaction($collection, $matches);
				break;
			case 'refunded':
				$this->_saveTransaction($collection, $matches);
				break;
			case 'cancelled':
				$this->_saveTransaction($collection, $matches);
				break;
			case 'in_mediation':
				$this->_saveTransaction($collection, $matches);
				break;
		}

		return true;
	}

/**
 * Parse custom variables in a string.
 * eg: -i189 Where '-i' as variable and '189' as value.
 *
 * @param string $appReference Data sent from app.
 * @return mixed $matches Array of variables matches from string otherwise false.
 */
	protected function _parseVariables($appReference) {
		if (!is_string($appReference) || !preg_match("/^(?<site>[a-z]{2})-i(?<item_id>\d*)-u(?<user_id>\d*)-p(?<product_id>\d*)?$/i", $appReference, $matches)) {
			return false;
		}
		return $matches;
	}

/**
 * Save transaction extracted from request.
 *
 * @param array $collection Transaction data.
 * @param array $matches Variables from external reference (like our app).
 * @return boolean Save operation status.
 */
	protected function _saveTransaction($collection, $matches) {
		if (!is_array($collection) || !is_array($matches)) {
			return false;
		}

		$data = array(
			'item_id' => $matches['item_id'],
			'product_id' => $matches['product_id'],
			'transaction_id' => $collection['id'],
			'gateway' => $this->MercadoPago->alias,
			'type' => $this->paymentType,
			'status' => $collection['status']
		);
		$this->MercadoPago->Payment->save($data);

		return true;
	}
}