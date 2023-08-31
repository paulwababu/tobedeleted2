<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    // Manage units
    public function managePage(Request $request, Property $property)
    {
        return view('manage-units', compact('property'));
    }

    // load property units
    public function load(Request $request, Property $property)
    {
        return $property->units()->with(['type', 'photos'])->filter($request)->paginate(50);
    }

    // create
    public function create(Request $request, Property $property)
    {
        $request->validate([
            'unit_type_id' => ['required', 'numeric'],
            'label' => ['required', 'string', 'max:10'],
            'deposit' => ['required', 'numeric'],
            'rent' => ['required', 'numeric'],
            'is_water_postpaid' => ['required', 'boolean'],
            'is_electricity_postpaid' => ['required', 'boolean'],
            'features' => ['required', 'string'],
            'status' => ['required', 'boolean']
        ]);

        $unit = $property->units()->where('label', $request->input('label'))->first();
        if ($unit){
            return response()->json([
                'status' => false,
                'message' => 'Unit label already exists for '.$property->name
            ]);
        }

        $property->units()->create([
            'unit_type_id' => $request->input('unit_type_id'),
            'account_no' => generateNumbers(8),
            'label' => $request->input('label'),
            'deposit' => $request->input('deposit'),
            'rent' => $request->input('rent'),
            'is_water_postpaid' => $request->input('is_water_postpaid'),
            'is_electricity_postpaid' => $request->input('is_electricity_postpaid'),
            'features' => $request->input('features'),
            'status' => $request->input('status')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Unit added successfully to '.$property->name
        ]);
    }

    // update
    public function update(Request $request, Property $property, Unit $unit)
    {
        if (!$request->user()->hasRole('admin') && $unit->status){
            return response()->json([
                'status' => false,
                'message' => 'Unit '.$unit->label.' is already occupied. Contact system admin on '.env('ADMIN_MSISDN').' to update its details'
            ]);
        }

        $request->validate([
            'unit_type_id' => ['required', 'numeric'],
            'label' => ['required', 'string', 'max:10'],
            'deposit' => ['required', 'numeric'],
            'rent' => ['required', 'numeric'],
            'is_water_postpaid' => ['required', 'boolean'],
            'is_electricity_postpaid' => ['required', 'boolean'],
            'features' => ['required', 'string'],
            'status' => ['required', 'boolean']
        ]);

        $existing_unit = $property->units()->where('id', '!=', $unit->id)->where('label', $request->input('label'))->first();
        if ($existing_unit){
            return response()->json([
                'status' => false,
                'message' => 'Unit label already exists for '.$property->name
            ]);
        }

        $unit->update([
            'label' => $request->input('label'),
            'deposit' => $request->input('deposit'),
            'rent' => $request->input('rent'),
            'is_water_postpaid' => $request->input('is_water_postpaid'),
            'is_electricity_postpaid' => $request->input('is_electricity_postpaid'),
            'features' => $request->input('features'),
            'status' => $request->input('status')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Unit updated successfully'
        ]);
    }

    // browse page
    public function browsePage()
    {
        return view('browse-units');
    }

    // browse
    public function browse(Request $request)
    {
//        return Unit::with(['property' => function($q) use ($request){
//            $q->where('property.location', '8052 Jett Terrace Suite 523')->with(['county']);
//        }, 'type'])->filter($request)->paginate(21);

        // TODO complete the query
        return Unit::where('status', false)->whereHas('property', function($q) use ($request){
            if ($request->has('location')){
                $q->where('location', 'like', '%'.$request->input('location').'%');
            }
            if ($request->has('type')){
                $q->where('type', $request->input('type'));
            }
            if ($request->has('county_id')){
                $q->where('county_id', $request->input('county_id'));
            }
        })->with(['property' => function($q1){
            $q1->with(['county']);
        }, 'type'])->filter($request)->paginate(21);
    }
}
