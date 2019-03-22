# Object

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masnathan/object.svg?style=flat-square)](https://packagist.org/packages/masnathan/object)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/MASNathan/Object/master.svg?style=flat-square)](https://travis-ci.org/MASNathan/Object)
[![Total Downloads](https://img.shields.io/packagist/dt/masnathan/object.svg?style=flat-square)](https://packagist.org/packages/masnathan/object)

Super Object that can handle everything you throw at him...

## Install

Via Composer

``` bash
$ composer require masnathan/object
```

## Usage

``` php
use MASNathan\SuperObject;

$object = new SuperObject();
$object->setMode('live');
$object->set('mode', 'live');
$object->mode = 'live';
$object['mode'] = 'live';

echo $object->getAppMode() // 'live'
echo $object->get('app_mode') // 'live'
echo $object->app_mode // 'live'
echo $object['mode'] // 'live'
```

So... let's suppose you have an array like this:

```php
$myBigDataArray = array(
	'details' => array(
		'first_name' => 'André',
		'last_name' => 'Filipe',
		'email' => 'andre.r.flip@gmail.com',
		'social' => array(
			'github' => 'https://github.com/MASNathan',
			'twitter' => 'https://twitter.com/masnathan'
		)
	),
	'account_info' => array(
		'admin' => true,
		'last_login' => 2015-06-13 13:37:00
	)
	'cart_items' => array(
		array('id' => 1337),
		// (...)
	)
);
```
Using the ```SuperObject``` class you can access it's information like this:

```php
$object = new SuperObject($myBigDataArray);

echo $object->getDetails()->getFirstName(); // 'André'
$object->getDetails()->isLastName('Roque'); // false
echo $object->getDetails()->getSocial()->getGithub(); // 'https://github.com/MASNathan'
echo $object->getDetails()->getSocial()->getFacebook(); // ''
$object->getAccountInfo()->isAdmin(); // true
$object->getAccountInfo()->unsetLastLogin(); // unsets $myBigDataArray['account_info']['last_login']

foreach ($object->getCartItems() as $item) {
	echo $item->getId(); // 1337
}
```

You can also retrive the contents of the SuperObject as an ```array``` or a ```StdClass```:

```php
$object->toArray(); // array( ... )
$object->toObject(); // StdClass( ... )
```
And even serialize/ deserialize the object

```php
unserialize(serialize($object));
// or as json
json_decode(json_encode($object));

```
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email andre.r.flip@gmail.com instead of using the issue tracker.

## Credits

- [André Filipe](https://github.com/masnathan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
