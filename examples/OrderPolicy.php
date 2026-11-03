<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Non-owners get a 404 rather than a 403, so they can't tell which order ids exist.
 * Paying and refunding are separate abilities from viewing. See docs/security.md.
 */
final class OrderPolicy
{
    public function view(User $user, Order $order): Response
    {
        return $this->ownedBy($user, $order);
    }

    public function pay(User $user, Order $order): Response
    {
        if ($user->isDelegate()) {
            return Response::deny(__('orders.only_the_owner_can_pay'));
        }

        return $this->ownedBy($user, $order);
    }

    private function ownedBy(User $user, Order $order): Response
    {
        return $order->user_id === $user->accountOwnerId()
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
