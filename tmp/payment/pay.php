<?php
	ini_set('display_errors', '0');
	header('Content-type: text/html; charset=UTF-8');
	if ($_SERVER['REMOTE_ADDR'] != '130.193.70.192'){  //130.193.70.192 только с этого адреса могут приходить запросы по оплате
		die('Ошибка');
	}

	$TABLE_PAY = 'payment_orders'; // Имя таблицы в которой находятся совершенные оплаты

//	if (intval($_POST['TestMode']) == 1)
//		die(Result());
	
	if (empty($_POST['InvoiceId']) && empty($_POST['AccountId']))
		die(Result());

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
	$Token = $_POST['Token'];

	include_once('_class/mysql.php');

	$DB = new DB;
	if (!$DB->link)
		die(Result());

// проверяем есть ли оплата с такой транзакцией
	$sql = 'select a.ID from '.$TABLE_PAY.' a '.
				 'where a.transaction_id = "'.$DB->escape_string($TransactionId).'" and a.waiting = 1';
	$result = $DB->query($sql) or die(Result());
	if ($DB->num_rows($result) == 0)
		die(Result());
	$id_pay = mysql_result($result, 0); 

	foreach ($_POST as $key => $value){ // на всякий случай сохраняем все переданные параметры в строку
		$data_post .= ' "'.$key.'" => "'.$value.'",';
	}
				
	$sql = "update $TABLE_PAY ".
					"set ".
						"`dor_id` = '".$DB->escape_string($dor_id)."', ".
						"`contr_id` = '".$DB->escape_string($contr_id)."', ".
						"`amount` = ".$DB->escape_string(floatval($Amount)).", ".
						"`datetime_load_server` = CURRENT_TIMESTAMP(), ".
						"`datetime_load` = '".$DB->escape_string($DateTime)."', ".
						"`datetime_unloading` = null, ".
						"`token` = '".$DB->escape_string($Token)."', ".
						"`card_last_four` = '".$DB->escape_string($CardLastFour)."', ".
						"`card_type` = '".$DB->escape_string($CardType)."', ".
						"`user_name` = '".$DB->escape_string($Name)."', ".
						"`ip_address` = '".$DB->escape_string($IpAddress)."', ".
						"`transaction_id` = '".$DB->escape_string($TransactionId)."', ".
						"`email` = '".$DB->escape_string($Email)."', ".
						'`is_loaded` = 0,'.
						"`query_string` = '".$DB->escape_string($data_post)."', ".
						"`waiting` = 0".
					"where `id` = ".$id_pay;
	$DB->query($sql) or die(Result());
	echo(Result());
	
	function Result(){
		return '{"code":0}'; // система оплаты всегда ждет этот ответ исходя из документации
	}
?>
