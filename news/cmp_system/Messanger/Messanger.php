<?php
class Messanger extends Contenter {
	function e_ViewTitle() {
		$this->loadView("send_email_title");
	}
	
	function e_View() {
		$this->loadView("send_email_view");
	}
	function SendEmail($to,$from,$title, $message)
	{
		$headers  .= "MIME-Version: 1.0\r\n";
		$headers  .= "Content-type: text/html; charset=UTF-8\r\n"; 
		$headers .= "From:".$from; 
		if (mail($to, '=?UTF-8?B?'.base64_encode($title).'?=', $message, $headers))
			$this->InsertEmail($to,$from,$title);
	}
	
	function e_Send($id_competition) {
		if ($this->User->getRole() != "administrator")  return;
		
//		$send_to[0] = Array("name"=>"Олександр","surname"=>"Громовий","email"=>"gromovyy@gmail.com");
//		$send_to = $this->Member->CompetitionMemberList($id_competition);
//		$send_to = $this->DBProc->user_email_list();
//		print_r($user_list);
//		print_r ($send_to);
		
		$title = $_POST["title"];
		$message = $_POST["message"];
		
		
//		echo $title;
//		echo $message;
		
		
		foreach ($send_to as $receiver) {
			$text =  $message;
//			$text =  "Доброго дня, ".$receiver["name"]." ".$receiver["surname"]."!".$message;
			
//			echo "Получатель = {$receiver["email"]}, Заголовок = $title, Сообщение = $message<br>";
			usleep(1000);
		}
		$this->redirect(getenv("HTTP_REFERER")); // возращаемся на предыдущую страницу		
		
		
	}
	
	function InsertEmail($to,$from, $title) {
		$id_row = $this->create_row("mssngr_messanger");
		$this->set_cell("mssngr_messanger","to",$id_row,$to);
		$this->set_cell("mssngr_messanger","from",$id_row,$from);
		$this->set_cell("mssngr_messanger","title",$id_row,$title);
		return $id_row;

	}
	
 	/**
	  * Подключение к внешней БД и отправка СМС
	  */
	function SendSMS($recipientPhone,$sms_message) 
	{
		$DBHost='77.120.116.10';
		$DBUser='infocenter';
		$DBPass='mTR8kw0';
		$DBName='users';
		$db1=mysql_connect($DBHost,$DBUser,$DBPass) or die("Could not connect to Host: " . mysql_error());
		mysql_select_db($DBName,$db1) or die("Could not connect: " . mysql_error());
		
		mysql_query("SET NAMES 'utf8';")or die("Could not set names utf8: " . mysql_error());
		$query = "insert into infocenter (number,sign,message) values('".$recipientPhone."','IC-GROUP',
		'".$sms_message."')";

		/*$query_infocenter = "insert into infocenter (number,sign,message) values('+380662106336','IC-GROUP',
		'Заказ с сайта: ".$site.". Имя:".$customerName.". Тел:".$customerPhone."')";*/
		
//***************Для блокировки отправки SMS закоментировать*************//
//		mysql_query($query)or die("Could not insert new sms: " . mysql_error()); 
//		mysql_query($query_infocenter)or die("Could not insert new sms: " . mysql_error()); 
//***************************************************************************//
		
		mysql_close($db1);	
	}

}