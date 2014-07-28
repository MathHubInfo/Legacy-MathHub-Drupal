<?php
/*
 * @file DeployKeys.php
 * Defines the class DeployKeys which extends the Client class with all deploy keys
 * centered admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class DeployKeys provides easy to use access to a GitLab instance.
 *
 */
class DeployKeys extends Client {

 /**
   *  The function getAllDeployKeys returns all deploy keys.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @return array
   *    Returns the keys in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllDeployKeys($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/keys'); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllDeployKeys failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllDeployKeys failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllDeployKeys failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getDeployKey returns a key.
   *  @param string $search_term
   *    The search term to identify the repository.
   *  @param string $key_id
   *    The key ID to identify the key.
   *  @return array
   *    Returns the key in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getDeployKey($search_term, $key_id) {
    if (isset($search_term) && isset($key_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/keys/'.$key_id); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getDeployKey failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getDeployKey failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getDeployKey failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function addDeployKey adds a deploy key.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $title
   *    The title of the key.
   *  @param string $key
   *    The key.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function addDeployKey($search_term, $title, $key) { 
    if (isset($search_term) && isset($title) && isset($key)) { 
      $search_term = urlencode($search_term);
      // set data
      $data = array('title' => $title, 'key' => $key);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/keys', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('addDeployKey failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('addDeployKey failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('addDeployKey failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteDeployKey deletes a deploy key.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $key_id
   *    The key id.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteDeployKey($search_term, $key_id) {
    if (isset($search_term) && isset($key_id)) { 
      $search_term = urlencode($search_term);
      $result = $this->delete('/projects/'.$search_term.'/keys/'.$key_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteDeployKey failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteDeployKey failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteDeployKey failed! Arguments not set correctly!');  //@nolint
    }
  }



}
/** @}*/
