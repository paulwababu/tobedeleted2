<?php

namespace App\Http\Controllers;

use App\Models\RentalPayment;
use App\Models\Unit;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    // rental payments
    public function manageRentalPaymentsPage()
    {
        return view('owner.rental-payments');
    }

    // load rental payments
    public function loadOwnerRentalPayments(Request $request)
    {
        $unit_ids = $request->user()->units()->get()->pluck('id');
        return RentalPayment::with(['unit'])->whereIn('unit_id', $unit_ids)->filter($request)->paginate(50);
    }
}
