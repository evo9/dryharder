<?php
  ini_set('display_errors', '0');
	header('Content-type: text/html; charset=UTF-8');
	
	if (count($_GET) == 0)
		die ('{"error": 1}');
		
	if (isset($_GET['dor_id']))
		$dor_id = intval(substr($_GET['dor_id'], 0, 50));
	else
		die ('{"error": 1}');		
	
	include_once('_class/mysql.php');
	
	$DB = new DB;
	if (!$DB->link)
		die ('{"error": 1}');
	
	$TABLE_PAY = 'payment_orders'; // Имя таблицы в которой находятся совершенные оплаты

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
				 'where `dor_id` = '.$dor_id.' and `waiting` = 1';
	$result = $DB->query($sql) or die('{"error": 1}');
//		$id_pay = mysql_result($result, 0);
	if ($DB->num_rows($result) > 0)
		echo('{"error": 0}');
	else
		echo('{"error": 1}');
?>