<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(Customer::class, 'customer');
    // }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Customer::with('user');

        if (Auth::user()->role === 'sales') {
            $query->where('user_id', Auth::id());
        }

        $customers = $query
            ->orderBy('id')
            ->paginate(10);

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
}
