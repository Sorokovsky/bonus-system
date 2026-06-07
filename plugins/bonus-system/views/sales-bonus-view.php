<?php
namespace Bonuses\Views;

use Override;

require_once "./view.php";

class SalesBonusView implements View
{
    #[Override]
    public function render(): string
    {
        return "<h1>SalesBonusView</h1>";
    }
}