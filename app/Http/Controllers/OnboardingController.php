<?php

namespace App\Http\Controllers;

use App\Models\Onboarding;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    // create
    public function create(Request $request)
    {
        $request->validate([
            'user_type' => ['required', 'numeric', Rule::in([1,2,3])],
            'name' => ['required', 'string', 'max:30'],
            'msisdn' => ['required', 'string', 'max:13', new ValidateMsisdn(true, false, 'Onboarding')],
            'email' => ['required', 'email', 'max:30', 'unique:onboardings'],
            'location' => ['required', 'string', 'max:50'],
            'national_id_no' => ['required', 'string', 'max:10']
        ]);

        Onboarding::create([
            'user_type' => $request->input('user_type'),
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email'),
            'location' => $request->input('location'),
            'national_id_no' => $request->input('national_id_no')
        ]);

        // todo alert admin
        session()->flash('success', "You account registration request has been received and is being reviewed. Thank you for choosing us");
        return back();
    }
}
