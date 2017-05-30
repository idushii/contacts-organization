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

	function find_phones($text){
		$phones=array();
		preg_match_all('(([\(][\d]{3}[\)])([ -]?)(([\d]{2,3})([ ]?)([\d]{2,4})([ ]?)([\d]{2})|([\d]{2,3})([-])([\d]{2,4})([-])([\d]{2})))',$text,$potential_phones);
		for($i=0;$i<count($potential_phones[0]);$i++){
			if ($potential_phones[0][$i]) {
				if (strlen($potential_phones[0][$i]) > 6) {
					$flag = true;
					for($numPhone=0; $numPhone<count($phones); $numPhone++) {
						//echo "{$phones[$numPhone]}, {$potential_phones[0][$i]}\n";
						if (stripos($phones[$numPhone], $potential_phones[0][$i])) {
							$flag = false;
						}
					}
					if ($flag)
						$phones[]=$potential_phones[0][$i];
				}
			}
		}
		return $phones;
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
	$phones = array();
	
	function pushEmails($url) {
		$html = getHTML($url);
		
		$dataEmails = find_emails($html);
		for($j=0; $j<count($dataEmails); $j++)
			array_push($GLOBALS['mails'], $dataEmails[$j]);

		$dataPhones = find_phones($html);
		for($j=0; $j<count($dataPhones); $j++)
			array_push($GLOBALS['phones'], $dataPhones[$j]);
		
	}
	
	function filterData($data) {
		sort($data);
		//var_dump($mails);
		$dataReturn = array();
		for($i=0; $i<count($data)-1; $i++) {
			if ($data[$i] != $data[$i+1]) {
				array_push($dataReturn, $data[$i]);
			}
		}
		array_push($dataReturn, $data[count($data)-1]);
		return $dataReturn;
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
	
	if ((count($mails) == 0) & (count($phones) == 0)) {
		echo "[[],[]]";
		return;
	}

	$filterMails = filterData($mails);
	$filterPhones = filterData($phones);

	echo json_encode([$filterMails, $filterPhones]);
?>