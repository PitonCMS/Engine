# Security

This is not a comprehensive cybersecurity overview, but does include a review of PitonCMS options and features to consider when building a client website.

## Security Headers
Response headers can be set in `config.local.php` and will be added to the HTTP response.

Default PitonCMS headers are defined in `vendor/pitoncms/engine/config/config.default.php` and can be overriden in `config.local.php`. The default response headers are:

```php
/**
 * Response Headers
 *
 * Sets response headers dynamically at application runtime
 * Define any standard or custom header as a key:value, under the ['header'] array
 */
$config['header']['X-Frame-Options'] = 'DENY';
$config['header']['X-Content-Type-Options'] = 'nosniff';
$config['header']['Referrer-Policy'] = 'no-referrer-when-downgrade';
$config['header']['Feature-Policy'] = 'self';
$config['header']['X-XSS-Protection'] = '1; mode=block';
$config['header']['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
$config['header']['Content-Security-Policy']['default-src'] = "'self'";
$config['header']['Content-Security-Policy']['script-src'] = "'self' 'nonce' 'unsafe-inline' 'strict-dynamic'";
$config['header']['Content-Security-Policy']['style-src'] = "'self' 'unsafe-inline' 'strict-dynamic'";
$config['header']['Content-Security-Policy']['font-src'] = "'self' 'strict-dynamic'";
$config['header']['Content-Security-Policy']['img-src'] = "*";
$config['header']['Content-Security-Policy']['base-uri'] = "'none'";
// $config['header']['Content-Security-Policy']['report-uri'] = "";
```

For more on these headers and others see [MDN HTTP Headers](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers).

You can also add standard and custom headers by adding the header name and value under the `['header']` array in `config.local.php`:

```php
$config['header']['Breakfast'] = 'pancakes';
```

Note that two specific headers, `Strict-Transport-Security` and `Content-Security-Policy`, are treated slightly differently.

### Strict Transport Security
This header tells the browser that all requests to this site must be sent as HTTPS. You can specify how far in the future to honor this request by setting the `max-age` (in seconds), and each page view with this header pushes that date out further. `includeSubDomains` also forces HTTPS on subdomains.

Note, this header is only set when _not_ on localhost. If you are developing on a domain other than localhost you might want to disable this header in `config.local.php` by setting it to `false`.

```php
$config['header']['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
```

If your website is not HTTPS then set this policy to `false` in `config.local.php`.

### Content Security Policy
A Content Security Policy is a very powerful way to secure your website from XSS attacks and other forms of malicious code injection. It instructs the browser how to control what resources the user agent is allowed to load for a given page. See [MDN Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy) for more information.

A Content Security Policy may easily break your site if you include inline script or new external assets, unless you add the required policies and whitelisted assets, or use [nonces](#csp-nonce).

The default CSP sent by PitonCMS is inherently a strict policy, see [Strict CSP With Google](https://csp.withgoogle.com/docs/strict-csp.html) for an explanation of this policy.

```apache
Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-cqCwEKNQNtOH4FI10G3FtQ==' 'unsafe-inline' 'strict-dynamic'; style-src 'self' 'unsafe-inline' https://fonts.gstatic.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src *; base-uri 'none'
```

The CSP header can be set directly as a string in `config.local.php`:

```php
$config['header']['Content-Security-Policy'] = "default-src 'self'; script-src 'strict-dynamic'; img-src *";
```

Or, you can define each CSP policy directive as a sub-array of `$config['header']['Content-Security-Policy']` using `['directive'] = value`. To disable a default directive set that directive to false in `config.local.php`:

```php
$config['header']['Content-Security-Policy']['style-src'] = false;
```

To disable CSP entirely just set the header to `false` in `config.local.php`:

```php
$config['header']['Content-Security-Policy'] = false;
```

To enable a reporting solution for CSP violations, set the URI endpoint in `config.local.php`:

```php
$config['header']['Content-Security-Policy']['report-uri'] = "https://endpoint.example.com";
```

#### CSP Nonce
PitonCMS will automatically generate a 128bit base64 encoded nonce which is sent in the `script-src` directive, and is available to print in all templates as `site.environment.cspNonce`. This nonce is regenerated on each page view.

To add a nonce to any CSP policy directive, just include the `'nonce'` value in the header string (with single quotes), and also add the `nonce="{{ site.environment.cspNonce }}"` to your elements. At runtime the header string will be replaced by the actual nonce and the matching nonce added to your HTML.

```php
$config['header']['Content-Security-Policy']['script-src'] = "'self' 'nonce' 'unsafe-inline' 'strict-dynamic'";
```

To remove the default nonce from the header, in `config.local.php` define the directive with a string that does not include `'nonce'`.

To print the nonce on any nonce-able elemnt in your templates, such as a script tag, print the Twig variable:

```php
<script nonce="{{ site.environment.cspNonce }}">
    // Analytics JS code, for example
</script>
```

The browser will check that the nonce attribute matches the header nonce and allow that block to run. Be sure to include a nonce on all script elements, or disable the script nonce header.