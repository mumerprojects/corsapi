<?php

namespace App\Http\Controllers;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use DB;
use Response;
use Mail;

class ClientController extends Controller
{
    
    public function codeRequestemail(Request $request) {
            $EMAIL=$request->get("EMAIL");
            $CODE=$request->get("CODE");
      
     $Get_Data = DB::table('newuser')->where('email', '=', $EMAIL)->sharedLock()->get();
     if (!$Get_Data->isEmpty())
      {Mail::send([], [], function ($message) use ($CODE,$EMAIL) {
      $message->to($EMAIL)
         ->from('no-reply@izzydigital.com','CAMA')
        ->subject('CAMA (Forget Password Service)')
        ->setBody("<h2>Forget Password</h2><p> <strong>Code</strong> : $CODE  <br><br>  <strong>Please use that code</strong></p>", 'text/html'); // for HTML
		});
		return "true";
		}
		else {return "false";}
       }
       
    //   public function updatePassword(Request $request )
    //   {
    //     $EMAIL = $request->get("EMAIL");
    //     $PASSWD = $request->get("PASSWORD");
    //   $var = DB::table('newuser')
    //     ->where('email', $EMAIL)
    //     ->update(['password' =>  password_hash($PASSWD,PASSWORD_DEFAULT)]);
    //     return $var;
    //   }
    
