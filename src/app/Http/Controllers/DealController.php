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
        $keyword = request('keyword');
        $sort = request('sort', 'id');
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $query = Deal::with('customer.user');

        if (Auth::user()->isSales()) {
            $query->whereHas('customer', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $query->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($query) use ($keyword) {

                // 商談名
                $query->where('title', 'like', "%{$keyword}%")

                    // 顧客名
                    ->orWhereHas('customer', function ($query) use ($keyword) {
                        $query->where('company_name', 'like', "%{$keyword}%");
                    })

                    // 担当営業
                    ->orWhereHas('customer.user', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
            });
        });

        if (in_array($sort, ['id', 'title', 'expected_contract_date', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id');
        }

        $deals = $query
            ->paginate(10)
            ->withQueryString();

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
