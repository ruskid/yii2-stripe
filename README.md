Yii2 Stripe wrapper. (Widgets and helpers)
==========
28/11/2014.
Simple and custom embedded checkout forms implemented. 
https://stripe.com/docs/checkout#integration-simple
https://stripe.com/docs/checkout#integration-custom

TODO:
I want to do custom forms and maybe add PayAction for faster use.
https://stripe.com/docs/tutorials/forms

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
    'image' => '/128x128.png'
]);
?>
```




