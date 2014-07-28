<?php
/*
 * @file Notes.php
 * Defines the class Notes which extends the Client class with all notes
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
 * The class Notes provides easy to use access to a GitLab instance.
 *
 */
class Notes extends Client {

  /**
   *  The function getAllIssueNotes returns the project notes.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $issue_id
   *    The issue id.
   *  @return array
   *    Returns the notes as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllIssueNotes($search_term, $issue_id) {
    if (isset($search_term) && isset($issue_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/issues/'.$issue_id.'/notes'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllIssueNotes failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllIssueNotes failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllIssueNotes failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getIssueNote returns the note.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $issue_id
   *    The issue id.
   *  @param string $note_id
   *    The note id.
   *  @return array
   *    Returns the notes as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getIssueNote($search_term, $issue_id, $note_id) {
    if (isset($search_term) && isset($issue_id) && isset($note_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/issues/'.$issue_id.'/notes/'.$note_id); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getIssueNote failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getIssueNote failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getIssueNote failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createIssueNote creates a new issue note.
   *  @param string $search_term
   *    The search term to identify the repository.
   *  @param string $issue_id
   *    The issue id.
   *  @param string $body
   *    The content of new issue.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createIssueNote($search_term, $issue_id, $body) { 
    if (isset($search_term) && isset($issue_id) && isset($body)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('body' => $body);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/issues/'.$issue_id.'/notes/', $data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createIssueNote failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createIssueNote failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createIssueNote failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getAllSnippetNotes returns the project snippets notes.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $snippet_id
   *    The snippet id.
   *  @return array
   *    Returns the notes as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllSnippetNotes($search_term, $snippet_id) {
    if (isset($search_term) && isset($snippet_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/snippets/'.$snippet_id.'/notes'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllSnippetNotes failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllSnippetNotes failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllSnippetNotes failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getSnippetNote returns the note.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $snippet_id
   *    The snippet id.
   *  @param string $note_id
   *    The note id.
   *  @return array
   *    Returns the notes as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getSnippetNote($search_term, $snippet_id, $note_id) {
    if (isset($search_term) && isset($snippet_id) && isset($note_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/snippets/'.$snippet_id.'/notes/'.$note_id); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getSnippetNote failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getSnippetNote failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getSnippetNote failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createSnippetNote creates a new snippet note.
   *  @param string $search_term
   *    The search term to identify the repository.
   *  @param string $snippet_id
   *    The snippet id.
   *  @param string $body
   *    The content of new snippet note.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createSnippetNote($search_term, $snippet_id, $body) { 
    if (isset($search_term) && isset($snippet_id) && isset($body)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('body' => $body);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/snippets/'.$snippet_id.'/notes/', $data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createSnippetNote failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createSnippetNote failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createSnippetNote failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getAllMergeRequestNotes returns the project merge
   *  request notes.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $merge_request_id
   *    The merge request id.
   *  @return array
   *    Returns the notes as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getAllMergeRequestNotes($search_term, $merge_request_id) {
    if (isset($search_term) && isset($merge_request_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/merge_requests/'.$merge_request_id.'/notes'); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getAllMergeRequestNotes failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getAllMergeRequestNotes failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getAllMergeRequestNotes failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function getMergeRequestNote returns the note.
   *  @param string $search_term
   *    The search_term to identify the repository.
   *  @param string $merge_request_id
   *    The merge request id.
   *  @param string $note_id
   *    The note id.
   *  @return array
   *    Returns the notes as an array.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case arguments were not set.
   */
  public function getMergeRequestNote($search_term, $merge_request_id, $note_id) {
    if (isset($search_term) && isset($merge_request_id) && isset($note_id)) {
      $search_term = urlencode($search_term);
      $result = $this->get('/projects/'.$search_term.'/merge_requests/'.$merge_request_id.'/notes/'.$note_id); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 200) {
          $result = json_decode($result, true); // decode the json string
          return $result;
        }
        else {
          throw new RuntimeException('getMergeRequestNote failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('getMergeRequestNote failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('getMergeRequestNote failed! Arguments not set correctly!'); //@nolint
    }
  }

  /**
   *  The function createMergeRequestNote creates a new merge request note.
   *  @param string $search_term
   *    The search term to identify the repository.
   *  @param string $merge_request_id
   *    The merge request id.
   *  @param string $body
   *    The content of new merge request note.
   *  @return bool
   *    Returns true in case it succedes otherwise it throws.
   *  @throws RuntimeException
   *    Throws RuntimeException if an HTTP error was encounterd or in case
   *    the request could not be performed.
   *  @throws InvalidArgumentException
   *    Throws InvalidArgumentException in case required arguments
   *    are not set.
   */
  public function createMergeRequestNote($search_term, $merge_request_id, $body) { 
    if (isset($search_term) && isset($merge_request_id) && isset($body)) {
      $search_term = urlencode($search_term);
      // set data
      $data = array('body' => $body);
      // execute query
      $result = $this->post('/projects/'.$search_term.'/merge_requests/'.$merge_request_id.'/notes/', $data); //@nolint
      if ($result !== false) {
        $return_code = intval(curl_getinfo($this->curlClient)['http_code']);
        if ($return_code === 201) {
          return true;
        }
        else {
          throw new RuntimeException('createMergeRequestNote failed! HTTP Error Code:'.$return_code); //@nolint
        }
      }
      else {
        throw new RuntimeException('createMergeRequestNote failed! Curl request to GitLab server failed!'); //@nolint
      }
    }
    else {
      throw new InvalidArgumentException('createMergeRequestNote failed! Arguments not set correctly!'); //@nolint
    }
  }
  
}
/** @}*/
