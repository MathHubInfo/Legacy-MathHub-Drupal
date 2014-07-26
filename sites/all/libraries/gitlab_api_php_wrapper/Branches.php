<?php
/*
 * @file Branches.php
 * Defines the class Branches which extends the Client class with all repository
 * branch centered admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class Branches provides easy to use access to a GitLab instance.
 *
 */
class Branches extends Client {

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
   *  The function createBranch creates a new GitLab repository branch.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $branch_name
   *    The branch name.
   *  @param string $ref
   *    Commit sha or existing branch to branch from.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */

  public function createBranch($search_term,
                               $branch_name,
                               $ref) { 
    if (isset($search_term) &&
        isset($branch_name) &&
        isset($ref)) { 
      $search_term = urlencode($search_term);
      // set data
      $data = array('branch_name' => $branch_name,
                    'ref' => $ref);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/repository/branches', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createBranch failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createBranch failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createBranch failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function deleteBranch returns a branch of a project.
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
  public function deleteBranch($search_term, $branch_name) {
    if (isset($search_term) && isset($branch_name)) {
      $search_term = urlencode($search_term);
      $branch_name = urlencode($branch_name);
      $result = $this->delete('/projects/'.$search_term.'/repository/branches/'.$branch_name); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteBranch failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteBranch failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteBranch failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/
