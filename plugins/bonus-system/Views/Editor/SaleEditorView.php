<?php
namespace BonusSystem\Views\Editor;

use BonusSystem\Models\DiscountType;
use BonusSystem\Models\SalesBonus;
use BonusSystem\Services\SettingsService;

class SaleEditorView
{
    public function render(SalesBonus $sale, int $index): void
    {
        ?>
        <div class="bonus-tier-row sales-row" data-id="<?php echo $index; ?>">
            <input name="<?php echo SettingsService::OPTION_NAME; ?>[sales][<?php echo $index; ?>][min_price]"
                value="<?php echo $sale->get_min_price(); ?>" placeholder="<?php _e("Мінімальна ціна", "bonus-system"); ?>"
                class="regular-text" step="any" min="0" style="width: 150px;">
            <select name="<?php echo SettingsService::OPTION_NAME; ?>[sales][<?php echo $index; ?>][discount_type]"
                class="discount-type">
                <option value="<?php echo DiscountType::PERCENT->value; ?>" <?php selected($sale->get_discount_type()->value, DiscountType::PERCENT->value); ?>>
                    % <?php _e("Відсоток", "bonus-system"); ?>
                </option>
                <option value="<?php echo DiscountType::FIXED->value; ?>" <?php selected($sale->get_discount_type()->value, DiscountType::FIXED->value); ?>>
                    ₴ <?php _e("Фіксована", "bonus-system"); ?>
                </option>
            </select>
            <input type="number" name="<?php echo SettingsService::OPTION_NAME; ?>[sales][<?php echo $index; ?>][discount_value]"
                value="<?php echo $sale->get_discount_amount(); ?>" class="discount-value" step="any" min="0"
                style="width: 120px;">
            <button type="button" class="button remove-tier remove-sales">
                <?php _e("Видалити", 'bonus-system'); ?>
            </button>
        </div>
        <?php
    }
}