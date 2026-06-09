<?php
namespace BonusSystem\Views\Editor;

use BonusSystem\Models\TextBonus;

class TextsEditorView
{
    private TextEditorView $text_editor_view;

    public function __construct()
    {
        $this->text_editor_view = new TextEditorView();
    }

    /**
     * @param array<TextBonus> $text_bonuses
     * @return void
     */
    public function render(array $text_bonuses): void
    {
        ?>
        <tr>
            <th><?php _e('Текстові бонуси', 'bonus-system'); ?></th>
            <td>
                <div id="bonus-texts-container">
                    <?php
                    foreach ($text_bonuses as $index => $text_bonus) {
                        $this->text_editor_view->render($text_bonus, $index);
                    }
                    ?>
                </div>
                <button type="button" class="button button-primary" id="add-text-tier">
                    + <?php _e('Додати текстовий бонус', 'bonus-system'); ?>
                </button>
            </td>
        </tr>
        <?php
    }
}