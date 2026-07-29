<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    /** @var \App\Models\User $this->admin */
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    /** @var \App\Models\User $this->sales */
    $this->sales = User::factory()->create([
        'role' => 'sales',
    ]);
});

it('ログインユーザーは顧客CSVをエクスポートできる', function () {
    Customer::factory()->count(3)->create();

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.export'));

    $response->assertOk();

    expect($response->headers->get('content-type'))
        ->toContain('text/csv');

    expect($response->headers->get('content-disposition'))
        ->toContain('customers_')
        ->toContain('.csv');
});

it('未ログインユーザーはCSVエクスポートへアクセスできない', function () {
    $response = $this->get(route('customers.export'));

    $response->assertRedirect(route('login'));
});

it('CSVにヘッダー行が出力される', function () {
    Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.export'));

    $content = $response->streamedContent();

    expect($content)->toContain(
        '会社名,担当者名,メールアドレス,電話番号,住所,業種,メモ,登録日'
    );
});

it('CSVに顧客データが出力される', function () {
    $customer = Customer::factory()->create([
        'company_name' => '株式会社Pai',
        'contact_name' => '山田太郎',
        'email' => 'pai@example.com',
        'phone' => '09012345678',
        'address' => '東京都千代田区',
        'industry' => 'IT',
        'memo' => 'テストメモ',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.export'));

    $content = $response->streamedContent();

    expect($content)
        ->toContain($customer->company_name)
        ->toContain($customer->contact_name)
        ->toContain($customer->email)
        ->toContain($customer->phone)
        ->toContain($customer->address)
        ->toContain($customer->industry)
        ->toContain($customer->memo);
});
