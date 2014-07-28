<?php
/*
 * @file SystemHooks.php
 * Defines the class SystemHooks which extends the Client class with all system hooks
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
 * The class SystemHooks provides easy to use access to a GitLab instance.
 *
 */
class SystemHooks extends Client {

 /**
   *  The function getAllHooks returns all hooks.
   *  @return array
   *    Returns the hooks in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllHooks() {
    $result = $this->get('/hooks'); 
    if ($result !== false) {
      $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
      if ($return_code === 200) {
        $result = json_decode($result, true); // decode the json string
        return $result;
      }
      else {
        throw new RuntimeException('getAllHooks failed! HTTP Error Code:'.$return_code); //@nolint
      }
    }
    else {
      throw new RuntimeException('getAllHooks failed! Curl request to GitLab server failed!'); //@nolint
    }
  }

  /**
   *  The function addSystemHook adds a system hook.
   *  @param string $url
   *    The hook url.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function addSystemHook($url) { 
    if (isset($url)) { 
      // set data
      $data = array('url' => $url);
      // execute query
      $result = $this->post('/hooks', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('addSystemHook failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('addSystemHook failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('addSystemHook failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function testSystemHook test a system hook.
   *  @param string $hook_id
   *    The hook id.
   *  @return array
   *    Returns the hooks in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function testSystemHook($hook_id) {
    if(isset($hook_id)) {
      $result = $this->get('/hooks/'.$hook_id); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('testSystemHook failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('testSystemHook failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('testSystemHook failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteSystemHook deletes a system hook.
   *  @param string $hook_id
   *    The hook id.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteSystemHook($hook_id) {
    if (isset($hook_id)) { 
      $result = $this->delete('/hooks/'.$hook_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteSystemHook failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteSystemHook failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteSystemHook failed! Arguments not set correctly!');  //@nolint
    }
  }

}
/** @}*/
