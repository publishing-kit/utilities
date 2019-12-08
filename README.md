# collection

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

PublishingKit/Collection is a simple PHP collection class, useful as an object-oriented alternative to PHP arrays.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/        
build/
docs/
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require publishing-kit/collection
```

## Usage

``` php
$skeleton = new PublishingKit\Collection();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email 450801+matthewbdaly@users.noreply.github.com instead of using the issue tracker.

## Credits

- [Matthew Daly][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/publishing-kit/collection.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/publishing-kit/collection/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/publishing-kit/collection.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/publishing-kit/collection.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/publishing-kit/collection.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/publishing-kit/collection
[link-travis]: https://travis-ci.org/publishing-kit/collection
[link-scrutinizer]: https://scrutinizer-ci.com/g/publishing-kit/collection/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/publishing-kit/collection
[link-downloads]: https://packagist.org/packages/publishing-kit/collection
[link-author]: https://github.com/matthewbdaly
[link-contributors]: ../../contributors
