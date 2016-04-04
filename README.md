# Purge All

> Facilitate purging entire Drupal site via Expire module.

[Expire module](https://www.drupal.org/project/expire) has a longstanding gap in
support of purging an entire site (or the domain associated with an entire site)
based on the configured trigger actions it provides.

## Module Implementation

This module builds on [Purge module](https://www.drupal.org/project/purge), but
has such a basic use case compared to much of the Purge module's plumbing that
it is largely leverage Purge configuration without touching the Purge API.

You can configure Purge All via the Expire module. Purge All settings and actions
will only be triggered if the Purge module has been selected as the expiration
method.

## Infrastructure Requirements

In order for this module to work, your proxy cache must be configured to receive
a custom HTTP request method: `PURGEALL`. The URL submitted to the proxy should
be wildcard purged.

Here is an example of a Varnish4 VCL to support PURGEALL:

```
if (req.method == "PURGEALL") {
  // Purge all objects from cache that match the incoming host
  ban ("req.url ~ ^/ && req.http.host == " + req.http.host);
  return (synth(200, "Purged"));
}
```

## Todos

* Remove duplicate code from `includes/PurgeAllExpireNode.php` if Expire module makes that possible.
* Support entity types other than Node.

## Credits

This module is built on the excellent work in [Drupal Issue #1304812](https://www.drupal.org/node/1304812), especially:

* [mauritsl](https://www.drupal.org/u/mauritsl) for original issue and Varnish3 VCL
* [sokrplare](https://www.drupal.org/u/sokrplare) for HTTP PURGEALL request.
