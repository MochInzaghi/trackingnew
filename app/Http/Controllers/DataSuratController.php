<?php

namespace App\Http\Controllers;

use App\Models\data_surat;
use Illuminate\Http\Request;

class DataSuratController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
    	if ($request->has('cari')) {
    		$data_surat = \App\Models\data_surat::where('alamat_penerima', 'LIKE', '%'.$request->cari.'%')->get();
    	}else{
    		$data_surat = \App\Models\data_surat::all();
    	}
    	return view('layout.index', [
            'data_surat' => \App\Models\data_surat::latest()->paginate(5)]);
    }

    public function table(Request $request)
    {
    	if ($request->has('cari')) {
    		$data_surat = \App\Models\data_surat::where('alamat_penerima', 'LIKE', '%'.$request->cari.'%')->get();
    	}else{
    		$data_surat = \App\Models\data_surat::all();
    	}
    	return view('layout.table.table_data_surat_keluar', [
            'data_surat' => \App\Models\data_surat::latest()->paginate(5)]);
    }

    public function create(Request $request)
    {
    	return view('layout.form.form_input_data_surat');
    }

    public function store(Request $request)
    {
        $attr = request()->validate([
            'no_agenda' => 'required',
            'tanggal' => 'required',
            'alamat_penerima' => 'required',
            'perihal' => 'required',
            'file' => 'file|mimes:pdf',
            'arsip' => 'required',
        ]);

        $attr = $request->all();

        $file = request()->file('file') ? request()->file('file')->store("files/datafiles") : null;
        
        $attr['file'] = $file;

        //create new post
        \App\Models\data_surat::create($attr);

        session()->flash('success', 'Data Surat berhasil di upload');

        return redirect('admin/data-surat');
    }

    public function edit(data_surat $data_surat)
    {
        return view('layout.form.form_edit_data_surat', compact('data_surat'));
    }

    public function update(data_surat $data_surat)
    {
        //validate the field
        $attr = request()->validate([
            'tanggal' => 'required',
            'alamat_penerima' => 'required',
            'perihal' => 'required',
            'file' => 'file|mimes:pdf',
        ]);

        if (request()->file('file')) {
            \Storage::delete($data_surat->file);
            $file = request()->file('file')->store("files/datafiles");
        } else {
            $file = $data_surat->file;
        }

        $attr['file'] = $file;

        $data_surat->update($attr);

        session()->flash('success', 'Data Surat berhasil diedit');

        return redirect('admin/data-surat');
    }

    public function destroy(data_surat $data_surat)
    {
        \Storage::delete($data_surat->file);
        $data_surat->delete();
        session()->flash('success', 'Data Surat berhasil dihapus');
        return redirect('admin/data-surat');
    }

}
