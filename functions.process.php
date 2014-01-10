<?php

$today 			= date('F j, Y');
$todaySigned 	= date('m-d-Y');

$header = '';
$header .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
$header .= '<tr>';
$header .= '<td width="50%" valign="bottom"><img src="logo.png" /></td>';
$header .= '<td width="50%" align="right" valign="bottom">';
$header .= $today . '<br/><br/>';
$header .= '<strong>Quote No:<br/>534032</strong>';
$header .= '</td>';
$header .= '</tr>';
$header .= '</table>';

$footer = '';
$footer .= '<div style="text-align:center;">';
$footer .= '14 Miner Street&nbsp;&nbsp;&#124;&nbsp;&nbsp;';
$footer .= 'Greenfield, MA 01301&nbsp;&nbsp;&#124;&nbsp;&nbsp;';
$footer .= '413.475.1810&nbsp;&nbsp;&#124;&nbsp;&nbsp;';
$footer .= 'www.freshjones.com';
$footer .= '</div>';


function createPDF($itemData)
{
	
	global $db;
	
	global $header;
	global $footer;
	global $today;
	global $todaySigned;
	global $filepath;
	
	$itemID 			= $itemData['item_id'];
	$itemCoverLetter 	= $itemData['coverletter'];
	$itemApprovalCode	= $itemData['approvalCode'];
	
	$filename = 'quote-' . $itemID . '.pdf';
	
	$quoteCollection 		= $db->quotes;
	$clientsCollection 		= $db->clients;
	
	$query = array( '_id' => $itemID );
	$quote = $quoteCollection->findOne($query);
	
	$client = $clientsCollection->findOne(array('code' => $quote['company']));
	
	$mpdf=new mPDF('c','Letter',9.5,'Arial',15,15,55,10);
	$mpdf->useOnlyCoreFonts = true;
	$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle("FreshJones - Quote");
	$mpdf->SetAuthor("FreshJones");
	$mpdf->SetDisplayMode('fullpage');
	
	// Define the Header/Footer before writing anything so they appear on the first page
	$mpdf->SetHTMLHeader($header);
	$mpdf->SetHTMLFooter($footer);
	
	$data = array();
	
	$data['quote'] 			= $quote;
	$data['client'] 		= $client;
	$data['coverletter'] 	= $itemCoverLetter;
	$data['approvalCode'] 	= $itemApprovalCode;
	
	$html = outputHTML($data);
	
	$stylesheet = file_get_contents('style.css');
	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($html,2);
	$mpdf->Output($filepath . $filename, 'F');
	
	return $filename;
	
}

function uploadToCloud($filename)
{
	
	global $filepath;
        
	$fullPath = $filepath . $filename;
        
    $apiKey = 'Ai2VSfBxQXG3rKVRsrZksz';
    $url = 'https://www.filepicker.io/api/store/S3?key=' . $apiKey . '&filename=' . urlencode($filename);
               
    $data['fileUpload'] = "@" . $fullPath;
    $data['filename'] = $filename;

    $curl_handle=curl_init();
    curl_setopt($curl_handle, CURLOPT_URL,$url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl_handle, CURLOPT_POST, true);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
    //curl_setopt($curl_handle, CURLOPT_VERBOSE, true);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($curl_handle);
   
    if( $response === false )
    {
    	die(curl_error($curl_handle));
    }
    else
    {
    	$return = json_decode($response, TRUE);
    }
    
    curl_close($curl_handle);
    
    return $return;
	
}


function sendQuoteEmail($data, $override=0)
{
	
	global $filepath;
	
	$override			= 1;
	$overrideEmail		= "billy@freshjones.com";
	
	$approvalURL 		= 'http://localhost:3000/quote/response/' . $data['approvalCode'];
	
	$to 				= $data['to'];
	$from 				= "billy@freshjones.com";
	$sender				= "billy@freshjones.com";
	$replyto 			= "billy@freshjones.com";
	$return 			= "billy@freshjones.com";
	
	$subject 			= "Your Quote";
	
	if($override == 1) { $to = $overrideEmail; }
	
	$host 				= "ssl://smtp.mailgun.org";
	$port 				= "465";
	$username 			= "postmaster@sandbox9592.mailgun.org";
	$password 			= "8jvbly3lrsq8";
	
	$messageText = '';
	$messageText .= "Hello,\n\n";
	$messageText .= "Attached, is your quote\n\n";
	$messageText .= "To approve, request a re-quote, or decline this quote please click the link below:\n\n";
	$messageText .= $approvalURL . "\n\n";
	
	$message = new Mail_mime();

	$message->setTXTBody($messageText);
	$message->addAttachment( $filepath . $data['filename'] );
	
	$body = $message->get();
	
	$extraheaders = array (
			'From' 					=> $from,
			'To' 					=> $to,
			'Sender' 				=> $sender,
			'Reply-To' 				=> $sender,
			'Return-Path'			=> $return,
			'Subject' 				=> $subject
	);
	
	$headers = $message->headers($extraheaders);

	$params = array();
	
	$mail = Mail::factory('mail', $params);
	
	$send = $mail->send($to, $headers, $body);
	
	if (PEAR::isError($send)) {
		
		echo $send->getMessage()."\n";
		exit();
		
	} else {
		
		return 'sent';
		
	}
	
}