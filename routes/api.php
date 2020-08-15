<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->post('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware('cors:api')->post('/Client/sendOTP', 'ClientController@sendOTP');
//Route::middleware('cors:api')->post('Client/codeRequestemailb','ClientController@codeRequestemailb');
	Route::post('Client/sendOTP', 'ClientController@sendOTP');
	Route::post('Client/codeRequestemailb','ClientController@codeRequestemailb');


Route::post('Client/sendPostNotification','ClientController@sendPostNotification');
Route::get('Client/getAllposts', 'ClientController@getAllposts');
Route::get('Client/NewLogin/{USER}/{PASSWRD}','ClientController@NewLogin');
Route::post('Client/codeRequestemail','ClientController@codeRequestemail');

Route::post('Client/sendverification','ClientController@sendverification');
Route::post('Client/NewSignup','ClientController@NewSignup');
Route::post('Client/NewProject','ClientController@NewProject');
Route::post('Client/NewClient','ClientController@NewClient');
Route::get('Client/getAllClients/{EMAIL}','ClientController@getAllClients');
Route::get('Client/getClientInfo/{ID}','ClientController@getClientInfo');
Route::post('Client/EditClient','ClientController@EditClient');
Route::get('Client/getAllProject/{EMAIL}/{ID}','ClientController@getAllProject');
Route::get('Client/getProjectInfo/{ID}','ClientController@getProjectInfo');
Route::post('Client/EditProject','ClientController@EditProject');
Route::post('Client/NewTask','ClientController@NewTask');
Route::post('Client/updatePassword','ClientController@updatePassword');
Route::get('Client/getProjectTasks/{ID}','ClientController@getProjectTasks');
//UpdateTask
Route::post('Client/UpdateTask','ClientController@UpdateTask');
//getTaskInfo
Route::get('Client/getTaskInfo/{ID}','ClientController@getTaskInfo');
Route::post('Client/UpdateTaskStatus','ClientController@UpdateTaskStatus');
Route::post('Client/postStatus','ClientController@postStatus');
//addMeeting
Route::get('Client/getUserMeetings/{UREMAIL}','ClientController@getUserMeetings');
Route::post('Client/addMeeting','ClientController@addMeeting');
Route::get('Client/getUserProject/{EMAIL}','ClientController@getUserProject');
Route::get('Client/getMeetingInfo/{ID}','ClientController@getMeetingInfo');
Route::get('Client/getPost/{ID}','ClientController@getPost');
Route::post('Client/UpdateMeeting','ClientController@UpdateMeeting');
Route::post('Client/postLike','ClientController@postLike');
//UpdateMeetingStatus.
Route::post('Client/UpdateMeetingStatus','ClientController@UpdateMeetingStatus');
Route::get('Client/getPostComments/{ID}','ClientController@getPostComments');
Route::post('Client/postComment','ClientController@postComment');