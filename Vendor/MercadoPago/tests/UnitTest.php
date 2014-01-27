<?php

// You need configure absolute_path here
require_once('../lib/mercadopago.php');

class UnitTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        
    }

    public function tearDown() {
        
    }

    var $mp;

    public function UnitTest() {
        $this->mp = new MP("CLIENT_ID", "CLIENT_SECRET");
    }

    /* Call preference added through button flow */

    public function testGetPreference() {

        $preferenceResult = $this->mp->get_preference("ID");

        $this->assertTrue($preferenceResult["status"] == 200);
    }

    /* Create a new preference and verify that data result are ok */

    public function testCreatePreference() {

        $preference = array(
            "items" => array(
                array(
                    "title" => "test1",
                    "quantity" => 1,
                    "currency_id" => "ARS",
                    "unit_price" => 10.2
                )
            )
        );

        $preferenceResult = $this->mp->create_preference($preference);

        $this->assertTrue($preferenceResult["status"] == 201);

        $this->assertTrue($preferenceResult["response"]["items"][0]["title"] == "test1"
                && (int) $preferenceResult["response"]["items"][0]["quantity"] == 1
                && (double) $preferenceResult["response"]["items"][0]["unit_price"] == 10.2
                && $preferenceResult["response"]["items"][0]["currency_id"] == "ARS");
    }

    /* We create a new preference, we modify this one and then we verify that data are ok. */

    public function testUpdatePreference() {

        $preference = array(
            "items" => array(
                array(
                    "title" => "test2",
                    "quantity" => 1,
                    "currency_id" => "ARS",
                    "unit_price" => 20.55
                )
            )
        );

        $preferenceResult = $this->mp->create_preference($preference);

        $preference = array(
            "items" => array(
                array(
                    "title" => "test2Modified",
                    "quantity" => 2,
                    "currency_id" => "USD",
                    "unit_price" => 100
                )
            )
        );

        $preferenceUpdatedResult = $this->mp->update_preference($preferenceResult["response"]["id"], $preference);

        $this->assertTrue($preferenceUpdatedResult["status"] == 200);

        $preferenceUpdatedResult = $this->mp->get_preference($preferenceResult["response"]["id"]);

        $this->assertTrue((double) $preferenceUpdatedResult["response"]["items"][0]["unit_price"] == 100
                && (double) $preferenceUpdatedResult["response"]["items"][0]["quantity"] == 2
                && $preferenceUpdatedResult["response"]["items"][0]["title"] == "test2Modified"
                && $preferenceUpdatedResult["response"]["items"][0]["currency_id"] == "USD");
    }

}

?>
