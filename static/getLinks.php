<?php
	header("Access-Control-Allow-Origin: *");
	//ini_set('display_errors','Off');

	function getLinkContakts($html, $home) {
		$links = array();
		//var_dump($html);
		preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/",$html,$links);

		$host = parse_url($home)['host'];
		$listSelectLinks = array();
		
		for($i=0; $i<count($links[1]); $i++) {
			if (strripos($links[1][$i], '+') !== false) continue;
		
			if (strripos($links[1][$i], 'http://') === false & strripos($links[1][$i], 'https://') === false) {
				$links[1][$i] = $home.'/'.$links[1][$i];
			}
			
			if (strripos(parse_url($links[1][$i])['host'], $host) !== false)
				array_push($listSelectLinks, $links[1][$i]);
		}
		
		sort($listSelectLinks);
		//print_r($links[1]);
		
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
	
	function pushLinks($url) {
		$html = getHTML($url);
		//$html = file_get_contents($url);
		$localLink = getLinkContakts($html, $url);
		for($i=0; $i<count($localLink); $i++)
			array_push($GLOBALS['links'], $localLink[$i]);
	}
		
	$url = explode(',',$_GET['url']);
	$links = array();
	for($i=0; $i<count($url); $i++) {
		if (strripos($url[$i], 'http://') === false) {
			pushLinks('http://'.$url[$i]);
			if (strripos($url[$i], 'www') === false) {
				pushLinks('http://www.'.$url[$i]);
			}
		} else {
			pushLinks($url[$i]);
		}//*/
	}
	
	sort($links);
	
	$listFilterLinks = array();
	
	for($i=0; $i<count($links)-1; $i++) {
		if ($links[$i] != $links[$i+1]) {
			$info = new SplFileInfo($links[$i]);
			$ext = $info->getExtension();
			if ( $ext != 'jpg' & $ext != 'png' & $ext != 'JPEG')
				array_push($listFilterLinks, $links[$i]);
		}
	}

	
	//echo print_r($listFilterLinks);
	echo json_encode($listFilterLinks);

?>