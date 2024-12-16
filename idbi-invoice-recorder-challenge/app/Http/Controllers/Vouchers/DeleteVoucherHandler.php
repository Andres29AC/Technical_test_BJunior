<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteVoucherHandler extends Controller
{
    public function __invoke(Request $request,$id)
    {
        $user = $request->user();
        $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first();
        if(!$voucher){
            return response()->json([
                'error' => 'Voucher not found or you do not have permission to delete it'
            ], Response::HTTP_NOT_FOUND);
        }
        $voucher->delete();
        return response()->json([
            'message' => 'Voucher deleted successfully'
        ], Response::HTTP_OK);
    }

}
