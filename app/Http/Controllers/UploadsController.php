<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Http\Requests;

class UploadsController extends Controller
{

    private $path;
    private $folder_exist;
    private $file_name;
    private $file_size;
    private $file_path;
    private $file_mime;

    public function __construct()
    {
        $this->path = base_path();
        $this->folder_exist = $this->path . '/public/upload/';
    }

    public function index()
    {
        $query = DB::table('uploads')
            ->where('user_id', '=', Auth::user()->id)
            ->get();

        return view('uploads/index', ['upload' => $query]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request = null)
    {
        if (!file_exists($this->folder_exist . Auth::user()->id))
        {
            mkdir($this->path . '/public/upload/' . Auth::user()->id, 0777);
            return $this->upload();

        }else{
            return $this->upload();
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        $query = DB::table('uploads')
            ->where('id_upload','=' , $id)
            ->get();

        return view('uploads/edit', ['upload' => $query]);
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function upload()
    {
        $this->file_name = Input::file('file')->getClientOriginalName();
        $this->file_size = Input::file('file')->getSize();
        $this->file_mime = Input::file('file')->getMimeType();

        $query = DB::table('uploads')
            ->where('name', '=', $this->file_name)
            ->get();

        if (count($query) > 0) {
            Session::flash('error', 'Un fichier du même nom existe déjà');
            return redirect()->action('HomeController@index');
        }else{
            $this->file_path = Input::file('file')->move($this->path . '/public/upload/' . Auth::user()->id, $this->file_name);
            $this->file_path = $this->file_path->getRealPath();

            DB::table('uploads')
                ->insert([
                    'name' => $this->file_name,
                    'size' => $this->file_size,
                    'path' => $this->file_path,
                    'mime' => $this->file_mime,
                    'user_id' => Auth::user()->id
                ]);

            Session::flash('message', 'Fichier(s) bien uploadé(s)');
            return redirect()->action('HomeController@index');

        }
    }
}