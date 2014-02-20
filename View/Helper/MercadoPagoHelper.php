<?php
/**
 * Mercado Pago Helper.
 *
 * @link https://developers.mercadopago.com/documentacion/render#resources-web-look
 */
class MercadoPagoHelper extends Helper {

	public $helpers = array('Html');

/**
 * Default Settings.
 *
 * @var array
 */
	public $defaultSettings = array();

/**
 * Colours enabled on Mercado Pago button.
 *
 * @var array
 */
	protected $_buttonColours = array(
		'blue',
		'grey',
		'green',
		'lightblue',
		'orange',
		'red'
	);

/**
 * Fonts enabled on Mercado Pago button.
 *
 * @var array
 */
	protected $_buttonFonts = array(
		'arial' => 'Ar',
		'georgia' => 'Ge',
		'trebuchet' => 'Tr'
	);

/**
 * Action type when click the button.
 *
 * @var array
 */
	protected $_buttonMode = array(
		'blank',
		'modal',
		'popup',
		'redirect'
	);

/**
 * Sizes enabled on Mercado Pago button.
 *
 * @var array
 */
	protected $_buttonSizes = array(
		'small' => 'S',
		'medium' => 'M',
		'large' => 'L'
	);

/**
 * Shapes enabled on Mercado Pago button.
 *
 * @var array
 */
	protected $_buttonShapes = array(
		'rounded' => 'Rn',
		'oval' => 'Ov',
		'square' => 'Sq'
	);

/**
 * Table name for this Model.
 *
 * @var array
 */
	protected $_buttonLogosAll = array(
		'ar' => 'ArAll',
		'br' => 'BrAll',
		'co' => 'CoAll',
		'mx' => 'MxAll',
		've' => 'VeAll'
	);

/**
 * Table name for this Model.
 *
 * @var array
 */
	protected $_buttonLogosOn = array(
		'ar' => 'ArOn',
		'br' => 'BrOn',
		'co' => 'CoOn',
		'mx' => 'MxOn',
		've' => 'VeOn'
	);

/**
 * Script used to render styles and action for button.
 *
 * @var array
 */
	protected $_javascript = array(
		'mercadopago' => 'PaymentGateway./js/mercadopago/render.js',
	);

/**
 * Setup default settings.
 *
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		if (!Configure::check(MERCADO_PAGO)) {
			throw new NotFoundException();
		}
		$site = Configure::read(MERCADO_PAGO);

		$this->defaultSettings = array(
			'name' => 'MP-Checkout',
			'class' => array(
				'color' => 'lightblue',
				'font' => $this->_buttonFonts['arial'],
				'size' => $this->_buttonSizes['small'],
				'shape' => $this->_buttonShapes['rounded'],
				'logo' => ''
			),
			'mode' => 'redirect'
		);

		if (!empty($settings)) {
			$this->defaultSettings = array_merge($this->defaultSettings, (array)$settings);
		}

		if (!Configure::check(MERCADO_PAGO)) {
			throw new NotFoundException();
		}
		$site = Configure::read(MERCADO_PAGO);
	}

/**
 * Return html button setting styles options.
 *
 * options:
 *		- name Anchor name.
 *		- class
 *			- color Use $_buttonColours
 *			- font Use $_buttonFonts
 *			- size Use $_buttonSizes
 *			- shape Use $_buttonShapes
 *			- logo Use $_buttonLogosAll or $buttonLogosOn
 *		- mode Use $_buttonMode
 *
 * @param string $title
 * @param string $url
 * @param array $options
 * @return mixed Html button otherwise false on error.
 * @link https://developers.mercadopago.com/documentacion/render#resources-web-look
 */
	public function getButton($title, $url, $options = array()) {
		if (empty($title) || empty($url) || !is_array($options) || !is_array($options['class'])) {
			return false;
		}

		$options = array_replace_recursive($this->defaultSettings, $options);
		$options['class'] = implode('-', $options['class']);

		$button = $this->_createLink($title, $url, $options);
		$button .= $this->_setScript();

		return $button;
	}

/**
 * Return button with custom classes, this not use rendering from Mercado Pago.
 *
 * @param string $title
 * @param string $url
 * @param array $options
 * @return mixed Html button otherwise false on error.
 */
	public function getCustomButton($title, $url, $options = array()) {
		if (empty($title) || empty($url)) {
			return false;
		}

		if (!empty($options)) {
			$options = array_replace_recursive($this->defaultSettings, $options);
		}

		$button = $this->_createLink($title, $url, $options);
		return $button;
	}

/**
 * Set script for mercado pago button.
 *
 * @param boolean $inline Default print inline.
 * @return string $script Return script.
 */
	protected function _setScript($inline = true) {
		$script = $this->Html->script($this->_javascript['mercadopago'], array('inline' => $inline));
		return $script;
	}

/**
 * Return html button setting styles options.
 *
 * @param string $title
 * @param string $url
 * @param array $options
 * @return string $link Anchor with configured options.
 */
	protected function _createLink($title, $url, $options) {
		return $this->Html->link(
			$title,
			$url,
			array(
				'name' => $options['name'],
				'class' => $options['class'],
				'mp-mode' => $options['mode']
			)
		);
	}
}