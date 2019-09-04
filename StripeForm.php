<?php

/**
 * @copyright Copyright Victor Demin, 2019
 * @license https://github.com/ruskid/yii2-stripe/LICENSE
 * @link https://github.com/ruskid/yii2-stripe#readme
 */

namespace ruskid\stripe;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @author Victor Demin <demmbox@gmail.com>
 */
class StripeForm extends \yii\widgets\ActiveForm {

    /**
     * @var string
     */
    public $stripeJs = 'https://js.stripe.com/v3/';

    /**
     * @var array
     */
    public $formEvents = [];

    /**
     * @var array
     */
    public $jsVars = [
        'stripe' => 'stripe',
        'elements' => 'elements',
        'card' => 'card',
        'form' => 'form',
    ];

    /**
     * @var array
     */
    public $elementsOptions = [];

    /**
     * @return string
     * @author Victor Demin
     */
    public function run() {
        $this->registerStripeScripts();
        $this->registerFormScripts();

        return parent::run();
    }

    /**
     * @author Victor Demin
     */
    protected function registerStripeScripts() {
        $view = $this->getView();
        $view->registerJsFile($this->stripeJs, ['position' => \yii\web\View::POS_HEAD]);

        $js = "var {$this->jsVars['stripe']} = Stripe('" . Yii::$app->stripe->publicKey . "');";

        if (!empty($this->elementsOptions)) {
            $js .= "var {$this->jsVars['elements']} = stripe.elements(" . Json::encode($this->elementsOptions) . ");";
        } else {
            $js .= "var {$this->jsVars['elements']} = stripe.elements();";
        }

        $view->registerJs($js, \yii\web\View::POS_BEGIN);
    }

    /**
     * @author Victor Demin
     */
    protected function registerFormScripts() {
        $js = "var {$this->jsVars['form']} = document.getElementById('" . $this->id . "');";

        foreach ($this->formEvents as $eventName => $handler) {
            $js .= "{$this->jsVars['form']}.addEventListener('{$eventName}', $handler);";
        }

        $this->view->registerJs($js, \yii\web\View::POS_END);
    }

    /**
     * @param array $elementOptions
     * @param array $eventHandlers
     * @param array $divOptions
     * @return string
     * @author Victor Demin
     */
    public function cardInput(array $elementOptions = [], array $eventHandlers = [], $divOptions = []) {
        $divOptions['id'] = $divOptions['id'] ?? $this->id . '_' . '_card_input';
        $this->mountElement('card', $divOptions['id'], $elementOptions, $eventHandlers);
        return Html::tag('div', '', $divOptions);
    }

    /**
     * @param array $elementOptions
     * @param array $eventHandlers
     * @param array $divOptions
     * @return string
     * @author Victor Demin
     */
    public function cardNumber(array $elementOptions = [], array $eventHandlers = [], $divOptions = []) {
        $divOptions['id'] = $divOptions['id'] ?? $this->id . '_' . '_card_number';
        $this->mountElement('cardNumber', $divOptions['id'], $elementOptions, $eventHandlers);
        return Html::tag('div', '', $divOptions);
    }

    /**
     * @param array $elementOptions
     * @param array $eventHandlers
     * @param array $divOptions
     * @return string
     * @author Victor Demin
     */
    public function cardExpiry(array $elementOptions = [], array $eventHandlers = [], $divOptions = []) {
        $divOptions['id'] = $divOptions['id'] ?? $this->id . '_' . '_card_expiry';
        $this->mountElement('cardExpiry', $divOptions['id'], $elementOptions, $eventHandlers);
        return Html::tag('div', '', $divOptions);
    }

    /**
     * @param array $elementOptions
     * @param array $eventHandlers
     * @param array $divOptions
     * @return string
     * @author Victor Demin
     */
    public function cardCvc(array $elementOptions = [], array $eventHandlers = [], $divOptions = []) {
        $divOptions['id'] = $divOptions['id'] ?? $this->id . '_' . '_card_cvc';
        $this->mountElement('cardCvc', $divOptions['id'], $elementOptions, $eventHandlers);
        return Html::tag('div', '', $divOptions);
    }

    /**
     * @param string $elementName
     * @param $mountID
     * @param array $elementOptions
     * @param array $events
     * @author Victor Demin
     */
    public function mountElement(string $elementName, $mountID, array $elementOptions = [], array $events = []) {
        $jsVarName = $this->jsVars[$elementName];

        if (!empty($elementOptions)) {
            $js = "var {$jsVarName} = elements.create('" . $elementName . "', " . Json::encode($elementOptions) . ");";
        } else {
            $js = "var {$jsVarName} = elements.create('" . $elementName . "');";
        }

        $js .= "{$jsVarName}.mount('#" . $mountID . "');";

        foreach ($events as $eventName => $handler) {
            $js .= "{$jsVarName}.addEventListener('{$eventName}', $handler);";
        }

        $this->view->registerJs($js, \yii\web\View::POS_END);
    }
}
