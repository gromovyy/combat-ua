<?php
// MVC ? Nope, never heard of it.


if (php_sapi_name() == "cli") {
	$cli = true;
	$html = false;
	$eol = PHP_EOL;
} else {
	$cli = false;
	$html = true;
	$eol='<br>';
}
	error_reporting(0);
if (!defined('INSTALL') and !$cli) {
	die();
}



if ($cli) {
	$step = 'install';
	$install_data['dbhost'] = 'localhost';
	$install_data['dbuser'] = 'root';
	$install_data['dbpass'] = '';
	$install_data['dbname'] = 'iccms_'.chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
	$install_data['email' ] = 'admin@site.com';
	$install_data['pass'  ] = '123';
	$install_data['title' ] = '';

	if (is_array($argv)) {
		foreach ($argv as $key => $value) {
			$param = explode('=', $value);
			$install_data[$param[0]]=$param[1];
		}
	}
} else {
	session_start();
	// $_SESSION = ''; // Обнуление сессии для разработки
	error_reporting(E_ERROR | E_WARNING | E_PARSE );
	//ini_set('display_errors', 'On');
	//print_r($_SESSION);
	if(isset($_GET['step'])){$step = $_GET['step'];}
	if (empty($_SESSION)) {
		$_SESSION['dbhost'] = 'localhost';
		$_SESSION['dbuser'] = 'root';
		$_SESSION['dbpass'] = '';
		$_SESSION['dbname'] = 'iccms_'.chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
		$_SESSION['email' ] = 'admin@site.com';
		$_SESSION['pass'  ] = '123';
		$_SESSION['title' ] = '';
	}
	$install_data = $_SESSION;
}
?>
<?php if ($html) { ?>
<!DOCTYPE html>
<html>
<head>
<style>
	html,body{height:100%;width:100%}
html{background:#000 url(tree_bark.png);background-color:rgba(0,0,0,0.55);overflow:hidden;animation:animation--background 10s linear infinite forwards}
html:after{position:absolute;left:0;top:0;height:100%;width:100%;background:#000}
.clubbing{position:absolute;top:50%;left:50%;animation:animation--clubbing 11.37s ease-out infinite forwards;background:rgba(255,255,255,0.5);width:.75em;height:.75em;-webkit-transition:box-shadow .45s ease-in-out;-moz-transition:box-shadow .45s ease-in-out;-o-transition:box-shadow .45s ease-in-out;transition:box-shadow .45s ease-in-out;-webkit-transform:scale(2.5) rotate(0) translate(0);-moz-transform:scale(2.5) rotate(0) translate(0);-ms-transform:scale(2.5) rotate(0) translate(0);-o-transform:scale(2.5) rotate(0) translate(0);transform:scale(2.5) rotate(0) translate(0)}
@keyframes animation--clubbing{0%{box-shadow:1.5em 0 rgba(255,0,0,0.1),6.00522em 2.48744em rgba(255,96,0,0.4),1.06066em 1.06066em rgba(255,191,0,0.4),2.48744em 6.00522em rgba(223,255,0,0.5),0 1.5em rgba(128,255,0,0.4),-2.48744em 6.00522em rgba(32,255,0,0),-1.06066em 1.06066em rgba(0,255,64,0.3),-6.00522em 2.48744em rgba(0,255,159,0.2),-1.5em 0 rgba(0,255,255,0.4),-6.00522em -2.48744em rgba(0,159,255,0.8),-1.06066em -1.06066em rgba(0,64,255,0.2),-2.48744em -6.00522em rgba(32,0,255,0.3),0 -1.5em rgba(127,0,255,0.4),2.48744em -6.00522em rgba(223,0,255,0.7),1.06066em -1.06066em rgba(255,0,191,0.7),6.00522em -2.48744em rgba(255,0,96,0.5);border-radius:.73em;width:.5em;height:.5em;transform:scale(2.5) rotate(332deg) translate(0)} 6.25%{box-shadow:6.5em 0 rgba(255,0,0,0.3),1.38582em .57403em rgba(255,96,0,0.5),4.59619em 4.59619em rgba(255,191,0,0.1),.57403em 1.38582em rgba(223,255,0,0.4),0 6.5em rgba(128,255,0,0),-0.57403em 1.38582em rgba(32,255,0,0.4),-4.59619em 4.59619em rgba(0,255,64,0.1),-1.38582em .57403em rgba(0,255,159,0.3),-6.5em 0 rgba(0,255,255,0.1),-1.38582em -0.57403em rgba(0,159,255,0.2),-4.59619em -4.59619em rgba(0,64,255,0.9),-0.57403em -1.38582em rgba(32,0,255,0.7),0 -6.5em rgba(127,0,255,0.4),.57403em -1.38582em rgba(223,0,255,0.8),4.59619em -4.59619em rgba(255,0,191,0.5),1.38582em -0.57403em rgba(255,0,96,0.8);border-radius:.22em;width:1.5em;height:1.5em;transform:scale(1.5) rotate(133deg) translate(0)} 12.5%{box-shadow:1.5em 0 rgba(255,0,0,0.2),6.00522em 2.48744em rgba(255,96,0,0.1),1.06066em 1.06066em rgba(255,191,0,0.4),2.48744em 6.00522em rgba(223,255,0,0.6),0 1.5em rgba(128,255,0,0.2),-2.48744em 6.00522em rgba(32,255,0,0.3),-1.06066em 1.06066em rgba(0,255,64,0.8),-6.00522em 2.48744em rgba(0,255,159,0),-1.5em 0 rgba(0,255,255,0.3),-6.00522em -2.48744em rgba(0,159,255,0.6),-1.06066em -1.06066em rgba(0,64,255,0.7),-2.48744em -6.00522em rgba(32,0,255,0.9),0 -1.5em rgba(127,0,255,0.6),2.48744em -6.00522em rgba(223,0,255,0.2),1.06066em -1.06066em rgba(255,0,191,0.4),6.00522em -2.48744em rgba(255,0,96,0.9);border-radius:.01em;width:2.5em;height:2.5em;transform:scale(1.5) rotate(163deg) translate(1em)} 18.75%{box-shadow:6.5em 0 rgba(255,0,0,0.5),1.38582em .57403em rgba(255,96,0,0.8),4.59619em 4.59619em rgba(255,191,0,0.5),.57403em 1.38582em rgba(223,255,0,0.6),0 6.5em rgba(128,255,0,0.4),-0.57403em 1.38582em rgba(32,255,0,0.6),-4.59619em 4.59619em rgba(0,255,64,0.1),-1.38582em .57403em rgba(0,255,159,0.3),-6.5em 0 rgba(0,255,255,0.2),-1.38582em -0.57403em rgba(0,159,255,0.3),-4.59619em -4.59619em rgba(0,64,255,0.3),-0.57403em -1.38582em rgba(32,0,255,0.9),0 -6.5em rgba(127,0,255,0.1),.57403em -1.38582em rgba(223,0,255,0.8),4.59619em -4.59619em rgba(255,0,191,0.9),1.38582em -0.57403em rgba(255,0,96,0.1);border-radius:.21em;width:.5em;height:.5em;transform:scale(1.5) rotate(317deg) translate(5em)} 25%{box-shadow:1.5em 0 rgba(255,0,0,0.1),6.00522em 2.48744em rgba(255,96,0,0.5),1.06066em 1.06066em rgba(255,191,0,0.1),2.48744em 6.00522em rgba(223,255,0,0.9),0 1.5em rgba(128,255,0,0.2),-2.48744em 6.00522em rgba(32,255,0,0.6),-1.06066em 1.06066em rgba(0,255,64,0.1),-6.00522em 2.48744em rgba(0,255,159,0.5),-1.5em 0 rgba(0,255,255,0.3),-6.00522em -2.48744em rgba(0,159,255,0),-1.06066em -1.06066em rgba(0,64,255,0.7),-2.48744em -6.00522em rgba(32,0,255,0.6),0 -1.5em rgba(127,0,255,0.7),2.48744em -6.00522em rgba(223,0,255,0.3),1.06066em -1.06066em rgba(255,0,191,0.4),6.00522em -2.48744em rgba(255,0,96,0.9);border-radius:.95em;width:2.5em;height:2.5em;transform:scale(2.5) rotate(131deg) translate(1em)} 31.25%{box-shadow:6.5em 0 rgba(255,0,0,0.3),1.38582em .57403em rgba(255,96,0,0.2),4.59619em 4.59619em rgba(255,191,0,0.9),.57403em 1.38582em rgba(223,255,0,0.9),0 6.5em rgba(128,255,0,0.3),-0.57403em 1.38582em rgba(32,255,0,0.7),-4.59619em 4.59619em rgba(0,255,64,0.9),-1.38582em .57403em rgba(0,255,159,0.4),-6.5em 0 rgba(0,255,255,0.8),-1.38582em -0.57403em rgba(0,159,255,0.4),-4.59619em -4.59619em rgba(0,64,255,0.2),-0.57403em -1.38582em rgba(32,0,255,0.7),0 -6.5em rgba(127,0,255,0.9),.57403em -1.38582em rgba(223,0,255,0.3),4.59619em -4.59619em rgba(255,0,191,0.4),1.38582em -0.57403em rgba(255,0,96,0.6);border-radius:.6em;width:-0.5em;height:-0.5em;transform:scale(2.5) rotate(236deg) translate(3em)} 37.5%{box-shadow:1.5em 0 rgba(255,0,0,0.5),6.00522em 2.48744em rgba(255,96,0,0.8),1.06066em 1.06066em rgba(255,191,0,0.1),2.48744em 6.00522em rgba(223,255,0,0.8),0 1.5em rgba(128,255,0,0),-2.48744em 6.00522em rgba(32,255,0,0.5),-1.06066em 1.06066em rgba(0,255,64,0.6),-6.00522em 2.48744em rgba(0,255,159,0),-1.5em 0 rgba(0,255,255,0.9),-6.00522em -2.48744em rgba(0,159,255,0.5),-1.06066em -1.06066em rgba(0,64,255,0.9),-2.48744em -6.00522em rgba(32,0,255,0.4),0 -1.5em rgba(127,0,255,0.3),2.48744em -6.00522em rgba(223,0,255,0.2),1.06066em -1.06066em rgba(255,0,191,0.3),6.00522em -2.48744em rgba(255,0,96,0.2);border-radius:.08em;width:1.5em;height:1.5em;transform:scale(2.5) rotate(99deg) translate(0)} 43.75%{box-shadow:6.5em 0 rgba(255,0,0,0),1.38582em .57403em rgba(255,96,0,0.3),4.59619em 4.59619em rgba(255,191,0,0.9),.57403em 1.38582em rgba(223,255,0,0.2),0 6.5em rgba(128,255,0,0.4),-0.57403em 1.38582em rgba(32,255,0,0),-4.59619em 4.59619em rgba(0,255,64,0.6),-1.38582em .57403em rgba(0,255,159,0.7),-6.5em 0 rgba(0,255,255,0.4),-1.38582em -0.57403em rgba(0,159,255,0.3),-4.59619em -4.59619em rgba(0,64,255,0.5),-0.57403em -1.38582em rgba(32,0,255,0.3),0 -6.5em rgba(127,0,255,0.9),.57403em -1.38582em rgba(223,0,255,0.9),4.59619em -4.59619em rgba(255,0,191,0.1),1.38582em -0.57403em rgba(255,0,96,0.1);border-radius:.48em;width:.5em;height:.5em;transform:scale(1.5) rotate(318deg) translate(1em)} 50%{box-shadow:1.5em 0 rgba(255,0,0,0.3),6.00522em 2.48744em rgba(255,96,0,0.3),1.06066em 1.06066em rgba(255,191,0,0.3),2.48744em 6.00522em rgba(223,255,0,0.7),0 1.5em rgba(128,255,0,0.2),-2.48744em 6.00522em rgba(32,255,0,0.7),-1.06066em 1.06066em rgba(0,255,64,0.3),-6.00522em 2.48744em rgba(0,255,159,0.3),-1.5em 0 rgba(0,255,255,0.9),-6.00522em -2.48744em rgba(0,159,255,0.7),-1.06066em -1.06066em rgba(0,64,255,0.8),-2.48744em -6.00522em rgba(32,0,255,0.1),0 -1.5em rgba(127,0,255,0.7),2.48744em -6.00522em rgba(223,0,255,0.8),1.06066em -1.06066em rgba(255,0,191,0.1),6.00522em -2.48744em rgba(255,0,96,0.8);border-radius:.78em;width:-0.5em;height:-0.5em;transform:scale(1.5) rotate(93deg) translate(1em)} 56.25%{box-shadow:6.5em 0 rgba(255,0,0,0.8),1.38582em .57403em rgba(255,96,0,0),4.59619em 4.59619em rgba(255,191,0,0.3),.57403em 1.38582em rgba(223,255,0,0.6),0 6.5em rgba(128,255,0,0),-0.57403em 1.38582em rgba(32,255,0,0.6),-4.59619em 4.59619em rgba(0,255,64,0.5),-1.38582em .57403em rgba(0,255,159,0),-6.5em 0 rgba(0,255,255,0.9),-1.38582em -0.57403em rgba(0,159,255,0.7),-4.59619em -4.59619em rgba(0,64,255,0.7),-0.57403em -1.38582em rgba(32,0,255,0.4),0 -6.5em rgba(127,0,255,0.8),.57403em -1.38582em rgba(223,0,255,0.3),4.59619em -4.59619em rgba(255,0,191,0.8),1.38582em -0.57403em rgba(255,0,96,0.6);border-radius:.08em;width:1.5em;height:1.5em;transform:scale(1.5) rotate(190deg) translate(4em)} 62.5%{box-shadow:1.5em 0 rgba(255,0,0,0.6),6.00522em 2.48744em rgba(255,96,0,0.5),1.06066em 1.06066em rgba(255,191,0,0.5),2.48744em 6.00522em rgba(223,255,0,0.6),0 1.5em rgba(128,255,0,0.3),-2.48744em 6.00522em rgba(32,255,0,0.8),-1.06066em 1.06066em rgba(0,255,64,0.6),-6.00522em 2.48744em rgba(0,255,159,0.5),-1.5em 0 rgba(0,255,255,0.8),-6.00522em -2.48744em rgba(0,159,255,0.4),-1.06066em -1.06066em rgba(0,64,255,0),-2.48744em -6.00522em rgba(32,0,255,0.9),0 -1.5em rgba(127,0,255,0.7),2.48744em -6.00522em rgba(223,0,255,0.6),1.06066em -1.06066em rgba(255,0,191,0.1),6.00522em -2.48744em rgba(255,0,96,0.6);border-radius:.01em;width:.5em;height:.5em;transform:scale(1.5) rotate(12deg) translate(4em)} 68.75%{box-shadow:6.5em 0 rgba(255,0,0,0.6),1.38582em .57403em rgba(255,96,0,0.1),4.59619em 4.59619em rgba(255,191,0,0.5),.57403em 1.38582em rgba(223,255,0,0.6),0 6.5em rgba(128,255,0,0.2),-0.57403em 1.38582em rgba(32,255,0,0.5),-4.59619em 4.59619em rgba(0,255,64,0.9),-1.38582em .57403em rgba(0,255,159,0.2),-6.5em 0 rgba(0,255,255,0.8),-1.38582em -0.57403em rgba(0,159,255,0.3),-4.59619em -4.59619em rgba(0,64,255,0.3),-0.57403em -1.38582em rgba(32,0,255,0.7),0 -6.5em rgba(127,0,255,0.6),.57403em -1.38582em rgba(223,0,255,0.7),4.59619em -4.59619em rgba(255,0,191,0.6),1.38582em -0.57403em rgba(255,0,96,0.4);border-radius:.17em;width:2.5em;height:2.5em;transform:scale(2.5) rotate(138deg) translate(0)} 75%{box-shadow:1.5em 0 rgba(255,0,0,0.3),6.00522em 2.48744em rgba(255,96,0,0.9),1.06066em 1.06066em rgba(255,191,0,0.2),2.48744em 6.00522em rgba(223,255,0,0.4),0 1.5em rgba(128,255,0,0.2),-2.48744em 6.00522em rgba(32,255,0,0.8),-1.06066em 1.06066em rgba(0,255,64,0.5),-6.00522em 2.48744em rgba(0,255,159,0),-1.5em 0 rgba(0,255,255,0.4),-6.00522em -2.48744em rgba(0,159,255,0.3),-1.06066em -1.06066em rgba(0,64,255,0.3),-2.48744em -6.00522em rgba(32,0,255,0.2),0 -1.5em rgba(127,0,255,0.8),2.48744em -6.00522em rgba(223,0,255,0.3),1.06066em -1.06066em rgba(255,0,191,0.5),6.00522em -2.48744em rgba(255,0,96,0.2);border-radius:.11em;width:.5em;height:.5em;transform:scale(2.5) rotate(290deg) translate(4em)} 81.25%{box-shadow:6.5em 0 rgba(255,0,0,0.5),1.38582em .57403em rgba(255,96,0,0.6),4.59619em 4.59619em rgba(255,191,0,0.3),.57403em 1.38582em rgba(223,255,0,0.4),0 6.5em rgba(128,255,0,0),-0.57403em 1.38582em rgba(32,255,0,0.7),-4.59619em 4.59619em rgba(0,255,64,0.2),-1.38582em .57403em rgba(0,255,159,0.2),-6.5em 0 rgba(0,255,255,0),-1.38582em -0.57403em rgba(0,159,255,0.8),-4.59619em -4.59619em rgba(0,64,255,0.9),-0.57403em -1.38582em rgba(32,0,255,0.3),0 -6.5em rgba(127,0,255,0.9),.57403em -1.38582em rgba(223,0,255,0.2),4.59619em -4.59619em rgba(255,0,191,0.3),1.38582em -0.57403em rgba(255,0,96,0.2);border-radius:.29em;width:3.5em;height:3.5em;transform:scale(1.5) rotate(206deg) translate(2em)} 87.5%{box-shadow:1.5em 0 rgba(255,0,0,0.5),6.00522em 2.48744em rgba(255,96,0,0.6),1.06066em 1.06066em rgba(255,191,0,0),2.48744em 6.00522em rgba(223,255,0,0),0 1.5em rgba(128,255,0,0.9),-2.48744em 6.00522em rgba(32,255,0,0),-1.06066em 1.06066em rgba(0,255,64,0),-6.00522em 2.48744em rgba(0,255,159,0.6),-1.5em 0 rgba(0,255,255,0),-6.00522em -2.48744em rgba(0,159,255,0.8),-1.06066em -1.06066em rgba(0,64,255,0.1),-2.48744em -6.00522em rgba(32,0,255,0.8),0 -1.5em rgba(127,0,255,0.1),2.48744em -6.00522em rgba(223,0,255,0.7),1.06066em -1.06066em rgba(255,0,191,0),6.00522em -2.48744em rgba(255,0,96,0.3);border-radius:.22em;width:.5em;height:.5em;transform:scale(1.5) rotate(229deg) translate(5em)} 93.75%{box-shadow:6.5em 0 rgba(255,0,0,0.7),1.38582em .57403em rgba(255,96,0,0.3),4.59619em 4.59619em rgba(255,191,0,0.5),.57403em 1.38582em rgba(223,255,0,0.8),0 6.5em rgba(128,255,0,0),-0.57403em 1.38582em rgba(32,255,0,0.4),-4.59619em 4.59619em rgba(0,255,64,0.6),-1.38582em .57403em rgba(0,255,159,0.2),-6.5em 0 rgba(0,255,255,0.5),-1.38582em -0.57403em rgba(0,159,255,0.9),-4.59619em -4.59619em rgba(0,64,255,0.7),-0.57403em -1.38582em rgba(32,0,255,0.8),0 -6.5em rgba(127,0,255,0.1),.57403em -1.38582em rgba(223,0,255,0),4.59619em -4.59619em rgba(255,0,191,0.4),1.38582em -0.57403em rgba(255,0,96,0.5);border-radius:.11em;width:.5em;height:.5em;transform:scale(2.5) rotate(85deg) translate(3em)} 100%{box-shadow:1.5em 0 rgba(255,0,0,0.2),6.00522em 2.48744em rgba(255,96,0,0.5),1.06066em 1.06066em rgba(255,191,0,0.4),2.48744em 6.00522em rgba(223,255,0,0.7),0 1.5em rgba(128,255,0,0.9),-2.48744em 6.00522em rgba(32,255,0,0.6),-1.06066em 1.06066em rgba(0,255,64,0.3),-6.00522em 2.48744em rgba(0,255,159,0.7),-1.5em 0 rgba(0,255,255,0.7),-6.00522em -2.48744em rgba(0,159,255,0.1),-1.06066em -1.06066em rgba(0,64,255,0.6),-2.48744em -6.00522em rgba(32,0,255,0.4),0 -1.5em rgba(127,0,255,0.6),2.48744em -6.00522em rgba(223,0,255,0.2),1.06066em -1.06066em rgba(255,0,191,0.7),6.00522em -2.48744em rgba(255,0,96,0.8);border-radius:.44em;width:.5em;height:.5em;transform:scale(2.5) rotate(257deg) translate(4em)} 0%{transform:scale(1.5) rotate(0) translate(0)} 100%{transform:scale(1.5) rotate(360) translate(0)}}@keyframes animation--background{0%{background-position:-362em} 100%{background-position:54em 848em}}
</style>
<meta charset="utf-8">
<link href='//fonts.googleapis.com/css?family=Ubuntu&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="">
<script src="leaverou.github.io/prefixfree/prefixfree.js"></script>
<title>Установка IC-CMS</title>

</head>
<body>
<center>
<h1>Установка IC-CMS</h1>
</center>
<h1 id="logo"><a href="http://ic.cms.com/">Logo</a></h1>
<?php } ?>
<?php if(empty($step) && !$cli){ ?>
<!-- Первая страничка -->
 	<div class="row ">
		<div class="col-lg-6">
     	<p>Добро пожаловать. Прежде чем мы начнём, потребуется информация о базе данных. 
     	Вот что вы должны знать до начала процедуры установки.</p>
     	<ol>
     		<li>Имя базы данных</li>
     		<li>Имя пользователя базы данных</li>
     		<li>Пароль к базе данных</li>
     		<li>Адрес сервера базы данных</li>
     	</ol>
     	<p>
	     	<strong>Если по какой-то причине автоматическое создание файла не удалось, не волнуйтесь. 
	     	Всё это предназначено лишь для заполнения файла настроек. Вы можете просто открыть 
	     	<code>config/config.php.example</code>
	     	 в текстовом редакторе, внести вашу информацию и сохранить его под именем <code>config/config.php</code>.
	     	 </strong>
     	 </p>
     	<p>Скорее всего, эти данные были предоставлены вашим хостинг-провайдером. Если у вас нет этой информации, свяжитесь с их службой поддержки или с вашим знакомым програмистом. А если есть…</p>
     
     	<p class="step"><a href="?step=1" class="button button-large">Начинаем установку!</a></p>
        </div>
        <div class="col-lg-6">
        	Если Вы не разбираетесь в этих словах, то просто нажмите кнопку ниже.
        	Ситема будет установлена со стандартными параметрами для Denwer, XAMMP ,OpenServer. <?php echo $eol ?>
        	Имя для входа: <code>admin@site.com</code> , пароль: <code>123</code> <?php echo $eol ?>
        	<a href="?step=install" class="simple-inslaltion">Просто установить и все</a>
        </div>
  </div>
<!-- Конец первой страничкки установки -->
<?php } ?>
<?php if($step == 1){ ?>
<!-- ШАГ 1 -->
	<form method="post" action="?step=2">
		<p>Введите здесь информацию о подключении к базе данных. Если вы в ней не уверены, свяжитесь с хостинг-провайдером.</p>
		<table class="form-table">
			<tbody><tr>
				<th scope="row"><label for="dbname">Имя базы данных</label></th>
				<td><input name="dbname" id="dbname" type="text" size="25" value="ic_cms"></td>
				<td>Имя базы данных, в которую вы хотите установить IC-CMS.</td>
			</tr>
			<tr>
				<th scope="row"><label for="user">Имя пользователя</label></th>
				<td><input name="user" id="user" type="text" size="25" value="user"></td>
				<td>Имя пользователя MySQL</td>
			</tr>
			<tr>
				<th scope="row"><label for="pass">Пароль</label></th>
				<td><input name="pass" id="pass" type="text" size="25" value="pass"></td>
				<td>…и пароль пользователя MySQL.</td>
			</tr>
			<tr>
				<th scope="row"><label for="host">Сервер базы данных</label></th>
				<td><input name="host" id="host" type="text" size="25" value="localhost"></td>
				<td>Если вы устанавливате IC-CMS на простой хостинг  или на локальный компьютер, сервером будет <code>localhost</code>. Если <code>localhost</code> не работает, нужно узнать правильный адрес в службе поддержки хостинг-провайдера.</td>
			</tr>
		</tbody></table>
			<p class="step"><input name="submit" type="submit" value="Соединяемся с бд!" class="button button-large"></p>
	</form>
<!-- / ШАГ 1 -->
<?php } ?>
<?php if($step == 2){ ?>
<!-- ШАГ 2 -->
Проверка соединения  с базой данных.
<?php 
	$mysqli = @new mysqli($dbuser,$dbhost,$dbpass);
	if ($mysqli->connect_error) {
		echo 'Подключение не удалось. <a href="?step=1">Вернутся и проверить настройки</a>';
	} else {
		$_SESSION['dbhost'] = $_POST['host'];
		$_SESSION['dbuser'] = $_POST['user'];
		$_SESSION['dbpass'] = $_POST['pass'];
		$_SESSION['dbname'] = $_POST['dbname'];
		echo 'Подключение успешно! <a href="?step=3">Вперед</a>';

	}
?>
<!--/ ШАГ 2 -->
<?php } ?>
<?php if($step == 3){ ?>
<!-- ШАГ 3 -->
<h1>Информация о сайте</h1>

<form method="post" action="?step=4">
	<table class="form-table">
		<tbody><tr>
			<th scope="row"><label for="title">Название сайта</label></th>
			<td><input name="title" type="text" id="title" size="25" value=""></td>
		</tr>
		<tr>
			<th scope="row"><label for="email">Почта сайта</label></th>
			<td>
				<input name="email" type="email" id="email" size="25" value="admin">
			</td>
		</tr>
				<tr>
			<th scope="row">
				<label for="admin_password">Пароль</label>
				<p></p>
			</th>
			<td>
				<input name="password" type="password" id="password" size="25" value="">
			</td>
		</tr>


	</tbody></table>
	<p class="step"><input type="submit" name="Submit" value="Установить IC-CMS" class="button button-large"></p>
</form>
<!-- // ШАГ 3 -->
<?php } ?>
<?php if($step == 4){
	// проверка всего
	$_SESSION['title'] = $_POST['title'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['pass'] = $_POST['password'];
	extract($_SESSION);
?>
<!-- ШАГ 4 -->
Вы собираетесь установить Cms с такими параметрами:
<table>
	<tr>
		<th>Параметр</th>
		<th>Значение</th>
	</tr>
	<tr>
		<td>Сервер базы данных</td>
		<td><?php echo $dbhost ?></td>
	</tr>
	<tr>
		<td>Логин MySQL</td>
		<td><?php echo $dbuser ?></td>
	</tr>
	<tr>
		<td>MySQL Password</td>
		<td><?php echo $dbpass ?></td>
	</tr>
	<tr>
		<td>MySQL Databse</td>
		<td><?php echo $dbname ?></td>
	</tr>
	<tr>
		<td>Site title</td>
		<td><?php echo $title ?></td>
	</tr>
	<tr>
		<td>Site email</td>
		<td><?php echo $email ?></td>
	</tr>
	<tr>
		<td>Admin password</td>
		<td><?php echo $pass ?></td>
	</tr>
</table>
<a href="?step=install">Начинаем!</a>
<a href="?step=1">Исправить настройки</a>
<!-- / ШАГ 4 -->
<?php } ?>
<?php 
if($step == 'install') { 

	if ($html) {
		echo "<div class='clubbing'></div>";
	}

echo("Начинаем установку $eol");
extract($install_data);

echo("Подключение к базе данных на $dbuser@$dbhost... $eol");
$mysqli = @new mysqli($dbhost,$dbuser,$dbpass);
if ($mysqli->connect_error) {
	die('Ошибка подключения: ' . $mysqli->connect_error.$eol);
}

if ($mysqli->select_db($dbname)) {
	echo "Подключились к базе данных `$dbname`... ОК! $eol";
} else {
	echo "Пробуем создать базу данных `$dbname`... ";
	if ($mysqli->query("CREATE DATABASE $dbname COLLATE utf8_general_ci")) {
		echo "OK! $eol";
	} else {
		echo "Ошибка! $eol Невозможно создать базу данных $dbname";die($eol);
	}
}

// Установка конфигурационного файла
echo "Создаем файл конфигурации...";
if ($config = file_get_contents('config/config.php.example')) {
	// База данных
	$config = preg_replace('/\$GL_DB_USER.*/i','$GL_DB_USER = "'.$dbuser.'";', $config);
	$config = preg_replace('/\$GL_DB_PASSW.*/i','$GL_DB_PASSW = "'.$dbpass.'";', $config);
	$config = preg_replace('/\$GL_HOST.*/i','$GL_HOST = "'.$dbhost.'";', $config);
	$config = preg_replace('/\$GL_DB_NAME.*/i','$GL_DB_NAME = "'.$dbname.'";', $config);
	
	$config = preg_replace('/\$GL_SITE_NAME.*/i','$GL_SITE_NAME = "'.$title.'";', $config);
	$config = preg_replace('/\$GL_EMAIL.*/i','$GL_EMAIL = "'.$email.'";', $config);
	$config = preg_replace('/\$GL_SITE_DIR.*/i','$GL_SITE_DIR = "'.getcwd().'";', $config);
	
	// $config = str_replace('dbuser', $dbuser, $config);
	// $config = str_replace('dbpass', $dbpass, $config);
	// $config = str_replace('dbhost', $dbhost, $config);
	// $config = str_replace('dbname', $dbname, $config);
	
	//Пользовательские данные
	//$config = str_replace('title', $title, $config);
	//$config = str_replace('email', $email, $config);

	if (file_put_contents("config/config.php",$config)) {
		echo "ОК! $eol";
	}
} else {
	echo "$eol Ошибка! Недоступен для чтения файл с примером настроек ";
}
	

if (!file_exists('lib/php/autoloader.php') or !file_exists('config/config.php')) {
	echo "Ошибка! Недоступен для чтения файл автозагрузки или файл конфигурации";
	echo ($html) ? '</body></html>' : $eol ;
	exit;
} 
echo "Установка начальных данных системы $eol";
require('lib/php/autoloader.php');
require('config/config.php');
	
	error_reporting(0);

	$updater  = new Updater();
	$updater->is_backup = false;
	$updater->e_UpdateFromFile('Updater');
	$updater->e_UpdateFromFileAll();
	echo "Установлена структура базы данных $eol";
	$obj = new Site();
	$id = $obj->Role->e_Insert();
	$obj->Role->set_cell('rl_role', 'role', $id, "administrator");
	$id = $obj->Role->e_Insert();
	$obj->Role->set_cell('rl_role', 'role', $id, "registered");
	$id = $obj->Role->e_Insert();
	$obj->Role->set_cell('rl_role', 'role', $id, "unregistered");
	echo "Установлены роли $eol";
	echo "Регистрация пользователя  $eol";
	$member = array();
	$user = $obj->EmailPasAuth->RegisterUser($email , $pass , $member, true);
	$obj->Contenter->set_cell("usr_user","role",$user,"administrator");
	$obj->User->setRole("administrator");
	$obj->Site->e_InsertPageType("Главная");

} ?>
<?php if ($html): ?>
</body>
</html>
<?php endif ?>