<?php

require 'controllers/Controller.php';

$page = new SurveyEditController;

if(isset($_GET['func'])) {
	$page->{$_GET['func']}();
} else {
	$page->display();
}