	  public function sendMessage($head,$msg,$_datetime,$_country){
		$timezone = "";
		if ($_country == "PAKISTAN")
		{
			$timezone=":00 GMT+5";
		}
		else 
		{
			$timezone=":00 GMT-5";
		}

		$content = array(
			"en" => $msg
            );
            
            $headings = array(
                "en" => $head
                );
		
		$fields = array(
			'app_id' => "180decf1-cd03-4301-92c9-315e5bc09e01",
			'included_segments' => array($_country),
            'data' => array("foo" => "bar"),
            'headings' => $headings,
			'contents' => $content,
			'send_after' => $_datetime.$timezone,
		);
		$fields = json_encode($fields);
    	print("\nJSON sent:\n");
    	print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array('Content-Type: application/json; charset=utf-8',
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
	$dvar = $request->dvar;
	$_country = $request->countr;
	//dd($request);
    $response = $this->sendMessage($title,$message,$dvar,$_country);
	\Session::flash('Notification_Success', 'Broadcast Notification is sent');
	return back();
	}
	
	
	public function sendPostNotification($head,$msg,$email){
		// $head = $request->get("head");
		// $msg = $request->get("message");
		// $playerid = $request->get("playerid");

		$User = DB::table('newuser')->where('email',$email)->first();
		$playerid = $User->player_id ; 
		
		$players = DB::table('newuser')->select('player_id')->where('player_id', '!=', $playerid)->get()->pluck('player_id');
		
	//	dd($check);
		$content = array(
			"en" => $msg
            );
            
            $headings = array(
                "en" => $head
                );
		
		$fields = array(
			'app_id' => "180decf1-cd03-4301-92c9-315e5bc09e01",
			//'included_segments' => array(''),
            'data' => array("foo" => "bar"),
            'headings' => $headings,
			'contents' => $content,
			'include_player_ids' => $players,
		);
		$fields = json_encode($fields);
    	//print("\nJSON sent:\n");
    	//print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array('Content-Type: application/json; charset=utf-8',
		'Authorization: Basic MzFkYzhlMDctYTFjNi00YTJlLWFkMmYtOGUyNTRkMDBjMDE1'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		//return $response;
	}

   public function getAllposts()
   {
	   //SELECT p.poid, u.fullname, p.uid, p.post FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid
	   $Data = Response::json( DB::select( DB::raw("SELECT p.poid, u.fullname, p.uid, p.post FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid ORDER BY p.poid DESC")));
	   return $Data;
   }

   //SELECT p.poid, p.post,u.fullname FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid WHERE p.poid = 1
   public function getPost($ID)
   {
	   
	   $Data = Response::json( DB::select( DB::raw("SELECT p.poid, p.post,u.fullname FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid WHERE p.poid = '$ID'")));
	   return $Data;
   }

   Public function postStatus(Request $request)
   {
	   
	   $EMAIL = $request->get("email");
	   $POST = $request->get("post");
	   $User = DB::table('newuser')->where('email',$EMAIL)->first();
	   $UID = $User->uid ; 
	   $mahead = $User->fullname.' Posted Status';
	   $this->sendPostNotification($mahead,$POST,$EMAIL);
	   DB::table('posts')->insert(['uid' => $UID,'post' => $POST]);
	   return "true";
   }

   public function NewLogin(Request $request,$USER,$PASSWRD)
   {
		$Data = DB::table('newuser')->where('email','=',$USER)->get();
	 if (!$Data->isEmpty())
	{ $tpass = $Data[0]->password;
		if (password_verify($PASSWRD, $tpass)) 
	 {
		 return Response::json('accept'); 
	 }
	 else {return Response::json('reject');}
		
	}
	 
	 else {return 'false'; } 
   }
   
   public function NewSignup(Request $request)
   {
	   $EMAIL= $request->input('email');
	   $PASSWORD = $request->input('password');
	   $USERNAME = $request->input('username');
	   $FULLNAME = $request->input('fullname');
	   $CONTACT = $request->input('contact');
	   $PLAYER = $request->input('player_id');
	   
		$post=DB::table('newuser')->insert([
			 
			 'email' => $EMAIL,
			 'password' => password_hash($PASSWORD, PASSWORD_DEFAULT),
			 'fullname' => $FULLNAME,
			 'username' => $USERNAME,
			 'contact' => $CONTACT,
			 'player_id' => $PLAYER
			 
			 ]);
			 
			 return Response::json('true');
	   
	   
   }
	   
// 		public function codeRequestemail(Request $request) {
// 			   $EMAIL=$request->get("EMAIL");
// 			   $CODE=$request->get("CODE");
		 
// 		$Get_Data = DB::table('newuser')->where('email', '=', $EMAIL)->sharedLock()->get();
// 		if (!$Get_Data->isEmpty())
// 		 {Mail::send([], [], function ($message) use ($CODE,$EMAIL) {
// 		 $message->to($EMAIL)
// 			->from('logisticedensdemo@gmail.com','Client Management')
// 		   ->subject('Client Managment (Forget Password Service)')
// 		   ->setBody("<h2>Forget Password</h2><p> <strong>Code</strong> : $CODE  <br><br>  <strong>Please use that code</strong></p>", 'text/html'); // for HTML
// 		   });
// 		   return "true";
// 		   }
// 		   else {return "false";}
// 		  }
		  
		  public function updatePassword(Request $request )
		  {
		   $EMAIL = $request->get("EMAIL");
		   $PASSWD = $request->get("PASSWORD");
		  $var = DB::table('newuser')
		   ->where('email', $EMAIL)
		   ->update(['password' =>  password_hash($PASSWD,PASSWORD_DEFAULT)]);
		   return $var;
		  }
		  
		  public function NewClient(Request $request)
   {
	   $UREMAIL = $request->input('uremail');
	   $EMAIL= $request->input('email');
	   $CLIENTNAME = $request->input('clientname');
	   $ADDRESS = $request->input('address');
	   $CONTACT = $request->input('contact');
	   $COMPANY = $request->input('company');
	   
	   $User = DB::table('newuser')->where('email',$UREMAIL)->first();
	   $UID = $User->uid ; 
	   
		$post=DB::table('clients')->insert([
			 'uid' =>   $UID,
			 'email' => $EMAIL,
			 'clientname' => $CLIENTNAME,
			 'address' => $ADDRESS,
			 'contact' => $CONTACT,
			 'company' => $COMPANY
			 
			 ]);
			 
			 return Response::json('true');
	   
	   
   }
   public function getAllClients($EMAIL)
   {
	   $User = DB::table('newuser')->where('email',$EMAIL)->first();
	   $UID = $User->uid ; 
	   $Data = DB::table('clients')->where('uid',$UID)->get();
	   return Response::json($Data);
   }
   
   public function getClientInfo($ID)
   {
	   $Data = DB::table('clients')->where('cid',$ID)->get();
	   return Response::json($Data);
   }
   
   public function EditClient(Request $request)
   {
	   $ID = $request->input('cid');
	   $EMAIL= $request->input('email');
	   $CLIENTNAME = $request->input('clientname');
	   $ADDRESS = $request->input('address');
	   $CONTACT = $request->input('contact');
	   $COMPANY = $request->input('company');
	   
		$post=DB::table('clients')
		->where('cid',$ID)
		->update([
			 
			 'email' => $EMAIL,
			 'clientname' => $CLIENTNAME,
			 'address' => $ADDRESS,
			 'contact' => $CONTACT,
			 'company' => $COMPANY
			 
			 ]);
			 
			 return Response::json('true');
   }
   
   public function getAllProject($EMAIL,$ID)
   {
	   $User = DB::table('newuser')->where('email',$EMAIL)->first();
	   $UID = $User->uid ; 
	   $Data = DB::table('project')->where([['uid',$UID],['cid',$ID]])->get();
	   return Response::json($Data);
   }
   
	public function NewProject(Request $request)
   {
	   $EMAIL= $request->input('email');
	   $CID = $request->input('cid');
	   $PRONAME = $request->input('name');
	   $ST = $request->input('startdate');
	   $EN = $request->input('deadline');
	   $DES = $request->input('description');
	   $User = DB::table('newuser')->where('email',$EMAIL)->first();
	   $UID = $User->uid ; 
		$post=DB::table('project')->insert([
			 
			 'uid' => $UID,
			 'cid' => $CID,
			 'projectname' => $PRONAME,
			 'startdate' => $ST,
			 'deadline' => $EN,
			 'description' => $DES,
			 
			 ]);
			 
			 return Response::json('true');
	   
	   
   }
   
   public function getProjectInfo($ID)
   {
	   $Data = DB::table('project')->where('pid',$ID)->first();
	   return Response::json($Data);
   }
   
   public function EditProject(Request $request)
   {
	   $PID = $request->input('pid');
	   $PRONAME = $request->input('projectname');
	   $ST = $request->input('startdate');
	   $EN = $request->input('deadline');
	   $DES = $request->input('description');
	   
		$post=DB::table('project')
		->where('pid',$PID)
		->update([     
			 'projectname' => $PRONAME,
			 'startdate' => $ST,
			 'deadline' => $EN,
			 'description' => $DES,
			 
			 ]);
			 
			 return Response::json('true');
	   
   }
   
   public function NewTask (Request $request)
   {
		$PID= $request->input('pid');
	   $NAME = $request->input('name');
	   $DES = $request->input('description');
	   $DEADLINE = $request->input('deadline');
	   $STATUS = $request->input('status');
	  
		$post=DB::table('tasks')->insert([
			 
			 'pid' => $PID,
			 'name' => $NAME,
			 'description' => $DES,
			 'deadline' => $DEADLINE,
			 'status' => $STATUS,
			 ]);
			 
			 return Response::json('true');
   }
   
   public function getProjectTasks($ID)
   {
	   $Data = DB::table('tasks')->where('pid',$ID)->get();
	   return Response::json($Data);
   }
   
   public function UpdateTask (Request $request)
   {
	   $TID= $request->input('tid');
	   $NAME = $request->input('name');
	   $DES = $request->input('description');
	   $DEADLINE = $request->input('deadline');
	   $STATUS = $request->input('status');
	  
		$post=DB::table('tasks')
		->where('tid',$TID)
		->update([
			 'name' => $NAME,
			 'description' => $DES,
			 'deadline' => $DEADLINE,
			 'status' => $STATUS,
			 ]);
			 
			 return Response::json('true');
   }
   
   public function getTaskInfo($ID)
   {
	   $Data = DB::table('tasks')->where('tid',$ID)->get();
	   return Response::json($Data);
   }
   
   public function UpdateTaskStatus (Request $request)
   {
	   $TID= $request->input('tid');
	   $STATUS = $request->input('status');
	  
		$post=DB::table('tasks')
		->where('tid',$TID)
		->update([
			 'status' => $STATUS,
			 ]);
			 
			 return Response::json('true');
   }
   
   public function getUserMeetings($UREMAIL)
   {
	   $User = DB::table('newuser')->where('email',$UREMAIL)->first();
	   $UID = $User->uid ; 
	   $Data = Response::json( DB::select( DB::raw("SELECT m.mid, c.clientname, p.projectname, m.description, m.meetingtime, m.status FROM meetings as m LEFT JOIN
	   project as p ON m.pid = p.pid LEFT JOIN clients as c ON p.cid = c.cid WHERE m.uid = '$UID'")));
	   return $Data;
   }
   
   public function addMeeting(Request $request )
   {
	   $UREMAIL = $request->input('email');
	   $PID = $request->input('pid');
	   $MEETING = $request->input('meetingtime');
	   $STATUS = $request->input('status');
	   $DESCRIPTION = $request->input('description');
	   
	   $User = DB::table('newuser')->where('email',$UREMAIL)->first();
	   $UID = $User->uid ; 
	   
		$post=DB::table('meetings')->insert([
			 'uid' =>   $UID,
			 'pid' => $PID,
			 'meetingtime' => $MEETING,
			 'description' => $DESCRIPTION,
			 'status' => $STATUS,
			 
			 ]);
			 
			 return Response::json('true');
   }
   //Get User All Projects
   public function getUserProject($EMAIL)
   {
	   $User = DB::table('newuser')->where('email',$EMAIL)->first();
	   $UID = $User->uid ; 
	   $Data = DB::table('project')->where('uid',$UID)->get();
	   return Response::json($Data);
   }
   
   public function getMeetingInfo($ID)
   {
	   $Data = Response::json( DB::select( DB::raw("SELECT m.mid,m.pid, c.clientname, p.projectname, m.description, m.meetingtime, m.status FROM meetings as m LEFT JOIN
	   project as p ON m.pid = p.pid LEFT JOIN clients as c ON p.cid = c.cid WHERE m.mid = '$ID'")));
	   return $Data;
   }
   
   public function UpdateMeeting(Request $request )
   {   $MID = $request->input('mid');
	   $PID = $request->input('pid');
	   $MEETING = $request->input('meetingtime');
	   $DESCRIPTION = $request->input('description');
	   
		$post=DB::table('meetings')
		->where('mid',$MID)
		->update([
			 'pid' => $PID,
			 'meetingtime' => $MEETING,
			 'description' => $DESCRIPTION, 
			 ]);
			 
			 return Response::json('true');
   }
   
   public function UpdateMeetingStatus (Request $request)
   {
	   $MID= $request->input('mid');
	   $STATUS = $request->input('status');
	  
		$post=DB::table('meetings')
		->where('mid',$MID)
		->update([
			 'status' => $STATUS,
			 ]);
			 
			 return Response::json('true');
   }

   Public function postLike(Request $request)
   {
	$EMAIL= $request->input('email');
	$POID = $request->input('post');
	$User = DB::table('newuser')->where('email',$EMAIL)->first();
	$UID = $User->uid ;
	$liker = $User->fullname;
	//SELECT p.post,u.player_id FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid WHERE p.poid = 1
	$Data2 =  DB::select( DB::raw("SELECT p.post,u.player_id FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid WHERE p.poid = '$POID'"));

	$poster = $Data2[0]->player_id;
	$mapost= $Data2[0]->post;
	$Data =  DB::select( DB::raw("SELECT * FROM `likes` WHERE `uid` = '$UID' AND `poid` = '$POID'"));
	if(!$Data)
	{   $post=DB::table('likes')
		->insert([
			 'uid' => $UID,
			 'poid' => $POID,
			 ]);
	$this->sendPostLikeNote($liker,$poster,$mapost);
	return Response::json('true');
	}
	else { return Response::json('false');}

	// $post=DB::table('likes')
	// 	->insert([p
	// 		 'uid' => $UID,
	// 		 'poid' => $POID,
	// 		 ]);

   }

   public function sendPostLikeNote($liker,$poster,$post){
	
	$head = $liker.' Liked Your Post';
	$msg = $liker.' Liked '.$post;

	$content = array(
		"en" => $msg
		);
		
		$headings = array(
			"en" => $head
			);
	
	$fields = array(
		'app_id' => "180decf1-cd03-4301-92c9-315e5bc09e01",
		//'included_segments' => array(''),
		'data' => array("foo" => "bar"),
		'headings' => $headings,
		'contents' => $content,
		'include_player_ids' => array($poster),
	);
	$fields = json_encode($fields);
	//print("\nJSON sent:\n");
	//print($fields);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
	array('Content-Type: application/json; charset=utf-8',
	'Authorization: Basic MzFkYzhlMDctYTFjNi00YTJlLWFkMmYtOGUyNTRkMDBjMDE1'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);
	
	//return $response;
}

public function getPostComments($ID)
{
    $Data = Response::json( DB::select( DB::raw("SELECT c.coid, c.poid, u.fullname, c.comment FROM postcomment as c LEFT JOIN newuser as u ON c.uid = u.uid WHERE c.poid = '$ID' ORDER BY c.coid DESC")));
	return $Data;
}

 Public function postComment(Request $request)
   {
	$EMAIL= $request->input('email');
	$POID = $request->input('post');
	$COMMENT = $request->input('comment');
	$User = DB::table('newuser')->where('email',$EMAIL)->first();
	$UID = $User->uid ;
	$liker = $User->fullname;
	//SELECT p.post,u.player_id FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid WHERE p.poid = 1
	$Data2 =  DB::select( DB::raw("SELECT p.post,u.player_id FROM posts as p LEFT JOIN newuser as u ON p.uid = u.uid WHERE p.poid = '$POID'"));

	$poster = $Data2[0]->player_id;
	$mapost= $Data2[0]->post;
    $post=DB::table('postcomment')
		->insert([
			 'uid' => $UID,
			 'poid' => $POID,
			 'comment' => $COMMENT,
			 ]);
			 
			 $arr1 = explode(' ',trim($liker));
			 //return Response::json($arr1[0].' '.$poster.' '.$mapost);
    $this->sendPostCommentNote($arr1[0],$poster,$mapost);
    return Response::json('true');

   }
   
    public function sendPostCommentNote($liker,$poster,$post){
	
	
	$head = $liker.' Comment on Post';
	//dd($head);
	$msg =  'POST: '.$post;

	$content = array(
		"en" => $msg
		);
		
		$headings = array(
			"en" => $head
			);
	
	$fields = array(
		'app_id' => "180decf1-cd03-4301-92c9-315e5bc09e01",
		//'included_segments' => array(''),
		'data' => array("foo" => "bar"),
		'headings' => $headings,
		'contents' => $content,
		'include_player_ids' => array($poster),
	);
	$fields = json_encode($fields);
	//print("\nJSON sent:\n");
	//print($fields);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
	array('Content-Type: application/json; charset=utf-8',
	'Authorization: Basic MzFkYzhlMDctYTFjNi00YTJlLWFkMmYtOGUyNTRkMDBjMDE1'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);
	
	//return $response;
}

function sendOTP(Request $request)
{
        //$ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, "https://2factor.in/API/V1/91b8988f-a19e-11ea-9fa5-0200cd936042/SMS/+923133042062/6578");
        //$response = curl_exec($ch);
        //curl_close($ch);
$num = $request->input('NUMBER');
$code = $request->input('CODE');    
$sid    = "AC24ec1dccf7922057a431631578a7631f";
$token  = "868aa214edb4efbbf1c2d01673bf4426";
$twilio = new Client($sid, $token);

$message = $twilio->messages
                  ->create($num, // to
                          [
                              'body' => 'OTP  => ' .$code,
                              'from' => '+12078202622'
                          ]
                  );
                  
                  return Response::json('true');
}

 public function codeRequestemailb(Request $request) {
            $EMAIL=$request->get("EMAIL");
            $CODE=$request->get("CODE");
      
    
     $Message = Mail::send([], [], function ($message) use ($CODE,$EMAIL) {
      $message->to($EMAIL)
         ->from('no-reply@izzydigital.com','QJSKILLS')
        ->subject('QJSKILLS (Code Verification)')
        ->setBody("<h2>Verification Code</h2><p> <strong>Code</strong> : $CODE  <br><br>  <strong>Please use that code</strong></p>", 'text/html'); // for HTML
		});
		return Response::json('true');
		
       }

}
