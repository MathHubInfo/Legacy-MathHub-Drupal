<?php
/*
 * @file RepositoryFiles.php
 * Defines the class RepositoryFiles which extends the Client class with all repository
 * files centered admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class RepositoryFiles provides easy to use access to a GitLab instance.
 *
 */
class RepositoryFiles extends Client {

  /**
   *  The function getRepositoryFile returns the file.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $file_path
   *    The file path.
   *  @param string $ref
   *    The name of a branch, tag or commit.
   *  @return array
   *    Returns the file as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getRepositoryFile($search_term, $file_path, $ref) {
    if (isset($search_term) && isset($file_path) && isset($ref)) {
      $query_data = array('file_path' => $file_path, 'ref' => $ref);
      $result = $this->get('/projects/'.$search_term.'/repository/files', $query_data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getRepositoryFile failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getRepositoryFile failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getRepositoryFile failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createRepositoryFile creates a new GitLab repository file.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $file_path
   *    The new file_path of the file.
   *  @param string $branch_name
   *    The branch_name name.
   *  @param string $content
   *    The content of the file.
   *  @param string $commit_message
   *    The commit message.
   *  @param string $encoding
   *    The encoding 'text' or 'base64'.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createRepositoryFile($search_term,
                                       $file_path,
                                       $branch_name,
                                       $content,
                                       $commit_message,
                                       $encoding = 'text') { 
    if (isset($search_term) &&
        isset($file_path) &&
        isset($branch_name) &&
        isset($content) &&
        isset($commit_message) &&
        isset($encoding)) { 
      $search_term = urlencode($search_term);
      // set data
      $data = array('file_path' => $file_path,
                    'branch_name' => $branch_name,
                    'content' => $content,
                    'commit_message' => $commit_message,
                    'encoding' => $encoding);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/repository/files', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createRepositoryFile failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createRepositoryFile failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createRepositoryFile failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function editRepositoryFile edits a an repository file.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $file_path
   *    The new file_path of the file.
   *  @param string $branch_name
   *    The branch_name name.
   *  @param string $content
   *    The content of the file.
   *  @param string $commit_message
   *    The commit message.
   *  @param string $encoding
   *    The encoding 'text' or 'base64'.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editRepositoryFile($search_term,
                                     $file_path,
                                     $branch_name,
                                     $content,
                                     $commit_message,
                                     $encoding = 'text') { 
    if (isset($search_term) &&
        isset($file_path) &&
        isset($branch_name) &&
        isset($content) &&
        isset($commit_message) &&
        isset($encoding)) { 
      $search_term = urlencode($search_term);
      // set data
      $data = array('file_path' => $file_path,
                    'branch_name' => $branch_name,
                    'content' => $content,
                    'commit_message' => $commit_message,
                    'encoding' => $encoding);
      // execute query
      $result = $this->put('/projects/'.$search_term.'/repository/files', $data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editRepositoryFile failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editRepositoryFile failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editRepositoryFile failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function deleteRepositoryFile deletes a file in a repository.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $file_path
   *    The file path.
   *  @param string $branch_name
   *    The branch_name name.
   *  @param string $commit_message
   *    The commit message.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteRepositoryFile($search_term,
                                       $file_path,
                                       $branch_name,
                                       $commit_message) {
    if (isset($search_term) && 
        isset($file_path) &&
        isset($branch_name) &&
        isset($commit_message)) { 
      $search_term = urlencode($search_term);
      $query_data = array('file_path' => $file_path,
                          'branch_name' => $branch_name,
                          'commit_message' => $commit_message);
      $result = $this->delete('/projects/'.$search_term.'/repository/files', $query_data);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteRepositoryFile failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteRepositoryFile failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteRepositoryFile failed! Arguments not set correctly!');  //@nolint
    }
  }



}
/** @}*/
