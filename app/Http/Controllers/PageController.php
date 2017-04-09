<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getAdminList()
    {
        return view('pages.admin_list', [
            'pages' => Page::all()
        ]);
    }

    public function edit(Request $request, Page $page)
    {
        if($request->method() == 'POST')
        {
            $page->fill($request->input());
            $page->save();
            $request->session()->flash('success', 'Article mis à jour.');
            return redirect()->route('lists_pages');
        }
        return view('pages.admin_edit', compact('page'));
    }
    public function create(Request $request)
    {
        if($request->method() == 'POST')
        {
            $page = new Page($request->input());
            if($page->save()) {
                $request->session()->flash('success', 'Article crée.');
                return redirect()->route('lists_pages');
            }
        }
        return view('pages.admin_create');
    }

    public function tooglePublication(Page $page)
    {
        $page->published = !$page->published;
        $page->save();

        session()->flash('success', "Status de l'article mis à jour.");
        return redirect()->route('lists_pages');
    }

    public function setHomepage(Page $page)
    {
        Page::where('home', true)->update(['home' => false]);
        $page->home = true;
        $page->save();

        session()->flash('success', "Article mis en page d'accueil.");
        return redirect()->route('lists_pages');
    }


    public function view(Page $page)
    {
        return view('pages.view', compact('page'));
    }

    public function uploadFile(Request $request)
    {
        $return = null;
        if($request->file('file'))
        {
            $path = $request->file->storeAs('images', 'filename.jpg');
            $return = ['downloadUrl' => $path];
        }

        return response()->json($return);
    }

}
