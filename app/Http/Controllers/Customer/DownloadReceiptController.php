<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DownloadReceiptController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        $path = $order->payment->payment_receipt;

        if (!$path || !Storage::exists($path)) {
            return response()->json(['message' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        return Storage::download($path, 'Receipt.pdf');
    }
}
