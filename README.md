Yii2 Stripe wrapper.
==========
28/11/2014.
Simple and custom embedded <b>checkout</b> forms implemented. 
<p>https://stripe.com/docs/checkout#integration-simple</p>
<p>https://stripe.com/docs/checkout#integration-custom</p>

TODO:
I want to do custom forms and maybe add PayAction for a faster use.
<p>https://stripe.com/docs/tutorials/forms</p>

Installation
--------------------------

gonna do via composer as soon as i can...

Usage
--------------------------
Add a new component in main.php
```php
'components' => [
...
'stripe' => [
    'class' => 'ruskid\stripe\YiiStripe',
    'publicKey' => "pk_test_xxxxxxxxxxxxxxxxxxx",
    'privateKey' => "sk_test_xxxxxxxxxxxxxxxxxx",
],
...

```

Just call the widget in the view, it will automatically register the scripts.
Check stripe documentation for more options.
```php
<?= 
YiiStripeModal::widget([
    'action' => '/',
    'name' => 'Demo test',
    'description' => '2 widgets ($20.00)',
    'amount' => 2000,
    'image' => '/128x128.png'
]);
?>
```

Custom form is an extended version of simple form, but you can customize the button (see buttonOptions) and handle token as you want (tokenFunction).
```php
<?= 
YiiStripeCustomModal::widget([
    'action' => '/',
    'name' => 'Demo test',
    'description' => '2 widgets ($20.00)',
    'amount' => 2000,
    'image' => '/128x128.png',
    'buttonOptions' => [
        'class' => 'btn btn-lg btn-success',
    ],
    'tokenFunction' => new JsExpression('function(token) { alert("Here you should control your token."); }'),
    'openedFunction' => new JsExpression('function() { alert("Model opened"); }'),
    'closedFunction' => new JsExpression('function() { alert("Model closed"); }'),
]);
?>
```




