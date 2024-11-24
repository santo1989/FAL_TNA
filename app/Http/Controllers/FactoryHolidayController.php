<?php

namespace App\Http\Controllers;

use App\Models\FactoryHoliday;
use Illuminate\Http\Request;

class FactoryHolidayController extends Controller
{
    public function index()
    {
        $holidays = FactoryHoliday::all();
        return view('backend.OMS.factory_holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('backend.OMS.factory_holidays.create');
    }

    
    public function store(Request $request)
    {
        // Decode holiday_details JSON string
        $holidayDetails = json_decode($request->holiday_details, true);

        // Ensure the holiday_details array was decoded successfully
        if (is_null($holidayDetails)) {
            return back()->withErrors(['holiday_details' => 'Invalid holiday details format.']);
        }

        // Replace holiday_details in the request with the decoded array for validation
        $request->merge(['holiday_details' => $holidayDetails]);

        // Validate the input
        $request->validate([
            'production_month' => 'required|date_format:Y-m',
            'holiday_details' => 'required|array',
            'holiday_details.*.holiday_date' => 'required|date',
            'holiday_details.*.description' => 'nullable|string|max:255',
            'holiday_details.*.is_weekend' => 'required|boolean',
        ]);

        // Loop through each holiday entry in the decoded holiday_details array
        foreach ($holidayDetails as $holiday) {
            FactoryHoliday::create([
                'holiday_date' => $holiday['holiday_date'],
                'description' => $holiday['description'],
                'is_weekend' => $holiday['is_weekend'],
                'is_default' => $holiday['is_weekend'], // Mark as default if it's a weekend
                'is_additional' => !$holiday['is_weekend'], // Mark as additional if not weekend
                'is_active' => true, // Set to active by default
            ]);
        }

        return redirect()->route('factory_holidays.index')->with('success', 'Holidays saved successfully.');
    }

    public function edit($id)
    {
        $holiday = FactoryHoliday::findOrFail($id);
        return view('backend.OMS.factory_holidays.edit', compact('holiday'));
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

    //show method
    public function show($id)
    {
        $holiday = FactoryHoliday::findOrFail($id);
        return view('backend.OMS.factory_holidays.show', compact('holiday'));
    }

    public function destroy($id)
    {
        FactoryHoliday::findOrFail($id)->delete();
        return redirect()->route('factory_holidays.index')->with('message', 'Holiday deleted successfully!');
    } 

   

    public function calander_views()
    {
        // Fetch holidays for the current year
        $currentYear = date('Y');
        $factory_holidays = FactoryHoliday::whereYear('holiday_date', $currentYear)->get();

        return view('backend.OMS.factory_holidays.calander_views', compact('factory_holidays'));
    }

}
