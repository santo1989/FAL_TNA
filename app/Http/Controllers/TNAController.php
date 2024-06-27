<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\TNA;
use Illuminate\Http\Request;

class TNAController extends Controller
{
     
    public function index()
    {
        $tnas = TNA::all()->where('status', 'active');
        $buyers = Buyer::all()->where('is_active', '0'); 
        return view('backend.library.tnas.index', compact('tnas', 'buyers'));
    }

    public function create()
    {
        $buyers = Buyer::all()->where('is_active', '0'); 
        
        return view('backend.library.tnas.create', compact('buyers'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TNA  $tNA
     * @return \Illuminate\Http\Response
     */
    public function show(TNA $tNA)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TNA  $tNA
     * @return \Illuminate\Http\Response
     */
    public function edit(TNA $tNA)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TNA  $tNA
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TNA $tNA)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TNA  $tNA
     * @return \Illuminate\Http\Response
     */
    public function destroy(TNA $tNA)
    {
        //
    }
}
