<?php
namespace BonusSystem\Views\Editor;

use BonusSystem\Models\TextBonus;
use BonusSystem\Services\SettingsService;

class TextEditorView
{
    public function render(TextBonus $text_bonus, int $index): void
    {
        ?>
        <div class="bonus-tier-row text-row" data-id="<?php echo $index; ?>">
            <input name="<?php echo SettingsService::OPTION_NAME; ?>[texts][<?php echo $index; ?>][min_price]"
                value="<?php echo $text_bonus->get_min_price(); ?>"
                placeholder="<?php _e("Мінімальна ціна", "bonus-system"); ?>" class="regular-text" step="any" min="0"
                style="width: 150px;">
            <input type="text" name="<?php echo SettingsService::OPTION_NAME; ?>[texts][<?php echo $index; ?>][name]"
                value="<?php echo esc_attr($text_bonus->get_name()); ?>" class="regular-text"
                placeholder="<?php _e("Текст бонусу", 'bonus-system'); ?>" style="width: 200px;">
            <button type="button" class="button remove-tier remove-text">
                <?php _e("Видалити", 'bonus-system'); ?>
            </button>
        </div>
        <?php
    }
}