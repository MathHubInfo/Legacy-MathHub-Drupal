# OpenID Connect GitHub

This integrates with the OpenID Connect module to allow sign in with GitHub.

While GitHub does not implement the actual OpenID Connect protocol, it does
provide a similar flow for obtaining user data based on OAuth 2.0 tokens.

## Configuration

1. Register an OAuth application on GitHub:
   https://github.com/settings/applications/new
   Note the new application's client ID and secret.
2. Go to admin/config/services/openid-connect and enable the GitHub client. Add
   the client ID and secret obtained from GitHub.
3. Enable the OpenID Connect login block at admin/structure/block. This will
   provide a block containing a "Log in with GitHub" button.

## References

* OpenID Connect module
  https://www.drupal.org/project/openid_connect
* GitHub OAuth API documentation
  https://developer.github.com/v3/oauth/
