<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class GetTotalAmountsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $vouchers = Voucher::where('user_id', $user->id)->get();
        $totalSoles = 0;
        $totalDollars = 0;
        foreach($vouchers as $voucher){
            if($voucher->currency == 'PEN'){
                $totalSoles += $voucher->total_amount;
            }elseif($voucher->currency == 'USD'){
                $totalDollars += $voucher->total_amount;
            }
        }
        return response()->json([
            'total_soles' => $totalSoles,
            'total_dollars' => $totalDollars,
        ]);
    }
}
