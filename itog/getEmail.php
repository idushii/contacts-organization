<?php 
	header("Access-Control-Allow-Origin: *");
	ini_set('display_errors','Off');
	function find_emails($text){
	$emails=array();
	preg_match_all('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/',$text,$potential_emails,PREG_SET_ORDER);
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

	
	$url = $_GET['url'];
	$html = file_get_contents($url); // Скачиваю код страницы
	//preg_match_all( "/^.*?@.*?\..*?$/", $html, $mails );
	
	$mails = find_emails($html);
	
	echo json_encode($mails);
?>