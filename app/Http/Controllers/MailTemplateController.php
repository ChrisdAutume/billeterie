<?php

namespace App\Http\Controllers;

use App\Models\MailTemplate;
use Illuminate\Http\Request;

class MailTemplateController extends Controller
{
    public function getAdminList()
    {
        return view('mail-template.admin_list', [
            'templates' => MailTemplate::all()
        ]);
    }

    public function edit(Request $request, MailTemplate $template)
    {
        if($request->method() == 'POST')
        {
            $template->fill($request->input());
            $template->save();
            $request->session()->flash('success', 'Article mis à jour.');
            return redirect()->route('lists_mail_templates');
        }
        return view('mail-template.admin_edit', compact('template'));
    }

    public function toogleActivation(MailTemplate $template)
    {
        $template->isActive = !$template->isActive;
        $template->save();

        session()->flash('success', "Status du template mis à jour.");
        return redirect()->route('lists_mail_templates');
    }

    public function view(MailTemplate $template)
    {
        return view('emails.emails', ['subject'=>$template->title, 'content' => markdown($template->content)]);
    }
}
