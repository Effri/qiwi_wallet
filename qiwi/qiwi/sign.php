<?php

ini_set( 'display_errors', '0' );
error_reporting( E_ALL );

header( "Content-type: application/json" );

$XMLHttpRequest = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

if ( $XMLHttpRequest ) 
{
	require 'config.php';
	require 'db.class.php';
	
	$login  = isset( $_POST['account'] )  ? $_POST['account']  : '';
	
	$db = new DB( $config ); 
	
	if ( $account = $db->fetch( "SELECT login FROM accounts WHERE login = ?", [ $login ] ) )
	{
	    $billId = time(); 

		getAccountByName($account);

    	$data = [
    		'publicKey'	 => $publicKey,
    		'billId' 	 => $billId,
    		'successUrl' => $successUrl
    	];
	}
	else
	{
	    $data = [
    		'error' => [
    			'message' => 'Аккаунт ' . $login . ' не найден!'
    		]
    	];
	}
}
else
{
	$data = [
		'error' => [
			'code' => 403,
			'message' => 'Forbidden'
		]
	];
}

echo json_encode( $data );

?>