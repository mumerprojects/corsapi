<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    
	  public function sendMessage($head,$msg){
		$content = array(
			"en" => $msg
            );
            
            $headings = array(
                "en" => $head
                );
		
		$fields = array(
			'app_id' => "180decf1-cd03-4301-92c9-315e5bc09e01",
			'included_segments' => array('Active Users'),
            'data' => array("foo" => "bar"),
            'headings' => $headings,
			'contents' => $content
		);
		
		$fields = json_encode($fields);
    	print("\nJSON sent:\n");
    	print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic MzFkYzhlMDctYTFjNi00YTJlLWFkMmYtOGUyNTRkMDBjMDE1'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
	
	 public function store(Request $request)
    {
    $title = $request->title;
    $message = $request->message;
    $response = $this->sendMessage($title,$message);
	\Session::flash('Notification_Success', 'Broadcast Notification is sent');
	return back();
	//$return["allresponses"] = $response;
	//$return = json_encode( $return);
	
	//print("\n\nJSON received:\n");
	//print($return);
	//print("\n");   
    //return ('title is : '.$title.' and msg is : '.$message);

    }
}
