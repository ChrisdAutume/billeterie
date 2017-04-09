<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileStorage;
use App\Models\File;

class FileController extends Controller
{
    public function upload(FileStorage $request)
    {
        $f = $request->file('file');
        $file = new File($request->file());
        $file->name = $f->getClientOriginalName();
        $file->mime = $f->getMimeType();
        $file->size = $f->getSize();
        $file->data = base64_encode(file_get_contents($f->getRealPath()));
        $file->save();

        return response()->json([
            'url' => url()->route('view_file', ['file'=>$file])
        ]);


    }

    public function display(File $file)
    {
        $data = base64_decode($file->data);
        return response()->make($data,200, [
           'Content-Type' => (new \finfo(FILEINFO_MIME))->buffer($data)
        ]);
    }
}
