
<?php
/**
 * @author Masoud Amini
 * @copyright 2013
 */
function zarinpalzg_config() {
    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"زرین پال - زرین گیت"),
     "merchantID" => array("FriendlyName" => "merchantID", "Type" => "text", "Size" => "50", ),
     "Currencies" => array("FriendlyName" => "Currencies", "Type" => "dropdown", "Options" => "Rial,Toman", ),
	 "MirrorName" => array("FriendlyName" => "نود اتصال", "Type" => "dropdown", "Options" => "آلمان,ایران,خودکار", "Description" => "چناانچه سرور شما در ایران باشد ایران دا انتخاب کنید و در غیر اینصورت آلمان و یا خودکار را انتخاب کنید", ),
     "afp" => array("FriendlyName" => "افزودن کارمزد به قیمت ها", "Type" => "yesno", "Description" => "در صورت انتخاب 1 درصد به هزینه پرداخت شده افزوده می شود.", ),
     );
	return $configarray;
}

function zarinpalzg_link($params) {

	# Gateway Specific Variables
	$merchantID = $params['merchantID'];
    $currencies = $params['Currencies'];
    $afp = $params['afp'];
	$mirrorname = $params['MirrorName'];
    
	# Invoice Variables
	$invoiceid = $params['invoiceid'];
	$description = $params["description"];
    $amount = $params['amount']; # Format: ##.##
    $currency = $params['currency']; # Currency Code

	# Client Variables
	$firstname = $params['clientdetails']['firstname'];
	$lastname = $params['clientdetails']['lastname'];
	$email = $params['clientdetails']['email'];
	$address1 = $params['clientdetails']['address1'];
	$address2 = $params['clientdetails']['address2'];
	$city = $params['clientdetails']['city'];
	$state = $params['clientdetails']['state'];
	$postcode = $params['clientdetails']['postcode'];
	$country = $params['clientdetails']['country'];
	$phone = $params['clientdetails']['phonenumber'];

	# System Variables
	$companyname = $params['companyname'];
	$systemurl = $params['systemurl'];
	$currency = $params['currency'];

	# Enter your code submit to the gateway...

	$code = '
    <form method="post" action="./zarinpalzg.php">
        <input type="hidden" name="merchantID" value="'. $merchantID .'" />
        <input type="hidden" name="invoiceid" value="'. $invoiceid .'" />
        <input type="hidden" name="amount" value="'. $amount .'" />
        <input type="hidden" name="currencies" value="'. $currencies .'" />
        <input type="hidden" name="afp" value="'. $afp .'" />
        <input type="hidden" name="systemurl" value="'. $systemurl .'" />
		<input type="hidden" name="email" value="'. $email .'" />
		<input type="hidden" name="cellnum" value="'. $phone .'" />
		<input type="hidden" name="mirrorname" value="'. $mirrorname .'" />
        <input type="submit" name="pay" value=" پرداخت " />
    </form>
    ';

	return $code;
}
?>
