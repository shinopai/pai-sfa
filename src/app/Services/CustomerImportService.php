<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Customer\StoreCustomerRequest;
use Illuminate\Support\Facades\Validator;

class CustomerImportService
{
    public function import(UploadedFile $file): array
    {
        $customers = [];
        $errors = [];

        $rules = [
            'company_name' => ['required', 'string', 'max:100'],
            'contact_name' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'memo' => ['nullable', 'string', 'max:2000'],
        ];

        $handle = fopen($file->getRealPath(), 'r');

        // 1. ヘッダー行の読み取りと空判定
        $header = fgetcsv($handle);

        // 完全な空ファイル、または空行のみの場合
        if ($header === false || empty(array_filter($header))) {
            fclose($handle);

            return [
                'success' => 0,
                'failed' => 1,
                'errors' => ['CSVファイルにデータが含まれていません。'],
            ];
        }

        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            // 2. 配列でない、または空行（改行のみ）の場合はスキップ
            if (! is_array($row) || empty(array_filter($row))) {
                continue;
            }

            $data = [
                'company_name' => $row[0] ?? '',
                'contact_name' => $row[1] ?? '',
                'email' => $row[2] ?: null,
                'phone' => $row[3] ?: null,
                'address' => $row[4] ?: null,
                'industry' => $row[5] ?: null,
                'memo' => $row[6] ?: null,
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "{$line}行目：{$message}";
                }

                continue;
            }

            $customers[] = $data;
        }

        fclose($handle);

        // 3. データ行が1件もなかった場合（ヘッダーのみ等）
        if (empty($customers) && empty($errors)) {
            return [
                'success' => 0,
                'failed' => 1,
                'errors' => ['CSVファイルにデータが含まれていません。'],
            ];
        }

        if (! empty($errors)) {
            return [
                'success' => 0,
                'failed' => count($errors),
                'errors' => $errors,
            ];
        }

        DB::transaction(function () use ($customers) {
            foreach ($customers as $customer) {
                Customer::create([
                    'user_id' => Auth::id(),
                    ...$customer,
                ]);
            }
        });

        return [
            'success' => count($customers),
            'failed' => 0,
            'errors' => [],
        ];
    }
}
