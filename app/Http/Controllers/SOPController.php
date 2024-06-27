<?php

namespace App\Http\Controllers;

use App\Models\SOP;
use Illuminate\Http\Request;

class SOPController extends Controller
{
    public function index()
    {
        $sops = SOP::all();
        return view('backend.library.sops.index', compact('sops'));
    }


    public function create()
    {
        $sops = SOP::all();
        return view('backend.library.sops.create', compact('sops'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'Perticulars' => 'required|min:3|max:191',
            'day'=>'required',
        ]);

        $sops = SOP::all();
        // Data insert
        $sops = new SOP;
        $sops->Perticulars = $request->Perticulars;
        $sops->day = $request->day;

        $sops->save();

        // Redirect
        return redirect()->route('sops.index')->withMessage('SOP and related data  are created successfully!');
    }


    public function show($id)
    {
        $sops = SOP::findOrFail($id);
        return view('backend.library.sops.show', compact('sops'));
    }


    public function edit($id)
    {
        $sops = SOP::findOrFail($id);
        return view('backend.library.sops.edit', compact('sops'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'Perticulars' => 'required|min:3|max:191',
        ]);

        // Data update
        $sops = SOP::findOrFail($id);
        $sops->Perticulars = $request->Perticulars;
        $sops->day = $request->day;
        $sops->save();

        // Redirect
        return redirect()->route('sops.index')->withMessage('SOP and related data  are updated successfully!');
    }


    public function destroy($id)
    {
        $sops = SOP::findOrFail($id); 
        
            $sops->delete(); 

        return redirect()->route('sops.index')->withMessage('SOP and related data  are deleted successfully!');
    } 
    
}
