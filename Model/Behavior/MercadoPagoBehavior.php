<?php
/**
 * PaymentGateway Behavior.
 *
 * @author Nicolás Lara
 */
class MercadoPagoBehavior extends ModelBehavior {

	protected $_defaults = array();

	protected $_sandbox = false;

	protected $_site = array();

/**
 * Configure settings.
 * Set production or sandbox environment.
 *
 */
	public function setup(Model $model, $settings = array()) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = $this->_defaults;
		}
		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], (array)$settings);

		if (!Configure::check(MERCADO_PAGO)) {
			throw new NotFoundException();
		}
		$this->_site = Configure::read(MERCADO_PAGO);
		$authentication = $this->_site['authentication'];

		App::import('Vendor', 'PaymentGateway.MP', array('file' => 'MercadoPago' . DS . 'lib' . DS . 'mercadopago.php'));
		$this->MP = new MP($authentication['clientId'], $authentication['clientSecret']);

		if ($this->_site['environment'] === 'development') {
			$this->_sandbox = $this->MP->sandbox_mode(true);
		}
	}

/**
 * Get authenticate Token.
 *
 * @param object $model
 * @return string token
 */
	public function getToken(Model $model) {
		return $this->MP->get_access_token();
	}

/**
 * Get payment info.
 *
 * @param object $model
 * @param int $paymentId
 * @return mixed Response from Mercado Pago Api otherwise an empty array.
 */
	public function getPayment(Model $model, $paymentId) {
		try {
			$payment = $this->MP->get_payment($paymentId);
			return $payment['response'];
		} catch(Exception $e) {
			return array();
		}
	}

/**
 * Create direct payment type.
 *
 * @param object $model
 * @param array $paymentData
 * @return mixed Response from Mercado Pago Api otherwise an Exception.
 */
	public function createPayment(Model $model, $paymentData) {
		try {
			$response = $this->MP->create_preference($paymentData);
			if ($response['status'] !== 201) {
				return false;
			}
		} catch(Exception $e) {
			return array();
		}
		return $response['response'];
	}

/**
 * Get subcription info.
 *
 * @param object $model
 * @param int $subscriptionId
 * @return mixed Response from Mercado Pago Api otherwise an empty array.
 */
	public function getSubscription(Model $model, $subscriptionId) {
		try {
			$payment = $this->MP->get_preapproval_payment($subscriptionId);
			return $payment['response'];
		} catch(Exception $e) {
			return array();
		}
	}

/**
 * Create subscription payment type.
 *
 * @param object $model
 * @param array $subscriptionData
 * @return mixed Response from Mercado Pago Api otherwise an Exception.
 */
	public function createSubscription(Model $model, $subscriptionData) {
		try {
			$response = $this->MP->create_preapproval_payment($subscriptionData);
			if ($response['status'] !== 201) {
				return false;
			}
		} catch(Exception $e) {
			return array();
		}
		return $response['response'];
	}

/**
 * Create user to use in sandbox mode on specific country.
 *
 * @param object $model
 * @param string $siteId Determine in which country will be create users.
 * @return mixed Response from Mercado Pago Api otherwise an Exception.
 */
	public function createUsers(Model $model, $siteId) {
		Configure::load('PaymentGateway.mercadopago_sites');

		$sites = Configure::read('MercadoPagoSites');
		if (!in_array($siteId, $sites, true)) {
			throw new NotFoundException();
		}

		return $this->MP->create_users(array('site_id' => $siteId));
	}

/**
 * Create direct payment and return appropiate init point url. Url could be production or sandbox mode.
 *
 * @param object $model
 * @param array $paymentData Options for create payment.
 * @return string Return init point url.
 */
	public function getPaymentUrl(Model $model, $paymentData) {
		$payment = $this->createPayment($model, $paymentData);
		if ($this->_sandbox) {
			return $payment['sandbox_init_point'];
		}

		return $payment['init_point'];
	}

/**
 * Verify the settings of mandatory fields for direct payment and returned.
 *
 * @param object $model
 * @param array $options
 * @return mixed Return array with preference configured otherwise false.
 * @link https://developers.mercadopago.com/documentacion/api/preferences
 */
	public function setPayment(Model $model, $options) {
		if (!is_array($options) || empty($options)) {
			return false;
		}

		$paymentData = array(
			'items' => array(
				array(
					'title' => null,
					'quantity' => null,
					'unit_price' => null,
					'currency_id' => $this->_site['currency'],
					'picture_url' => null,
					'id' => null,
					'description' => null
				)
			),
			'payer' => array(
				'name' => null,
				'surname' => null,
				'email' => null
			),
			'back_urls' => array(
				'success' => null,
				'pending' => null,
				'failure' => null
			),
			'payment_methods' => array(
				'excluded_payment_methods' => array(),
				'excluded_payment_types' => array(),
				'installments' => null
			),
			'external_reference' => null,
			'notification_url' => null
		);
		return array_replace_recursive($paymentData, $options);
	}
}