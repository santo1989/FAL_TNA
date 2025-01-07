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
        // Fetch holidays for the current year
        $currentYear = date('Y');
        $factory_holidays = FactoryHoliday::whereYear('holiday_date', $currentYear)->get();

        // Pass holidays to the create view
        return view('backend.OMS.factory_holidays.create', compact('factory_holidays'));
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
       $all_data = $request->merge(['holiday_details' => $holidayDetails]);

        // dd($all_data);

        // Validate the input
        $request->validate([
            'production_month' => 'required|date_format:Y-m',
            'holiday_details' => 'required|array',
            'holiday_details.*.holiday_date' => 'required|date',
            'holiday_details.*.description' => 'nullable|string|max:255',
            'holiday_details.*.is_weekend' => 'required|boolean',
        ]);

        // dd($holidayDetails);

        //filter out if is_weekend value is 0 or 1 then skip that record from $holidayDetails array but if is_weekend value is true or false then include that record in $holidayDetails array
        $holidayDetails = array_filter($holidayDetails, function($holiday) {
            return $holiday['is_weekend'] === true || $holiday['is_weekend'] === false;
        });
        // dd($holidayDetails);
        // Loop through each holiday entry in the decoded holiday_details array
        foreach ($holidayDetails as $holiday) {
            // Create a new FactoryHoliday record for each holiday

            FactoryHoliday::create([
                'holiday_date' => $holiday['holiday_date'],
                'description' => $holiday['description'],
                'is_weekend' => $holiday['is_weekend'],
                'is_default' => $holiday['is_weekend'], // Mark as default if it's a weekend
                'is_additional' => !$holiday['is_weekend'], // Mark as additional if not weekend
                'is_active' => true, // Set to active by default
            ]);
        }

        return redirect()->route('factory_holidays.index')->withMessage( 'Holidays saved successfully.');
    }

    public function edit($id)
    {
        $holiday = FactoryHoliday::findOrFail($id);
        return view('backend.OMS.factory_holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $holiday = FactoryHoliday::findOrFail($id);
        $holiday->update([
            'holiday_date' => $request->holiday_date, 
            'description' => $request->description,
        ]);

        return redirect()->route('factory_holidays.index')->withMessage( 'Holiday updated successfully!');
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

    public function getHolidays($year, $month)
    {
        // Fetch holidays for the specified year and month
        $holidays = FactoryHoliday::whereYear('holiday_date', $year)
            ->whereMonth('holiday_date', $month)
            ->get(['holiday_date', 'description', 'is_weekend']);

        // Return holidays as JSON response
        return response()->json($holidays);
    }



    public function bulkDelete(Request $request)
    {
        // Decode the `ids` field into an array
        $ids = json_decode($request->input('ids', '[]'), true);

        // Ensure `ids` is an array and contains only numeric values
        if (!is_array($ids) || empty($ids)) {
            FactoryHoliday::truncate(); // Delete all records if no valid IDs provided
        } else {
            // Filter valid numeric IDs
            $validIds = array_filter($ids, fn($id) => is_numeric($id));

            if (!empty($validIds)) {
                FactoryHoliday::whereIn('id', $validIds)->delete();
            } else {
                return redirect()->route('factory_holidays.index')->with('error', 'Invalid IDs provided.');
            }
        }

        return redirect()->route('factory_holidays.index')->with('success', 'Holidays deleted successfully.');
    }



}
