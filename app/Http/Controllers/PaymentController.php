<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mollie\Api\MollieApiClient;

class PaymentController extends Controller
{
    private function mollie(): MollieApiClient
    {
        $mollie = new MollieApiClient;
        $mollie->setApiKey(config('services.mollie.key'));

        return $mollie;
    }

    /**
     * Create a Mollie payment for an appointment and redirect to checkout.
     */
    public function create(Appointment $appointment)
    {
        $appointment->load(['service', 'customer']);

        if (! in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->route('booking.confirmation', $appointment->id)
                ->with('error', 'Deze afspraak kan niet meer worden betaald.');
        }

        // Create or reuse pending payment
        $payment = Payment::where('appointment_id', $appointment->id)
            ->where('status', 'open')
            ->first();

        if (! $payment) {
            $payment = Payment::create([
                'appointment_id' => $appointment->id,
                'amount_cents' => $appointment->price_cents,
                'currency' => 'EUR',
                'status' => 'open',
            ]);
        }

        $payload = [
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($payment->amount_cents / 100, 2, '.', ''),
            ],
            'description' => "Dawara Barbershop — {$appointment->service->name}",
            'redirectUrl' => route('payment.success', ['appointment' => $appointment->id]),
            'metadata' => [
                'appointment_id' => $appointment->id,
                'payment_id' => $payment->id,
            ],
        ];

        // Only include webhook URL on public domains (Mollie rejects localhost / private IPs)
        $webhookUrl = config('services.mollie.webhook_url') ?? route('payment.webhook');
        if ($webhookUrl && ! preg_match('/localhost|127\.\d+\.\d+\.\d+|\.test$|\.local$|\.herd\.dev$/i', $webhookUrl)) {
            $payload['webhookUrl'] = $webhookUrl;
        }

        try {
            $molliePayment = $this->mollie()->payments->create($payload);

            $payment->update([
                'mollie_payment_id' => $molliePayment->id,
                'mollie_checkout_url' => $molliePayment->getCheckoutUrl(),
            ]);

            return redirect()->away($molliePayment->getCheckoutUrl());
        } catch (\Exception $e) {
            Log::error('Mollie payment creation failed', ['error' => $e->getMessage()]);

            return redirect()->route('booking.confirmation', $appointment->id)
                ->with('error', 'Betaling kon niet worden gestart. Probeer het later opnieuw.');
        }
    }

    /**
     * Customer redirected here after Mollie checkout.
     */
    public function success(Request $request, Appointment $appointment)
    {
        $payment = Payment::where('appointment_id', $appointment->id)->latest()->first();

        if ($payment && $payment->mollie_payment_id) {
            try {
                $molliePayment = $this->mollie()->payments->get($payment->mollie_payment_id);
                $this->updatePaymentStatus($payment, $molliePayment);
            } catch (\Exception $e) {
                Log::error('Mollie success check failed', ['error' => $e->getMessage()]);
            }
        }

        $message = $payment && $payment->status === 'paid'
            ? 'Betaling succesvol ontvangen!'
            : 'Betalingstatus wordt verwerkt.';

        return redirect()->route('booking.confirmation', $appointment->id)
            ->with('success', $message);
    }

    /**
     * Mollie webhook — called server-to-server.
     */
    public function webhook(Request $request)
    {
        $id = $this->extractMolliePaymentId($request);
        if (! $id) {
            Log::warning('Mollie webhook received without payment id', [
                'content_type' => $request->header('content-type'),
                'query' => $request->query(),
                'body' => $request->getContent(),
            ]);

            // Return 200 so manual/ngrok pings do not look like app failures.
            // Real Mollie webhooks include an id like tr_xxx and will be processed below.
            return response()->noContent(200);
        }

        $payment = Payment::where('mollie_payment_id', $id)->first();
        if (! $payment) {
            Log::warning('Mollie webhook received for unknown payment', ['id' => $id]);

            return response()->noContent(200);
        }

        try {
            $molliePayment = $this->mollie()->payments->get($id);
            $this->updatePaymentStatus($payment, $molliePayment);
        } catch (\Exception $e) {
            Log::error('Mollie webhook failed', ['error' => $e->getMessage(), 'id' => $id]);

            return response()->noContent(500);
        }

        return response()->noContent(200);
    }

    private function extractMolliePaymentId(Request $request): ?string
    {
        $id = $request->input('id') ?: $request->query('id');

        if ($id) {
            return trim((string) $id);
        }

        $raw = trim($request->getContent());

        if ($raw === '') {
            return null;
        }

        $json = json_decode($raw, true);
        if (is_array($json) && ! empty($json['id'])) {
            return trim((string) $json['id']);
        }

        parse_str($raw, $parsed);
        if (! empty($parsed['id'])) {
            return trim((string) $parsed['id']);
        }

        // Helpful for testing: allow raw body to be just "tr_xxx".
        if (preg_match('/^(tr|ord|pfl|pay|ch)_/i', $raw)) {
            return $raw;
        }

        return null;
    }

    private function updatePaymentStatus(Payment $payment, $molliePayment): void
    {
        $status = match ($molliePayment->status) {
            'paid', 'paidout' => 'paid',
            'authorized' => 'authorized',
            'canceled' => 'cancelled',
            'failed', 'expired' => 'failed',
            default => 'open',
        };

        $update = ['status' => $status];
        if (in_array($status, ['paid', 'authorized'])) {
            $update['paid_at'] = now();
            $update['method'] = $molliePayment->method ?? null;
        }

        $payment->update($update);
    }
}
