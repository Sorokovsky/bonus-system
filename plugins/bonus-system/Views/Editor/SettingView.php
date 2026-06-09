<?php

namespace BonusSystem\Views\Editor;

use BonusSystem\Models\SettingsModel;
use BonusSystem\Services\SettingsService;

class SettingView
{
    private SalesEditorView $sales_editor_view;
    private SettingsScriptsView $settings_scripts_view;
    private SettingsStylesView $settings_styles_view;

    public function __construct()
    {
        $this->sales_editor_view = new SalesEditorView();
        $this->settings_scripts_view = new SettingsScriptsView();
        $this->settings_styles_view = new SettingsStylesView();
    }

    public function render(SettingsModel $settings): void
    {
        ?>
        <div class="wrap">
            <h1><?php _e('Система бонусів', 'bonus-system') ?></h1>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields(SettingsService::OPTION_NAME);
                do_settings_sections(SettingsService::OPTION_NAME);
                submit_button('Зберегти налаштування', 'bonus-system');
                ?>
            </form>
        </div>
        <?php
        $this->settings_scripts_view->render($settings->get_sales());
        $this->settings_styles_view->render();
    }

    public function render_sales_fields(SettingsModel $settings): void
    {
        $this->sales_editor_view->render($settings->get_sales());
    }
}