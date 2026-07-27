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
}
