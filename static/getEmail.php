<?php 
	header("Access-Control-Allow-Origin: *");
	ini_set('display_errors','Off');
	function find_emails($text){
	$emails=array();
	preg_match_all('([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})',$text,$potential_emails,PREG_SET_ORDER);
	for($i=0;$i<count($potential_emails);$i++){
		$potential_email=$potential_emails[$i][0];
		if (filter_var($potential_email,FILTER_VALIDATE_EMAIL)){
			if (!in_array($potential_email,$emails)){
				$emails[]=$potential_email;
			}
		}
	}
	return $emails;
}

	function getHTML($url) {
		// создание нового cURL ресурса
		$ch = curl_init();

		// установка URL и других необходимых параметров
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// загрузка страницы и выдача её браузеру
		$html = curl_exec($ch);

		// завершение сеанса и освобождение ресурсов
		curl_close($ch);
		
		return $html;
	}

	
	$mails = array();
	
	function pushEmails($url) {
	
		$html = getHTML($url);
		$dataEmails = find_emails($html);
		for($j=0; $j<count($dataEmails); $j++)
			array_push($GLOBALS['mails'], $dataEmails[$j]);
	}
	
	$url = explode(',',$_GET['url']);
	for($i=0; $i<count($url); $i++) {
		if (strripos($url[$i], 'http://') === false) {
			pushEmails('http://'.$url[$i]);
			if (!strripos($url[$i], 'www')) {
				pushEmails('http://www.'.$url[$i]);
			}
		} else {
			pushEmails($url[$i]);
		}//*/
	}
	
	echo json_encode($mails);
?>