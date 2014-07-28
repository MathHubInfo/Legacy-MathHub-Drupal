<?php
/*
 * @file Repositories.php
 * Defines the class Repositories which extends the Client class with all 
 * repositories centered admin API calls to GitLab.
 * 
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class Repositories provides easy to use access to a GitLab instance.
 *
 */
class Repositories extends Client {

  /**
   *  The function getAllProjectRepositoryTags lists all the project
   *  repository tags.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the list of projects as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getAllProjectRepositoryTags($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/repository/tags');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getAllProjectRepositoryTags failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllProjectRepositoryTags failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllProjectRepositoryTags failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createProjectRepositoryTag creates a new GitLab project
   *  repository tag.
   *  @param string $search_term
   *    The search term of the project.
   *  @param tag_name 
   *    The name of the tag.
   *  @param ref 
   *    Create tag using commit sha, another tag name, or branch name.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createProjectRepositoryTag($search_term, $tag_name, $ref) { //@nolint
    if (isset($search_term) && isset($tag_name) && isset($ref)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('tag_name' => $tag_name,
                    'ref' => $ref);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/repository/tags', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createProjectRepositoryTag failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createProjectRepositoryTag failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createProjectRepositoryTag failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryTree returns the repoistory tree.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param array $params
   *    Optional parameters
   *    - path - The path inside repository. 
   *    - ref_name - The name of a repository branch or tag.
   *  @return array
   *    Returns the project tree as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getRepositoryTree($search_term, array $params = null) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/repository/tree', $params);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getRepositoryTree failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryTree failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryTree failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryFileRaw gets the raw file content of a file.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $sha
   *    The commit or branch name
   *  @param string $filepath
   *    The filepath.
   *  @return string
   *    Returns the file.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getRepositoryFileRaw($search_term, $sha, $filepath) {
    if (isset($search_term) && isset($sha) && isset($filepath)) {
      $search_term = urlencode($search_term);
      $query_data = array('file_path' => $file_path);
      $result = $this->get('/projects/'.$search_term.'/repository/blobs/'.$sha, $query_data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getRepositoryFileRaw failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryFileRaw failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryFileRaw failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryBlobRaw gets the raw blob content.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $sha
   *    The blob sha.
   *  @return string
   *    Returns the blob.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getRepositoryBlobRaw($search_term, $sha) {
    if (isset($search_term) && isset($sha)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/repository/raw_blobs/'.$sha); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getRepositoryBlobRaw failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryBlobRaw failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryBlobRaw failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryArchive gets the archive.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $sha
   *    The optional sha.
   *  @return array
   *    Returns the archive as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getRepositoryArchive($search_term, $sha = null) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      if(isset($sha)) {
        $query_data = array('sha' => $sha);
        $result = $this->get('/projects/'.$search_term.'/repository/archive', $query_data);
      }
      else {
        $result = $this->get('/projects/'.$search_term.'/repository/archive');
      } 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getRepositoryArchive failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryArchive failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryArchive failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryDiff compares the repository between
   *  Tags, branches and SHAs.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $from_id
   *    The from identifier. SHA, branch, tag.
   *  @param string $to_id
   *    The to identifier. SHA, branch, tag.
   *  @return array
   *    Returns the project diff as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getRepositoryDiff($search_term, $from_id, $to_id) {
    if (isset($search_term) && isset($from_id) && isset($to_id)) {
      $search_term = urlencode($search_term);
      $from_id = urlencode($from_id);
      $to_id = urlencode($to_id);
      $result = $this->get('/projects/'.$search_term.'/repository/compare?from='.$from_id.'&to='.$to_id); //@nolint 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getRepositoryDiff failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryDiff failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryDiff failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getRepositoryContributors returns the Contributors.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the project contributor list as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   */
  public function getRepositoryContributors($search_term) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/repository/contributors'); //@nolint 
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return json_decode($result, true);
        }
        else {
          throw new RuntimeException('getRepositoryContributors failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryContributors failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryContributors failed! Arguments not set correctly!'); //@nolint
    }
  }

}
/** @}*/
