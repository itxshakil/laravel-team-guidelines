<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use InvalidArgumentException;

/**
 * Shares an order's tax across its lines so the shares add up to the order's tax exactly.
 * Rounding each line on its own left refunds a cent or two short. See docs/money-and-payments.md.
 */
final class AllocateOrderTaxAction
{
    /**
     * @param  array<int, int>  $lineCents  taxable amount per line, keyed by line id
     * @return array<int, int> tax in cents per line, keyed by line id
     */
    public function execute(int $orderTaxCents, array $lineCents): array
    {
        $taxableCents = array_sum($lineCents);

        if ($taxableCents <= 0) {
            throw new InvalidArgumentException('An order with tax needs a taxable total above zero.');
        }

        $shares = [];
        $remainders = [];

        foreach ($lineCents as $lineId => $cents) {
            $exact = $orderTaxCents * $cents / $taxableCents;
            $shares[$lineId] = (int) floor($exact);
            $remainders[$lineId] = $exact - $shares[$lineId];
        }

        arsort($remainders);

        $leftover = $orderTaxCents - array_sum($shares);

        foreach (array_keys($remainders) as $lineId) {
            if ($leftover === 0) {
                break;
            }

            $shares[$lineId]++;
            $leftover--;
        }

        return $shares;
    }
}
