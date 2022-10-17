<?php
	function IsNullOrEmptyString($question){
	    return (!isset($question) || trim($question)==='');
	}
	function displayMoneySign($value){
		$closing = "";
		if (!IsNullOrEmptyString($value)) {
			$closing .= '<span> $' . $value . ' </span></div>';
		} else {
			$closing .= "</div>";
		}
		return $closing;
	}

	function moneyFormat($number) {
		return number_format( $number , 2, ".", "," );
	}

	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];

		if($submit === "pdf"){
			require_once 'generate_pdf.php';
		} else if($submit === "excel"){
			require 'generate_excel.php';
		}
	}
?> 
