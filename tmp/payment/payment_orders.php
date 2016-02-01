<?php
	ini_set('display_errors', '0');
	header('Content-type: text/html; charset=UTF-8');

	if (count($_GET) == 0) 
		die ('{"error": 1}');   

	include_once('_class/mysql.php');
	include_once('_class/GenerateXML.php');  

	$DB = new DB;
	if (!$DB->link)
		die ('{"error": 1}');

// проверка логина пароля, который агент передаст.
	if (isset($_GET['Lg']) && !empty($_GET['Lg']) && isset($_GET['Pd']) && !empty($_GET['Pd'])){
		$lg = substr($_GET['Lg'], 0, 50);
		$pd = substr($_GET['Pd'], 0, 50);
		
		if (('dryharder' !== $lg) && (sha1('dryharderpay123') !== $pd))
			die('{"error": 1}');
	}
	else
		die('{"error": 1}');

	if (isset($_GET['guid']) && !empty($_GET['guid'])){
		$guid = $_GET['guid'];
	}
	else
		die('{"error": 1}');

	$TABLE_PAY = 'payment_orders'; // Имя таблицы в которой находятся совершенные оплаты

	if (isset($_GET['Load'])){ // Загружаем оплаты
		$sql = 'select '.
							'`id`, '.
							'`dor_id`, '.
							'`contr_id`, '.
							'`amount`, '.
							'`datetime_load_server`, '.
							'`datetime_load`, '.
							'`datetime_unloading`, '.
							'`token`, '.
							'`card_last_four`, '.
							'`card_type`, '.
							'`user_name`, '.
							'`ip_address`, '.
							'`transaction_id`, '.
							'`email`, '.
							'`is_loaded`, '.
							'`waiting` '.
					 'from '.$TABLE_PAY.' '.
					 'where `is_loaded` = 0 and `waiting` = 0';
		$result = $DB->query($sql) or die('{"error": 1}');
		                                                 
		$xml = new GenerateXML();

		while ($product=$DB->fetch_array($result, MYSQL_BOTH)){
			$a = $xml->addEl('Pay');
			$xml->addEl('mysql_id', $product['id'], $a);
			$xml->addEl('dor_id', $product['dor_id'], $a);
			$xml->addEl('contr_id', $product['contr_id'], $a);
			$xml->addEl('amount', $product['amount'], $a);
			$xml->addEl('token', $product['token'], $a);
			$xml->addEl('card_last_four', $product['card_last_four'], $a);
			$xml->addEl('card_type', $product['card_type'], $a);

			$sql2 = "update ".$TABLE_PAY." set guid='".$DB->escape_string($guid)."' where id = ".$product['id'];
			$result2 = $DB->query($sql2) or die('{"error": 1}');
		}

		echo $xml->saveXML();
	}
	else
	if (isset($_GET['SavePay'])){ // сохранение оплаты    			
		$sql2 = "update ".$TABLE_PAY." set is_loaded=1, token=null, datetime_unloading = CURRENT_TIMESTAMP() where guid = '".$DB->escape_string($guid)."'";
		$result2 = $DB->query($sql2) or die('{"error": 1}');	

		$xml = new GenerateXML();
		$xml->addEl('Error', 0);
		echo $xml->saveXML();
	}
?>