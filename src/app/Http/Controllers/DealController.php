<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Deal\StoreDealRequest;
use App\Http\Requests\Deal\UpdateDealRequest;
use App\Models\Deal;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Deal::with(['customer', 'user']);

        if (Auth::user()->isSales()) {
            $query->where('user_id', Auth::id());
        }

        $deals = $query
            ->orderBy('id')
            ->paginate(10);

        return view('deals.index', compact('deals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Auth::user()->isAdmin()
            ? Customer::orderBy('id')->get()
            : Customer::where('user_id', Auth::id())
            ->orderBy('id')
            ->get();

        $users = Auth::user()->isAdmin()
            ? User::where('role', 'sales')
            ->orderBy('id')
            ->get()
            : collect();

        return view('deals.create', compact('customers', 'users'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDealRequest $request)
    {
        $deal = Deal::create([
            'customer_id' => $request->customer_id,
            'user_id' => Auth::user()->isAdmin()
                ? $request->user_id
                : Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'status' => $request->status,
            'expected_contract_date' => $request->expected_contract_date,
            'memo' => $request->memo,
        ]);

        return redirect()
            ->route('deals.index')
            ->with('success', '商談を登録しました。');
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
    public function edit(Deal $deal)
    {
        $customers = Auth::user()->isAdmin()
            ? Customer::orderBy('id')->get()
            : Customer::where('user_id', Auth::id())
            ->orderBy('id')
            ->get();

        $users = Auth::user()->isAdmin()
            ? User::where('role', 'sales')
            ->orderBy('id')
            ->get()
            : collect();

        return view('deals.edit', compact('deal', 'customers', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDealRequest $request, Deal $deal)
    {
        $deal->update([
            'customer_id' => $request->customer_id,
            'user_id' => Auth::user()->isAdmin()
                ? $request->user_id
                : Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'status' => $request->status,
            'expected_contract_date' => $request->expected_contract_date,
            'memo' => $request->memo,
        ]);

        return redirect()
            ->route('deals.index')
            ->with('success', '商談情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        $deal->delete();

        return redirect()
            ->route('deals.index')
            ->with('success', '商談を削除しました。');
    }
}
