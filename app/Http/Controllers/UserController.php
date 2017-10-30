<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users'=>User::all()]);
    }

    public function changeLevel(User $user, $level)
    {
        $user->level = intval($level);
        $user->save();

        return redirect()->back();
    }

    public function loginInDev(User $user,Request $request)
    {
        if (App::environment() !== 'local') {
            abort('401', "You can't be here !");
        }

        Auth::login($user);
        return redirect()->route('home');

    }
}
