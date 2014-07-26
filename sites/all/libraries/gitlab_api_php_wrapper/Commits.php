<?php
/*
 * @file Commits.php
 * Defines the class Commits which extends the Client class with all commit
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
 * The class Commits provides easy to use access to a GitLab instance.
 *
 */
class Commits extends Client {

  /**
   *  The function getAllRepositoryCommits returns all commits.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $ref_name
   *    The name of a branch or tag. (optional)
   *  @return array
   *    Returns the commit list as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllRepositoryCommits($search_term, $ref_name = null) {
    if (isset($search_term)) {
      if(isset($ref_name)) {
        $query_data = array('ref_name' => $ref_name);
        $result = $this->get('/projects/'.$search_term.'/repository/commits', $query_data); //@nolint
      }
      else {
        $result = $this->get('/projects/'.$search_term.'/repository/commits'); //@nolint
      }
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllRepositoryCommits failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllRepositoryCommits failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllRepositoryCommits failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryCommit returns a specific commit.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $sha
   *    The commit hash, name of branch or tag name.
   *  @return array
   *    Returns the commit list as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getRepositoryCommit($search_term, $sha) {
    if (isset($search_term) && isset($sha)) {
      $result = $this->get('/projects/'.$search_term.'/repository/commits/'.$sha); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getRepositoryCommit failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryCommit failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryCommit failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryCommitDiff returns the diff of a specific 
   *  commit.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $sha
   *    The commit hash, name of branch or tag name.
   *  @return array
   *    Returns the commit list as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getRepositoryCommitDiff($search_term, $sha) {
    if (isset($search_term) && isset($sha)) {
      $result = $this->get('/projects/'.$search_term.'/repository/commits/'.$sha.'/diff'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getRepositoryCommitDiff failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryCommitDiff failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryCommitDiff failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/
