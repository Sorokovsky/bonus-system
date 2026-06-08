<?php
namespace BonusSystem\Views;

use BonusSystem\Models\SalesBonus;
use BonusSystem\Models\SettingsModel;
use BonusSystem\Services\SettingsService;

class SettingsScriptsView
{
    /**
     * @param array<SalesBonus> $sales
     * @return void
     */
    public function render(array $sales): void
    {
?>
<script>
    jQuery(document).ready(function($) {
        let nextId = <?php echo count($sales); ?>;

        function updateIndexes() {
            $('.bonus-tier-row').each(function(newIndex) {
                const row = $(this);
                const oldId = row.data('id');

                row.data('id', newIndex);

                row.find('input, select').each(function() {
                    const $field = $(this);
                    const name = $field.attr('name');
                    if (name) {
                        const newName = name.replace(/tiers\]\[\d+\]/, `tiers][${newIndex}]`);
                        $field.attr('name', newName);
                    }
                });
            });

            nextId = $('.bonus-tier-row').length;
        }

        function addNewTier() {
            const rowId = nextId;
            const optionName = '<?php echo SettingsService::OPTION_NAME; ?>';

            const newRow = `
                    <div class="bonus-tier-row" data-id="${rowId}">
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
                        <button type="button" class="button remove-tier">
                            <?php _e('Видалити', 'bonus-system'); ?>
                        </button>
                    </div>
                `;

            $('#bonus-tiers-container').append(newRow);
            nextId++;
        }

        function removeTier(button) {
            const row = button.closest('.bonus-tier-row');
            const container = $('#bonus-tiers-container');

            if (container.children('.bonus-tier-row').length === 1) {
                row.find('input').val('');
                row.find('select').val('percent');
                return;
            }

            row.remove();

            updateIndexes();
        }

        $('#add-tier').on('click', function(e) {
            e.preventDefault();
            addNewTier();
        });

        $(document).on('click', '.remove-tier', function(e) {
            e.preventDefault();
            removeTier($(this));
        });

        updateIndexes();

        if ($('.bonus-tier-row').length === 0) {
            addNewTier();
        }
    });
</script>
<?php
    }
}