<?php
namespace BonusSystem\Views\Editor;

use BonusSystem\Models\SalesBonus;
use BonusSystem\Models\TextBonus;
use BonusSystem\Services\SettingsService;

class SettingsScriptsView
{
    /**
     * @param array<SalesBonus> $sales
     * @param array<TextBonus> $texts
     * @return void
     */
    public function render(array $sales, array $texts): void
    {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                let nextSalesId = <?php echo count($sales); ?>;
                function updateSalesIndexes() {
                    $('#bonus-tiers-container .bonus-tier-row.sales-row').each(function (newIndex) {
                        const row = $(this);
                        row.data('id', newIndex);

                        row.find('input, select').each(function () {
                            const $field = $(this);
                            const name = $field.attr('name');
                            if (name) {
                                const newName = name.replace(/sales\]\[\d+\]/, `sales][${newIndex}]`);
                                $field.attr('name', newName);
                            }
                        });
                    });
                    nextSalesId = $('#bonus-tiers-container .bonus-tier-row.sales-row').length;
                }

                function addNewSalesTier() {
                    const rowId = nextSalesId;
                    const optionName = '<?php echo SettingsService::OPTION_NAME; ?>';

                    const newRow = `
                        <div class="bonus-tier-row sales-row" data-id="${rowId}">
                            <input type="number"
                                   name="${optionName}[sales][${rowId}][min_price]"
                                   placeholder="<?php _e('Мінімальна ціна', 'bonus-system'); ?>"
                                   class="regular-text"
                                   step="any"
                                   style="width: 150px;">
                            <select name="${optionName}[sales][${rowId}][discount_type]" class="discount-type">
                                <option value="percent" selected>% <?php _e('Відсоток', 'bonus-system'); ?></option>
                                <option value="fixed">₴ <?php _e('Фіксована', 'bonus-system'); ?></option>
                            </select>
                            <input type="number"
                                   name="${optionName}[sales][${rowId}][discount_value]"
                                   placeholder="<?php _e('Значення', 'bonus-system'); ?>"
                                   class="discount-value"
                                   step="any"
                                   style="width: 120px;">
                            <input type="number"
                                   name="${optionName}[sales][${rowId}][min_products_count]"
                                   placeholder="<?php _e('Мінімальні кількість товарів', 'bonus-system'); ?>"
                                   class="regular-text"
                                   step="any"
                                   style="width: 120px;">
                            <button type="button" class="button remove-tier remove-sales">
                                <?php _e('Видалити', 'bonus-system'); ?>
                            </button>
                        </div>
                    `;

                    $('#bonus-tiers-container').append(newRow);
                    nextSalesId++;
                }

                function removeSalesTier(button) {
                    const row = button.closest('.bonus-tier-row');
                    const container = $('#bonus-tiers-container');
                    const salesRows = container.find('.bonus-tier-row.sales-row');

                    if (salesRows.length === 1) {
                        row.find('input').val('');
                        row.find('select').val('percent');
                        return;
                    }

                    row.remove();
                    updateSalesIndexes();
                }

                let nextTextId = <?php echo count($texts); ?>;

                function updateTextIndexes() {
                    $('#bonus-texts-container .bonus-tier-row.text-row').each(function (newIndex) {
                        const row = $(this);
                        row.data('id', newIndex);

                        row.find('input').each(function () {
                            const $field = $(this);
                            const name = $field.attr('name');
                            if (name) {
                                const newName = name.replace(/texts\]\[\d+\]/, `texts][${newIndex}]`);
                                $field.attr('name', newName);
                            }
                        });
                    });
                    nextTextId = $('#bonus-texts-container .bonus-tier-row.text-row').length;
                }

                function addNewTextTier() {
                    const rowId = nextTextId;
                    const optionName = '<?php echo SettingsService::OPTION_NAME; ?>';

                    const newRow = `
                        <div class="bonus-tier-row text-row" data-id="${rowId}">
                            <input type="number"
                                   name="${optionName}[texts][${rowId}][min_price]"
                                   placeholder="<?php _e('Мінімальна ціна', 'bonus-system'); ?>"
                                   class="regular-text"
                                   step="any"
                                   style="width: 150px;">
                            <input type="text"
                                   name="${optionName}[texts][${rowId}][name]"
                                   placeholder="<?php _e('Текст бонусу', 'bonus-system'); ?>"
                                   class="regular-text"
                                   style="width: 200px;">
                            <input type="number"
                                   name="${optionName}[texts][${rowId}][min_products_count]"
                                   placeholder="<?php _e('Мінімальні кількість товарів', 'bonus-system'); ?>"
                                   class="regular-text"
                                   step="any"
                                   style="width: 120px;">
                            <button type="button" class="button remove-tier remove-text">
                                <?php _e('Видалити', 'bonus-system'); ?>
                            </button>
                        </div>
                    `;

                    $('#bonus-texts-container').append(newRow);
                    nextTextId++;
                }

                function removeTextTier(button) {
                    const row = button.closest('.bonus-tier-row');
                    const container = $('#bonus-texts-container');
                    const textRows = container.find('.bonus-tier-row.text-row');

                    if (textRows.length === 1) {
                        row.find('input').val('');
                        return;
                    }

                    row.remove();
                    updateTextIndexes();
                }

                $('#add-sales-tier').on('click', function (e) {
                    e.preventDefault();
                    addNewSalesTier();
                });

                $('#add-text-tier').on('click', function (e) {
                    e.preventDefault();
                    addNewTextTier();
                });

                $(document).on('click', '.remove-sales', function (e) {
                    e.preventDefault();
                    removeSalesTier($(this));
                });

                $(document).on('click', '.remove-text', function (e) {
                    e.preventDefault();
                    removeTextTier($(this));
                });

                updateSalesIndexes();
                updateTextIndexes();

                if ($('#bonus-tiers-container .bonus-tier-row.sales-row').length === 0) {
                    addNewSalesTier();
                }

                if ($('#bonus-texts-container .bonus-tier-row.text-row').length === 0) {
                    addNewTextTier();
                }
            });
        </script>
        <?php
    }
}