<?php
    /*
     * update to rest ::: E.hassani ::: zarinpal
     *::: www.vahabonline.ir
     *::: myvahab@gmail.com
    */
	# Required File Includes
	include('../../../dbconnect.php');
	include('../../../includes/functions.php');
	include('../../../includes/gatewayfunctions.php');
	include('../../../includes/invoicefunctions.php');

	$gatewaymodule = 'zarinpalwg'; # Enter your gateway module name here replacing template

	$GATEWAY = getGatewayVariables($gatewaymodule);
	if (!$GATEWAY['type']) die('Module Not Activated'); # Checks gateway module is active before accepting callback

	# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
	$invoiceid  = $_GET['invoiceid'];
	$Amount 	= $_GET['Amount'];
	$Authority  = $_GET['Authority'];
	$invoiceid  = checkCbInvoiceID($invoiceid, $GATEWAY['name']); # Checks invoice ID is a valid invoice number or ends processing

	$CaculatedFee = round($Amount*0.025);
	
	if($GATEWAY['afp'] == 'on'){
		$PaidFee 	= 0;
		$HiddenFee  = $CaculatedFee;
	} else {
		$PaidFee 	= $CaculatedFee;
		$HiddenFee  = 0;
	}

	switch($GATEWAY['MirrorName']){
		case 'آلمان': 
			$mirror = 'de';
			break;
		case 'ایران':
			$mirror = 'ir';
			break;
		default:
			$mirror = 'www';
			break;
	}

	if($_GET['Status'] == 'OK'){
		try {
//			$client = new SoapClient('https://'. $mirror .'.zarinpal.com/pg/services/WebGate-ZarinGate/wsdl', array('encoding' => 'UTF-8'));

			$data = array('MerchantID' => $GATEWAY['merchantID'], 'Authority' => $Authority, 'Amount' => $Amount+$HiddenFee);
			$jsonData = json_encode($data);
			$ch = curl_init('https://www.zarinpal.com/pg/rest/WebGate-ZarinGate/PaymentVerification.json');
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
			curl_close($ch);
			$result = json_decode($result, true);




			
			$result  = $result->Status;
			$transid = $result->RefID;
			
			checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does
			
		} catch (Exception $e) {
			echo '<h2>وقوع وقفه!</h2>';
			print_r($e);
		}
	} else {
		$resultO = new stdClass();
		$result = -77;
	}

	if($GATEWAY['Currencies'] == 'Rial'){
		$Amount  *= 10;
		$PaidFee *= 10;
	}
	
	if ($result['Status'] == 100) {
		addInvoicePayment($invoiceid, $transid, $Amount, $PaidFee, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
		logTransaction($GATEWAY['name'], array('Get' => $_GET, 'Websevice' => (array) $resultO), 'Successful'); # Save to Gateway Log: name, data array, status
	} else {
		logTransaction($GATEWAY['name'], array('Get' => $_GET, 'Websevice' => (array) $resultO), 'Unsuccessful'); # Save to Gateway Log: name, data array, status
	}
	Header('Location: '.$CONFIG['SystemURL'].'/clientarea.php?action=invoices');
    
?>
