<?php

namespace App\Http\Controllers;

use App\Models\FactoryHoliday;
use Illuminate\Http\Request;

class FactoryHolidayController extends Controller
{
    public function index()
    {
        $holidays = FactoryHoliday::all();
        return view('backend.library.factory_holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('backend.library.factory_holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'holiday_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        // $table->date('holiday_date');
        // $table->boolean('is_default')->default(false); // For Friday or auto-added holidays
        // $table->boolean('is_weekend')->default(false); // For Saturday or Sunday
        // $table->boolean('is_additional')->default(false); // For Additional holidays
        // $table->boolean('is_active')->default(true);
        // $table->string('description')->nullable(); // Optional description

        

        FactoryHoliday::create([
            'holiday_date' => $request->holiday_date,
            'is_default' => false,
            'is_active' => true,
            'is_weekend' => false,
            'is_additional' => false,
            'description' => $request->description,
        ]);

        return redirect()->route('factory_holidays.index')->with('message', 'Holiday added successfully!');
    }

    public function edit($id)
    {
        $holiday = FactoryHoliday::findOrFail($id);
        return view('backend.library.factory_holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        $holiday = FactoryHoliday::findOrFail($id);
        $holiday->update([
            'holiday_date' => $request->holiday_date,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);

        return redirect()->route('factory_holidays.index')->with('message', 'Holiday updated successfully!');
    }

    public function destroy($id)
    {
        FactoryHoliday::findOrFail($id)->delete();
        return redirect()->route('factory_holidays.index')->with('message', 'Holiday deleted successfully!');
    }
}
