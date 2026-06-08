<?php
namespace BonusSystem\Repositories;

use BonusSystem\Models\SalesBonusModel;
use BonusSystem\Models\SettingsModel;

class BonusRepository
{
    private SettingsModel $settings_model;

    public function __construct()
    {
        $this->settings_model = new SettingsModel();
    }

    public function get_all_bonuses(): array
    {
        $tiers = $this->settings_model->get_tiers();
        if (empty($tiers)) {
            return [];
        }
        $bonuses = [];
        foreach ($tiers as $tier) {
            $bonus = $this->create_bonus_from_tier($tier);
            if ($bonus) {
                $bonuses[] = $bonus;
            }
        }
        $this->sort_bonuses_by_min_price($bonuses);
        return $bonuses;
    }

    private function create_bonus_from_tier(array $tier): ?SalesBonusModel
    {
        if (empty($tier['min_amount'])) {
            return null;
        }
        $discountType = $tier['discount_type'] ?? 'percent';
        $discountValue = 0;

        if ($discountType === 'percent') {
            $discountValue = (float) ($tier['discount_percent'] ?? 0);
        } else {
            $discountValue = (float) ($tier['discount_fixed'] ?? 0);
        }

        if ($discountValue <= 0) {
            return null;
        }

        return new SalesBonusModel(
            (float) $tier['min_amount'],
            $discountType,
            $discountValue
        );
    }

    private function sort_bonuses_by_min_price(array &$bonuses): void
    {
        usort($bonuses, function ($a, $b) {
            return $a->get_min_price() <=> $b->get_min_price();
        });
    }
}