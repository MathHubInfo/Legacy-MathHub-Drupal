<?php
/*
 * @file Milestones.php
 * Defines the class Milestones which extends the Client class with all 
 * milestones centered admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class Milestones provides easy to use access to a GitLab instance.
 *
 */
class Milestones extends Client {

  /**
   *  The function getAllMilestones returns all milestones of a project.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @return array
   *    Returns the milestones in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllMilestones($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/milestones'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllMilestones failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllMilestones failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllMilestones failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getMilestone returns a milestone.
   *  @param string $search_term
   *    The search term to identify the repository.
   *  @param string $milestone_id
   *    The milestone id to identify the milestone.
   *  @return array
   *    Returns the milestones in an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getMilestone($search_term, $milestone_id) {
    if (isset($search_term) && isset($milestone_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/milestones/'.$milestone_id); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getMilestone failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getMilestone failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getMilestone failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createMilestone creates a new project milestone.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $title
   *    The title of the milestone.
   *  @param array $params
   *    An array of non required parameters.
   *    - description - The description of the milestone
   *    - due_date - The due date of the milestone
   *    - For more information visit
   *      @link http://doc.gitlab.com/ce/api/milestones.html Create Milestone @endlink
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createMilestone($search_term,
                                  $title,
                                  array $params = array()) { 
    if (isset($search_term) && isset($title)) { 
      $search_term = urlencode($search_term);
      // set data
      $required_data = array('title' => $title);
      $data = array_merge($required_data, $params);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/milestones', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createMilestone failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createMilestone failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createMilestone failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function editMilestone edits a an existing milestone.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $milestone_id
   *    The milestone ID to identify the milestone. 
   *  @param array $params
   *    An array of parameters.
   *    - title - The title
   *    - description - The description 
   *    - due_date - The due date 
   *    - state_event - The state (close|activate)
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editMilestone($search_term,
                                $milestone_id,
                                array $params) { 
    if (isset($search_term) &&
        isset($milestone_id) &&
        isset($params)) { 
      $search_term = urlencode($search_term);
      // execute query
      $result = $this->put('/projects/'.$search_term.'/milestones/'.$milestone_id,
                           $params);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editMilestone failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editMilestone failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editMilestone failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function closeMilestone closes a an existing milestone.
   *  This function performs the same request as edit just with predefined
   *  parameters.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $milestone_id
   *    The milestone ID to identify the milestone. 
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function closeMilestone($search_term, $milestone_id) { 
    if (isset($search_term) && isset($milestone_id)) {
      $search_term = urlencode($search_term);
      // execute query
      $result = $this->put('/projects/'.$search_term.'/milestones/'.$milestone_id,
                           array('state_event' => 'close'));
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('closeMilestone failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('closeMilestone failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('closeMilestone failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/
