<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    // manage page
    public function managePage()
    {
        return view('manage-inquiries');
    }

    // load
    public function load(Request $request)
    {
        $user = $request->user();
        $inquiries = Inquiry::filter($request)->with(['property', 'user']);
        if ($user->hasRole('owner') || $user->hasRole('agent')) {
            $inquiries = $inquiries->where('company_id', $request->user()->company_id);
        }
        return $inquiries->paginate(20);
    }

    // respond to inquiry
    public function respond(Request $request, Property $property, Inquiry $inquiry)
    {
        $request->validate([
            'inquiry_response' => ['required', 'string']
        ]);

        $inquiry->update([
            'response' => $request->input('inquiry_response'),
            'response_date' => now()->toDateTimeString(),
            'response_by' => $request->user()->id,
            'status' => true
        ]);

        // TODO send SMS alert to user

        return response()->json([
            'status' => true,
            'message' => 'Inquiry responded to successfully'
        ]);
    }
}
