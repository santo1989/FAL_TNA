<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\MarchentSOP;
use App\Models\SOP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarchentSOPController extends Controller
{
    public function index()
    {
        $marchent_sops = MarchentSOP::all();
        $buyers = Buyer::all()->where('is_active', '0'); 
        $sops = SOP::all();
        return view('backend.library.marchent_sops.index', compact('marchent_sops', 'buyers' , 'sops'));
    }


    public function create()
    {
        $marchent_sops = MarchentSOP::all();
        $buyers = Buyer::all()->where('is_active', '0'); 
        $sops = SOP::all();
        // dd($buyers, $sops);
        return view('backend.library.marchent_sops.create', compact('marchent_sops', 'buyers', 'sops'));
    }


    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'buyer_id' => 'required',
            'sop_id' => 'required|array',
            'day' => 'required|array',
        ]);

        $buyer_id = $request->buyer_id;
        $sop_ids = $request->sop_id;
        $days = $request->day;

        $marchent_sops = MarchentSOP::all();

        // Check if the MarchentSOP already exists
        foreach ($marchent_sops as $marchent_sop) {
            if ($marchent_sop->buyer_id == $buyer_id) {
                return redirect()->route('marchent_sops.index')->withErrors('MarchentSOP already exists!');
            }
        }

        DB::transaction(function () use ($buyer_id, $sop_ids, $days) {
            foreach ($sop_ids as $index => $sop_id) {
                // Fetch the SOP
                $sop = SOP::findOrFail($sop_id);

                // Create a new MarchentSOP instance
                $marchent_sop = new MarchentSOP;
                $marchent_sop->buyer_id = $buyer_id;
                $marchent_sop->buyer_name = Buyer::findOrFail($buyer_id)->name;
                $marchent_sop->sop_id = $sop_id;
                $marchent_sop->Perticulars = $sop->Perticulars;
                $marchent_sop->day = $days[$index]; // Match the day to the corresponding sop_id
                $marchent_sop->assign_date = date('Y-m-d');
                $marchent_sop->assign_by = auth()->user()->name;
                $marchent_sop->save();
            }
        });

        return redirect()->route('marchent_sops.index')->withMessage('MarchentSOP created successfully!');
    }




    public function show($id)
    {
        $marchent_sop = MarchentSOP::findOrFail($id);
        $buyers = Buyer::all()->where('is_active', '0');
        $sops = SOP::all();
        return view('backend.library.marchent_sops.show', compact('marchent_sop', 'buyers'));
    }


    public function edit($id)
    {
        $marchent_sop = MarchentSOP::findOrFail($id);
        $buyers = Buyer::all()->where('is_active', '0');
        $sops = SOP::all();
        return view('backend.library.marchent_sops.edit', compact('marchent_sop', 'buyers', 'sops'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'buyer_id' => 'required',
        ]);

        // Data update
        $marchent_sop = MarchentSOP::findOrFail($id);
        $marchent_sops = MarchentSOP::all();
        $user_id = $request->user_id;
        $buyer_id = $request->buyer_id;

        if ($marchent_sop->buyer_id == $request->buyer_id && $marchent_sop->user_id == $request->user_id) {
            return redirect()->route('marchent_sops.index')->withErrors('No changes made!');
        } else {
            foreach ($marchent_sops as $marchent_sop) {
                if ($marchent_sop->buyer_id == $request->buyer_id && $marchent_sop->user_id == $request->user_id) {
                    return redirect()->route('marchent_sops.index')->withErrors('MarchentSOP already exists!');
                }
            }
        }
        $marchent_sop->buyer_id = $buyer_id;
        $marchent_sop->user_id = $user_id;
        $marchent_sop->buyer_name = Buyer::findOrFail($buyer_id)->name;
        $marchent_sop->user_name = User::findOrFail($user_id)->name;
        $marchent_sop->assign_date = date('Y-m-d');
        $marchent_sop->assign_by = auth()->user()->name;
        $marchent_sop->save();

        // Redirect
        return redirect()->route('marchent_sops.index')->withMessage('MarchentSOP updated successfully!');
    }


    public function destroy($id)
    {
        MarchentSOP::findOrFail($id)->delete();
        return redirect()->route('marchent_sops.index');
    }
    
}
