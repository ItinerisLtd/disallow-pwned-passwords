# Disallow Pwned Password

[![Packagist Version](https://img.shields.io/packagist/v/itinerisltd/disallow-pwned-passwords.svg)](https://packagist.org/packages/itinerisltd/disallow-pwned-passwords)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/itinerisltd/disallow-pwned-passwords.svg)](https://packagist.org/packages/itinerisltd/disallow-pwned-passwords)
[![Packagist Downloads](https://img.shields.io/packagist/dt/itinerisltd/disallow-pwned-passwords.svg)](https://packagist.org/packages/itinerisltd/disallow-pwned-passwords)
[![GitHub License](https://img.shields.io/github/license/itinerisltd/disallow-pwned-passwords.svg)](https://github.com/ItinerisLtd/disallow-pwned-passwords/blob/master/LICENSE)
[![Hire Itineris](https://img.shields.io/badge/Hire-Itineris-ff69b4.svg)](https://www.itineris.co.uk/contact/)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Goal](#goal)
- [Explain It Like I'm Five](#explain-it-like-im-five)
- [Minimum Requirements](#minimum-requirements)
- [Installation](#installation)
- [Usage](#usage)
- [FAQ](#faq)
  - [Did you just send all the passwords to someone else?](#did-you-just-send-all-the-passwords-to-someone-else)
  - [How do you compare user passwords with the 5,371,313,595 pwned ones?](#how-do-you-compare-user-passwords-with-the-5371313595-pwned-ones)
  - [What to do if I don't trust haveibeenpwned.com?](#what-to-do-if-i-dont-trust-haveibeenpwnedcom)
  - [What to do if I don't trust the plugin author?](#what-to-do-if-i-dont-trust-the-plugin-author)
  - [I have installed this plugin. Does it mean my WordPress site is *unhackable*?](#i-have-installed-this-plugin-does-it-mean-my-wordpress-site-is-unhackable)
  - [Can strong passwords been pwned?](#can-strong-passwords-been-pwned)
  - [How to disable WooCommerce password strength meter?](#how-to-disable-woocommerce-password-strength-meter)
  - [Will you add support for older PHP versions?](#will-you-add-support-for-older-php-versions)
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
> * Passwords obtained from previous breach corpuses
>
> -- [NIST Digital Identity Guidelines](https://pages.nist.gov/800-63-3/sp800-63b.html)

This plugin's solely purpose is to **disallow WordPress and WooCommerce users reusing passwords listed in [have I been pwned](https://haveibeenpwned.com/) database**.

## Explain It Like I'm Five

* [Troy Hunt](https://www.troyhunt.com), a well-kown security expert, collected 5,371,313,595 (and counting) pwned passwords from previous security breaches
* Pwned passwords stored as SHA-1 hashes on haveibeenpwned.com
* Whenever WordPress / WooCommerce users attempt to change their passwords, this plugin hashes the user password
* Take the first 5 characters from the hash
* Ask haveibeenpwned.com for all pwned passwords with the same first 5 hash characters
* Check how many times the user password appears on the have I been pwned database
* Disallow the password change if it has been pwned

Users aged older than five could learn more from:

* [Have I Been Pwned's FAQs](https://haveibeenpwned.com/FAQs)
* [Why SHA-1 was chosen in the Pwned Passwords](https://www.troyhunt.com/introducing-306-million-freely-downloadable-pwned-passwords/)
* [I've [Troy Hunt] Just Launched "Pwned Passwords" V2 With Half a Billion Passwords for Download](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2/#cloudflareprivacyandkanonymity)
* [Validating Leaked Passwords with k-Anonymity](https://blog.cloudflare.com/validating-leaked-passwords-with-k-anonymity/)

## Minimum Requirements

- PHP v7.1
- WordPress v4.9.8
- **(Optional)** WooCommerce v3.4.4

## Installation

```bash
$ composer require itinerisltd/disallow-pwned-passwords
```

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

## FAQ

### Did you just send all the passwords to someone else?

No. **User passwords never leave your server, not even in hashed form**.

### How do you compare user passwords with the 5,371,313,595 pwned ones?

Curious users can learn more from:

* [I've Just Launched "Pwned Passwords" V2 With Half a Billion Passwords for Download](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2/#cloudflareprivacyandkanonymity)
* [Validating Leaked Passwords with k-Anonymity](https://blog.cloudflare.com/validating-leaked-passwords-with-k-anonymity/)

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

* WordPress core
* PHP
* this plugin
* all other WordPress themes and plugins
* everything on the server
* other security practices
* your mindset

Strongly recommended:

* [WP Password Argon Two](https://github.com/TypistTech/wp-password-argon-two) - Securely store WordPress user passwords in database with Argon2i hashing and SHA-512 HMAC using PHP's native functions
* [WP Cloudflare Guard](https://wordpress.org/plugins/wp-cloudflare-guard/) - Connecting WordPress with Cloudflare firewall, protect your WordPress site at DNS level. Automatically create firewall rules to block dangerous IPs
* [wp-password-bcrypt](https://github.com/roots/wp-password-bcrypt)

### Can strong passwords been pwned? 

Yes. Example:

- [`correct horse battery staple`](https://www.xkcd.com/936/)

### How to disable WooCommerce password strength meter?

For testing only, use at your own risk!

```php
add_action( 'wp_print_scripts', function () {
    wp_dequeue_script( 'wc-password-strength-meter' );
}, 10000);
```

### Will you add support for older PHP versions?

Never! This plugin will only works on [actively supported PHP versions](https://secure.php.net/supported-versions.php).

Don't use it on **end of life** or **security fixes only** PHP versions.

## Testing

```bash
$ composer test
$ composer check-style
```

Pull requests without tests will not be accepted!

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please submit an [issue](https://github.com/ItinerisLtd/disallow-pwned-password/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change Log

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email hello@itineris.co.uk instead of using the issue tracker.

## Credits

[Disallow Pwned Password](https://github.com/ItinerisLtd/disallow-pwned-passwords) is a [Itineris Limited](https://www.itineris.co.uk/) project created by [Tang Rufus](https://typist.tech).

Full list of contributors can be found [here](https://github.com/ItinerisLtd/disallow-pwned-password/graphs/contributors).

Special thanks to [Troy Hunt](https://www.troyhunt.com/) whose [Have I been pwned database](https://haveibeenpwned.com/) makes this plugin possible. Also, the k-Anonymity validation is an awesome work of [Junade Ali](https://twitter.com/icyapril) from [Cloudflare](https://www.cloudflare.com/).

## License

[Disallow Pwned Password](https://github.com/ItinerisLtd/disallow-pwned-password) is licensed under the GPLv2 (or later) from the [Free Software Foundation](http://www.fsf.org/).
Please see [License File](LICENSE) for more information.
