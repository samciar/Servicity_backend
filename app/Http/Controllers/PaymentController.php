<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'method' => 'sometimes|in:credit_card,paypal,bank_transfer,wallet'
        ]);

        $query = Payment::query()
            ->with(['booking', 'payer', 'payee']);

        if (Auth::user()->isClient()) {
            $query->where('payer_id', Auth::id());
        } elseif (Auth::user()->isTasker()) {
            $query->where('payee_id', Auth::id());
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('method')) {
            $query->where('payment_method', $request->method);
        }

        $payments = $query->latest()
            ->get();

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'currency' => ['required', Rule::in([
                Payment::CURRENCY_USD,
                Payment::CURRENCY_EUR,
                Payment::CURRENCY_GBP
            ])],
            'payment_method' => ['required', Rule::in([
                Payment::METHOD_CREDIT_CARD,
                Payment::METHOD_PAYPAL,
                Payment::METHOD_BANK_TRANSFER,
                Payment::METHOD_WALLET
            ])],
            'transaction_id' => 'nullable|string|max:255'
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $this->authorize('create-payment', $booking);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payer_id' => $booking->client_id,
            'payee_id' => $booking->tasker_id,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'status' => Payment::STATUS_PENDING
        ]);

        return response()->json($payment->load(['booking', 'payer', 'payee']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['booking', 'payer', 'payee'])->findOrFail($id);
        $this->authorize('view', $payment);

        return response()->json($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);
        $this->authorize('update', $payment);

        $validated = $request->validate([
            'transaction_id' => 'sometimes|nullable|string|max:255',
        ]);

        $payment->update($validated);

        return response()->json($payment->load(['booking', 'payer', 'payee']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);
        $this->authorize('delete', $payment);

        $payment->delete();

        return response()->json(null, 204);
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);
        $this->authorize('update-status', $payment);

        $validated = $request->validate([
            'transaction_id' => 'sometimes|nullable|string|max:255',
        ]);

        if ($payment->markAsCompleted($validated['transaction_id'] ?? null)) {
            return response()->json(['message' => 'Payment marked as completed']);
        }

        return response()->json(['message' => 'Failed to update payment status'], 500);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $id)
    {
        $payment = Payment::findOrFail($id);
        $this->authorize('update-status', $payment);

        if ($payment->markAsFailed()) {
            return response()->json(['message' => 'Payment marked as failed']);
        }

        return response()->json(['message' => 'Failed to update payment status'], 500);
    }

    /**
     * Refund payment
     */
    public function refund(string $id)
    {
        $payment = Payment::findOrFail($id);
        $this->authorize('refund', $payment);

        if ($payment->refund()) {
            return response()->json(['message' => 'Payment refunded successfully']);
        }

        return response()->json(['message' => 'Failed to refund payment'], 500);
    }

    /**
     * Get completed payments
     */
    public function completed()
    {
        $payments = Payment::completed()
            ->with(['booking', 'payer', 'payee'])
            ->where(function($query) {
                if (Auth::user()->isClient()) {
                    $query->where('payer_id', Auth::id());
                } elseif (Auth::user()->isTasker()) {
                    $query->where('payee_id', Auth::id());
                }
            })
            ->get();

        return response()->json($payments);
    }

    /**
     * Get available payment methods
     */
    public function paymentMethods()
    {
        return response()->json(Payment::paymentMethods());
    }

    /**
     * Get available currencies
     */
    public function currencies()
    {
        return response()->json(Payment::currencies());
    }
}
