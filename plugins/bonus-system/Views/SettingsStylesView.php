<?php

namespace BonusSystem\Views;

class SettingsStylesView
{
    public function render()
    {
        ?>
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