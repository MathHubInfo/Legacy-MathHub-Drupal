
<?php
/*
 * @file MergeRequests.php
 * Defines the class MergeRequests which extends the Client class with all merge
 * request centered admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class MergeRequests provides easy to use access to a GitLab instance.
 *
 */
class MergeRequests extends Client {
 
  /**
   *  The function getAllMergeRequests returns the merge request list.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $state
   *    Return all requests or just those that are merged, opened or closed.
   *  @return array
   *    Returns the merge requests as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllMergeRequests($search_term, $state = null) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      if (isset($state)) {
        $query_data = array('state' => $state);
        $result = $this->get('/projects/'.$search_term.'/merge_requests', $query_data); //@nolint
      }
      $result = $this->get('/projects/'.$search_term.'/merge_requests'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllMergeRequests failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllMergeRequests failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllMergeRequests failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getMergeRequest returns the merge request.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $merge_request_id
   *    The merge request ID.
   *  @return array
   *    Returns the merge request as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getMergeRequest($search_term, $merge_request_id) {
    if (isset($search_term) && isset($merge_request_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/merge_requests/'.$merge_request_id); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getMergeRequest failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getMergeRequest failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getMergeRequest failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createMergeRequest creates a new GitLab merge request.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $source_branch
   *    The source branch.
   *  @param string $target_branch
   *    The target branch.
   *  @param string $title
   *    The title of the merge request.
   *  @param array $params
   *    An array of non required parameters.
   *    - assignee_id - Assignee user ID.
   *    - target_project_id - The target project.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createMergeRequest($search_term,
                                     $source_branch,
                                     $target_branch,
                                     $title,
                                     array $params = array()) { 
    if (isset($search_term) &&
        isset($source_branch) &&
        isset($target_branch) &&
        isset($title)) { 
      $search_term = urlencode($search_term);
      // set data
      $required_data = array('source_branch' => $source_branch,
                             'target_branch' => $target_branch,
                             'title' => $title);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/merge_requests', $data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createMergeRequest failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createMergeRequest failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createMergeRequest failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function updateMergeRequest updates the merge request.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $merge_request_id
   *    The merge request ID to identify the request.
   *  @param array $params
   *    The array with update parameters
   *    - source_branch - The source branch
   *    - target_branch - The target branch
   *    - assignee_id - Assignee user ID
   *    - title - Title of MR
   *    - state_event - New state (close|reopen|merge)
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function updateMergeRequest($search_term, $merge_request_id, array $params) {
    if (isset($search_term) && isset($merge_request_id) && isset($params)) {
      $search_term = urlencode($search_term);
      $result = $this->put('/projects/'.$search_term.'/merge_request/'.$merge_request_id,
                           $params);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('updateMergeRequest failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('updateMergeRequest failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('updateMergeRequest failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function acceptMergeRequest accepts the merge request.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $merge_request_id
   *    The merge request ID to identify the request.
   *  @param string $merge_commit_message
   *    The optional merge request commit message.
   *  @return mixed string|bool
   *    Returns true in case it succedes or it returns the message.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function acceptMergeRequest($search_term, 
                                     $merge_request_id,
                                     $merge_commit_message = null) {
    if (isset($search_term) && isset($merge_request_id)) {
      $search_term = urlencode($search_term);
      if (isset($merge_commit_message)) {
        $data = array('merge_commit_message' => $merge_commit_message);
      }
      $result = $this->put('/projects/'.$search_term.'/merge_request/'.$merge_request_id.'/merge', //@nolint
                           $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else if($return_code === 405) {
          return $result;
        }
        else {
          throw new RuntimeException('acceptMergeRequest failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('acceptMergeRequest failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('acceptMergeRequest failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createMergeRequestComment creates a new GitLab merge request.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $merge_request_id
   *    The merge request ID to identify the request.
   *  @param string $note
   *    Text of comment.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createMergeRequestComment($search_term,
                                            $merge_request_id,
                                            $note) { 
    if (isset($search_term) &&
        isset($merge_request_id) &&
        isset($note)) { 
      $search_term = urlencode($search_term);
      // set data
      $data = array('note' => $note);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/merge_request/'.$merge_request_id.'/comments', $data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createMergeRequestComment failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createMergeRequestComment failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createMergeRequestComment failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getAllMergeRequestComments returns the merge request list.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $merge_request_id
   *    The merge request ID to identify the request.
   *  @return array
   *    Returns the merge request comments as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllMergeRequestComments($search_term, $state = null) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/merge_request/'.$merge_request_id.'/comments'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllMergeRequestComments failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllMergeRequestComments failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllMergeRequestComments failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/

