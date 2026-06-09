<?php
namespace BonusSystem\Views\Client;

use BonusSystem\Models\Bonus;

class CartView
{

    /**
     * Summary of render
     * @param array<Bonus> $all_bonuses
     * @return void
     */
    public function render(array $all_bonuses): void
    {
        ?>
        <div class="bonus-cart-container">
            <h1>Кошик</h1>
            <?php echo WC()->cart->get_subtotal(); ?>
            <?php if (empty($all_bonuses)): ?>
                <p>Ваш кошик порожній</p>
            <?php else: ?>
                <pre><?php var_dump($all_bonuses); ?></pre>
            <?php endif; ?>
        </div>
        <?php
    }
}