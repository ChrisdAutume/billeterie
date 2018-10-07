<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use Redirect;
use Log;
use Illuminate\Http\Request;


class PushController extends Controller
{

    public function index()
    {
        return view('push.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['title' => 'required|string', 'content' => 'required|string']);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        $res = $this->sendMessage($request->input('title'), $request->input('content'));
        return redirect('admin/notifications');
    }

    public function sendMessage($title, $content) {
      $content      = array(
          "en" => $content
      );
      $headings      = array(
          "en" => $title
      );
      $ONE_SIGNAL_APP_ID = env('ONE_SIGNAL_APP_ID');
      $fields = array(
          'app_id' => $ONE_SIGNAL_APP_ID,
          'included_segments' => array(
              'All'
          ),
          'contents' => $content,
          'headings' => $headings,
      );
      
      $fields = json_encode($fields);
      $ONE_SIGNAL_API_KEY = env('ONE_SIGNAL_API_KEY');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json; charset=utf-8',
          'Authorization: Basic '.$ONE_SIGNAL_API_KEY,
      ));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      
      $response = curl_exec($ch);
      curl_close($ch);
      
      return $response;
  }
  

  }