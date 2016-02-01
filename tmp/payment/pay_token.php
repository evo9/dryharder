<?php
	ini_set('display_errors', '0');
	header('Content-type: text/html; charset=UTF-8');
  
  if (count($_GET) == 0) //если ни каких параметров в строке нет, то убиваем выполнение скрипта
		die ('{"error": 1, "Msg": "Ошибка данных"}');  

	$url_serv = 'http://www.himstat.ru/cl/laundry2_test/api/';
	
	$TABLE_PAY = 'payment_orders'; // Имя таблицы в которой находятся совершенные оплаты 
	
	include_once('_class/mysql.php');

	if (isset($_GET['SessionID']) && !empty($_GET['SessionID'])){
		$SessionID = strtolower($_GET['SessionID']);
		if (!preg_match("/^[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}$/", $SessionID))
			die('{"error": 1, "Msg": "Ошибка данных"}');
	}
	else
		die('{"error": 1, "Msg": "Ошибка данных"}');

	if (isset($_GET['user_id']) && !empty($_GET['user_id'])){
		$user_id = substr($_GET['user_id'], 0, 50);
		$user_id = intval($user_id);
	}
	else
		die('{"error": 1, "Msg": "Ошибка данных"}');

	if (isset($_GET['card_id']) && !empty($_GET['card_id'])){
		$card_id = substr($_GET['card_id'], 0, 50);
		$card_id = intval($card_id);
	}
	else
		die('{"error": 1, "Msg": "Ошибка данных"}');

	if (isset($_GET['dor_id']) && !empty($_GET['dor_id'])){
		$dor_id = substr($_GET['dor_id'], 0, 50);
		$dor_id = intval($dor_id);
	}
	else
		die('{"error": 1, "Msg": "Ошибка данных"}');

	if (isset($_GET['amount']) && !empty($_GET['amount'])){
		$amount = substr($_GET['amount'], 0, 50);
		$amount = floatval($amount);
	}
	else
		die('{"error": 1, "Msg": "Ошибка данных"}');

	$email = '';
	$doc_num = '';
	$Token = '';
	GetToken();
	
	if ($Token == '')
		die('{"error": 1, "Msg": "Нет токена карты оплаты"}');

	if ($email == '')
		die('{"error": 1, "Msg": "Нет адреса email"}');
		
	if (IsGoodOrder())
		PayByToken();
	else
		die('{"error": 1, "Msg": "Ошибка заказа"}');

	function GetToken(){
		global $url_serv;
		global $email;
		global $Token;
		global $SessionID;
		global $card_id;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($curl, CURLOPT_TIMEOUT, 5000 / 1000-5);
		curl_setopt($curl, CURLOPT_URL, $url_serv.'TokenPay?id='.$card_id.'&SessionID='.$SessionID);
		$result = curl_exec($curl);
		if ((curl_errno($curl) == 0) && (strpos($result, '502 Bad Gateway') === false)){
			curl_close($curl);

			$obj = json_decode($result);
			if($obj){		
				switch($obj->{'error'}){
					case 0:{
						$Token = $obj->{'token'};
						$email = $obj->{'email'};
						break;
					}
					default:{
						if ($obj->{'Msg'})
	  					die('{"error": 1, "Msg": "'.$obj->{'Msg'}.'"}');
	  				else
	  					die('{"error": 1}');
						break;
					}
				}
			}
			else			
	  		die('{"error": 1, "Msg": "Ошибка токена"}');
		}
		else
		  die('{"error": 1, "Msg": "Ошибка токена"}');
	}
	
	function IsGoodOrder(){ 
		global $url_serv;
		global $doc_num;
		global $dor_id;
		global $user_id;
		global $SessionID;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5000 / 1000-5);
		curl_setopt($curl, CURLOPT_URL, $url_serv.'IsGoodOrder2?dor_id='.$dor_id./*'&contr_id='.$user_id.*/'&SessionID='.$SessionID);
		$result = curl_exec($curl);
		if ((curl_errno($curl) == 0) && (strpos($result, '502 Bad Gateway') === false)){
			curl_close($curl);

			$obj = json_decode($result);
			if($obj){		
				switch($obj->{'error'}){
					case 0:{
						$doc_num = $obj->{'doc_num'};
						return true;
					}
					case 1:{
						return false;
						break;
					}
				}
			}
			else
				return false;
		}
		else
			return false;
	}

  function PayByToken(){
  	global $amount;
  	global $dor_id;
  	global $doc_num;
  	global $user_id;
  	global $Token;
		$ch = curl_init();

		$ext_headers = array(
			'Content-Type: application/json'
		);

		$curl_options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERPWD => ':', // авторизационные данные из ЛК cloudPayments
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => $ext_headers,
			CURLOPT_URL => 'https://api.cloudpayments.ru/payments/tokens/charge',
			CURLOPT_POSTFIELDS =>
				'{'.
					'"Amount":'.$amount.','.
					'"Currency":"RUB",'.
					'"InvoiceId":"'.$order_id.'",'.
					'"Description":"Оплата заказа №'.$doc_num.'",'.
					'"AccountId":"'.$user_id.'",'.
					'"Token":"'.$Token.'"'.
					'"TestMode": true,'.
					'"JsonData": "{'.
							'"type": "pay_order_token"'.
							'"contr_id": "'.$user_id.'"'.
							'"dor_id": "'.$dor_id.'"'.
							'"doc_num": "'.$doc_num.'"'.
							'"amount": "'.$amount.'"'.
						'}"'.
				'}'
		);
		curl_setopt_array($ch, $curl_options);
		$data = curl_exec($ch);
		
		$obj = json_decode($data);
		if ($obj){ 
			if (($obj->{'Success'} == 'true') || ($obj->{'Success'} == true)) {
				$obj2 = $obj->{'Model'};
				
				$DB = new DB;
  			if (!$DB->link)
    			die (APIConstants::ERROR_CONNECTING_DATABASE());
				
				$sql = "insert into $TABLE_PAY(".
					"`dor_id`, ".
					"`contr_id`, ". 
					"`amount`, ".
					"`datetime_load_server`, ".
					"`datetime_load`, ".
					"`datetime_unloading`, ".
					"`card_last_four`, ".
					"`card_type`, ".
					"`user_name`, ".
					"`ip_address`, ".
					"`transaction_id`, ".
					"`email`, ".
					"`is_loaded`)".
				"values(".
					"'".$DB->escape_string($dor_id)."', ".
					"'".$DB->escape_string($user_id)."', ".
					$DB->escape_string($amount).", ".
					"CURRENT_TIMESTAMP(), ".
					"'".$DB->escape_string($obj2 = $obj->{'CreatedDateIso'})."', ".
					"null, ".
					"null, ".
					"'".$DB->escape_string($obj2 = $obj->{'CardLastFour'})."', ".
					"'".$DB->escape_string($obj2 = $obj->{'CardType'})."', ".
					"'".$DB->escape_string($obj2 = $obj->{'Name'})."', ".
					"'".$DB->escape_string($obj2 = $obj->{'IpAddress'})."', ".
					"'".$DB->escape_string($obj2 = $obj->{'TransactionId'})."', ".
					"'".$DB->escape_string($obj2 = $obj->{'Email'})."', ".
					"0)";		
				$DB->query($sql) or die('{"error": 1, "Msg": "Сохранения оплаты"}');
				
        echo('{"error": 0, "Msg": "'.$obj2->{'CardHolderMessage'}.'"}');
				return true;
			}
			elseif (($obj->{'Success'} == 'false') || ($obj->{'Success'} == false)) {
				if ($obj->{'Message'}){ //некорректный запрос
					echo('{"error": 1, "Msg": "'.$obj->{'Message'}.'"}');
					return false;
				}
				elseif ($obj->{'Model'}){ // транзакция отклонена
					$obj2 = $obj->{'Model'};
					echo('{"error": 1, "Msg": "'.$obj2->{'CardHolderMessage'}.'", "Status": "Отклонено", "Reason": "'.$obj2->{'Reason'}.
							'", "ReasonCode": '.$obj2->{'ReasonCode'}.'}');
					return false;
				}
			}
		}
		else
			return false;
	}
?>
