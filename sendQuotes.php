<?php
require_once('mpdf.php');
require_once('functions.process.php');
require_once('functions.php');
include_once('Mail.php');
include_once('Mail/mime.php');

// Configuration
$dbhost = '127.0.0.1';
$port = '3002';
$dbname = 'meteor';

$filepath = '/Users/floyd/htdocs/local.gyst.dev/pdfs/';

setlocale(LC_MONETARY, 'en_US');

// Connect to test database
$m = new Mongo("mongodb://$dbhost:$port");
$db = $m->$dbname;

$outgoingCollection 	= $db->outgoing;

$query = array( 'status' => 'send' );
$outgoing = $outgoingCollection->find($query);
$numOutGoing = $outgoing->count();

if(!$numOutGoing)
{
	
	print 'nothing to process';
	
} else {
	
	foreach ($outgoing as $each) 
	{
	
		$toArray = explode("\n", $each['to']);
	
		$quotePDF = createPDF( $each );
	
		$uploadToCloud = 'no upload';
		//ok if we have a file now lets upload it to the cloud
		if( file_exists($filepath . $quotePDF) )
		{
			$uploadToCloud = uploadToCloud($quotePDF);
		}

		$updateQuote = array();
		$updateQuote['status'] = 'waiting';
		
		if(!is_array($uploadToCloud) || empty($uploadToCloud) )
		{
			
			die('We have an error');
			
		} else {
			
			$data = array();
			$data['filename'] 			= $quotePDF;
			$data['to'] 				= $toArray;
			$data['subject'] 			= $each['subject'];
			$data['approvalCode'] 		= $each['approvalCode'];
			
			//excellent we uploaded the file to the cloud
			//we should also now send an email with the attachment
			$sendEmail = sendQuoteEmail($data);
			
			$updateQuote['file'] = array('url'=>$uploadToCloud['url'], 'filename' => $uploadToCloud['filename'] );
			
		}
		
		$mailSent = false;
		
		if($sendEmail == 'sent')
		{
			$mailSent = true;
		}
	
		//we need to save the data back to the account now and archive the outgoing
		//or delete it not sure which yet...
		
		/*
		 * Array
			(
			    [url] => https://www.filepicker.io/api/file/uNJ2NLpT3aUdpTZlETng
			    [size] => 26693
			    [type] => application/pdf
			    [filename] => quote-SADdjSFiRFY7CENxf.pdf
			)
			
			Array
			(
			    [url] => https://www.filepicker.io/api/file/Ke1RwbTpej3oEiaAPYuw
			    [size] => 27371
			    [type] => application/pdf
			    [filename] => quote-j6wGH5zCNFY7KnYd8.pdf
			)
		 */
		
		//we need to record the quote prf and set the status to waiting
		$quotesCollection 	= $db->quotes;
		
		$quotesCollection->update( array('_id' => $each['item_id']), array('$set' => $updateQuote));
			
		//for now lets set the outgoing item to status : archive
		$outgoingCollection->update( array('_id' => $each['_id']), array('$set' => array('status' => 'archive')));
			
	}

	print 'done';
	
}


?>
