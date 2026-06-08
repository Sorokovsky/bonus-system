<?php

namespace BonusSystem\Views;

use BonusSystem\Models\SalesBonus;

class SalesEditorView
{
    private SaleEditorView $sale_editor_view;

    public function __construct()
    {
        $this->sale_editor_view = new SaleEditorView();
    }

    /**
     * @param array<SalesBonus> $sales
     */
    public function render(array $sales) : void
    {
        ?>
        <tr>
            <th><?php _e('Рівні бонусів', 'bonus-system'); ?></th>
            <td>
                <div id="bonus-tiers-container">
                    <?php
                    foreach ($sales as $index => $sale) {
                        $this->sale_editor_view->render($sale, $index);
                    }
                    ?>
                </div>
                <button type="button" class="button button-primary" id="add-tier">
                    + <?php _e('Додати рівень', 'bonus-system'); ?>
                </button>
            </td>
        </tr>
        <?php
    }
}