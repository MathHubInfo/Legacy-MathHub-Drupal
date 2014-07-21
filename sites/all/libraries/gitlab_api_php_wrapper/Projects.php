<?php
/*
 * @file Projects.php
 * Defines the class Projects which extends the Client class with all project
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
 * The class Projects provides easy to use access to a GitLab instance.
 *
 */
class Projects extends Client {

  /**
   *  The function getAllProjects lists all the projects.
   *  @return array
   *    Returns the list of projects as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @todo Look at the page and per_page options
   *    @link http://doc.gitlab.com/ce/api/ Pagination @endlink
   */
  public function getAllProjects() {
    $result = $this->get('/projects/all');
    if ($result !== false) {
      $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
      if ($return_code === 200) {
        return json_decode($result, true);
      }
      else {
        throw new RuntimeException('getAllProjects failed! HTTP Error Code:'.$return_code); //@nolint
      }
    }
    else {
      throw new RuntimeException('getAllProjects failed! Curl request to GitLab server failed!'); //@nolint
    }
  }

  /**
   *  The function getProject returns the GitLab project specified by the ID
   *  or NAMESPACE/PROJEKT.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the project as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProject($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getProject failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProject failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProject failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getProjectEvents returns the GitLab project specified by
   *  the ID.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the user as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProjectEvents($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/events');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result['0'];
        }
        else {
          throw new RuntimeException('getProjectEvents failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProjectEvents failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProjectEvents failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createProject creates a new GitLab project for the user.
   *  @param string $user_search_term
   *    The serach term for the user.
   *  @param string $project_name
   *    The name of the new project.
   *  @param array $params
   *    An array of non required parameters.
   *    - description - short project description
   *    - default_branch - 'master' by default
   *    - issues_enabled
   *    - merge_requests_enabled
   *    - wiki_enabled
   *    - snippets_enabled
   *    - public - if true same as setting visibility_level = 20
   *    - visibility_level
   *    - For more information visit
   *      @link http://doc.gitlab.com/ce/api/projects.html 
   *      Create project for user @endlink
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createProject($user_search_term, $project_name, array $params = array()) { //@nolint
    if (isset($user_search_term) && isset($project_name)) {
      $user_search_term = urlencode($user_search_term);
      // search for user id
      $result = $this->get('/users/?search='.$user_search_term);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          $user_id = $result['0']['id'];
        }
        else {
          throw new RuntimeException('createProject failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createProject failed! Curl request to GitLab server failed!'); //@nolint
      }

      // set data
      $required_data = array('user_id' => $user_id,
                             'name' => $project_name);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/projects/user/'.$user_id, $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createProject failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createProject failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createProject failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createProjectViaID creates a new GitLab project for the user
   *  with the given user_id. This function is faster than createProject as 
   *  this function does not need to search for the user id.
   *  @param string $user_id
   *    The user ID of the user.
   *  @param string $project_name
   *    The name of the new project.
   *  @param array $params
   *    An array of non required parameters.
   *    - description - short project description
   *    - default_branch - 'master' by default
   *    - issues_enabled
   *    - merge_requests_enabled
   *    - wiki_enabled
   *    - snippets_enabled
   *    - public - if true same as setting visibility_level = 20
   *    - visibility_level
   *    - For more information visit
   *      @link http://doc.gitlab.com/ce/api/projects.html 
   *      Create project for user @endlink
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createProjectViaID($user_id, $project_name, array $params = array()) { //@nolint
    if (isset($user_id) && isset($project_name)) {
      // set data
      $required_data = array('user_id' => $user_id,
                             'name' => $project_name);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/projects/user/'.$user_id, $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createProjectViaID failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createProjectViaID failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createProjectViaID failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteProject deletes a GitLab project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteProject($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->delete('/projects/'.$search_term);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteProject failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteProject failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteProject failed! Arguments not set correctly!');  //@nolint
    }
  }

  /**
   *  The function getAllProjectMember returns an array of GitLab users, 
   *  which are members of the specific project identified by the ID or
   *  NAMESPACE/PROJEKT. The query paramter allows to search
   *  for a specific user.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $username_query
   *    The username to query for.
   *  @return array
   *    Returns the project members in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllProjectMember($search_term, $username_query = null) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $request_url = '/projects/'.$search_term.'/members';
      if (isset($username_query)) {
        $request_url .= '/?query='.urlencode($username_query);
      }
      $result = $this->get($request_url);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllProjectMember failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllProjectMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllProjectMember failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getProjectMember returns the specified member of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $user_id
   *    The user_id to identify the user.
   *  @return array
   *    Returns the project member.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProjectMember($search_term, $user_id) {
    if (isset($search_term) && isset($user_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/members/'.$user_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getProjectMember failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProjectMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProjectMember failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function addMember adds a user to the members of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $user_id
   *    The user ID of the user.
   *  @param string $access_level
   *    The access level of the new member.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function addMember($search_term, $user_id, $access_level) {
    if (isset($search_term) && isset($user_id) && isset($access_level)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('user_id' => $user_id,
                    'access_level' => $access_level);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/members', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('addMember failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('addMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('addMember failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function editMember edits a member of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $user_id
   *    The user ID of the user.
   *  @param string $access_level
   *    The access level of the new member.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editMember($search_term, $user_id, $access_level) {
    if (isset($search_term) && isset($user_id) && isset($access_level)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('access_level' => $access_level);
      $result = $this->put('/projects/'.$search_term.'/members/'.$user_id,
                           $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editMember failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editMember failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function removeProjectMember removes a project member.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $user_id
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
  public function removeProjectMember($search_term, $user_id) {
    if (isset($search_term) && isset($user_id)) {
      $search_term = urlencode($search_term);
      $result = $this->delete('/projects/'.$search_term.'/members/'.$user_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('removeProjectMember failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('removeProjectMember failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('removeProjectMember failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getAllProjectHooks returns an array of all project hooks.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the hooks as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllProjectHooks($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/hooks/');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllProjectHooks failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllProjectHooks failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllProjectHooks failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getProjectHook returns an array of all project hooks.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $hook_id
   *    The hook_id to identify the hook.
   *  @return array
   *    Returns the hook.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProjectHook($search_term, $hook_id) {
    if (isset($search_term) && isset($hook_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/hooks/'.$hook_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getProjectHook failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProjectHook failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProjectHook failed! Arguments not set correctly!'); //@nolint
    }
  }

/**
  *  The function createProjectHooks creates a project hook.
  *  @param string $search_term
  *    The search_term to identify the project.
  *  @param string $hook_url
  *    The hook url.
  *  @param array $params
  *    An array of non required parameters.
  *    - push_events - Trigger hook on push events
  *    - issues_events - Trigger hook on issues events
  *    - merge_requests_events - Trigger hook on merge_requests events
  *    - For more information visit
  *      @link http://doc.gitlab.com/ce/api/projects.html Add project hook @endlink
  *  @return bool
  *    Returns true in case it succedes otherwise it throws.
  *  @throws RuntimeException
  *    Throws RuntimeException if an HTTP error was encounterd or in case
  *    the request could not be performed.
  *  @throws InvalidArgumentException
  *    Throws InvalidArgumentException in case required arguments
  *    are not set.
  */
  public function createProjectHooks($search_term, $hook_url, array $params = array()) {  //@nolint
    if (isset($search_term) && isset($hook_url)) {
      $search_term = urlencode($search_term);
      // set data
      $required_data = array('hook_url' => $hook_url);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/hooks', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createProjectHooks failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createProjectHooks failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createProjectHooks failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function editProjectHook edits a hook of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $hook_id
   *    The hook id.
   *  @param string $hook_url
   *    The hook url.
   *  @param array $params
   *    An array of non required parameters.
   *    - push_events - Trigger hook on push events
   *    - issues_events - Trigger hook on issues events
   *    - merge_requests_events - Trigger hook on merge_requests events
   *    - For more information visit
   *      @link http://doc.gitlab.com/ce/api/projects.html Add project hook @endlink
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editProjectHook($search_term, $hook_id, $hook_url) {
    if (isset($search_term) && isset($hook_id) && isset($hook_url)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('hook_url' => $hook_url);
      $result = $this->put('/projects/'.$search_term.'/hooks/'.$hook_id,
                           $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editProjectHook failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editProjectHook failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editProjectHook failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteProjectHook removes a project hook.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $hook_id
   *    The ID of the hook.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteProjectHook($search_term, $hook_id) {
    if (isset($search_term) && isset($hook_id)) {
      $search_term = urlencode($search_term);
      $result = $this->delete('/projects/'.$search_term.'/hooks/'.$hook_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteProjectHook failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteProjectHook failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteProjectHook failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getAllBranches returns an array with all branches of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the branches as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllBranches($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/repository/branches');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllBranches failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllBranches failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllBranches failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getBranch returns a branch of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $branch_name
   *    The name of the branch.
   *  @return array
   *    Returns the branch as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getBranch($search_term, $branch_name) {
    if (isset($search_term) && isset($branch_name)) {
      $search_term = urlencode($search_term);
      $branch_name = urlencode($branch_name);
      $result = $this->get('/projects/'.$search_term.'/repository/branches/'.$branch_name); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getBranch failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getBranch failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getBranch failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function protectBranch protects a branch of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $branch_name
   *    The name of the branch.
   *  @return array
   *    Returns the branch as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function protectBranch($search_term, $branch_name) {
    if (isset($search_term) && isset($branch_name)) {
      $search_term = urlencode($search_term);
      $branch_name = urlencode($branch_name);
      $result = $this->put('/projects/'.$search_term.'/repository/branches/'.$branch_name.'/protect',  //@nolint
                           array());
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('protectBranch failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('protectBranch failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('protectBranch failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function unprotectBranch unprotects a branch of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $branch_name
   *    The name of the branch.
   *  @return array
   *    Returns the branch as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function unprotectBranch($search_term, $branch_name) {
    if (isset($search_term) && isset($branch_name)) {
      $search_term = urlencode($search_term);
      $branch_name = urlencode($branch_name);
      $result = $this->put('/projects/'.$search_term.'/repository/branches/'.$branch_name.'/unprotect', //@nolint
                           array());
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('unprotectBranch failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('unprotectBranch failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('unprotectBranch failed! Arguments not set correctly!'); //@nolint
    }
  }

/**
  *  The function createForkRelation creates a from/to relation between existing projects.
  *  @param string $search_term
  *    The search_term to identify the project.
  *  @param string $forked_from_search_term
  *    The forked_from_search_term to identify the other project.
  *  @return bool
  *    Returns true in case it succedes otherwise it throws.
  *  @throws RuntimeException
  *    Throws RuntimeException if an HTTP error was encounterd or in case
  *    the request could not be performed.
  *  @throws InvalidArgumentException
  *    Throws InvalidArgumentException in case required arguments
  *    are not set.
  */
  public function createForkRelation($search_term, $forked_from_search_term) {
    if (isset($search_term) && isset($forked_from_search_term)) {
      $search_term = urlencode($search_term);
      $forked_from_search_term = urlencode($forked_from_search_term);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/fork/'.$forked_from_search_term
                            , array());  
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createForkRelation failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createForkRelation failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createForkRelation failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteForkRelation deletes a fork relation.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteForkRelation($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->delete('/projects/'.$search_term.'/fork/');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteForkRelation failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteForkRelation failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteForkRelation failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function searchProject returns an array of project with the
   *  search_term as a substring of the project name.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns an array with the projects.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function searchProject($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/search'.$search_term);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('searchProject failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('searchProject failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('searchProject failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getProjectLabels returns an array with the labels of a project.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the Labels as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProjectLabels($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/labels/');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getProjectLabels failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProjectLabels failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProjectLabels failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/
