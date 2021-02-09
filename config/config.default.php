<?php
/**
 * Default Configuration Settings
 *
 * DO NOT MODIFY THIS FILE
 * Copy this file to <project>/config/config.local.php to define all environment specific settings.
 */

/**
 * Production Environment Flag
 *
 * Boolean variable controls debug and environment modes.
 * Always set to true in production.
 * Set to false in config.local.php for development.
 */
$config['environment']['production'] = true;

/**
 * Database
 *
 * Database configuration settings
 * - host:     Database server
 * - dbname:   Database name
 * - username: Database user name
 * - password: Database password
 */
$config['database']['host'] = 'localhost';
$config['database']['dbname'] = '';
$config['database']['username'] = '';
$config['database']['password'] = '';

/**
 * Session
 *
 * Session management settings
 * - cookieName:             Application cookie name
 * - checkIpAddress:         Whether to verify request IP address
 * - checkUserAgent:         Whether to verify the request useragent string
 * - salt:                   Complex string to hash for session ID
 * - secureCookie:           Set to true if HTTPS, false if HTTP
 * - secondsUntilExpiration: How many seconds to set the session on each request, defaults to 2 hours.
 *      Can accept expression such as 60*60*24;
 */
$config['session']['cookieName'] = 'pitoncms';
$config['session']['checkIpAddress'] = true;
$config['session']['checkUserAgent'] = true;
$config['session']['salt'] = '';
$config['session']['secureCookie'] = true;
$config['session']['secondsUntilExpiration'] = 7200;

/**
 * Email
 *
 * Email configuration settings. Defaults to sendmail
 * - from:     Send-from email address
 * - protocol: 'mail' (default) or 'smtp'
 *
 * These settings only apply for SMTP connections
 * - smtpHost: SMTP server name
 * - smtpUser: User name
 * - smtpPass: Password
 * - smtpPort: Port to use, likely 465
 */
$config['email']['from'] = 'pitoncms@localhost.com';
$config['email']['protocol'] = 'mail';
$config['email']['smtpHost'] = '';
$config['email']['smtpUser'] = '';
$config['email']['smtpPass'] = '';
$config['email']['smtpPort'] = '';

/**
 * Pagination
 *
 * Set default number of results to return in pagination queries.
 * Affects both administration and front end pagination.
 */
$config['pagination']['resultsPerPage'] = 20;

/**
 * Response Headers
 *
 * Sets response headers dynamically at application runtime
 * Define any standard or custom header as a key:value, under the ['header'] array
 *
 * Notes
 * - Strict-Transport-Security is *only* set when not on localhost to avoid forcing future requests to https
 * - Content-Security-Policy can be either a simple string, a bool false to disable CSP, or a sub-array of directives and values
 *      - See https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy for more information
 *      - See https://csp.withgoogle.com/docs/strict-csp.html for the default policy explanation below
 *      - To include a nonce in the directive, include the string 'nonce' (quoted) in the value and this will be expanded to a unique base64 nonce
 *      - Consider adding a report URI to send CSP violations to a central log
 */
$config['header']['X-Frame-Options'] = 'DENY';
$config['header']['X-Content-Type-Options'] = 'nosniff';
$config['header']['Referrer-Policy'] = 'no-referrer-when-downgrade';
$config['header']['Feature-Policy'] = 'self';
$config['header']['X-XSS-Protection'] = '1; mode=block';
$config['header']['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
$config['header']['Content-Security-Policy']['default-src'] = "'self'";
$config['header']['Content-Security-Policy']['script-src'] = "'self' 'nonce' 'unsafe-inline' 'strict-dynamic'";
$config['header']['Content-Security-Policy']['style-src'] = "'self' 'unsafe-inline' https://fonts.gstatic.com https://fonts.googleapis.com";
$config['header']['Content-Security-Policy']['font-src'] = "'self' https://fonts.gstatic.com";
$config['header']['Content-Security-Policy']['img-src'] = "*";
$config['header']['Content-Security-Policy']['base-uri'] = "'none'";
// $config['header']['Content-Security-Policy']['report-uri'] = "";
