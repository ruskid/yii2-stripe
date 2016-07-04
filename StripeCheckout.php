<?php

/**
 * @copyright Copyright Victor Demin, 2014
 * @license https://github.com/ruskid/yii2-stripe/LICENSE
 * @link https://github.com/ruskid/yii2-stripe#readme
 */

namespace ruskid\stripe;

use Yii;
use yii\helpers\Html;

/**
 * Yii stripe simple form checkout class.
 * https://stripe.com/docs/checkout#integration-simple
 *
 * @author Victor Demin <demmbox@gmail.com>
 */
class StripeCheckout extends \yii\base\Widget {

    /**
     * Form's action that will perform a charge
     * @var string form's action url
     */
    public $action = "/";

    /**
     * Additional options to be added to the opening form-tag.
     * @var array additional form-tag options.
     * @see Html::beginForm()
     */
    public $formOptions = [];

    /**
     * @see Stripe. The amount (in cents) that's shown to the user.
     * Note that you will still have to explicitly include it when you create a charge using the Stripe API.
     * @var integer Stripe's amount
     */
    public $amount;

    /**
     * @see Stripe's javascript location
     * @var string url to stripe's javascript
     */
    public $stripeJs = "https://checkout.stripe.com/checkout.js";

    /**
     * @see Stripe. The name of your company or website.
     * @var string Stripe's modal name
     */
    public $name = "Demo Site";

    /**
     * @see Stripe. A description of the product or service being purchased.
     * @var string Stripe's modal description
     */
    public $description = "2 widgets ($20.00)";

    /**
     * @see Stripe. A relative URL pointing to a square image of your brand or product.
     * The recommended minimum size is 128x128px.
     * @var string Stripe's modal image
     */
    public $image = "/128x128.png";

    /**
     * @see Stripe. The currency of the amount (3-letter ISO code). The default is USD.
     * @var string currency
     */
    public $currency;

    /**
     * @see Stripe. The text to be shown on the default blue button.
     * @var string label
     */
    public $label = 'Pay';

    /**
     * @see Stripe. The label of the payment button in the Checkout form (e.g. “Subscribe”, “Pay {{amount}}”, etc.).
     * If you include {{amount}}, it will be replaced by the provided amount.
     * Otherwise, the amount will be appended to the end of your label.
     * @var string
     */
    public $panelLabel;

    /**
     * @see Stripe. Specify whether Checkout should validate the billing ZIP code (true or false).
     * The default is false.
     * @var boolean
     */
    public $validateZipCode;

    /**
     * @see Stripe. If you already know the email address of your user, you can provide it to Checkout to be pre-filled.
     * @var string user's email
     */
    public $userEmail;

    /**
     * @see Stripe. Specify whether to include the option to "Remember Me" for future purchases (true or false).
     * The default is true.
     * @var boolean
     */
    public $allowRemember;

    /**
     * @see Stripe. Specify whether Checkout should collect the user's billing address (true or false).
     * The default is false.
     * @var boolean
     */
    public $collectBillingAddress;

    const BUTTON_CLASS = 'stripe-button';

    /**
     * @see Init extension default
     */
    public function init() {
        StripeHelper::prepareBoolean($this->allowRemember);
        StripeHelper::prepareBoolean($this->validateZipCode);
        StripeHelper::prepareBoolean($this->collectBillingAddress);
        parent::init();
    }

    /**
     * Will show the Stripe's simple form modal
     */
    public function run() {
        echo $this->generateStripeForm();
    }

    /**
     * Will generate the stripe form
     * @return string the generated stripe's modal form
     */
    private function generateStripeForm() {
        return Html::beginForm($this->action, 'POST', $this->formOptions)
            . $this->generateScriptTag()
            . Html::endForm();
    }

    /**
     * Will generate Stripe script tag with passed parameters
     * @return string the generated script tag
     */
    private function generateScriptTag() {
        return Html::script('', [
                    'src' => $this->stripeJs,
                    'data-key' => Yii::$app->stripe->publicKey,
                    'data-amount' => $this->amount,
                    'data-name' => $this->name,
                    'data-description' => $this->description,
                    'data-image' => $this->image,
                    'data-currency' => $this->currency,
                    'data-panel-label' => $this->panelLabel,
                    'data-zip-code' => $this->validateZipCode,
                    'data-email' => $this->userEmail,
                    'data-label' => $this->label,
                    'data-allow-remember-me' => $this->allowRemember,
                    'data-billing-address' => $this->collectBillingAddress,
                    'class' => self::BUTTON_CLASS,
        ]);
    }

}
