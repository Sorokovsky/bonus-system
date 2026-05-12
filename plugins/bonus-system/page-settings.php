<?php

class PageSettings
{
    public function __construct()
    {
        add_action("admin_menu", [$this, "add_admin_menu"]);
        add_action("admin_init", [$this, "register_settings"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_admin_scripts"]);
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            "woocommerce",
            __("Налаштування бонусів", "bonus-system"),
            __("Бонусна система", "bonus-system"),
            'manage_options',
            "bonus-system-settings",
            [$this, "render_page"],
            30
        );
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'woocommerce_page_bonus-system-settings') {
            return;
        }

        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Зміна типу знижки
                $(document).on("change", ".discount-type", function() {
                    var row = $(this).closest(".bonus-tier");
                    var type = $(this).val();
                    
                    if (type === "percent") {
                        row.find(".discount-percent-field").show();
                        row.find(".discount-fixed-field").hide();
                    } else {
                        row.find(".discount-percent-field").hide();
                        row.find(".discount-fixed-field").show();
                    }
                });
                
                // Додавання нового рівня
                $(".add-bonus-tier").on("click", function() {
                    var currentCount = $("#bonus-tiers-list .bonus-tier").length;
                    var newIndex = currentCount;
                    
                    var template = $(".bonus-tier-template .bonus-tier").clone();
                    template.find(".tier-index").text(newIndex + 1);
                    
                    // Оновлюємо name атрибути
                    template.find("input, select").each(function() {
                        var name = $(this).attr("name");
                        if (name) {
                            name = name.replace(/__INDEX__/g, newIndex);
                            $(this).attr("name", name);
                        }
                    });
                    
                    // Очищаємо значення
                    template.find(".min-amount").val("");
                    template.find(".discount-percent-input").val("");
                    template.find(".discount-fixed-input").val("");
                    template.find(".discount-type").val("percent");
                    
                    // Показуємо правильне поле
                    template.find(".discount-percent-field").show();
                    template.find(".discount-fixed-field").hide();
                    
                    $("#bonus-tiers-list").append(template);
                });
                
                // Видалення рівня
                $(document).on("click", ".remove-tier", function() {
                    var currentCount = $("#bonus-tiers-list .bonus-tier").length;
                    if (currentCount > 1) {
                        $(this).closest(".bonus-tier").remove();
                    } else {
                        alert("Потрібен хоча б один рівень бонусів");
                    }
                });
                
                // Валідація перед відправкою
                $("form").on("submit", function() {
                    var isValid = true;
                    var amounts = [];
                    var errors = [];
                    
                    $("#bonus-tiers-list .bonus-tier").each(function(index) {
                        var minAmount = $(this).find(".min-amount").val();
                        var discountType = $(this).find(".discount-type").val();
                        var discountValue = discountType === "percent" ? 
                            $(this).find(".discount-percent-input").val() : 
                            $(this).find(".discount-fixed-input").val();
                        
                        if (!minAmount || minAmount <= 0) {
                            errors.push("Рівень " + (index + 1) + ": Мінімальна сума має бути більше 0");
                            isValid = false;
                        }
                        
                        if (!discountValue || discountValue <= 0) {
                            errors.push("Рівень " + (index + 1) + ": Значення знижки має бути більше 0");
                            isValid = false;
                        }
                        
                        if (discountType === "percent" && discountValue > 100) {
                            errors.push("Рівень " + (index + 1) + ": Відсоток знижки не може перевищувати 100%");
                            isValid = false;
                        }
                        
                        if (minAmount && amounts.includes(parseInt(minAmount))) {
                            errors.push("Рівень " + (index + 1) + ": Суми мають бути унікальними");
                            isValid = false;
                        }
                        if (minAmount) {
                            amounts.push(parseInt(minAmount));
                        }
                    });
                    
                    if (!isValid) {
                        alert("Помилки валідації:\\n" + errors.join("\\n"));
                    }
                    
                    return isValid;
                });
            });
        ');
    }

    public function register_settings()
    {
        register_setting(
            'bonus_system_settings_group',
            'bonus_system_tiers',
            [$this, 'sanitize_tiers']
        );

        register_setting(
            'bonus_system_settings_group',
            'bonus_system_free_shipping_tier',
            'sanitize_text_field'
        );

        add_settings_section(
            'bonus_system_main_section',
            __('Налаштування рівнів бонусів', 'bonus-system'),
            [$this, 'render_section_description'],
            'bonus_system_settings_page'
        );

        add_settings_field(
            'bonus_tiers',
            __('Рівні бонусів', 'bonus-system'),
            [$this, 'render_tiers_field'],
            'bonus_system_settings_page',
            'bonus_system_main_section'
        );

        add_settings_field(
            'free_shipping',
            __('Безкоштовна доставка', 'bonus-system'),
            [$this, 'render_free_shipping_field'],
            'bonus_system_settings_page',
            'bonus_system_main_section'
        );
    }

    public function render_section_description()
    {
        echo '<p>' . __('Налаштуйте рівні бонусної системи. Додайте рівні з мінімальною сумою та знижкою (відсоток або фіксована сума).', 'bonus-system') . '</p>';
        echo '<p><strong>' . __('Важливо:', 'bonus-system') . '</strong> ' . __('Всі поля обов\'язкові для заповнення. Мінімальні суми повинні бути унікальними.', 'bonus-system') . '</p>';
    }

    public function render_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('bonus_system_settings_group');
                do_settings_sections('bonus_system_settings_page');
                submit_button('Зберегти налаштування');
                ?>
            </form>
        </div>
        <?php
    }

    public function render_tiers_field()
    {
        $tiers = get_option('bonus_system_tiers', []);

        if (empty($tiers)) {
            $tiers = [
                ['min_amount' => '', 'discount_type' => 'percent', 'discount_percent' => '', 'discount_fixed' => '']
            ];
        }

        ?>
        <div class="bonus-tiers-container">
            <style>
                .bonus-tier {
                    background: #f9f9f9;
                    border: 1px solid #ddd;
                    padding: 15px;
                    margin-bottom: 15px;
                    border-radius: 4px;
                    position: relative;
                }

                .bonus-tier h4 {
                    margin-top: 0;
                    margin-bottom: 15px;
                    color: #23282d;
                }

                .bonus-tier input,
                .bonus-tier select {
                    margin-right: 10px;
                }

                .bonus-tier .remove-tier {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    color: #dc3232;
                    cursor: pointer;
                    background: none;
                    border: none;
                    font-size: 20px;
                    line-height: 1;
                }

                .bonus-tier .remove-tier:hover {
                    color: #a00;
                }

                .bonus-tier label {
                    display: inline-block;
                    width: 160px;
                    font-weight: 600;
                }

                .bonus-tier .small-text {
                    width: 100px;
                }

                .bonus-tier select {
                    width: 160px;
                }

                .discount-percent-field,
                .discount-fixed-field {
                    display: inline-block;
                }

                .add-bonus-tier {
                    margin-top: 10px;
                    background: #0073aa;
                    color: white;
                    border: none;
                    padding: 8px 15px;
                    border-radius: 3px;
                    cursor: pointer;
                }

                .add-bonus-tier:hover {
                    background: #005a87;
                }

                .bonus-tier-template {
                    display: none;
                }
            </style>

            <div id="bonus-tiers-list">
                <?php foreach ($tiers as $index => $tier): ?>
                    <div class="bonus-tier">
                        <button type="button" class="remove-tier">×</button>
                        <h4><?php _e('Рівень', 'bonus-system'); ?> <span class="tier-index"><?php echo $index + 1; ?></span></h4>

                        <label><?php _e('Мінімальна сума (₴):', 'bonus-system'); ?></label>
                        <input type="number" name="bonus_system_tiers[<?php echo $index; ?>][min_amount]"
                            value="<?php echo esc_attr($tier['min_amount']); ?>" class="small-text min-amount" step="1" min="0"
                            placeholder="100" />
                        <br><br>

                        <label><?php _e('Тип знижки:', 'bonus-system'); ?></label>
                        <select name="bonus_system_tiers[<?php echo $index; ?>][discount_type]" class="discount-type">
                            <option value="percent" <?php selected($tier['discount_type'], 'percent'); ?>>
                                <?php _e('Відсоток (%)', 'bonus-system'); ?>
                            </option>
                            <option value="fixed" <?php selected($tier['discount_type'], 'fixed'); ?>>
                                <?php _e('Фіксована сума (₴)', 'bonus-system'); ?>
                            </option>
                        </select>
                        <br><br>

                        <!-- Поле для відсотка - окреме ім'я -->
                        <div class="discount-percent-field"
                            style="display: <?php echo ($tier['discount_type'] == 'percent') ? 'inline-block' : 'none'; ?>">
                            <label><?php _e('Відсоток знижки (%):', 'bonus-system'); ?></label>
                            <input type="number" name="bonus_system_tiers[<?php echo $index; ?>][discount_percent]"
                                value="<?php echo isset($tier['discount_percent']) ? esc_attr($tier['discount_percent']) : ''; ?>"
                                class="small-text discount-percent-input" step="0.1" min="0" max="100" placeholder="10" />
                        </div>

                        <!-- Поле для фіксованої суми - окреме ім'я -->
                        <div class="discount-fixed-field"
                            style="display: <?php echo ($tier['discount_type'] == 'fixed') ? 'inline-block' : 'none'; ?>">
                            <label><?php _e('Фіксована знижка (₴):', 'bonus-system'); ?></label>
                            <input type="number" name="bonus_system_tiers[<?php echo $index; ?>][discount_fixed]"
                                value="<?php echo isset($tier['discount_fixed']) ? esc_attr($tier['discount_fixed']) : ''; ?>"
                                class="small-text discount-fixed-input" step="0.01" min="0" placeholder="50" />
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="add-bonus-tier">+ <?php _e('Додати рівень', 'bonus-system'); ?></button>

            <!-- Шаблон -->
            <div class="bonus-tier-template" style="display:none;">
                <div class="bonus-tier">
                    <button type="button" class="remove-tier">×</button>
                    <h4><?php _e('Рівень', 'bonus-system'); ?> <span class="tier-index"></span></h4>

                    <label><?php _e('Мінімальна сума (₴):', 'bonus-system'); ?></label>
                    <input type="number" name="bonus_system_tiers[__INDEX__][min_amount]" value="" class="small-text min-amount"
                        step="1" min="0" placeholder="100" />
                    <br><br>

                    <label><?php _e('Тип знижки:', 'bonus-system'); ?></label>
                    <select name="bonus_system_tiers[__INDEX__][discount_type]" class="discount-type">
                        <option value="percent"><?php _e('Відсоток (%)', 'bonus-system'); ?></option>
                        <option value="fixed"><?php _e('Фіксована сума (₴)', 'bonus-system'); ?></option>
                    </select>
                    <br><br>

                    <div class="discount-percent-field" style="display: inline-block;">
                        <label><?php _e('Відсоток знижки (%):', 'bonus-system'); ?></label>
                        <input type="number" name="bonus_system_tiers[__INDEX__][discount_percent]" value=""
                            class="small-text discount-percent-input" step="0.1" min="0" max="100" placeholder="10" />
                    </div>

                    <div class="discount-fixed-field" style="display: none;">
                        <label><?php _e('Фіксована знижка (₴):', 'bonus-system'); ?></label>
                        <input type="number" name="bonus_system_tiers[__INDEX__][discount_fixed]" value=""
                            class="small-text discount-fixed-input" step="0.01" min="0" placeholder="50" />
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_free_shipping_field()
    {
        $free_shipping_tier = get_option('bonus_system_free_shipping_tier', '');
        $tiers = get_option('bonus_system_tiers', []);
        ?>
        <select name="bonus_system_free_shipping_tier">
            <option value=""><?php _e('Не використовувати', 'bonus-system'); ?></option>
            <?php foreach ($tiers as $index => $tier):
                if (!empty($tier['min_amount'])): ?>
                    <option value="<?php echo esc_attr($tier['min_amount']); ?>" <?php selected($free_shipping_tier, $tier['min_amount']); ?>>
                        <?php echo sprintf(__('При досягненні %s ₴ (рівень %d)', 'bonus-system'), $tier['min_amount'], $index + 1); ?>
                    </option>
                <?php endif; endforeach; ?>
        </select>
        <p class="description">
            <?php _e('Автоматична безкоштовна доставка при досягненні вибраного рівня (опціонально)', 'bonus-system'); ?>
        </p>
        <?php
    }

    public function sanitize_tiers($input)
    {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        $seen_amounts = [];

        foreach ($input as $tier) {
            if (!isset($tier['min_amount']) || !isset($tier['discount_type'])) {
                continue;
            }

            $min_amount = absint($tier['min_amount']);
            $discount_type = ($tier['discount_type'] === 'fixed') ? 'fixed' : 'percent';

            // Отримуємо значення в залежності від типу
            if ($discount_type === 'percent') {
                $discount_value = isset($tier['discount_percent']) ? floatval($tier['discount_percent']) : 0;
            } else {
                $discount_value = isset($tier['discount_fixed']) ? floatval($tier['discount_fixed']) : 0;
            }

            if ($min_amount <= 0 || $discount_value <= 0) {
                continue;
            }

            if ($discount_type === 'percent' && $discount_value > 100) {
                continue;
            }

            if (!in_array($min_amount, $seen_amounts)) {
                $sanitized[] = [
                    'min_amount' => $min_amount,
                    'discount_type' => $discount_type,
                    'discount_percent' => ($discount_type === 'percent') ? $discount_value : '',
                    'discount_fixed' => ($discount_type === 'fixed') ? $discount_value : ''
                ];
                $seen_amounts[] = $min_amount;
            }
        }

        usort($sanitized, function ($a, $b) {
            return $a['min_amount'] - $b['min_amount'];
        });

        return $sanitized;
    }
}