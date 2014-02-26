# Payment Gateways

Plugin CakePHP to pay with different payments gateway. Easy to add a new payment gateway.

Now only implemented Mercado Pago.

## Requirements
  
* PHP version: 5.5+
* CakePHP version: 2.x+

## Configuration Files

You have to fill the next settings:
* `gateways.default` has among other things `clientId` and `clientSecret` per country.
* `config.default` set if plugin is in sandbox or production mode, also you have to set sandbox `clientId` and `clientSecret`.

## Installation

The plugin is pretty easy to set up, all you need to do is to clone it to you application plugins folder and load the needed tables. You can create database tables using the schema shell.

`./Console/cake schema create payments --plugin PaymentGateway`

### Enable Plugin

You need to enable the plugin in your app/Config/bootstrap.php file:

`CakePlugin::load('PaymentGateway', array('bootstrap' => true));`

### IPN

Add generic route to receive IPN notifications from gateways in your app/Config/routes.php file:

`array('/ipn/:controller/process', array('plugin' => 'PaymentGateway', 'action' => 'process'))`

Also create function in your app/Controller/AppController.php file to impact changes when IPN notify:

`protected function _afterPaymentGateway($notification, $status) {//code}`

## MercadoPago Helper

Add on your Controllers:

`public $helpers = array('PaymentGateway.MercadoPago');`

and then start creating buttons on your Views:

* Create Mercado Pago default button styles:

`<?=$this->MercadoPago->getButton($title, $initPointUrl)?>`

* Or add settings in third parameter:

`<?=$this->MercadoPago->getButton($title, $initPointUrl, $options)?>`

* Create Custom button with your own styles:

`<?=$this->MercadoPago->getCustomButton($title, $initPointUrl, array('class' => 'custom-class'))?>`

## MercadoPago Behavior

Use Mercado Pago api adding behavior in your Models:

`public $actsAs = array('PaymentGateway.MercadoPago');`

For example get specific direct payment:

* in your controllers:

`$this-><Model>->getPayment($paymentId);`

* in your model:

`$this->getPaymentUrl($paymentId);`

> Also you can create direct payments, get and create subscription payments.

## Documentation

[Mercado Pago Developers](https://developers.mercadopago.com/)

## Contributing to this Plugin

Please feel free to contribute to the plugin with new issues, requests, unit tests and code fixes or new features. If you want to contribute some code, create a feature branch from develop, and send us your pull request. Unit tests for new features and issues detected are mandatory to keep quality high.
