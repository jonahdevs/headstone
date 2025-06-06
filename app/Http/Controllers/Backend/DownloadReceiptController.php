<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DownloadReceiptController extends Controller
{
    public function orderDownload(Request $request, Order $order)
    {
        $path = $order->payment->payment_receipt;

        if (!$path || !Storage::exists($path)) {
            return response()->json(['message' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        return Storage::download($path, 'Receipt.pdf');
    }

    public function transactionDownload(Request $request, Transaction $transaction)
    {
        $path = $transaction->payment_receipt;

        if (!$path || !Storage::exists($path)) {
            return response()->json(['message' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        return Storage::download($path, 'Receipt.pdf');
    }
}
