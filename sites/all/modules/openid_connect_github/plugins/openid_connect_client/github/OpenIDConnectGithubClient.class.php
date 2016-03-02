<?php

/**
 * @file
 * GitHub OAuth2 client.
 */

class OpenIDConnectGithubClient extends OpenIDConnectClientBase {

  /**
   * A mapping of OpenID Connect user claims to GitHub user properties.
   *
   * https://developer.github.com/v3/users
   *
   * @var array
   */
  protected $userInfoMapping = array(
    'name' => 'name',
    'sub' => 'id',
    'email' => 'email',
    'preferred_username' => 'login',
    'picture' => 'avatar_url',
    'profile' => 'html_url',
    'website' => 'blog',
  );

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();

    $form['github_scopes'] = array(
      '#title' => t('Scopes'),
      '#description' => t(
        'The <a href="@docs" target="_blank">scopes</a> to request from GitHub, comma-separated.'
        . ' The <code>user:email</code> scope is required.',
        array(
          '@docs' => 'https://developer.github.com/v3/oauth/#scopes',
        )
      ),
      '#default_value' => $this->getSetting('github_scopes', 'user:email'),
      '#type' => 'textfield',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function authorize($scope = 'openid email') {
    parent::authorize($this->getSetting('github_scopes', 'user:email'));
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveUserInfo($access_token) {
    $request_options = array(
      'headers' => array(
        'Authorization' => 'token ' . $access_token,
        'Accept' => 'application/json',
      ),
    );
    $endpoints = $this->getEndpoints();
    $response = drupal_http_request($endpoints['github_user'], $request_options);
    if (!isset($response->error) && $response->code == 200) {
      $data = drupal_json_decode($response->data);
      $claims = array();
      foreach ($this->userInfoMapping as $claim => $key) {
        if (array_key_exists($key, $data)) {
          $claims[$claim] = $data[$key];
        }
      }

      // GitHub names can be empty. Fall back to the login name.
      if (empty($claims['name']) && isset($data['login'])) {
        $claims['name'] = $data['login'];
      }

      // Convert the updated_at date to a timestamp.
      if (!empty($data['updated_at'])) {
        $claims['updated_at'] = strtotime($data['updated_at']);
      }

      // The email address is only provided in the User resource if the user has
      // chosen to display it publicly. So we need to make another request to
      // find out the user's email address(es).
      if (empty($claims['email'])) {
        $email_response = drupal_http_request($endpoints['github_user'] . '/emails', $request_options);
        if (isset($email_response->error) || $email_response->code != 200) {
          openid_connect_log_request_error(__FUNCTION__, $this->name, $email_response);

          return FALSE;
        }
        $emails = drupal_json_decode($email_response->data);
        foreach ($emails as $email) {
          // See https://developer.github.com/v3/users/emails/
          if (!empty($email['primary'])) {
            $claims['email'] = $email['email'];
            $claims['email_verified'] = $email['verified'];
            break;
          }
        }
      }

      return $claims ?: FALSE;
    }
    else {
      openid_connect_log_request_error(__FUNCTION__, $this->name, $response);

      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoints() {
    return array(
      'authorization' => 'https://github.com/login/oauth/authorize',
      'token' => 'https://github.com/login/oauth/access_token',
      'github_user' => 'https://api.github.com/user',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function decodeIdToken($id_token) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveTokens($authorization_code) {
    // Exchange `code` for access token and ID token.
    $redirect_uri = OPENID_CONNECT_REDIRECT_PATH_BASE . '/' . $this->name;
    $post_data = array(
      'code' => $authorization_code,
      'client_id' => $this->getSetting('client_id'),
      'client_secret' => $this->getSetting('client_secret'),
      'redirect_uri' => url($redirect_uri, array('absolute' => TRUE)),
    );
    $request_options = array(
      'method' => 'POST',
      'data' => drupal_http_build_query($post_data),
      'timeout' => 15,
      'headers' => array(
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Accept' => 'application/json',
      ),
    );
    $endpoints = $this->getEndpoints();
    $response = drupal_http_request($endpoints['token'], $request_options);
    if (!isset($response->error) && $response->code == 200) {
      $response_data = drupal_json_decode($response->data);

      return array(
        // Fake the ID token.
        'id_token' => NULL,
        'access_token' => $response_data['access_token'],
      );
    }
    else {
      openid_connect_log_request_error(__FUNCTION__, $this->name, $response);
      return FALSE;
    }
  }
}
