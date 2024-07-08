<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\BuyerAssign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerAssignController extends Controller
{
    public function index()
    {
        $buyer_assigns = BuyerAssign::all();
        $buyers = Buyer::all();
        $users = User::all();
        return view('backend.library.buyer_assigns.index', compact('buyer_assigns', 'buyers', 'users'));
    }


    public function create()
    {
        $buyer_assigns = BuyerAssign::all();
        $buyers = Buyer::all()->where('is_active', '0');
        $users = User::all()->where('is_active', '0');
        // dd($buyers, $users);
        return view('backend.library.buyer_assigns.create', compact('buyer_assigns', 'buyers', 'users'));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_id' => 'required',
            'buyer_id' => 'required|array',
        ]);

        $user_id = $request->user_id;
        $buyer_ids = $request->buyer_id;

        DB::transaction(function () use ($user_id, $buyer_ids) {
            foreach ($buyer_ids as $buyer_id) {
                $existingAssignment = BuyerAssign::where('user_id', $user_id)
                    ->where('buyer_id', $buyer_id)
                    ->first();

                if ($existingAssignment) {
                    return redirect()->route('buyer_assigns.index')->withErrors('BuyerAssign already exists!');
                }

                $buyer_assign = new BuyerAssign;
                $buyer_assign->buyer_id = $buyer_id;
                $buyer_assign->user_id = $user_id;
                $buyer_assign->buyer_name = Buyer::findOrFail($buyer_id)->name;
                $buyer_assign->user_name = User::findOrFail($user_id)->name;
                $buyer_assign->assign_date = date('Y-m-d');
                $buyer_assign->assign_by = auth()->user()->name;
                $buyer_assign->status = 0;
                $buyer_assign->save();
            }
        });

        return redirect()->route('buyer_assigns.index')->withMessage('BuyerAssign created successfully!');
    }



    public function show($id)
    {
        $buyer_assign = BuyerAssign::findOrFail($id);
        $buyers = Buyer::all()->where('is_active', '0');
        $users = User::all()->where('is_active', '0');
        return view('backend.library.buyer_assigns.show', compact('buyer_assign', 'buyers'));
    }


    public function edit($id)
    {
        $buyer_assign = BuyerAssign::findOrFail($id);
        $buyers = Buyer::all()->where('is_active', '0');
        $users = User::all()->where('is_active', '0');
        return view('backend.library.buyer_assigns.edit', compact('buyer_assign', 'buyers', 'users'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'buyer_id' => 'required',
        ]);

        // Data update
        $buyer_assign = BuyerAssign::findOrFail($id);
        $buyer_assigns = BuyerAssign::all();
        $user_id = $request->user_id;
        $buyer_id = $request->buyer_id;

        if ($buyer_assign->buyer_id == $request->buyer_id && $buyer_assign->user_id == $request->user_id) {
            return redirect()->route('buyer_assigns.index')->withErrors('No changes made!');
        } else {
            foreach ($buyer_assigns as $buyer_assign) {
                if ($buyer_assign->buyer_id == $request->buyer_id && $buyer_assign->user_id == $request->user_id) {
                    return redirect()->route('buyer_assigns.index')->withErrors('BuyerAssign already exists!');
                }
            }
        }
        $buyer_assign->buyer_id = $buyer_id;
        $buyer_assign->user_id = $user_id;
        $buyer_assign->buyer_name = Buyer::findOrFail($buyer_id)->name;
        $buyer_assign->user_name = User::findOrFail($user_id)->name;
        $buyer_assign->assign_date = date('Y-m-d');
        $buyer_assign->assign_by = auth()->user()->name;
        $buyer_assign->save();

        // Redirect
        return redirect()->route('buyer_assigns.index')->withMessage('BuyerAssign updated successfully!');
    }


    public function destroy($id)
    {
        if (auth()->user()->role_id == 1) {
            BuyerAssign::findOrFail($id)->delete();
            return redirect()->route('buyer_assigns.index')->withMessage('Successfully Deleted!');
        } else {
            return redirect()->route('buyer_assigns.index')->withErrors('You are not authorized to  do this!');
        }  
    }
    
}
