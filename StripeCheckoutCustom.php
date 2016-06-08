<?php

/**
 * @copyright Copyright Victor Demin, 2014
 * @license https://github.com/ruskid/yii2-stripe/LICENSE
 * @link https://github.com/ruskid/yii2-stripe#readme
 */

namespace ruskid\stripe;

use Yii;
use yii\helpers\Html;
use yii\web\JsExpression;

/**
 * Yii stripe custom form checkout class.
 * https://stripe.com/docs/checkout#integration-custom
 *
 * @author Victor Demin <demmbox@gmail.com>
 */
class StripeCheckoutCustom extends StripeCheckout {

    /**
     * Custom button options
     * @var array
     */
    public $buttonOptions = ['class' => 'btn btn-lg btn-success'];

    /**
     * @var boolean whether the button label should be HTML-encoded.
     */
    public $encodeLabel = true;

    /**
     * @see Stripe
     * The callback to invoke when the Checkout process is complete. function(token) token is the token object created.
     * token.id can be used to create a charge or customer. token.email contains the email address entered by the user.
     *
     * new JsExpression('function(token) {
     *      alert(token.id);
     *      alert(token.email);
     * }');
     *
     * @var JsExpression
     */
    public $tokenFunction;

    /**
     * @see Stripe. function() The callback to invoke when Checkout is opened (not supported in IE6 and IE7).
     * @var JsExpression
     */
    public $openedFunction;

    /**
     * @see Stripe. function() The callback to invoke when Checkout is closed (not supported in IE6 and IE7).
     * @var JsExpression
     */
    public $closedFunction;

    private static $handlerRegistered = false;

    /**
     * @see Init extension default
     */
    public function init() {
        if (!isset($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->getId();
        }
        if (!isset($this->tokenFunction)) {
            $this->tokenFunction = new JsExpression('function(token) { alert("Define your token handler"); }');
        }
        if (!isset($this->openedFunction)) {
            $this->openedFunction = new JsExpression('function() { }');
        }
        if (!isset($this->closedFunction)) {
            $this->closedFunction = new JsExpression('function() { }');
        }
        parent::init();
    }

    /**
     * Will show the Stripe's simple form modal
     */
    public function run() {
        $this->registerScripts();
        echo $this->generateButton();
    }

    /**
     * Will register the scripts.
     */
    private function registerScripts() {
        $view = $this->getView();

        $view->registerJsFile($this->stripeJs, ['position' => \yii\web\View::POS_END]);

        if (!self::$handlerRegistered) {
            $js = "var handler = StripeCheckout.configure({
                key: '" . Yii::$app->stripe->publicKey . "'
            });";
            $view->registerJs($js);
            $js = 'jQuery("window").on("popstate", function(e) {
                handler.close();
            });';
            $view->registerJs($js);

            self::$handlerRegistered = true;
        }

        $js = 'jQuery("#' . $this->buttonOptions['id'] . '").on("click", function(e) {
                    handler.open({
                        name: "' . $this->name . '",
                        description: "' . $this->description . '",
                        amount: ' . $this->amount . ',
                        image: "' . $this->image . '",
                        currency: "' . $this->currency . '",
                        panelLabel: "' . $this->panelLabel . '",
                        zipCode: "' . $this->validateZipCode . '",
                        email: "' . $this->userEmail . '",
                        allowRememberMe: "' . $this->allowRemember . '",
                        token: ' . $this->tokenFunction . ',
                        opened: ' . $this->openedFunction . ',
                        closed: ' . $this->closedFunction . '
                    });
                    e.preventDefault();
         });';
        $view->registerJs($js);

    }

    /**
     * Will generate the pay button
     * @return string
     */
    private function generateButton() {
        return Html::tag('button', $this->encodeLabel ? Html::encode($this->label) : $this->label, $this->buttonOptions);
    }

}
