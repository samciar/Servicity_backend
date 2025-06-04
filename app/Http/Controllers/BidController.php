<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BidController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bids = Bid::with(['task', 'tasker'])
            ->whereHas('task', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        return response()->json($bids);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'bid_amount' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:500',
        ]);

        $bid = Bid::create([
            ...$validated,
            'tasker_id' => Auth::id(),
            'status' => Bid::STATUS_PENDING
        ]);

        return response()->json($bid, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bid = Bid::with(['task', 'tasker'])->findOrFail($id);
        $this->authorize('view', $bid);

        return response()->json($bid);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bid = Bid::findOrFail($id);
        $this->authorize('update', $bid);

        $validated = $request->validate([
            'bid_amount' => 'sometimes|numeric|min:0',
            'message' => 'sometimes|string|max:500',
        ]);

        $bid->update($validated);

        return response()->json($bid);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bid = Bid::findOrFail($id);
        $this->authorize('delete', $bid);

        $bid->delete();

        return response()->json(null, 204);
    }

    /**
     * Accept a bid
     */
    public function accept(string $id)
    {
        $bid = Bid::findOrFail($id);
        $this->authorize('accept', $bid);

        if ($bid->accept()) {
            return response()->json(['message' => 'Bid accepted successfully']);
        }

        return response()->json(['message' => 'Failed to accept bid'], 500);
    }

    /**
     * Reject a bid
     */
    public function reject(string $id)
    {
        $bid = Bid::findOrFail($id);
        $this->authorize('reject', $bid);

        if ($bid->reject()) {
            return response()->json(['message' => 'Bid rejected successfully']);
        }

        return response()->json(['message' => 'Failed to reject bid'], 500);
    }

    /**
     * Withdraw a bid
     */
    public function withdraw(string $id)
    {
        $bid = Bid::findOrFail($id);
        $this->authorize('withdraw', $bid);

        if ($bid->withdraw()) {
            return response()->json(['message' => 'Bid withdrawn successfully']);
        }

        return response()->json(['message' => 'Failed to withdraw bid'], 500);
    }

    /**
     * Get pending bids for current user
     */
    public function pending()
    {
        $bids = Bid::with(['task', 'tasker'])
            ->where('tasker_id', Auth::id())
            ->pending()
            ->get();

        return response()->json($bids);
    }

    /**
     * Get accepted bids for current user
     */
    public function accepted()
    {
        $bids = Bid::with(['task', 'tasker'])
            ->where('tasker_id', Auth::id())
            ->accepted()
            ->get();

        return response()->json($bids);
    }
}
