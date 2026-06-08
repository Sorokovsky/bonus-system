<?php
namespace BonusSystem\Views;

use BonusSystem\Models\SettingsModel;

class SettingsView
{
    private array $tiers;

    public function render(array $settings): void
    {
        ?>
        <div class="wrap">
            <h1><?php _e('Система бонусів', 'bonus-system'); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('bonus_system_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th><?php _e('Активувати систему', 'bonus-system'); ?></th>
                        <td>
                            <input type="checkbox" 
                                   name="<?php echo SettingsModel::OPTION_NAME; ?>[enabled]" 
                                   value="1" 
                                   <?php checked($settings['enabled'], true); ?>>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><?php _e('Рівні бонусів', 'bonus-system'); ?></th>
                        <td>
                            <div id="bonus-tiers-container">
                                <?php $this->render_tiers($settings['tiers']); ?>
                            </div>
                            <button type="button" class="button" id="add-tier">
                                + <?php _e('Додати рівень', 'bonus-system'); ?>
                            </button>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        
        <?php $this->render_scripts(); ?>
        <?php
    }

    private function render_tiers(array $tiers): void {
        foreach ($tiers as $index => $tier) {
            $this->render_tier_row($index, $tier);
        }
    }

    private function render_tier_row(int $index, array $tier): void 
    {
    $discount_type = $tier['discount_type'] ?? 'percent';
    $discount_percent = $tier['discount_percent'] ?? ($tier['discount_value'] ?? '');
    $discount_fixed = $tier['discount_fixed'] ?? ($tier['discount_value'] ?? '');
    ?>
    <div class="bonus-tier-row" data-index="<?php echo $index; ?>">
        <input type="number" 
               name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][min_amount]" 
               value="<?php echo esc_attr($tier['min_amount'] ?? ''); ?>" 
               placeholder="Мін. сума" 
               step="any"
               style="width: 150px;">
        
        <select name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][discount_type]"
                class="discount-type">
            <option value="percent" <?php selected($discount_type, 'percent'); ?>>
                % Відсоток
            </option>
            <option value="fixed" <?php selected($discount_type, 'fixed'); ?>>
                ₴ Фіксована
            </option>
        </select>
        
        <input type="number" 
               name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][discount_percent]" 
               value="<?php echo esc_attr($discount_percent); ?>" 
               placeholder="Відсоток" 
               step="any"
               style="width: 120px; <?php echo $discount_type !== 'percent' ? 'display:none;' : ''; ?>"
               class="discount-percent">
        
        <input type="number" 
               name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][discount_fixed]" 
               value="<?php echo esc_attr($discount_fixed); ?>" 
               placeholder="Сума" 
               step="any"
               style="width: 120px; <?php echo $discount_type !== 'fixed' ? 'display:none;' : ''; ?>"
               class="discount-fixed">
        
        <button type="button" class="button remove-tier">×</button>
    </div>
    <?php
}
    private function render_scripts(): void {
    $tier_count = count($this->tiers);
    ?>
    <script>
    jQuery(document).ready(function($) {
        let tierCount = <?php echo $tier_count; ?>;
        const optionName = '<?php echo SettingsModel::OPTION_NAME; ?>';
        
        // Функція для показу/приховування полів
        function toggleDiscountFields(select) {
            const row = select.closest('.bonus-tier-row');
            const type = select.val();
            
            if (type === 'percent') {
                row.find('.discount-percent').show();
                row.find('.discount-fixed').hide();
                row.find('.discount-fixed').val('');
            } else {
                row.find('.discount-percent').hide();
                row.find('.discount-fixed').show();
                row.find('.discount-percent').val('');
            }
        }
        
        // Додати новий рівень
        $('#add-tier').on('click', function() {
            const template = `
                <div class="bonus-tier-row" data-index="${tierCount}">
                    <input type="number" 
                           name="${optionName}[tiers][${tierCount}][min_amount]" 
                           placeholder="Мін. сума" 
                           step="any"
                           style="width: 150px;">
                    <select name="${optionName}[tiers][${tierCount}][discount_type]" class="discount-type">
                        <option value="percent">% Відсоток</option>
                        <option value="fixed">₴ Фіксована</option>
                    </select>
                    <input type="number" 
                           name="${optionName}[tiers][${tierCount}][discount_percent]" 
                           placeholder="Відсоток" 
                           step="any"
                           style="width: 120px;"
                           class="discount-percent">
                    <input type="number" 
                           name="${optionName}[tiers][${tierCount}][discount_fixed]" 
                           placeholder="Сума" 
                           step="any"
                           style="width: 120px; display: none;"
                           class="discount-fixed">
                    <button type="button" class="button remove-tier">×</button>
                </div>
            `;
            $('#bonus-tiers-container').append(template);
            
            // Налаштувати обробник для нового рядка
            const newRow = $('.bonus-tier-row').last();
            toggleDiscountFields(newRow.find('.discount-type'));
            newRow.find('.discount-type').on('change', function() {
                toggleDiscountFields($(this));
            });
            
            tierCount++;
        });
        
        // Обробник зміни типу знижки
        $(document).on('change', '.discount-type', function() {
            toggleDiscountFields($(this));
        });
        
        // Видалити рівень
        $(document).on('click', '.remove-tier', function() {
            $(this).closest('.bonus-tier-row').remove();
        });
        
        // Ініціалізація - налаштувати всі існуючі рядки
        $('.discount-type').each(function() {
            toggleDiscountFields($(this));
        });
        
        // Автоматично показати хоча б один рядок, якщо їх немає
        if ($('.bonus-tier-row').length === 0) {
            $('#add-tier').click();
        }
    });
    </script>
    
    <style>
        .bonus-tier-row {
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .bonus-tier-row input,
        .bonus-tier-row select {
            margin: 0;
        }
        .remove-tier {
            color: red;
            border-color: red;
        }
        .remove-tier:hover {
            background-color: #ff000011;
        }
    </style>
    <?php
}
}