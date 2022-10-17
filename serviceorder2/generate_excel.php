<?php

require 'vendor/autoload.php';
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Style\Fill;
use \PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

	$COUNT = 0;
    $EID = 1;
    $EXT = 2;
    $ALIAS = 3;
    $LAST_NAME = 4;
    $FIRST_NAME = 5;
    $FULLNAME = 6;
    $SUPERVISOR = 7;
    $MANAGER = 8;
    $DEPT = 9;
    $DEPT_CODE = 10;
    $DIVISION = 11;
    $ROLE = 12;
    $ACCOUNT = 13;
    $PROD_DATE = 14;
    $STATUS = 15;
    $HIRED_DATE = 16;
    $WAVE = 17;
    $EMAIL = 18;
    $GENDER = 19;
    $BDAY = 20;
    $letter = 'A';
    $number = 1;
    
    $transaction_ref_id = @$_POST['transaction_ref_id'];
   
    $worksheet->getCell('A1')->setValue('First Name'); 
    $worksheet->getCell('B1')->setValue('Last Name'); 
    $worksheet->getCell('C1')->setValue('Address'); 
    $worksheet->getCell('D1')->setValue('Email Address'); 
    $worksheet->getCell('E1')->setValue('Service Order ID'); 
    $worksheet->getCell('F1')->setValue('Date'); 
    $worksheet->getCell('G1')->setValue('LOB'); 
    $worksheet->getCell('H1')->setValue('Quantity'); 
    $worksheet->getCell('I1')->setValue('Description'); 
    $worksheet->getCell('J1')->setValue('Price'); 
    $worksheet->getCell('K1')->setValue('Total'); 
    $worksheet->getStyle('A1:K1')->getFont()->setBold(true);
    //! END HEADER
    
    // TABLE BODY
    $total = 0.0;
	$suffix = 'A';


    $row = 2;
	// Loop service orders
	for ($i = 0 ; $i < count(@$_POST['quantity']); $i ++) {

		$amount = (float)$_POST['quantity'][$i] * (float)$_POST['price'][$i];

        $worksheet->getCell('A'.$row)->setValue($_POST['first_name']); 
        $worksheet->getCell('B'.$row)->setValue($_POST['last_name']); 
        $worksheet->getCell('C'.$row)->setValue($_POST['address']); 
        $worksheet->getCell('D'.$row)->setValue($_POST['email_address']); 
        $worksheet->getCell('E'.$row)->setValue($transaction_ref_id . '-' . $suffix++); 
        $worksheet->getCell('F'.$row)->setValue(date("F d, Y", strtotime($_POST['date']))); 
        $worksheet->getCell('G'.$row)->setValue($_POST['department']);

        $worksheet->getCell('H'.$row)->setValue($_POST['quantity'][$i]); 
        $worksheet->getCell('I'.$row)->setValue($_POST['description'][$i]); 
        $worksheet->getCell('J'.$row)->setValue(moneyFormat($_POST['price'][$i])); 
        $worksheet->getCell('K'.$row)->setValue(moneyFormat($amount)); 

    	$row ++;

    	$total += $amount;
	}

	// TOTAL 
    
    //! END TABLE BODY

    // END TABLE

	// ACKNOWLEDGEMENT


	//! END ACKNOWLEDGEMENT
	
	$writer = IOFactory::createWriter($spreadsheet, "Xlsx");

	$timestamp = ucwords($_POST['first_name']) . ' ' . ucwords($_POST['last_name']) . ' ' . date("m.d.y") .' - Service Order';
	$writer->save(__DIR__."/excel/" . $timestamp  . ".xlsx");
	
	
	header("Location: /excel/". $timestamp  . ".xlsx");

	