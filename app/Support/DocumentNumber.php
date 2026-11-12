<?php

namespace App\Support;

use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;

class DocumentNumber
{
    public static function generatePurchaseNumber(): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = 'PB-' . $today . '-';
        
        $lastPurchase = Purchase::where('purchase_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPurchase) return $prefix . '0001';

        $lastSeq = (int) substr($lastPurchase->purchase_number, -4);
        return $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    }

    public static function generateInvoiceNumber(): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = 'INV-' . $today . '-';

        $lastSale = Sale::where('invoice_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastSale) return $prefix . '0001';

        $lastSeq = (int) substr($lastSale->invoice_number, -4);
        return $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    }
}