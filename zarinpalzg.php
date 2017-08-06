<?php

    /*
     * @author Masoud Amini
     * @copyright 2013
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
		$Fee = round($Amount*0.01);
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
	
	$CallbackURL = 'http://' . $_SERVER['SERVER_NAME']  .'/modules/gateways/callback/zarinpalzg.php?invoiceid='. $_POST['invoiceid'] .'&Amount='. $Amount;
	try {
		$client = new SoapClient('https://'. $mirror .'.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8'));
	
		$result = $client->PaymentRequest(
											array(
													'MerchantID' 	=> $_POST['merchantID'],
													'Amount' 		=> $Amount+$Fee,
													'Description' 	=> 'Invoice ID: '. $_POST['invoiceid'],
													'Email' 		=> $_POST['email'],
													'Mobile' 		=> $_POST['cellnum'],
													'CallbackURL' 	=> $CallbackURL
												)
										);
	} catch (Exception $e) {
		echo '<h2>وقوع وقفه!</h2>';
		echo $e->getMessage();
	}
	if($result->Status == 100){ 
		$url = 'https://www.zarinpal.com/pg/StartPay/' . $result->Authority . '/ZarinGate';
		redirect($url);
	} else {
		echo "<h2>وقوع خطا در ارتباط!</h2>"
			.'کد خطا'. $result->Status;
	}
?>
