<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RetailerMeta;
use App\Models\Shop as ModelsShop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RetailerTransferController extends Controller
{
    public function __invoke(Request $request, RetailerMeta $retailerMeta): RedirectResponse
    {
        $request->validate([
            'transfer_to' => 'required',
        ]);

        ModelsShop::where('retailer_id', $retailerMeta->user_id)->update([
            'retailer_id' => $request->transfer_to,
        ]);
        $retailerMeta->isClientTransfered = true;
        $retailerMeta->clientTransferedTo = $request->transfer_to;
        $retailerMeta->save();

        return redirect()->back();
    }
}
