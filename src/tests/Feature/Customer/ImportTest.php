<?php

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\UploadedFile;
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

it('ログインユーザーは顧客CSVをインポートできる', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_valid.csv'),
        'customers_valid.csv',
        'text/csv',
        null,
        true
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertRedirect(route('customers.import'));

    expect(Customer::count())->toBe(2);

    $response->assertSessionHas('result.success', 2);
    $response->assertSessionHas('result.failed', 0);

    $this->assertDatabaseHas('customers', [
        'company_name' => '株式会社サンプルA',
        'contact_name' => '山田太郎',
        'email' => 'yamada@example.com',
    ]);

    $this->assertDatabaseHas('customers', [
        'company_name' => '株式会社サンプルB',
        'contact_name' => '佐藤花子',
        'email' => 'sato@example.com',
    ]);
});

it('必須項目エラーのあるCSVはインポートできない', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_required_error.csv'),
        'customers_required_error.csv',
        'text/csv',
        null,
        true
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertRedirect(route('customers.import'));

    expect(Customer::count())->toBe(0);

    $response->assertSessionHas('result.failed', 1);
    $response->assertSessionHas('result.success', 0);

    $result = session('result');

    expect($result['errors'])->not->toBeEmpty();
});

it('メールアドレス形式が不正なCSVはインポートできない', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_invalid_email.csv'),
        'customers_invalid_email.csv',
        'text/csv',
        null,
        true
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertRedirect(route('customers.import'));

    expect(Customer::count())->toBe(0);

    $response->assertSessionHas('result.success', 0);
    $response->assertSessionHas('result.failed', 1);

    $result = session('result');

    expect($result['errors'])->not->toBeEmpty();
});

it('1件でもエラーがある場合は全件ロールバックされる', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_rollback.csv'),
        'customers_rollback.csv',
        'text/csv',
        null,
        true
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertRedirect(route('customers.import'));

    // 正常データが含まれていても登録されない
    expect(Customer::count())->toBe(0);

    $response->assertSessionHas('result.success', 0);
    $response->assertSessionHas('result.failed', 1);

    $result = session('result');

    expect($result['errors'])->not->toBeEmpty();

    $this->assertDatabaseMissing('customers', [
        'company_name' => '株式会社サンプルA',
        'contact_name' => '山田太郎',
        'email' => 'yamada@example.com',
    ]);
});

it('未ログインユーザーは顧客CSVをインポートできない', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_valid.csv'),
        'customers_valid.csv',
        'text/csv',
        null,
        true
    );

    $response = $this->post(route('customers.import.store'), [
        'csv' => $file,
    ]);

    $response->assertRedirect(route('login'));

    expect(Customer::count())->toBe(0);
});

it('CSV以外のファイルはインポートできない', function () {
    // $file = new UploadedFile(
    //     base_path('tests/Fixtures/csv/customers_invalid.pdf'),
    //     'customers_invalid.pdf',
    //     'text/plain',
    //     null,
    //     true
    // );
    $file = UploadedFile::fake()->create('customers_invalid.pdf', 100, 'application/pdf');

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertSessionHasErrors('csv');

    expect(Customer::count())->toBe(0);
});

it('空のCSVファイルはインポートできない', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_empty.csv'),
        'customers_empty.csv',
        'text/csv',
        null,
        true
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertRedirect(route('customers.import'));

    expect(Customer::count())->toBe(0);

    $response->assertSessionHas('result.success', 0);
    $response->assertSessionHas('result.failed', 1);

    $result = session('result');

    expect($result['errors'])->not->toBeEmpty();
});

it('インポート結果が表示される', function () {
    $file = new UploadedFile(
        base_path('tests/Fixtures/csv/customers_empty.csv'),
        'customers_empty.csv',
        'text/csv',
        null,
        true
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.import.store'), [
            'csv' => $file,
        ]);

    $response->assertRedirect(route('customers.import'));

    $response->assertSessionHas('result.success', 0);
    $response->assertSessionHas('result.failed', 1);

    $result = session('result');

    expect($result['errors'])->toContain(
        'CSVファイルにデータが含まれていません。'
    );
});
