<?php

/**
 * @copyright Copyright Victor Demin, 2015
 * @license https://github.com/ruskid/yii2-stripe/LICENSE
 * @link https://github.com/ruskid/yii2-stripe#readme
 */

namespace ruskid\stripe;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Jquery Payment Library js.
 *
 * @author Victor Demin <demin@trabeja.com>
 */
class JqueryPaymentAsset extends AssetBundle {

    public $sourcePath = '@bower/jquery.payment';
    public $js = [
        'lib/jquery.payment.js',
    ];

}
