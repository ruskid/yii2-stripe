Yii2 Stripe Wrapper.
==========
Installation
--------------------------

The preferred way to install this extension is through http://getcomposer.org/download/.

Either run

```sh
php composer.phar require ruskid/yii2-stripe "dev-master"
```

or add

```json
"ruskid/yii2-stripe": "dev-master"
```

to the require section of your `composer.json` file.


Usage
--------------------------
Add a new component in main.php
```php
'components' => [
...
'stripe' => [
    'class' => 'ruskid\stripe\Stripe',
    'publicKey' => "pk_test_xxxxxxxxxxxxxxxxxxx",
    'privateKey' => "sk_test_xxxxxxxxxxxxxxxxxx",
],
...

```

To render simple checkout form just call the widget in the view, it will automatically register the scripts.
Check stripe documentation for more options.
```php
use ruskid\stripe\StripeCheckout;

<?=
StripeCheckout::widget([
    'action' => '/',
    'name' => 'Demo test',
    'description' => '2 widgets ($20.00)',
    'amount' => 2000,
    'image' => '/128x128.png',
]);
?>
```

Custom checkout form is an extended version of simple form, but you can customize the button (see buttonOptions) and handle token as you want (tokenFunction).
```php
use ruskid\stripe\StripeCheckoutCustom;

<?=
StripeCheckoutCustom::widget([
    'action' => '/',
    'name' => 'Demo test',
    'description' => '2 widgets ($20.00)',
    'amount' => 2000,
    'image' => '/128x128.png',
    'buttonOptions' => [
        'class' => 'btn btn-lg btn-success',
    ],
    'tokenFunction' => new JsExpression('function(token) {
                alert("Here you should control your token.");
    }'),
    'openedFunction' => new JsExpression('function() {
                alert("Model opened");
    }'),
    'closedFunction' => new JsExpression('function() {
                alert("Model closed");
    }'),
]);
?>
```
StripeFormV2 - is getting depreciated on 14 September 2019

You can use new StripeForm.php. But I don't see it so useful

```php
use ruskid\stripe\StripeForm;

 $form = StripeForm::begin([
     'action' => Url::toRoute('payment/create'),
     'options' => [
         'autocomplete' => 'on',
         'data-secret' => 'payment intent client secret',
     ],
     'elementsOptions' => [
         'locale' => 'es' // stripe elements language
     ],
     'formEvents' => [
         'beforeSubmit' => 'what ever form events',
         'submit' => new JsExpression('function(event) {
              // you can define as you want
              // stripe.handleCardPayment()
              // stripe.createToken()
         }'),
     ]
 ]);

echo $form->cardInput([
    'hidePostalCode' => true,
    'style' => [
        'base' => [
            'color' => 'blue',
        ],
        'invalid' => [
            'color' => 'red',
            'iconColor' => 'red'
        ]
    ],
], [
    'change' => new JsExpression('function(event) {        
        if (event.error) {
            alert(event.error.message);
        } else {
           // input is good
        }
    }')
]);

echo $form->cardNumber();
echo $form->cardExpiry();
echo $form->cardCvc();

echo Html::submitButton('Submit');

StripeForm::end();
```
