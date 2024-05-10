<?php
namespace CraftedSupply\Modules\HttpHeaders;

/**
 * Add http headers to front end pages for security, with helper functions for correctly
 * formatting the more complex headers.
 */
function craftedsupply_http_headers() {

  // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options
  header( 'X-Content-Type-Options: nosniff' );
  // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
  header( 'X-Frame-Options: SAMEORIGIN' );
  // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection
  header( 'X-XSS-Protection: 1; mode=block' );
  // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
  header( 'Referrer-Policy: same-origin' );

  /*
  * Permissions Policy & Content Security Policy are way more complicated. See this file
  * in the feature/http-headers branch for a good option
  * https://bitbucket.org/craftedsupply/wordpress-boilerplate/src/62c615fd6f0600db3b83b30a1a0b37b88edfeefe/dist/web/app/themes/craftedsupply/lib/modules/http-headers.php?at=feature%2Fhttp-headers
  */
}

add_action( 'send_headers', __NAMESPACE__ . '\\craftedsupply_http_headers', 50 ); // applies to front-end
add_action( 'admin_init', __NAMESPACE__ . '\\craftedsupply_http_headers', 50 ); // applies to back-end
