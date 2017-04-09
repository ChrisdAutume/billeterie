<?php

namespace App\Http\Controllers;

use App\Models\Item_list;
use App\Models\Liste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListeController extends Controller
{
    public function addItemAction (Request $request)
    {
        Auth::user()->requireLevel(2);
        if($request->isMethod('POST'))
        {
            if($request->has(['liste_id', 'content']))
            {
                $liste = Liste::findOrFail(intval($request->input('liste_id')));
                $inputs = explode(',', $request->input('content'));

                $added = 0;
                foreach ($inputs as $input)
                {

                    $list_item = new Item_list();
                    $list_item->list_id = $liste->id;
                    $list_item->value = trim($input);
                    try {
                        $list_item->save();
                    } catch(\Exception $e)
                    {
                        $added--;
                    }
                    $added++;
                }
                $request->session()->flash('success', $added." ajouté à la liste ".$liste->name);
            }
        }

        return view('admin.liste.add_item', ['lists'=>Liste::all()]);
    }
}
