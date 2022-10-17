<?php
require_once 'dompdf/lib/html5lib/Parser.php';
require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';
require_once 'functions.php';

Dompdf\Autoloader::register();

// DECLARATION 

$ps_contact_info = $_POST['first_name'] . ' ' . $_POST['last_name'] . '</b> | ' . $_POST['address'] . ' | ' . $_POST['email_address'];

$transaction_ref_id = @$_POST['transaction_ref_id'];

$date = date("F d, Y", strtotime($_POST['date']));

$ps_contact_info_mt = 5;

use Dompdf\Dompdf;

	$html = '<div style="font-size: 12px; font-family: sans-serif; margin-left: 20px; margin-right: 20px; margin-top: 10px">';
	$html .=  '<div style="width: 100%; float: left;">';
	$html .= 	'<img src="src/img/Orange-Digital-Technologies-LOGO-ONLY.PNG" style="width: 87px; height: 87px; margin-top: 0px;">';
	$html .= 	'<div style="float: right; width: 300px; text-align: right; font-size: 11px !important;">';
	$html .= 		'<p style="line-height: 8px;"><b>Orange Digital Technologies</b></p>';
	$html .= 		'<p style="line-height: 8px;">10620 Treena Street, Suite 230</p>';
	$html .= 		'<p style="line-height: 8px;">San Diego, California, 92131 USA</p>';
	$html .= 		'<p style="line-height: 8px;">info@orangedigitaltechnologies.com</p>';
	$html .= 		'<p style="line-height: 8px;">(619) 695-0098</p>';
	$html .= 	'</div>';
	$html .= '</div>';

	$html .= '<div style="width: 100%; margin-top: 10px">';
	$html .= 	'<h2 style="font-size: 21px; line-height: 16px; margin: 20px 0px 3px 0px;">Service Order</h2>';
	$html .= 	'<hr style="border: none; background-color: black; height: 1px; margin: 5px 0px 0px 0px !important;">';
	$html .= '<div style="font-size: 13px; line-height: 19px; margin-top: '. $ps_contact_info_mt .'px;">';
	$html .= '<b>' . $ps_contact_info;
	$html .= '</div>';
	$html .= '</div>';

	$html .= '<div style="margin-top: 20px; font-size: 15px; letter-spacing: .5px;">';
	$html .=   '<table style="width: 600px">';
	$html .=      '<tr style="display: none;">';
	$html .=         '<td valign="top" style="width: 175px; padding-top: 4px;"><b>Service Order ID:</b> ' . $transaction_ref_id . '</td>';
	$html .=      '</tr>';

	$html .=      '<tr>';
	$html .=         '<td style="width: 175px; padding-top: 4px; font-size: 13px;"><b>Date:</b> ' . $date . '</td>';
	$html .=      '</tr>';
	$html .= '</table>';
	$html .= '</div>';


	$html .= '<div style="width: 100%;margin-top: 30px;">';
	$html .= '<table style="width: 100%; border-collapse: collapse;">';
	$html .=	'<tr>';
	$html .=		'<td style="border: 1px solid white; background-color: #CC5500; font-weight: bold;font-size: 13px; color: white; text-align: center;padding-left: 20px; display: none;">Service Order ID</td>';
	$html .=		'<td style="border: 1px solid white; background-color: #CC5500; font-weight: bold;font-size: 13px; color: white; text-align: center;padding-left: 20px; ">Qty</td>';
	$html .=		'<td style="border: 1px solid white;background-color: #CC5500; font-weight: bold;font-size: 13px; color: white; text-align: center;">Description</td>';
	$html .=		'<td style="border: 1px solid white;background-color: #CC5500; font-weight: bold;font-size: 13px; color: white; text-align: center;">Price</td>';
	$html .=		'<td style="border: 1px solid white;background-color: #CC5500; font-weight: bold;font-size: 13px; color: white; text-align: center;">Discount</td>';
	$html .=		'<td style="border: 1px solid white;background-color: #CC5500; font-weight: bold;font-size: 13px; color: white; text-align: center;">Amount</td>';
	$html .=	'</tr>';

	$total_discount = 0.0;
	$total = 0.0;
	$suffix = 'A';
	// Loop service orders
	for ($i = 0 ; $i < count(@$_POST['quantity']); $i ++) {
		$discount = (float) $_POST['discount'][$i];
		$amount = ((float)$_POST['quantity'][$i] * (float)$_POST['price'][$i]) - $discount;

		$html .=	'<tr>';
		$html .=		'<td style="border: 1px solid #eaeaea;font-size: 13px; text-align: center;color: black; padding: 2px; display: none;">' . $transaction_ref_id . '-' . $suffix++ . '</td>';
		$html .=		'<td style="border: 1px solid #eaeaea;font-size: 13px; text-align: center;color: black; padding: 2px;">' . $_POST['quantity'][$i] . '</td>';
		$html .=		'<td style="border: 1px solid #eaeaea;font-size: 13px; text-align: center;color: black; padding: 2px;">' . $_POST['description'][$i] . '</td>';
		$html .=		'<td style="border: 1px solid #eaeaea;font-size: 13px; text-align: center;color: black; padding: 2px;">$' . moneyFormat($_POST['price'][$i]) . '</td>';
		$html .=		'<td style="border: 1px solid #eaeaea;font-size: 13px; text-align: center;color: black; padding: 2px;">$' . moneyFormat($discount) . '</td>';
		$html .=		'<td style="border: 1px solid #eaeaea;font-size: 13px; text-align: center;color: black; padding: 2px;">$' . moneyFormat($amount) . '</td>';
		$html .=	'</tr>';

		$total += $amount;
		$total_discount += (float) $_POST['discount'][$i];
	}

	$html .= '<tr>';
	$html .= '<td style="display: none;"></td>';
	$html .= '<td></td>';
	$html .= '<td></td>';
	$html .= '<td  style="border: 1px solid #eaeaea;font-size: 15px; text-align: center;color: black; padding: 2px;"><b>Total</b></td>';
	$html .= '<td  style="border: 1px solid #eaeaea;font-size: 15px; text-align: center;color: black; padding: 2px;"><b>$' . moneyFormat($total_discount) . '</b></td>';
	$html .= '<td  style="border: 1px solid #eaeaea;font-size: 15px; text-align: center;color: black; padding: 2px;"><b>$' . moneyFormat($total) . '</b></td>';
	$html .= '</tr>';

	$html .= '</table>';
	$html .= '</div>';

	$margin_top = (20 / count(@$_POST['quantity']));


	// $payment = array("One", "Two", "Three", "Four");


	$html .= '<div style="margin-top: ' . $margin_top . 'px;font-size: 15px;">';
	$html .= '<span style="font-size: 13px;"><b>Payment Option</b></span><br>';
	$html .= '<table style="width: 300px;">';
	

	// Full
	$mark_red = "";
	$checked = '<input type="checkbox" style="padding: 0px;margin-bottom: -4px;margin-top: 4px;">';
	if($_POST['payment_option'] == 'full'){
		$mark_red = ' color: black;';
		$checked = '<input type="checkbox" checked style="margin-top: 4px;">';
	}

	$html .= '<tr>';
	$html .= '<td>' . $checked . '<span style="font-size: 13px;margin-left: 10px; padding: 0px;margin-top: 4px;' . $mark_red . '">Full</span>' . '</td>';
	$html .= '</tr>';

	// Installment
	$mark_red = "";
	$checked = '<input type="checkbox" style="padding: 0px;margin-bottom: -4px;margin-top: 4px;">';
	if($_POST['payment_option'] == 'installment'){
		$mark_red = ' color: black;';
		$checked = '<input type="checkbox" checked style="padding: 0px;margin-bottom: -4px;margin-top: 4px;">';
	}
	// $html .= $checked . '<span style="font-size: 13px;margin-left: 10px; margin-top: -4px;' . $mark_red . '">Installment</span><br>';

	$html .= '<tr>';
	$html .= '<td>' . $checked . '<span style="font-size: 13px;margin-left: 10px; margin-top: -4px;' . $mark_red . '">Installment</span>' . '</td>';
	$html .= '</tr>';


	// Number of Payments

	if($_POST['payment_option'] == 'installment'){
		$html .= '<tr>';
		$html .= '<td><span style="font-size: 13px;margin-left: 0px; margin-top: 7px">Number of Payments</span><span style="font-size: 13px;margin-left: 10px; text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_POST['number_of_payments'] .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>';
		$html .= '</tr>';
	}

	$html .= '</table>';
	$html .= '</div>';

	$surcharge = @$_POST['surcharge'];

	if(@$_POST['surcharge'] == ""){
		$surcharge = 0;
	}

	if($_POST['payment_option'] == 'installment'){
		
		$html .= '<div style="margin-top: 10px;font-size: 15px;">';
		$html .= '<table style="width: 400px">';


		for($index = 0; $index < count($_POST['installment']); $index++){
			

			if($index == 0){
				$html .= '<tr>';
				$html .= '<td style="width: 130px; font-size: 13px;">Payment ' . ucfirst(convertNumber($index+1)) . ':</td>';
				$html .= '<td align="right" style="width: 220px; font-size: 13px;">Upon completion of docusign:</td>';
				$html .= '<td style="width: 50px; font-size: 13px;" align="right">$' . moneyFormat($_POST['installment'][$index]) . '</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="width: 130px; font-size: 13px;"></td>';
				$html .= '<td align="right" style="width: 220px; font-size: 13px;">plus:</td>';
				$html .= '<td style="width: 50px; font-size: 13px;" align="right">$' . moneyFormat($surcharge) . '</td>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<td style="width: 130px; font-size: 13px;"></td>';
				$html .= '<td style="width: 220px; font-size: 13px;" align="right"><b>Payment One Total:</b></td>';
				$html .= '<td style="width: 50px; font-size: 13px;" align="right"><b>$' . moneyFormat(((float)$surcharge + (float)$_POST['installment'][$index])) . '</b></td>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<td style="height: 10px; width: 130px; font-size: 13px;"><br></td>';
				$html .= '<td style="height: 10px; width: 220px; font-size: 13px;" align="right"><br></td>';
				$html .= '<t dstyle="height: 10px; width: 50px; font-size: 13px;" align="right"><br></td>';
				$html .= '</tr>';
			} else {
				$html .= '<tr>';
				$html .= '<td style="width: 130px; font-size: 13px;">Payment ' . ucfirst(convertNumber($index+1)) . ':</td>';
				$html .= '<td align="right" style="width: 220px; font-size: 13px;"></td>';
				$html .= '<td style="width: 50px; font-size: 13px;" align="right">$' . moneyFormat($_POST['installment'][$index]) . '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		$html .= '</div>';

		// The first payment includes the one-time non-refundable installment surcharge of $30.

		$html .= '<div style="margin-top: 0px;font-size: 15px;">';
		$html .= '<span style="font-size: 13px">The first payment includes the one-time non-refundable installment surcharge of $' . $surcharge . '.</span>';
		$html .= '<br>';
		$html .= '<span style="font-size: 13px">The succeeding payments will be auto-charged 30 days after the 1st payment.</span>';
                $html .= '<br><br>';
                $html .= '<span style="font-size: 13px">If payment is not received on the due date, Client will be assessed a late charge equal to $25 on top of the unpaid amount per invoice.</span>';
		$html .= '</div>';

	} else {
		$margin_top = 100;
	}

	$html .= '<div style="margin-top: ' . $margin_top . 'px;border-top: 1px dashed black;font-size: 13px;"><center><b>Payment Authorization</b></center></div>';
	$html .= '<div style="margin-top: 10px;">';
	$html .= '<span style="line-height: 18px;">I hereby authorize Orange Digital Technologies to process the payment for the above services using my credit/debit card information below.</span>';
	$html .= '</div>';
	$html .= '<div style="margin-top: 20px;">';
	$html .= '<table style="width: 75%;">';

	$html .= '<tr>';
	$html .= '<td>Name on the card</td>';
	$html .= '<td style="border-bottom: 1px solid black;">' . @$_POST['name_on_the_card'] . '</td>';
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td style="width: 230px;">Last 4 digits of the credit/debit card &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	$html .= '<td style="border-bottom: 1px solid black;">' . @$_POST['last_4_digit'] . '</td>';
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td>Billing Address</td>';
	$html .= '<td style="border-bottom: 1px solid black;">' . @$_POST['billing_address'] . '</td>';
	$html .= '</tr>';

	$html .= '</table>';
	$html .= '</div>';

	$html .= '<div style="margin-top: 60px;">';
	$html .= '<div style="font-size: 13px;width: 200px; float: left;">';
	$html .= '_____________________________<br>';
	$html .= '<span style="font-size: 13px;">Card Holder\'s Signature</span>';
	$html .= '</div>';

	$html .= '<div style="font-size: 13px;width: 200px; float: left; margin-left: 100px;">';
	$html .= '_____________________________<br>';
	$html .= '<span style="font-size: 13px;">Date Signed</span>';
	$html .= '</div>';

	$html .= '</div>';

	$html .= '</div>';

	// Instantiate Dompdf object
	$dompdf = new Dompdf();

	// Load Formatted pdf output
	$dompdf->loadHtml($html);

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('letter', 'portrait');

	// Render the HTML as PDF
	$dompdf->render();

	// Output the generated PDF to Browser$_POST['first_name'] . ' ' . $_POST['last_name'] 
	$dompdf->stream(ucwords($_POST['first_name']) . ' ' . ucwords($_POST['last_name']) . ' ' . date("m.d.y").' - Service Order.pdf');
?> 
