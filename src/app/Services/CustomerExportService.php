<?php

namespace App\Services;

use App\Models\Customer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerExportService
{
    public function export(): StreamedResponse
    {
        $fileName = 'customers_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(
            function () {
                $handle = fopen('php://output', 'w');

                // UTF-8 BOM（Excel対応）
                fwrite($handle, "\xEF\xBB\xBF");

                // ヘッダー
                fputcsv($handle, [
                    '会社名',
                    '担当者名',
                    'メールアドレス',
                    '電話番号',
                    '住所',
                    '業種',
                    'メモ',
                    '登録日',
                ]);

                Customer::query()
                    ->orderBy('id')
                    ->chunk(500, function ($customers) use ($handle) {
                        foreach ($customers as $customer) {
                            fputcsv($handle, [
                                $customer->company_name,
                                $customer->contact_name,
                                $customer->email,
                                $customer->phone,
                                $customer->address,
                                $customer->industry,
                                $customer->memo,
                                optional($customer->created_at)->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }
}
