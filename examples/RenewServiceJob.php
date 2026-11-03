<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Billing\ChargeRenewalAction;
use App\Exceptions\GatewayRefusedException;
use App\Models\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Safe to run late, twice, or alongside another copy. See docs/jobs-and-queues.md.
 */
#[Tries(3)]
#[Timeout(60)]
#[Backoff(30, 120)]
final class RenewServiceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public UserService $service) {}

    public function handle(ChargeRenewalAction $chargeRenewal): void
    {
        $service = $this->service->fresh();

        // A customer may have renewed by hand, or switched auto-renew off, since dispatch.
        if ($service === null || ! $service->isDueForRenewal()) {
            return;
        }

        try {
            $chargeRenewal->execute($service, $service->renewalIdempotencyKey());
        } catch (GatewayRefusedException $exception) {
            // A refusal fails the same way on every retry, so don't spend them.
            $this->fail($exception);
        }

        // Anything else (timeout, 5xx) is an unknown outcome and is rethrown, so the queue
        // retries under the same idempotency key and the gateway replays the first answer.
    }
}
