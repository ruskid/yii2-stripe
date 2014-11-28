<?php

/**
 * @copyright Copyright Victor Demin, 2014
 * @license https://github.com/ruskid/yii-stripe/LICENSE
 * @link https://github.com/ruskid/yii-stripe#readme
 */

namespace ruskid\stripe;

/**
 * Yii Stripe helper class.
 *
 * @author Victor Demin <demmbox@gmail.com>
 */
class YiiStripeHelper {

    /**
     * If the value is boolean, then it must be in quotes.
     * @param boolean|string $value
     */
    public static function prepareBoolean(&$value) {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
    }

}
