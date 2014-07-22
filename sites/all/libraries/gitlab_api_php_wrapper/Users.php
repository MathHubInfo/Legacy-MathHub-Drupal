<?php
/*
 * @file Users.php
 * Defines the class users which extends the Client
 * class with all User centered API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class Users provides easy to use access to a GitLab instance.
 *
 */
class Users extends Client {

  /**
   *  The function listUsers lists all the users.
   *  @return array
   *    Returns the list of users as an array of objects.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @todo Look at the page and per_page options
   *    @link http://doc.gitlab.com/ce/api/ Pagination @endlink
   */
  public function listUsers() {
    $result = $this->get('/users/');
    if ($result !== false) {
      $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
      if ($return_code === 200) {
        return json_decode($result, true);
      }
      else {
        throw new RuntimeException('listUsers failed! HTTP Error Code:'.$return_code); //@nolint
      }
    }
    else {
      throw new RuntimeException('listUsers failed! Curl request to GitLab server failed!'); //@nolint
    }
  }

  /**
   *  The function getUserID returns the GitLab user ID of a specific user.
   *  @param string $search_term
   *    The search term, which is either a GitLab username or an email adresse.
   *  @return int
   *    Returns the user ID of the user.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getUserID($search_term) {
    if (isset($search_term)) {
      $result = $this->get('/users/?search='.$search_term);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return intval($result['0']['id']);
        }
        else {
          throw new RuntimeException('getUserID failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getUserID failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getUserID failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getUser returns the GitLab user.
   *  @param string $search_term
   *    The search term, which is either a GitLab username or an email adresse.
   *  @return array
   *    Returns the user as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   *  @todo parse the result better and include error checks
   */
  public function getUser($search_term) {
    if (isset($search_term)) {
      $result = $this->get('/users/?search='.$search_term);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result['0'];
        }
        else {
          throw new RuntimeException('getUser failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getUser failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getUser failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getUserByID returns the GitLab user specified by the ID.
   *  @param int $user_id
   *    The user_id of the user.
   *  @return array
   *    Returns the user as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getUserByID($user_id) {
    if (isset($user_id)) {
      $result = $this->get('/users/'.$user_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getUserByID failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getUserByID failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getUserByID failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createUser creates a GitLab user
   *  @param string $username
   *    The username of the new user.
   *  @param string $name
   *    The name of the new user.
   *  @param string $password
   *    The password of the new user.
   *  @param string $email
   *    The email adresse of the new user.
   *  @param array $params
   *    An array of non required parameters.
   *    - skype - Skype ID
   *    - linkedin - Linkedin
   *    - twitter - Twitter account
   *    - website_url - Website url
   *    - projects_limit - Number of projects user can create
   *    - extern_uid - External UID
   *    - provider - External provider name
   *    - bio - User's bio
   *    - admin - User is admin - true or false (default)
   *    - can_create_group - User can create groups - true or false
   *    - For more information visit
   *      @link http://doc.gitlab.com/ce/api/users.html User Creation @endlink
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createUser($username, $name, $password, $email, array $params = array()) { //@nolint
    if (isset($username) && isset($name) && isset($password) && isset($email)) {
      // set data
      $required_data = array(
        'username' => $username,
        'name' => $name,
        'password' => $password,
        'email' => $email);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/users', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createUser failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createUser failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createUser failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function editUser edits a GitLab user
   *  @param int $user_id
   *    The user ID of the user, which is changed.
   *  @param array $params
   *    An array of non required parameters.
   *    - email - Email
   *    - username - Username
   *    - name - Name
   *    - password - Password
   *    - skype - Skype ID
   *    - linkedin - Linkedin
   *    - twitter - Twitter account
   *    - website_url - Website url
   *    - projects_limit - Number of projects user can create
   *    - extern_uid - External UID
   *    - provider - External provider name
   *    - bio - User's bio
   *    - admin - User is admin - true or false (default)
   *    - can_create_group - User can create groups - true or false
   *    - For more information visit
   *      @link http://doc.gitlab.com/ce/api/users.html User modification @endlink
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editUser($user_id, array $params) {
    if (isset($params) && isset($user_id)) {
      $result = $this->put('/users/'.$user_id, $params);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editUser failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editUser failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editUser failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteUser deletes a GitLab user.
   *  @param int $user_id
   *    The user ID of the user.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteUser($user_id) {
    if (isset($user_id)) {
      $result = $this->delete('/users/'.$user_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteUser failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteUser failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteUser failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function lists all SSH keys of a specific users
   *  @param int $user_id
   *    The user ID of the user.
   *  @return
   *    Returns the list of SSH-Keys as an array of SSH-Key arrays.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   *  @todo Look at the page and per_page options
   *    @link http://doc.gitlab.com/ce/api/ Pagination @endlink
   */
  public function listSSHKeys($user_id) {
    if (isset($user_id)) {
      $result = $this->get('/users/'.$user_id.'/keys');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('listSSHKeys failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('listSSHKeys failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('listSSHKeys failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function adds a SSH key to a specific user.
   *  @param int $user_id
   *    The user ID of the user.
   *  @param string $title
   *    The title of the new SSH keys.
   *  @param string $key
   *    The SSH keys to add to the users keys.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function addSSHKey($user_id, $title, $key) {
    if (isset($user_id) && isset($key) && isset($title)) {
      // set data
      $data = array('title' => $title, 'key' => $key);
      $result = $this->post('/users/'.$user_id.'/keys', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('addSSHKey failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('addSSHKey failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('addSSHKey failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deletes a specific SSH key from a user
   *  @param int $user_id
   *    The user ID of the user.
   *  @param int $key_id
   *    The key ID of the SSH key to be deleted.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteSSHKey($user_id, $key_id) {
    if (isset($user_id) && isset($key_id)) {
      $result = $this->delete('/users/'.$user_id.'/keys/'.$key_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('addSSHKey failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('addSSHKey failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('addSSHKey failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/
