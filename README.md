PHP Compatibility Coding Standard for PHP_CodeSniffer
=====================================================

This repository is a mirror of the [wimg/PHPCompatibility] repository.

The [wimg/PHPCompatibility] has Composer compatibility issues, this repository fixes this, by
adding the [wimg/PHPCompatibility] repository to a sub-directory using git subtree.

This repository only provides a composer.json package file and a update script.

## Usage

Using Composer (preferred method):

```bash
composer require --dev "frenck/php-compatibility"
```

Or modify your `composer.json` to include `frenck/php-compatibility` in the `require-dev` sections:

```json
{
  "name": "acme/my-project",
  "require": {
    "…": "*"
  },
  "require-dev": {
    "frenck/php-compatibility": "*"
  }
}
```

The original code is not modified and automatically synced to this mirror every 15 minutes.

[wimg/PHPCompatibility]: https://github.com/wimg/PHPCompatibility
