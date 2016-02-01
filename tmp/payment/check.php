<?php
	ini_set('display_errors', '0');
	header('Content-type: text/html; charset=UTF-8');
	if ($_SERVER['REMOTE_ADDR'] != '130.193.70.192'){ //130.193.70.192 только с этого адреса могут приходить запросы по оплате
		die('Ошибка');
	} 

	$url_serv = 'http://www.himstat.ru/cl/laundry2_test/api/';  
	if (empty($_POST['InvoiceId']) && empty($_POST['AccountId']))
		die(Result(13));

	$dor_id = $_POST['InvoiceId'];
	$contr_id = $_POST['AccountId'];

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
	curl_setopt($curl, CURLOPT_TIMEOUT, 5000 / 1000-5);
	curl_setopt($curl, CURLOPT_URL, $url_serv.'IsGoodOrder?dor_id='.$dor_id.'&contr_id='.$contr_id);
	$result = curl_exec($curl);
	if ((curl_errno($curl) == 0) && (strpos($result, '502 Bad Gateway') === false)){
		curl_close($curl);

		$obj = json_decode($result);
		if($obj){		
			switch($obj->{'error'}){
				case 0:{
					include_once('_class/mysql.php');

					$DB = new DB;
					if (!$DB->link)
						die(Result(13)); 	  

					foreach ($_POST as $key => $value){ // сохраняем все переданные параметры в строку
						$data_post .= ' "'.$key.'" => "'.$value.'",';
					}	

					$dor_id = $_POST['InvoiceId'];
					$contr_id = $_POST['AccountId'];
					$TransactionId = $_POST['TransactionId'];
					$Amount = $_POST['Amount'];
					$Name = $_POST['Name'];
					$Email = $_POST['Email'];
					$DateTime = $_POST['DateTime'];
					$IpAddress = $_POST['IpAddress'];
					$CardLastFour = $_POST['CardLastFour'];
					$CardType = $_POST['CardType'];
						
					// проверяем есть ли оплата с такой транзакцией, хотя ее быть еще и так не должно.
					$sql = 'select a.ID from '.$TABLE_PAY.' a '.
								 'where a.transaction_id = "'.$DB->escape_string($TransactionId).'"';
					$result = $DB->query($sql) or die(Result(13));
					if ($DB->num_rows($result) > 0)
						die(Result(13));
						
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
						"`is_loaded`, ".
						"`query_string`, ".
						"`waiting`".
						")".
					"values(".
						"'".$DB->escape_string($dor_id)."', ".
						"'".$DB->escape_string($contr_id)."', ".
						$DB->escape_string(floatval($Amount)).", ".
						"CURRENT_TIMESTAMP(), ".
						"'".$DB->escape_string($DateTime)."', ".
						"null, ".
						"'".$DB->escape_string($CardLastFour)."', ".
						"'".$DB->escape_string($CardType)."', ".
						"'".$DB->escape_string($Name)."', ".
						"'".$DB->escape_string($IpAddress)."', ".
						"'".$DB->escape_string($TransactionId)."', ".
						"'".$DB->escape_string($Email)."', ".
						"0, ".
						"'".$DB->escape_string($data_post)."',".
						"1".
					")";		
					
					$DB->query($sql) or die(Result(13));	
						
					echo(Result(0));
					break;
				}
				case 1:{
					echo(Result(10));
					break;
				}
			}
		}
		else
			echo(Result(10));
	}
	else{
		echo(Result(13));
	}
	
	function Result($code){
		return '{"code":'.$code.'}';
	}
?>