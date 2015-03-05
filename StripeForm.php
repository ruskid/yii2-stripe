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
 * Yii stripe custom form.
 * https://stripe.com/docs/tutorials/forms
 *
 * @author Victor Demin <demmbox@gmail.com>
 */
class StripeForm extends \yii\widgets\ActiveForm {

    /**
     * @see Stripe's javascript location
     * @var string url to stripe's javascript
     */
    public $stripeJs = 'https://js.stripe.com/v2/';

    /**
     * Js Expression that will handle the response.
     *
     * If not set the default behavior will be used:
     * function stripeResponseHandler(status, response) {
     *      var $form = $('#payment-form');
     *        if (response.error) {
     *           // Show the errors on the form
     *           $form.find('.payment-errors').text(response.error.message);
     *           $form.find('button').prop('disabled', false);
     *        } else {
     *           // response contains id and card, which contains additional card details
     *           var token = response.id;
     *           // Insert the token into the form so it gets submitted to the server
     *           $form.append($('<input type="hidden" name="stripeToken" />').val(token));
     *           // and submit
     *           $form.get(0).submit();
     *        }
     * }
     *
     * @var JsExpression
     */
    public $stripeResponseHandler;

    /**
     * Js Expression that will handle the request.
     *
     * If not set the default behavior will be used:
     * jQuery(function($) {
     *    $('#payment-form').submit(function(event) {
     *         var $form = $(this);
     *         // Disable the submit button to prevent repeated clicks
     *         $form.find('button').prop('disabled', true);
     *          Stripe.card.createToken($form, stripeResponseHandler);
     *          // Prevent the form from submitting with the default action
     *          return false;
     *      });
     *   });
     *
     * @var JsExpression
     */
    public $stripeRequestHandler;

    /**
     * Input id and name tags of the hidden token input that will be sent to PayAction.
     * @var string
     */
    public $tokenInputName = 'stripeToken';

    /**
     * If the default behavior for the response is used, then you can set the id of error's container.
     * Note! this property is useless if you set your own response handler.
     * @var string
     */
    public $errorContainerId = "payment-errors";

    /**
     * This will load jquery payment library which i find very useful. You can disable it by setting this property to false
     * https://github.com/stripe/jquery.payment. NOT IMPLEMENTED YET.
     * @var boolean
     */
    public $useJqueryPayment = false;

    //Stripe constants
    const NUMBER_ID = 'number';
    const CVC_ID = 'cvc';
    const MONTH_ID = 'exp-month';
    const YEAR_ID = 'exp-year';
    //Autofill spec. @see https://html.spec.whatwg.org/multipage/forms.html
    const AUTO_CC_ATTR = 'cc-number';
    const AUTO_EXP_ATTR = 'cc-exp'; //todo.
    const AUTO_MONTH_ATTR = 'cc-exp-month';
    const AUTO_YEAR_ATTR = 'cc-exp-year';

    /**
     * @see Init extension default
     */
    public function init() {
        parent::init();

        //Set default response behavior
        if (!isset($this->stripeResponseHandler)) {
            $this->stripeResponseHandler = 'function stripeResponseHandler(status, response) {
                    var $form = $("#' . $this->options['id'] . '");
                    if (response.error) {
                        $form.find("#' . $this->errorContainerId . '").text(response.error.message);
                        $form.find("button").prop("disabled", false);
                    } else {
                        var token = response.id;
                        $form.append($("<input type=\"hidden\" name=\"' . $this->tokenInputName . '\" id=\"' . $this->tokenInputName . '\" />").val(token));
                        $form.get(0).submit();
                    }
            };';
        }

        //Set default request behavior
        if (!isset($this->stripeRequestHandler)) {
            $this->stripeRequestHandler = 'jQuery(function($) {
                $("#' . $this->options['id'] . '").submit(function(event) {
                    var $form = $(this);
                    $form.find("button").prop("disabled", true);
                    Stripe.card.createToken($form, stripeResponseHandler);
                    return false;
                });
            });';
        }
    }

    /**
     * Will show the Stripe's simple form modal
     */
    public function run() {
        $this->registerFormScripts();
        if ($this->useJqueryPayment) {
            $this->registerJqueryPaymentScripts();
        }
    }

    /**
     * Will register mandatory javascripts to work
     */
    public function registerFormScripts() {
        $view = $this->getView();
        $view->registerJsFile($this->stripeJs, ['position' => \yii\web\View::POS_HEAD]);

        $js = "Stripe.setPublishableKey('" . Yii::$app->stripe->publicKey . "');";
        $view->registerJs($js, \yii\web\View::POS_BEGIN);

        //form scripts
        $view->registerJs($this->stripeResponseHandler, \yii\web\View::POS_READY);
        $view->registerJs($this->stripeRequestHandler, \yii\web\View::POS_READY);
    }

    /**
     * Will register jquery payment scripts and apply them to the inputs.
     */
    public function registerJqueryPaymentScripts() {
        $view = $this->getView();
        JqueryPaymentAsset::register($view);

        $js = "jQuery(function($) {
                $('input[data-stripe=" . self::NUMBER_ID . "]').payment('formatCardNumber');
                //$('input[data-stripe=" . self::CVC_ID . "]').payment('formatCardExpiry');
                $('input[data-stripe=" . self::CVC_ID . ").payment('formatCardCVC');
        });";
        $view->registerJs($js);
    }

    /**
     * Will generate card number input
     * @param array $options
     * @return string genetared input tag
     */
    public function numberInput($options = []) {
        if (empty($options)) {
            $options = [
                'class' => 'form-control',
                'autocomplete' => self::AUTO_CC_ATTR,
                'placeholder' => '•••• •••• •••• ••••',
                'required' => true,
                'type' => 'tel',
            ];
        } else {
            StripeHelper::secCheck($options);
        }
        $options['data-stripe'] = self::NUMBER_ID;
        return Html::input('text', null, null, $options);
    }

    /**
     * Will generate cvc input
     * @param array $options
     * @return string genetared input tag
     */
    public function cvcInput($options = []) {
        if (empty($options)) {
            $options = [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'placeholder' => '•••',
                'required' => true,
                'type' => 'tel',
            ];
        } else {
            StripeHelper::secCheck($options);
        }
        $options['data-stripe'] = self::CVC_ID;
        return Html::input('text', null, null, $options);
    }

    /**
     * Will generate year input
     * @param array $options
     * @return string genetared input tag
     */
    public function yearInput($options = []) {
        if (empty($options)) {
            $options = [
                'class' => 'form-control',
                'autocomplete' => self::AUTO_YEAR_ATTR,
                'placeholder' => '••••',
                'required' => true,
                'type' => 'tel',
            ];
        } else {
            StripeHelper::secCheck($options);
        }
        $options['data-stripe'] = self::YEAR_ID;
        return Html::input('text', null, null, $options);
    }

    /**
     * Will generate month input
     * @param array $options
     * @return string genetared input tag
     */
    public function monthInput($options = []) {
        if (empty($options)) {
            $options = [
                'class' => 'form-control',
                'autocomplete' => self::AUTO_MONTH_ATTR,
                'placeholder' => '••',
                'required' => true,
                'type' => 'tel',
            ];
        } else {
            StripeHelper::secCheck($options);
        }
        $options['data-stripe'] = self::MONTH_ID;
        return Html::input('text', null, null, $options);
    }

}
