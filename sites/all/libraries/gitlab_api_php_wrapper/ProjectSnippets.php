<?php
/*
 * @file ProjectSnippets.php
 * Defines the class ProjectSnippets which extends the Client class with all project
 * snippets centered admin API calls to GitLab.
 *
 */

/* !
 *  \addtogroup GitLabAPI
 *  @{
 */
namespace GitLabAPI;

require_once 'Client.php';

/**
 * The class ProjectSnippets provides easy to use access to a GitLab instance.
 *
 */
class ProjectSnippets extends Client {

  /**
   *  The function getAllProjectSnippets lists all the project snippets
   *  of the project specified by the ID or NAMESPACE/PROJEKT.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @return array
   *    Returns the list of project snippets as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @todo Look at the page and per_page options
   *    @link http://doc.gitlab.com/ce/api/ Pagination @endlink
   */
  public function getAllProjectSnippets($search_term) {
    $result = $this->get('/projects/'.$search_term.'/snippets');
    if ($result !== false) {
      $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
      if ($return_code === 200) {
        return json_decode($result, true);
      }
      else {
        throw new RuntimeException('getAllProjectSnippets failed! HTTP Error Code:'.$return_code); //@nolint
      }
    }
    else {
      throw new RuntimeException('getAllProjectSnippets failed! Curl request to GitLab server failed!'); //@nolint
    }
  }

  /**
   *  The function getProjectSnippet returns the GitLab project snippet specified by the ID
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $snippet_id
   *    The snippet_id to identify the project snippet.
   *  @return array
   *    Returns the project snippet as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProjectSnippet($search_term, $snippet_id) {
    if (isset($search_term) && isset($snippet_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/snippets/'.$snippet_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getProjectSnippet failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProjectSnippet failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProjectSnippet failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createProjectSnippet creates a new GitLab project snippet.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $title
   *    The title of the snippet.
   *  @param string $file_name
   *    The file name of a snippet.
   *  @param string $code
   *    The code of the snippet.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createProjectSnippet($search_term,
                                       $title,
                                       $file_name,
                                       $code) { 
    if (isset($search_term) && isset($title) && isset($file_name) && isset($code)) { //@nolint
      $search_term = urlencode($search_term);
      // set data
      $data = array('title' => $title,
                    'file_name' => $file_name,
                    'code' => $code);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/snippets', $data);
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
   *  The function deleteProjectSnippet deletes a GitLab project snippet.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $snippet_id
   *    The snippet_id to identify the project snippet.
   *  @return
   *    Returns true if succcesful otherwise throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function deleteProjectSnippet($search_term, $snippet_id) {
    if (isset($search_term)) {
      $search_term = urlencode($search_term);
      $result = $this->delete('/projects/'.$search_term.'/snippets/'.$snippet_id);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('deleteProjectSnippet failed! HTTP Error Code: '.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('deleteProjectSnippet failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('deleteProjectSnippet failed! Arguments not set correctly!');  //@nolint
    }
  }

  /**
   *  The function editProjectSnippet edits a GitLab project snippet.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $snippet_id
   *    The snippet_id to identify the project snippet.
   *  @param array $params
   *    An array of non required parameters.
   *    - title - The title of a snippet
   *    - file_name - The name of a snippet file
   *    - code - The content of a snippet
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function editProjectSnippet($search_term, $snippet_id, array $params) {
    if (isset($params) && isset($search_term) && isset($snippet_id)) {
      $result = $this->put('/projects/'.$search_term.'/snippets/'.$snippet_id, $params);
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          return true;
        }
        else {
          throw new RuntimeException('editProjectSnippet failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('editProjectSnippet failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('editProjectSnippet failed! Arguments not set correctly!'); //@nolint
    }
  }

 /**
   *  The function getProjectSnippetContent returns the content of a snippet.
   *  @param string $search_term
   *    The search_term to identify the project.
   *  @param string $snippet_id
   *    The snippet_id to identify the project snippet.
   *  @return string
   *    Returns the content of a snippet as text.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getProjectSnippetContent($search_term, $snippet_id) {
    if (isset($search_term) && isset($snippet_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/snippets/'.$snippet_id.'/raw');
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getProjectSnippetContent failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getProjectSnippetContent failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getProjectSnippetContent failed! Arguments not set correctly!'); //@nolint
    }
  }


}
/** @}*/
