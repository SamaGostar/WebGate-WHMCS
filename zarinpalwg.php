<?php

    /*
     *::: www.vahabonline.ir
     *::: myvahab@gmail.com
    */
	 
	function redirect($url){
		if(!headers_sent()) {
			header('Location: '. $url);
			exit;
		}
	}

    $Amount = intval($_POST['amount']);
    
	if($_POST['currencies'] == 'Rial'){
		$Amount = round($Amount/10);
	}
	
	if($_POST['afp']=='on'){
		$Fee = round($Amount*0.025);
	} else {
		$Fee = 0;
	}
	
	switch($_POST['mirrorname']){
		case 'آلمان': 
			$mirror = 'de';
			break;
		case 'ایران':
			$mirror = 'ir';
			break;
		default:
			$mirror = 'de';
			break;
	}
	
	$CallbackURL = $_POST['systemurl'] .'/modules/gateways/callback/zarinpalwg.php?invoiceid='. $_POST['invoiceid'] .'&Amount='. $Amount;
	try {
		
		$data = array('MerchantID' => $_POST['merchantID'],
			'Amount' => $Amount+$Fee,
			'Email' 		=> $_POST['email'],
			'Mobile' 		=> $_POST['cellnum'],
			'CallbackURL' => $CallbackURL,
			'Description' => 'Invoice ID: '. $_POST['invoiceid']);
		$jsonData = json_encode($data);
		$ch = curl_init('https://www.zarinpal.com/pg/rest/WebGate-ZarinGate/PaymentRequest.json');
		curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData)
		));
		$result = curl_exec($ch);
		$err = curl_error($ch);
		$result = json_decode($result, true);
		curl_close($ch);


	} catch (Exception $e) {
		echo '<h2>وقوع وقفه!</h2>';
		echo $e->getMessage();
	}
if ($result["Status"] == 100) {
	header('Location: https://www.zarinpal.com/pg/StartPay/' . $result["Authority"]. '/ZarinGate');
} else {
		echo "<h2>وقوع خطا در ارتباط!</h2>"
			.'کد خطا'. $result->Status;
	}
?>
