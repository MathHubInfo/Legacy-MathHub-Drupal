<?php
/*
 * @file Groups.php
 * Defines the class Groups which extends the Client class with all group
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
 * The class Groups provides easy to use access to a GitLab instance.
 *
 */
class Groups extends Client {

  /**
   *  The function getAllGroups returns an array of groups.
   *  @return array
   *    Returns the file as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllGroups() {
      $result = $this->get('/groups'); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllGroups failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllGroups failed! Curl request to GitLab server failed!'); //@nolint
      }
  }

  /**
   *  The function getGroup returns the details of a group.
   *  @param string $group_id
   *    The group id to identify the group.
   *  @return array
   *    Returns the group.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getGroup($group_id) {
    if (isset($group_id)) {
      $result = $this->get('/groups/'.$group_id); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getGroup failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getGroup failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getGroup failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createGroup creates a new GitLab group.
   *  @param string $name
   *    The name of the group.
   *  @param string $path
   *    The new path of the group.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createGroup($name, $path) {
    if (isset($name) && isset($path)) {
      // set data
      $data = array('name' => $name, 'path' => $path);
      // execute query
      $result = $this->post('/groups/', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createGroup failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createGroup failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createGroup failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function transferProjectToGroup transfers a project to a group.
   *  @param string $search_term
   *    The project search term.
   *  @param string $group_id
   *    The group id.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function transferProjectToGroup($search_term, $group_id) {
    if (isset($search_term) && isset($group_id)) {
      $search_term = urlencode($search_term);
      // execute query
      $result = $this->post('/groups/'.$group_id.'/projects/'.$search_term,
                            array());
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('transferProjectToGroup failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('transferProjectToGroup failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('transferProjectToGroup failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteGroup deletes a GitLab group.
   *  @param string $group_id
   *    The group id to identify the group.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteGroup($group_id) {
    if (isset($group_id)) { 
      $result = $this->delete('/groups/'.$group_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteGroup failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteGroup failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteGroup failed! Arguments not set correctly!');  //@nolint
    }
  }

  /**
   *  The function getAllGroupMembers returns members of a group.
   *  @param string $group_id
   *    The group id to identify the group.
   *  @return array
   *    Returns the group members.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllGroupMembers($group_id) {
    if (isset($group_id)) {
      $result = $this->get('/groups/'.$group_id.'/members'); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllGroupMembers failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllGroupMembers failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllGroupMembers failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function addGroupMember creates a new GitLab group.
   *  @param string $group_id
   *    The group id to identify the group.
   *  @param string $user_id
   *    The user id.
   *  @param string $access_level
   *    The access level of the new group member.
   *    The levels are currently:
   *    - GUEST     = 10
   *    - REPORTER  = 20
   *    - DEVELOPER = 30
   *    - MASTER    = 40
   *    - OWNER     = 50
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function addGroupMember($group_id, $user_id, $access_level = 10) {
    if (isset($group_id) && isset($user_id)) {
      // set data
      $data = array('user_id' => $user_id,
                    'access_level' => $access_level);
      // execute query
      $result = $this->post('/groups/'.$group_id.'/members', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('addGroupMember failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('addGroupMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('addGroupMember failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function removeGroupMember removes a GitLab group member from the 
   *  Group.
   *  @param string $group_id
   *    The group id to identify the group.
   *  @param string $user_id
   *    The user id.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function removeGroupMember($group_id, $user_id) {
    if (isset($group_id)) { 
      $result = $this->delete('/groups/'.$group_id.'/members/'.$user_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('removeGroupMember failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('removeGroupMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('removeGroupMember failed! Arguments not set correctly!');  //@nolint
    }
  }

}
/** @}*/
