=== Disallow Pwned Password ===

Contributors: itinerisltd, tangrufus
Tags: authentication, have-i-been-pwned, hibp, password, security, woocommerce
Requires at least: 4.9.8
Tested up to: 5.0.3
Requires PHP: 7.0
Stable tag: 0.3.2
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Disallow WordPress and WooCommerce users using pwned passwords.

== Description ==

## Goal

Spoiler Alert: **User passwords never leave your server, not even in hashed form**.

Although reusing passwords is solely users' fault but when evil attackers brute forced users' passwords, and stole all their personal information or spent users' hard earn money through your site. **Those lazy users blame you**, the site owner/developer.

> When processing requests to establish and change memorized secrets, verifiers SHALL compare the prospective secrets against a list that contains values known to be commonly-used, expected, or compromised. For example,...
>
> - Passwords obtained from previous breach corpuses
>
> -- [NIST Digital Identity Guidelines](https://pages.nist.gov/800-63-3/sp800-63b.html)

This plugin's solely purpose is to **disallow WordPress and WooCommerce users reusing passwords listed in [Have I Been Pwned](https://haveibeenpwned.com/) database**.

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

## For Developers

Fork the plugin on [GitHub](https://github.com/ItinerisLtd/disallow-pwned-passwords).

== Frequently Asked Questions ==

### What are the minimum requirements?

- PHP v7.0
- WordPress v4.9.8
- **(Optional)** WooCommerce v3.4.4

### Did you just send all the passwords to someone else?

No. **User passwords never leave your server, not even in hashed form**.

### How do you compare user passwords with the 6,493,641,194 pwned ones?

Curious users can learn more from:

- [I've Just Launched "Pwned Passwords" V2 With Half a Billion Passwords for Download](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2/#cloudflareprivacyandkanonymity)
- [Validating Leaked Passwords with k-Anonymity](https://blog.cloudflare.com/validating-leaked-passwords-with-k-anonymity/)

Paranoia users should check the [plugin implementation](https://github.com/ItinerisLtd/disallow-pwned-passwords/tree/master/src).

### What to do if I don't trust haveibeenpwned.com?

[Troy Hunt](https://www.troyhunt.com) is a well-kown security expert. You should trust him more than me (the plugin author).
Anyways, you can replace the default API client with yours:

`
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
`

This plugin uses [league/container](https://packagist.org/packages/league/container). Learn more from [its documents](http://container.thephpleague.com/3.x/).

### What to do if I don't trust the plugin author?

Good question! You shouldn't blindly trust any random security guide/plugin from the scary internet - including this one!

Review the [plugin implementation](https://github.com/ItinerisLtd/disallow-pwned-passwords/tree/master/src).

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

`
add_action('wp_print_scripts', function () {
    wp_dequeue_script('wc-password-strength-meter');
}, 10000);
`

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

### Besides wp.org, where can I give a ★★★★★ review?

Thanks! Glad you like it. It's important to let my boss knows somebody is using this project. Please consider:

- give ★★★★★ reviews on [wp.org](https://wordpress.org/support/plugin/disallow-pwned-passwords/reviews/#new-post)
- tweet something good with mentioning [@itineris_ltd](https://twitter.com/itineris_ltd) and [@TangRufus](https://twitter.com/tangrufus)
- ️️★ star this [Github repo](https://github.com/ItinerisLtd/disallow-pwned-passwords)
- watch this [Github repo](https://github.com/ItinerisLtd/disallow-pwned-passwords)
- write blog posts
- submit pull requests
- [hire Itineris](https://www.itineris.co.uk/services/)

### Where to report security related issues?

If you discover any security related issues, please email [hello@itineris.co.uk](mailto:hello@itineris.co.uk) instead of using the issue tracker.

== Screenshots ==

1. WordPress
1. WooCommerce

== Changelog ==

Please see [CHANGELOG](https://github.com/ItinerisLtd/disallow-pwned-passwords/blob/master/CHANGELOG.md) for more information on what has changed recently.
