<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Customer\CustomerImportRequest;
use App\Services\CustomerImportService;
use App\Services\CustomerExportService;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\RedirectResponse;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keyword = request('keyword');
        $sort = request('sort', 'id');
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $query = Customer::with('user');

        if (Auth::user()->isSales()) {
            $query->where('user_id', Auth::id());
        }

        $query->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($query) use ($keyword) {

                // 会社名
                $query->where('company_name', 'like', "%{$keyword}%")

                    // 担当者名
                    ->orWhere('contact_name', 'like', "%{$keyword}%")

                    // メールアドレス
                    ->orWhere('email', 'like', "%{$keyword}%")

                    // 担当営業
                    ->orWhereHas('user', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
            });
        });

        if (in_array($sort, ['id', 'company_name', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id');
        }

        $customers = $query
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'sales')
            ->orderBy('id')
            ->get();

        return view('customers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        Customer::create([
            'user_id' => Auth::user()->isAdmin()
                ? $request->user_id
                : Auth::id(),
            'company_name' => $request->company_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'industry' => $request->industry,
            'memo' => $request->memo,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', '顧客を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $users = Auth::user()->isAdmin()
            ? User::where('role', 'sales')
            ->orderBy('id')
            ->get()
            : collect();

        return view('customers.edit', compact('customer', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update([
            'user_id' => Auth::user()->role === 'admin'
                ? $request->user_id
                : Auth::id(),
            'company_name' => $request->company_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'industry' => $request->industry,
            'memo' => $request->memo,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', '顧客情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', '顧客を削除しました。');
    }

    /**
     * Export
     */
    public function export(CustomerExportService $service): StreamedResponse
    {
        return $service->export();
    }

    /**
     * Import view
     */
    public function showImport()
    {
        return view('customers.import');
    }

    /**
     * Import
     */
    public function import(
        CustomerImportRequest $request,
        CustomerImportService $service
    ): RedirectResponse {
        $result = $service->import(
            $request->file('csv')
        );

        if ($result['failed'] > 0) {
            return redirect()
                ->route('customers.import')
                ->withInput()
                ->with('result', $result);
        }

        return redirect()
            ->route('customers.import')
            ->with('success', 'CSVをインポートしました。')
            ->with('result', $result);
    }
}
