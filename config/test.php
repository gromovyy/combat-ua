<?php
	$content = file_get_contents("config.php.example");
	$dbname = 'test';
	$config = preg_replace('/\$GL_DB_NAME.*/i','$GL_DB_NAME = "'.$dbname.'";', $content);
	echo getcwd();
?>