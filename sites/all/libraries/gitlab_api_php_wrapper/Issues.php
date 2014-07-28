
<?php
/*
 * @file Isssues.php
 * Defines the class Isssues which extends the Client class with all issues
 * related admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class Isssues provides easy to use access to a GitLab instance.
 *
 */
class Isssues extends Client {
 
  /**
   *  The function getAllIssues returns the issues list.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @return array
   *    Returns the issues in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   *  @todo Look at the page and per_page options
   *    @link http://doc.gitlab.com/ce/api/ Pagination @endlink
   */
  public function getAllIssues($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/issues'); 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllIssues failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllIssues failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllIssues failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getIssue returns a project issue.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $issue_id
   *    The ID of a project issue.
   *  @return array
   *    Returns the merge request as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getIssue($search_term, $issue_id) {
    if (isset($search_term) && isset($issue_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/issues/'.$issue_id); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getIssue failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getIssue failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getIssue failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createIssue creates a new GitLab project issue.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $title
   *    The title of the new issue.
   *  @param array $params
   *    An array of non required parameters.
   *    - description - The description of an issue
   *    - assignee_id - The user ID of the assignee.
   *    - milestone_id - The milestone ID.
   *    - labels - Comma-separated label names for an issue.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createIssue($search_term,
                              $title,
                              array $params = array()) { 
    if (isset($search_term) && isset($title)) { 
      $search_term = urlencode($search_term);
      // set data
      $required_data = array('title' => $title);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/issues', $data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createIssue failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createIssue failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createIssue failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function editIssue edits an issue.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $issue_id
   *    The merge request ID to identify the request.
   *  @param array $params
   *    The array with update parameters.
   *    - title - The title of an issue.
   *    - description - The description of an issue.
   *    - assignee_id - The user ID of the assignee.
   *    - milestone_id - The milestone ID.
   *    - labels - Comma-separated label names for an issue.
   *    - state_event - The state of an issue (close|reopen).
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editIssue($search_term, $issue_id, array $params) {
    if (isset($search_term) && isset($issue_id) && isset($params)) {
      $search_term = urlencode($search_term);
      $result = $this->put('/projects/'.$search_term.'/issues/'.$issue_id,
                           $params);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editIssue failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editIssue failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editIssue failed! Arguments not set correctly!'); //@nolint
    }
  }

/**
   *  The function closeIssue closes an issue.
   *  This function is a short hand function of editIssue.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $issue_id
   *    The merge request ID to identify the request.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function closeIssue($search_term, $issue_id) {
    if (isset($search_term) && isset($issue_id)) {
      $search_term = urlencode($search_term);
      $result = $this->put('/projects/'.$search_term.'/issues/'.$issue_id,
                           array('state_event' => 'close'));
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('closeIssue failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('closeIssue failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('closeIssue failed! Arguments not set correctly!'); //@nolint
    }
  }

/**
   *  The function reopenIssue reopens an issue.
   *  This function is a short hand function of editIssue.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $issue_id
   *    The merge request ID to identify the request.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function reopenIssue($search_term, $issue_id) {
    if (isset($search_term) && isset($issue_id)) {
      $search_term = urlencode($search_term);
      $result = $this->put('/projects/'.$search_term.'/issues/'.$issue_id,
                           array('state_event' => 'reopen'));
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('reopenIssue failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('reopenIssue failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('reopenIssue failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/

