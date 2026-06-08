<?php
namespace BonusSystem\Services;

use BonusSystem\Models\SalesBonusModel;
use BonusSystem\Repositories\BonusRepository;

class DiscountService
{
    private BonusRepository $repository;

    public function __construct(BonusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find_best_bonus(): ?object
    {
        $bonuses = $this->repository->get_all_bonuses();

        if (empty($bonuses)) {
            return null;
        }

        $subtotal = WC()->cart->get_subtotal();
        $bestBonus = null;
        $maxDiscount = 0;

        foreach ($bonuses as $bonus) {
            if (!$bonus->is_available($subtotal)) {
                continue;
            }

            $discount = $bonus->calculate_discount($subtotal);

            if ($discount > $maxDiscount) {
                $maxDiscount = $discount;
                $bestBonus = $bonus;
            }
        }

        return $bestBonus;
    }

    public function calculate_bonus_discount(SalesBonusModel $bonus, float $subtotal): float
    {
        return $bonus->calculate_discount($subtotal);
    }
}