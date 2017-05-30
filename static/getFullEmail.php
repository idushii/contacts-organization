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
	
	function getLinkContakts($html, $home) {
		$links = array();
		//var_dump($html);
		preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/",$html,$links);

		$host = parse_url($home)['host'];
		$listSelectLinks = array();
		for($i=0; $i<count($links[1]); $i++) {
		
			if (strripos($links[1][$i], 'http://') === false) {
				if ($links[1][$i][0] == '/') 
					$links[1][$i] = $home.$links[1][$i];
				else 
					$links[1][$i] = 'http://'.$links[1][$i];
			}
			
			if (strripos(parse_url($links[1][$i])['host'], $host) !== false)
				array_push($listSelectLinks, $links[1][$i]);
			
				
		}
		
		sort($listSelectLinks);
		
		$listFilterLinks = array();
		
		for($i=0; $i<count($listSelectLinks)-1; $i++) {
			if ($listSelectLinks[$i] != $listSelectLinks[$i+1]) {
				array_push($listFilterLinks, $listSelectLinks[$i]);
			}
		}
		
		return $listFilterLinks;
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
	
	function pushEmails($url, $flag = true) {
		$html = getHTML($url);
		
		$dataEmails = find_emails($html);
		for($j=0; $j<count($dataEmails); $j++)
			array_push($GLOBALS['mails'], $dataEmails[$j]);
		
		if ($flag) {
			$linksContakt = getLinkContakts($html, $url);
			for($i=0; $i<count($linksContakt); $i++)
				pushEmails($linksContakt[$i], false);
		}//*/
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
	
	if (count($mails) == 0) {
		echo "[]";
		return;
	}
	
	sort($mails);
	//var_dump($mails);
	$filterMails = array();
	for($i=0; $i<count($mails)-1; $i++) {
		if ($mails[$i] != $mails[$i+1]) {
			array_push($filterMails, $mails[$i]);
		}
	}
	array_push($filterMails, $mails[count($mails)-1]);

	
	echo json_encode($filterMails);
?>