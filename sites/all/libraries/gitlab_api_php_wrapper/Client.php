<?php
/*
 * @file Client.php
 * Defines the class Client which is the base class for all GitLab API classes.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

class RuntimeException extends \RuntimeException {};
class InvalidArgumentException extends \InvalidArgumentException {};

/**
 * The class Client provides the basic functionality to make a
 * request to the GitLab API.
 *
 */
class Client {
  /**
   *  @var string $authenticationToken
   *    The authentication token used to authenticate with the GitLab server.
   */
  private $authenticationToken;
  /**
   *  @var int $sudoID
   *    The ID of admin account, corresponding to the authentication token.
   */
  private $sudoID;
  /**
   *  @var string $gitlabServerUrl
   *    The GitLab server URL to connect to.
   */
  private $gitlabServerUrl;
  /**
   *  @var curl_handle $curlClient
   *    The curl handle used for requests.
   */
  protected $curlClient;

  /**
   *  The constructor constructs a new client object.
   *
   *  It constructs a new client object by setting all properties of the
   *  class and then initalizing curl.
   *  @param string $authentication_token
   *    The authentication token used to authenticate with the GitLab server.
   *  @param int $sudo_id
   *    The ID of admin account, corresponding to the authentication token.
   *  @param string $gitlab_server_url
   *    The GitLab server URL to connect to.
   *  @throws RuntimeException
   *    Throws RuntimeException if Curl could not initalize.
   */
  public function __construct($authentication_token, $sudo_id, $gitlab_server_url) { //@nolint
    $this->authenticationToken = $authentication_token;
    $this->sudoID = $sudo_id;
    $this->gitlabServerUrl = $gitlab_server_url;
    $this->curlClient = curl_init();
    if ($this->curlClient === false) {
      throw new RuntimeException('Error Creating Curl Session');
    }
  }

  /**
   *  The destructors destroys a client object.
   *
   *  It destructs the client object by closing the curl session.
   */
  public function __destruct() {
    if (isset($this->curlClient)) {
      curl_close($this->curlClient);
    }
  }

  /**
   *  The function delete performs a delete request to the GitLab server.
   *
   *  @param string $sub_url
   *    The sub URL which then gets appended to the server URL.
   *  @param string $data
   *    The data which might be send with the request.
   *  @return object
   *    Returns the result of the request.
   */
  protected function delete($sub_url, array $data = null) {
    if(isset($data))
    {
       $sub_url .= '?' . http_build_query($data);
    }
    $url = $this->gitlabServerUrl.$sub_url;
    curl_setopt_array($this->curlClient, array(
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLINFO_HEADER_OUT => true,
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_HTTPHEADER => array(
        'PRIVATE-TOKEN: '.$this->authenticationToken,
        'SUDO: '.$this->sudoID
      ),
    ));
    return curl_exec($this->curlClient);
  }

  /**
   *  The function get performs a get request to the GitLab server.
   *
   *  @param string $sub_url
   *    The sub URL which then gets appended to the server URL.
   *  @param string $data
   *    The data which might be send with the request.
   *  @return object
   *    Returns the result of the request.
   */
  protected function get($sub_url, array $data = null) {
    if(isset($data))
    {
       $sub_url .= '?' . http_build_query($data);
    }
    $url = $this->gitlabServerUrl.$sub_url;
    curl_setopt_array($this->curlClient, array(
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLINFO_HEADER_OUT => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'PRIVATE-TOKEN: '.$this->authenticationToken,
        'SUDO: '.$this->sudoID
      ),
    ));
    return curl_exec($this->curlClient);
  }

  /**
   *  The function post performs a post request to the GitLab server.
   *
   *  @param string $sub_url
   *    The sub URL which then gets appended to the server URL.
   *  @param array $data
   *    An array containing the data to be send with the request.
   *  @return object
   *    Returns the result of the request.
   */
  protected function post($sub_url, array $data) {
    $data = json_encode($data);
    curl_setopt_array($this->curlClient, array(
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_URL => $this->gitlabServerUrl.$sub_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLINFO_HEADER_OUT => true,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'PRIVATE-TOKEN: '.$this->authenticationToken,
        'SUDO: '.$this->sudoID
      ),
    ));
    return curl_exec($this->curlClient);
  }

  /**
   *  The function post performs a post request to the GitLab server.
   *
   *  @param string $sub_url
   *    The sub URL which then gets appended to the server URL.
   *  @param array $data
   *    An array containing the data to be send with the request.
   *  @return object
   *    Returns the result of the request.
   */
  protected function put($sub_url, array $data) {
    $data = json_encode($data);
    curl_setopt_array($this->curlClient, array(
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_URL => $this->gitlabServerUrl.$sub_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLINFO_HEADER_OUT => true,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'PRIVATE-TOKEN: '.$this->authenticationToken,
        'SUDO: '.$this->sudoID
      ),
    ));
    return curl_exec($this->curlClient);
  }
}
/** @}*/
