<?php

ini_set( 'display_errors', '0' );
error_reporting( E_ALL );

$json = file_get_contents('php://input');

$decoded = json_decode($json, true);

$siteId    = isset( $decoded['bill']['siteId'] )              ? $decoded['bill']['siteId']              : '';
$account   = isset( $decoded['bill']['customer']['account'] ) ? $decoded['bill']['customer']['account'] : '';
$amount    = isset( $decoded['bill']['amount']['value'] )     ? $decoded['bill']['amount']['value']     : 0;
$currency  = isset( $decoded['bill']['amount']['currency'] )  ? $decoded['bill']['amount']['currency']  : '';
$billId    = isset( $decoded['bill']['billId'] )              ? $decoded['bill']['billId']              : 0;
$status    = isset( $decoded['bill']['status']['value'] )     ? $decoded['bill']['status']['value']     : '';

if ( $status != 'PAID' )
	die;

$sign = isset( $_SERVER['HTTP_X_API_SIGNATURE_SHA256'] ) ? $_SERVER['HTTP_X_API_SIGNATURE_SHA256'] : '';

$invoice_parameters = "$currency|$amount|$billId|$siteId|$status";

require 'config.php';

$hash = hash_hmac('sha256', $invoice_parameters, $secretKey );

if ( $sign !== $hash )
	die( 'Bad sign!' );

require 'db.class.php';

$db = new DB( $config );

// $query = 'UPDATE payments SET date_complete = NOW(), status = 1 WHERE siteId = "'.$this->db->real_escape_string($siteId).'"LIMIT 1';
// $this->db->query($query);

$sql = "INSERT INTO `completed` ( `srv`, `account`, `amount` ) VALUES ( 1, ?, ? )";

if ( $db->prepareAndExecute( $sql, [ $account, $amount ] )->rowCount() )
	die( 'Платеж #' . $billId . ' принят!' );
	
echo 'Платеж #' . $billId . ' не принят!';

?>