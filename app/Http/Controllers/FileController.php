<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileStorage;
use App\Models\File;
use Illuminate\Support\Facades\Session;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class FileController extends Controller
{
    public function upload(FileStorage $request)
    {
        $f = $request->file('file');

        // Let's optimise the image:
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($f->getRealPath());
        clearstatcache();

        $file = new File($request->file());
        $file->name = $f->getClientOriginalName();
        $file->mime = $f->getMimeType();
        $file->size = filesize($f->getRealPath());
        $file->data = base64_encode(file_get_contents($f->getRealPath()));
        $file->save();

        if ($request->isJson()) {
            return response()->json([
                'url' => url()->route('view_file', ['file' => $file])
            ]);
        } else {
            Session::flash('success', "Fichier ajouté !");
            return redirect()->route('admin_list_files');
        }

    }

    public function adminApiList()
    {
        $files = File::all(['uuid', 'name', 'size', 'mime', 'created_at']);
         return response()->json($files->toArray());
    }

    public function adminList()
    {
        return view('admin.file.list', ['files' => File::all(['uuid', 'name', 'size', 'mime', 'created_at'])]);
    }

    public function delete(File $file)
    {
        if ($file->delete())
            Session::flash('success', "Fichier supprimé");
        else
            Session::flash('error', "Le fichier n'a pus être supprimé !");
        return redirect()->route('admin_list_files');
    }
    public function display(File $file)
    {
        $data = base64_decode($file->data);
        return response()->make($data,200, [
           'Content-Type' => (new \finfo(FILEINFO_MIME))->buffer($data)
        ]);
    }
}
