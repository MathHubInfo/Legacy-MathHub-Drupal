<?php

/**
 * @file
 * Gitlab OAuth2 client.
 */

class OpenIDConnectGitlabClient extends OpenIDConnectClientBase {

  /**
   * A mapping of OpenID Connect user claims to Gitlab user properties.
   *
   * OpenID drupal user => gitlab user
   *
   * @var array
   */
  protected $userInfoMapping = array(
    'realname' => 'name',
    'sub' => 'id',
    'email' => 'email',
    'preferred_username' => 'username',
    'website' => 'website_url',
    );

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function authorize($scope = 'api') {
    //default declaration does not work. I guess the function is called somewhere with nonempty parameter
    $scope='api';
    $redirect_uri = OPENID_CONNECT_REDIRECT_PATH_BASE . '/' . $this->name;

    $url_options = array(
      'query' => array(
        'client_id' => $this->getSetting('client_id'),
        'response_type' => 'code',
        'redirect_uri' => url($redirect_uri, array('absolute' => TRUE)),
        'state' => openid_connect_create_state_token(),
      ),
    );
    $endpoints = $this->getEndpoints();
    // Clear $_GET['destination'] because we need to override it.
    unset($_GET['destination']);
    drupal_goto($endpoints['authorization'], $url_options);

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
      'grant_type' => 'authorization_code',
      );
    $request_options = array(
      'method' => 'POST',
      'data' => drupal_http_build_query($post_data),
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



  /**
   * {@inheritdoc}
   */
  public function retrieveUserInfo($access_token) {
    $request_options = array(
      'method' => 'GET',
      'timeout' => 15,
      );


    $endpoints = $this->getEndpoints();
    $u=$endpoints['gitlab_user']."?access_token=".$access_token;
    $response = drupal_http_request($u, $request_options);


    if (!isset($response->error) && $response->code == 200) {
      $data = drupal_json_decode($response->data);
      $claims = array();
      foreach ($this->userInfoMapping as $claim => $key) {
        if (array_key_exists($key, $data)) {
          $claims[$claim] = $data[$key];
        }
      }

      // empty name. Fall back to the login name.
      if (empty($claims['name']) && isset($data['username'])) {
        $claims['name'] = $data['username'];
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
      'authorization' => 'https://gl.mathhub.info/oauth/authorize',
      'token' => 'https://gl.mathhub.info/oauth/token',
      'gitlab_user' => 'https://gl.mathhub.info/api/v3/user',
      );
  }

  /**
   * {@inheritdoc}
   */
  public function decodeIdToken($id_token) {
    return array();
  }


};