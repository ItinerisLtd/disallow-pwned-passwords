# Disallow Pwned Password

[![CircleCI](https://circleci.com/gh/ItinerisLtd/disallow-pwned-passwords.svg?style=svg)](https://circleci.com/gh/ItinerisLtd/disallow-pwned-passwords)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ItinerisLtd/disallow-pwned-passwords/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ItinerisLtd/disallow-pwned-passwords/?branch=master)
[![GitHub License](https://img.shields.io/github/license/itinerisltd/disallow-pwned-passwords.svg)](https://github.com/ItinerisLtd/disallow-pwned-passwords/blob/master/LICENSE)
[![Hire Itineris](https://img.shields.io/badge/Hire-Itineris-ff69b4.svg)](https://www.itineris.co.uk/contact/)

Packagist:
[![Packagist Version](https://img.shields.io/packagist/v/itinerisltd/disallow-pwned-passwords.svg)](https://packagist.org/packages/itinerisltd/disallow-pwned-passwords)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/itinerisltd/disallow-pwned-passwords.svg)](https://packagist.org/packages/itinerisltd/disallow-pwned-passwords)
[![Packagist Downloads](https://img.shields.io/packagist/dt/itinerisltd/disallow-pwned-passwords.svg)](https://packagist.org/packages/itinerisltd/disallow-pwned-passwords)

WordPress:
[![Wordpress Plugin Version](https://img.shields.io/wordpress/plugin/v/disallow-pwned-passwords.svg?style=flat)](https://wordpress.org/plugins/disallow-pwned-passwords/)
[![Wordpress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/disallow-pwned-passwords.svg?style=flat)](https://wordpress.org/plugins/disallow-pwned-passwords/)
[![Wordpress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/disallow-pwned-passwords.svg?style=flat)](https://wordpress.org/plugins/disallow-pwned-passwords/)
[![Wordpress Plugin Rating](https://img.shields.io/wordpress/plugin/rating/disallow-pwned-passwords.svg?style=flat)](https://wordpress.org/support/plugin/disallow-pwned-passwords/reviews/#new-post)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/disallow-pwned-passwords.svg?style=flat)](https://wordpress.org/plugins/disallow-pwned-passwords/advanced/)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Goal](#goal)
- [Explain It Like I'm Five](#explain-it-like-im-five)
- [Minimum Requirements](#minimum-requirements)
- [Installation](#installation)
  - [Composer (Recommended)](#composer-recommended)
  - [WP CLI](#wp-cli)
  - [Classic](#classic)
- [Usage](#usage)
- [Performance](#performance)
- [FAQ](#faq)
  - [Did you just send all the passwords to someone else?](#did-you-just-send-all-the-passwords-to-someone-else)
  - [How do you compare user passwords with the 6,493,641,194 pwned ones?](#how-do-you-compare-user-passwords-with-the-6493641194-pwned-ones)
  - [What to do if I don't trust haveibeenpwned.com?](#what-to-do-if-i-dont-trust-haveibeenpwnedcom)
  - [What to do if I don't trust the plugin author?](#what-to-do-if-i-dont-trust-the-plugin-author)
  - [I have installed this plugin. Does it mean my WordPress site is *unhackable*?](#i-have-installed-this-plugin-does-it-mean-my-wordpress-site-is-unhackable)
  - [Can strong passwords been pwned?](#can-strong-passwords-been-pwned)
  - [How to disable WooCommerce password strength meter?](#how-to-disable-woocommerce-password-strength-meter)
  - [Will you add support for older PHP versions?](#will-you-add-support-for-older-php-versions)
  - [It looks awesome. Where can I find some more goodies like this?](#it-looks-awesome-where-can-i-find-some-more-goodies-like-this)
  - [Besides wp.org, where can I give a :star::star::star::star::star: review?](#besides-wporg-where-can-i-give-a-starstarstarstarstar-review)
- [Alternatives](#alternatives)
- [Testing](#testing)
- [Feedback](#feedback)
- [Change Log](#change-log)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Goal

Spoiler Alert: **User passwords never leave your server, not even in hashed form**.

Although reusing passwords is solely users' fault but when evil attackers brute forced users' passwords, and stole all their personal information or spent users' hard earn money through your site. **Those lazy users blame you**, the site owner/developer.

> When processing requests to establish and change memorized secrets, verifiers SHALL compare the prospective secrets against a list that contains values known to be commonly-used, expected, or compromised. For example,...
>
> - Passwords obtained from previous breach corpuses
>
> -- [NIST Digital Identity Guidelines](https://pages.nist.gov/800-63-3/sp800-63b.html)

This plugin's solely purpose is to **disallow WordPress and WooCommerce users reusing passwords listed in [Have I Been Pwned](https://haveibeenpwned.com/) database**.

## Explain It Like I'm Five

- [Troy Hunt](https://www.troyhunt.com), a well-kown security expert, collected 6,493,641,194 (and counting) pwned passwords from previous security breaches
- Pwned passwords stored as SHA-1 hashes on haveibeenpwned.com
- Whenever WordPress / WooCommerce users attempt to change their passwords, this plugin hashes the user password
- Take the first 5 characters from the hash
- Ask haveibeenpwned.com for all pwned passwords with the same first 5 hash characters
- Check how many times the user password appears on the have I been pwned database
- Disallow the password change if it has been pwned

Users aged older than five could learn more from:

- [Have I Been Pwned's FAQs](https://haveibeenpwned.com/FAQs)
- [Why SHA-1 was chosen in the Pwned Passwords](https://www.troyhunt.com/introducing-306-million-freely-downloadable-pwned-passwords/)
- [I've [Troy Hunt] Just Launched "Pwned Passwords" V2 With Half a Billion Passwords for Download](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2/#cloudflareprivacyandkanonymity)
- [Validating Leaked Passwords with k-Anonymity](https://blog.cloudflare.com/validating-leaked-passwords-with-k-anonymity/)

## Minimum Requirements

- PHP v7.0
- WordPress v4.9.8
- **(Optional)** WooCommerce v3.4.4

## Installation

### Composer (Recommended)

```sh-session
$ composer require itinerisltd/disallow-pwned-passwords
```

### WP CLI

```sh-session
$ wp plugin install disallow-pwned-passwords --activate
```

### Classic

Download the plugin zip file from https://wordpress.org/plugins/disallow-pwned-passwords/
Then, follow [https://codex.wordpress.org/Managing_Plugins#Installing_Plugins](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

## Usage

Activate and forget.

This plugin intercepts when:

- creating new users on `/wp-admin/user-new.php`
- changing other users' passwords on `/wp-admin/user-edit.php`
- changing your password on `/wp-admin/profile.php`
- new user registration on `/wp-login.php?action=rp`

Additional interceptions if WooCommerce is installed:

- [`WC_Form_Handler::process_reset_password`](https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-form-handler.php) on Home » My account » Lost password
- [`WC_Form_Handler::save_account_details`](https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-form-handler.php) on Home » My account » Account details
- [`WC_Form_Handler::process_registration`](https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-form-handler.php) on Home » My account
- [`WC_Checkout::validate_checkout`](https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-checkout.php) on Home » Checkout

## Performance

By default, this plugin caches Have I Been Pwned API responses for 1 week using [WP Object Cache](https://codex.wordpress.org/Class_Reference/WP_Object_Cache).

If you don't have a [persistent cache plugin](https://codex.wordpress.org/Class_Reference/WP_Object_Cache#Persistent_Caching), it has no effect and doesn't cache anything.

In rare cases, persistent cache plugins might not be compatible, you can disable by:

```php
<?php

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use League\Container\Container;


add_action('i_dpp_register', function (Container $container): void {
    $container->add(ClientInterface::class, Client::class);
});
```

## FAQ

### Did you just send all the passwords to someone else?

No. **User passwords never leave your server, not even in hashed form**.

### How do you compare user passwords with the 6,493,641,194 pwned ones?

Curious users can learn more from:

- [I've Just Launched "Pwned Passwords" V2 With Half a Billion Passwords for Download](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2/#cloudflareprivacyandkanonymity)
- [Validating Leaked Passwords with k-Anonymity](https://blog.cloudflare.com/validating-leaked-passwords-with-k-anonymity/)

Paranoia users should check the [plugin implementation](./src).

### What to do if I don't trust haveibeenpwned.com?

[Troy Hunt](https://www.troyhunt.com) is a well-kown security expert. You should trust him more than me (the plugin author).
Anyways, you can replace the default API client with yours:

```php
<?php

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use League\Container\Container;

class YourCustomClient implements ClientInterface
{
    // Your implementation.
}

add_action('i_dpp_register', function (Container $container): void {
    $container->add(ClientInterface::class, YourCustomClient::class);
});
```

This plugin uses [league/container](https://packagist.org/packages/league/container). Learn more from [its documents](http://container.thephpleague.com/3.x/).

### What to do if I don't trust the plugin author?

Good question! You shouldn't blindly trust any random security guide/plugin from the scary internet - including this one!

Review the [plugin implementation](./src).

### I have installed this plugin. Does it mean my WordPress site is *unhackable*?

No website is *unhackable*.

To have a secure WordPress site, you have to keep all these up-to-date:

- WordPress core
- PHP
- this plugin
- all other WordPress themes and plugins
- everything on the server
- other security practices
- your mindset

Strongly recommended:

- [WP Password Argon Two](https://github.com/TypistTech/wp-password-argon-two) - Securely store WordPress user passwords in database with Argon2i hashing and SHA-512 HMAC using PHP's native functions
- [WP Cloudflare Guard](https://wordpress.org/plugins/wp-cloudflare-guard/) - Connecting WordPress with Cloudflare firewall, protect your WordPress site at DNS level. Automatically create firewall rules to block dangerous IPs
- [Two-Factor](https://wordpress.org/plugins/two-factor/)
- [wp-password-bcrypt](https://github.com/roots/wp-password-bcrypt)

### Can strong passwords been pwned?

Yes. Example:

- [`correct horse battery staple`](https://www.xkcd.com/936/)

### How to disable WooCommerce password strength meter?

For testing only, use at your own risk!

```php
add_action('wp_print_scripts', function () {
    wp_dequeue_script('wc-password-strength-meter');
}, 10000);
```

### Will you add support for older PHP versions?

Never! This plugin will only works on [actively supported PHP versions](https://secure.php.net/supported-versions.php).

Don't use it on **end of life** or **security fixes only** PHP versions.

Note: Current version supports PHP 7.0 because wordpress.org svn pre-commit hook rejects PHP 7.1+ syntax. However, you should not use PHP 7.0 because [it has reached **end of life** since 10 January 2019](https://secure.php.net/eol.php).

### It looks awesome. Where can I find some more goodies like this?

- Articles on [Itineris' blog](https://www.itineris.co.uk/blog/)
- More projects on [Itineris' GitHub profile](https://github.com/itinerisltd)
- More plugins on [Itineris](https://profiles.wordpress.org/itinerisltd/#content-plugins) and [TangRufus](https://profiles.wordpress.org/tangrufus/#content-plugins) wp.org profiles
- Follow [@itineris_ltd](https://twitter.com/itineris_ltd) and [@TangRufus](https://twitter.com/tangrufus) on Twitter
- Hire [Itineris](https://www.itineris.co.uk/services/) to build your next awesome site

### Besides wp.org, where can I give a :star::star::star::star::star: review?

Thanks! Glad you like it. It's important to let my boss knows somebody is using this project. Please consider:

- give :star::star::star::star::star: reviews on [wp.org](https://wordpress.org/support/plugin/disallow-pwned-passwords/reviews/#new-post)
- tweet something good with mentioning [@itineris_ltd](https://twitter.com/itineris_ltd) and [@TangRufus](https://twitter.com/tangrufus)
- ️️:star: star this [Github repo](https://github.com/ItinerisLtd/disallow-pwned-passwords)
- watch this [Github repo](https://github.com/ItinerisLtd/disallow-pwned-passwords)
- write blog posts
- submit [pull requests](https://github.com/ItinerisLtd/disallow-pwned-passwords)
- [hire Itineris](https://www.itineris.co.uk/services/)

## Alternatives

- [wp-haveibeenpwned](https://github.com/coenjacobs/wp-haveibeenpwned)
- [Passwords Evolved](https://github.com/carlalexander/passwords-evolved)
- [Forbid Pwned Passwords](https://github.com/heyitsmikeyv/forbid-pwned-passwords)
- [Pwned Password Checker](https://github.com/BenjaminNelan/PwnedPasswordChecker)

## Testing

```sh-session
$ composer test
$ composer phpstan:analyse
$ composer style:check
```

Pull requests without tests will not be accepted!

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please submit an [issue](https://github.com/ItinerisLtd/disallow-pwned-passwords/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change Log

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email hello@itineris.co.uk instead of using the issue tracker.

## Credits

[Disallow Pwned Password](https://github.com/ItinerisLtd/disallow-pwned-passwords) is a [Itineris Limited](https://www.itineris.co.uk/) project created by [Tang Rufus](https://typist.tech).

Full list of contributors can be found [here](https://github.com/ItinerisLtd/disallow-pwned-passwords/graphs/contributors).

Special thanks to [Troy Hunt](https://www.troyhunt.com/) whose [Have I been pwned database](https://haveibeenpwned.com/) makes this plugin possible. Also, the k-Anonymity validation is an awesome work of [Junade Ali](https://twitter.com/icyapril) from [Cloudflare](https://www.cloudflare.com/).

## License

[Disallow Pwned Password](https://github.com/ItinerisLtd/disallow-pwned-passwords) is licensed under the GPLv2 (or later) from the [Free Software Foundation](http://www.fsf.org/).
Please see [License File](LICENSE) for more information.
