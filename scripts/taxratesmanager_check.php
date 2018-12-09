<?php
require_once dirname(__FILE__).'/../app/Mage.php';
Mage::app();

$logger = new Yireo_TaxRatesManager_Logger_Console();
$check = new Yireo_TaxRatesManager_Check_Check($logger);
$check->execute();