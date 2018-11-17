<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class EtuUTTController extends Controller
{
    /*
     * Login to EtuUTT
     */
    public function login(Request $request=null)
    {
        if($request->getContentType() == 'application/json')
        {
            return response()->json(['error' => 'Wrong API access'], 401);
        }
        return redirect()->to(Config::get('services.etuutt.baseuri.public').'/api/oauth/authorize?client_id=' . Config::get('services.etuutt.client.id') . '&scopes=private_user_account&response_type=code&state=xyz');
    }

    public function callback(Request $request)
    {
        if(!$request->has('authorization_code'))
            abort(401);

        $client = new \GuzzleHttp\Client([
            'base_uri' => Config::get('services.etuutt.baseuri.api'),
            'auth' => [
                Config::get('services.etuutt.client.id'),
                Config::get('services.etuutt.client.secret')
            ]
        ]);

        $params = [
            'grant_type'         => 'authorization_code',
            'authorization_code' => $request->input('authorization_code')
        ];
        try {
            $response = $client->post('/api/oauth/token', ['form_params' => $params]);
        } catch (GuzzleException $e) {
            // An error 400 from the server is usual when the authorization_code
            // has expired. Redirect the user to the OAuth gateway to be sure
            // to regenerate a new authorization_code for him :-)
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 400) {
                return $this->login();
            }
            abort(500);
        }
        $json = json_decode($response->getBody()->getContents(), true);
        $access_token = $json['access_token'];
        $refresh_token = $json['refresh_token'];
        try {
            $response = $client->get('/api/private/user/account?access_token=' . $json['access_token']);
        } catch (GuzzleException $e) {
            abort(500);
        }
        $json = json_decode($response->getBody()->getContents(), true)['data'];

        $user = User::where('mail', $json['email'])->first();

        if($user === null) {
            //Utilisateur inconnu donc création
            $user = new User([
                'firstname' => $json['firstName'],
                'lastname' => $json['lastName'],
                'name' => $json['firstName'].' '.$json['lastName'],
                'mail' => $json['email']
            ]);
            $user->etuutt_access_token = $access_token;
            $user->etuutt_refresh_token = $refresh_token;
            $user->last_login = new Carbon('NOW');
            $user->save();


            //$picture = @file_get_contents('http://local-sig.utt.fr/Pub/trombi/individu/' . $user->student_id . '.jpg');
            //@file_put_contents(public_path() . '/uploads/students-trombi/' . $user->student_id . '.jpg', $picture);

        }else {
            //Utilisateur connu, maj des token
            $user->etuutt_access_token = $access_token;
            $user->etuutt_refresh_token = $refresh_token;
            $user->last_login = new Carbon('NOW');
            $user->save();
        }

        Auth::login($user);
        return redirect()->intended();

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flash('success', "Vous avez été déconnecté !");

        return redirect()->route('home');
    }
}
