<?php
namespace BonusSystem\Views;

use BonusSystem\Models\SettingsModel;

class SettingsView
{
    private array $tiers;

    public function render(array $settings): void
    {
        $this->tiers = $settings['tiers'] ?? [];
        ?>
        <div class="wrap">
            <h1><?php _e('Система бонусів', 'bonus-system'); ?></h1>
            
            <?php settings_errors(); ?>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('bonus_system_settings');
                do_settings_sections('bonus_system_settings');
                ?>
                
                <table class="form-table">
                    <tr>
                        <th><?php _e('Активувати систему', 'bonus-system'); ?></th>
                        <td>
                            <input type="checkbox" 
                                   name="<?php echo SettingsModel::OPTION_NAME; ?>[enabled]" 
                                   value="1" 
                                   <?php checked($settings['enabled'] ?? false, true); ?>>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><?php _e('Рівні бонусів', 'bonus-system'); ?></th>
                        <td>
                            <div id="bonus-tiers-container">
                                <?php $this->render_tiers($this->tiers); ?>
                            </div>
                            <button type="button" class="button button-primary" id="add-tier">
                                + <?php _e('Додати рівень', 'bonus-system'); ?>
                            </button>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Зберегти налаштування', 'bonus-system')); ?>
            </form>
        </div>
        
        <?php $this->render_scripts(); ?>
        <?php
    }

    private function render_tiers(array $tiers): void 
    {
        if (empty($tiers)) {
            // Показуємо порожній рядок як приклад
            $this->render_tier_row(0, [
                'min_amount' => '',
                'discount_type' => 'percent',
                'discount_value' => ''
            ]);
        } else {
            foreach ($tiers as $index => $tier) {
                $this->render_tier_row($index, $tier);
            }
        }
    }

    private function render_tier_row(int $index, array $tier): void 
    {
        $discount_value = $tier['discount_value'] ?? ($tier['discount_percent'] ?? ($tier['discount_fixed'] ?? ''));
        ?>
        <div class="bonus-tier-row" data-id="<?php echo $index; ?>">
            <input type="number" 
                   name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][min_amount]" 
                   value="<?php echo esc_attr($tier['min_amount'] ?? ''); ?>" 
                   placeholder="<?php _e('Мін. сума', 'bonus-system'); ?>" 
                   class="regular-text"
                   step="any"
                   style="width: 150px;">
            
            <select name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][discount_type]" 
                    class="discount-type">
                <option value="percent" <?php selected($tier['discount_type'] ?? '', 'percent'); ?>>
                    % <?php _e('Відсоток', 'bonus-system'); ?>
                </option>
                <option value="fixed" <?php selected($tier['discount_type'] ?? '', 'fixed'); ?>>
                    ₴ <?php _e('Фіксована', 'bonus-system'); ?>
                </option>
            </select>
            
            <input type="number" 
                   name="<?php echo SettingsModel::OPTION_NAME; ?>[tiers][<?php echo $index; ?>][discount_value]" 
                   value="<?php echo esc_attr($discount_value); ?>" 
                   placeholder="<?php _e('Значення', 'bonus-system'); ?>" 
                   class="discount-value"
                   step="any"
                   style="width: 120px;">
            
            <button type="button" class="button remove-tier">
                <?php _e('Видалити', 'bonus-system'); ?>
            </button>
        </div>
        <?php
    }

    private function render_scripts(): void 
    {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Лічильник для нових рядків
            let nextId = <?php echo count($this->tiers); ?>;
            
            // Функція оновлення індексів
            function updateIndexes() {
                $('.bonus-tier-row').each(function(newIndex) {
                    const row = $(this);
                    const oldId = row.data('id');
                    
                    // Оновлюємо data-id
                    row.data('id', newIndex);
                    
                    // Оновлюємо name атрибути всіх полів
                    row.find('input, select').each(function() {
                        const $field = $(this);
                        const name = $field.attr('name');
                        if (name) {
                            const newName = name.replace(/tiers\]\[\d+\]/, `tiers][${newIndex}]`);
                            $field.attr('name', newName);
                        }
                    });
                });
                
                // Оновлюємо лічильник
                nextId = $('.bonus-tier-row').length;
            }
            
            // Функція додавання нового рядка
            function addNewTier() {
                const rowId = nextId;
                const optionName = '<?php echo SettingsModel::OPTION_NAME; ?>';
                
                const newRow = `
                    <div class="bonus-tier-row" data-id="${rowId}">
                        <input type="number" 
                               name="${optionName}[tiers][${rowId}][min_amount]" 
                               placeholder="<?php _e('Мін. сума', 'bonus-system'); ?>" 
                               class="regular-text"
                               step="any"
                               style="width: 150px;">
                        <select name="${optionName}[tiers][${rowId}][discount_type]" class="discount-type">
                            <option value="percent">% <?php _e('Відсоток', 'bonus-system'); ?></option>
                            <option value="fixed">₴ <?php _e('Фіксована', 'bonus-system'); ?></option>
                        </select>
                        <input type="number" 
                               name="${optionName}[tiers][${rowId}][discount_value]" 
                               placeholder="<?php _e('Значення', 'bonus-system'); ?>" 
                               class="discount-value"
                               step="any"
                               style="width: 120px;">
                        <button type="button" class="button remove-tier">
                            <?php _e('Видалити', 'bonus-system'); ?>
                        </button>
                    </div>
                `;
                
                $('#bonus-tiers-container').append(newRow);
                nextId++;
            }
            
            // Функція видалення рядка
            function removeTier(button) {
                const row = button.closest('.bonus-tier-row');
                const container = $('#bonus-tiers-container');
                
                // Якщо це останній рядок, не видаляємо, а очищаємо
                if (container.children('.bonus-tier-row').length === 1) {
                    row.find('input').val('');
                    row.find('select').val('percent');
                    return;
                }
                
                // Видаляємо рядок
                row.remove();
                
                // Оновлюємо індекси
                updateIndexes();
            }
            
            // Обробник додавання
            $('#add-tier').on('click', function(e) {
                e.preventDefault();
                addNewTier();
            });
            
            // Обробник видалення (використовуємо event delegation)
            $(document).on('click', '.remove-tier', function(e) {
                e.preventDefault();
                removeTier($(this));
            });
            
            // Ініціалізація: встановлюємо коректні індекси
            updateIndexes();
            
            // Якщо немає жодного рядка, додаємо порожній
            if ($('.bonus-tier-row').length === 0) {
                addNewTier();
            }
        });
        </script>
        
        <style>
            .bonus-tier-row {
                margin-bottom: 15px;
                padding: 10px;
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }
            .bonus-tier-row input,
            .bonus-tier-row select {
                margin: 0;
            }
            .bonus-tier-row .remove-tier {
                color: #dc3232;
                border-color: #dc3232;
            }
            .bonus-tier-row .remove-tier:hover {
                background-color: #dc3232;
                color: white;
                border-color: #dc3232;
            }
            #add-tier {
                margin-top: 10px;
            }
        </style>
        <?php
    }
}