Okay, so we don’t actually have a Drupal module. Instead we have quirky content and configuration tricks to make this work.

In addition to all the normal mod_auth_urs setup, the httpd virtualhost config file has

```ApacheConf
<LocationMatch "/urs-login$">
    AuthType  UrsOAuth2
    Require   valid-user
</LocationMatch>
```

The Drupal site uses the `path` module to map `/urs-login` to a node with text format "PHP". I think this is from the `php` core module or `views_php` contrib module.

The important bits in the PHP code are:

1.  Look at `$_SERVER['REDIRECT_REMOTE_USER']` and several `$_SERVER['REDIRECT_URS_***']` variables set indirectly by mod_auth_urs
2.  Look for the Drupal account with the Earthdata Login profile ID (from `REDIRECT_REMOTE_USER`) using `user_load_by_name()`
3.  If the Drupal user does not exist, then
    a.  Create a new user account with `user_save()`
    b.  Send the user an email using `drupal_mail('user', 'register_pending_approval', ...)`
    c.  Redirect the browser to a welcome page
4.  Otherwise, reset the Drupal password to a random value and force a login using `user_authenticate()`
5.  If the Drupal user is blocked, then show an unwelcome (?) page
6.  Otherwise, the Drupal user is active, so
    a.  Reload the user details using `user_load()` and the result UID from `user_authenticate()`
    b.  Force the `name` field to match the Earthdata Login profile ID
    c.  Finish login with `user_login_finalize()`
    d.  Redirect to original destination
