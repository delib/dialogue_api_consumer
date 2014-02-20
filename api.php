<?php

/*****************************************************************************/
/* All requests to the API must be authenticated via http Basic Auth,
with the username 'api' and a password matching the API secret provided by Delib.

In addition, some methods require an __ac cookie to be sent with the request,
Authenticating the Dialogue App user. */
/*****************************************************************************/

ini_set('display_errors', 1);
session_start();

// The url of the staging/live Dialogue App instance
// Hit this URL in a browser for the latest API documentation
$API_BASE = "http://127.0.0.1:20000/api/2.0";

// Set up by Delib in the Dialogue App instance.  This is a secret.
// Keep it as secret as you'd keep a mysql password (for example),
// so definitely don't expose it client-side :)
// If anyone actually uses "fishes" in production it will be a sad day.
$API_SECRET = "fishes";

// This stores the auth cookie for the user on whose behalf the API
// is currently acting.  This is needed for the authenticated methods
// eg creating ideas.
// TODO: You'll need a separate cookiejar for each visitor.
// Perhaps include PHP session ID in the filename to ensure it's unique?
$COOKIE_JAR = "/tmp/cookiejar";  

// GET/POST request.
// I don't know if this is the best way to do it; I got it from stackoverflow :/
// Returns the requested URL and request method and parameters, and the
// resulting HTTP status code and the decoded JSON object, if there is one.
// if $raw_response is True, returns http response headers as well.
// Most of this output is just for testing/illustration.
// In practice you probably only need to return the status code and JSON data
function dialogue_api_request($path, $data=array(), $method, $raw_response=False){
  global $API_BASE, $API_SECRET, $COOKIE_JAR;
  
  if($method != 'GET' and $method != 'POST') {
    return "Invalid request method - must be GET or POST";
  }

  $url = $API_BASE . $path;
  
  if($method == 'GET') {
    $query = http_build_query($data);
    $url .= '?' . $query;
  }
  
  $ch = curl_init($url);
  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
  curl_setopt($ch, CURLOPT_USERPWD, "api:" . $API_SECRET);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $COOKIE_JAR);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $COOKIE_JAR);

  if($method == 'POST') {
    curl_setopt($ch, CURLOPT_POST, True);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  }
  
  if($raw_response) {
    curl_setopt($ch, CURLOPT_HEADER, True);
  }
    
  $response = curl_exec($ch);
  $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  
  $ret = array('url'=>$url, 'method'=>$method, 'data'=>$data, 'response status'=>$http_status);

  if($raw_response) {
    $ret['response body'] = $response;
  }
  else {
    $ret['response body'] = json_decode($response);
  }
  return $ret;
  
}

function dialogue_api_get($path, $data=array(), $raw_response=False) {
  return dialogue_api_request($path, $data, 'GET', $raw_response);
}

function dialogue_api_post($path, $data=array(), $raw_response=False) {
  return dialogue_api_request($path, $data, 'POST', $raw_response);
}

/*******************************************************/
/* Anonymous methods (no user authentication required) */
/*******************************************************/

function register_user($username, $password, $email) {
  $data = array('username'=>$username, 'password'=>$password, 'email'=>$email);
  return dialogue_api_post("/users", $data);
}

function request_password_reset_token($username) {
  $data = array('username'=>$username);
  return dialogue_api_post("/users/password/reset", $data);
}

function reset_password($username, $token, $new_password) {
  $data = array('username'=>$username, 'token'=>$token, 'password'=>$new_password);
  return dialogue_api_post("/users/password/reset/consume", $data);
}

function get_idea($discussion_id, $idea_id) {
  return dialogue_api_get("/discussions/".$discussion_id."/ideas/".$idea_id);
}

function login_user($username, $password) {
  $data = array('__ac_name'=>$username, '__ac_password'=>$password);
  
  // get raw response (for testing purposes only)
  // because we want to see what cookie is being sent
  $raw_response = dialogue_api_post("/users/login", $data, True);
  return $raw_response;
}

function get_tags_for_site() {
  return dialogue_api_get("/tags");
}

function get_tags_for_discussion($discussion_id) {
  return dialogue_api_get("/discussions/".$discussion_id."/tags");
}

function get_discussion_list() {
  return dialogue_api_get("/discussions");
}

function get_discussion($discussion_id) {
  return dialogue_api_get("/discussions/".$discussion_id);
}

function search_ideas($discussion_id="", $text="", $tags="", $sort="", $order="") {
  $data = array('text'=>$text, 'tags'=>$tags);
  if($sort) $data['sort']=$sort;
  if($order) $data['order']=$order;
  
  if($discussion_id) {
    return dialogue_api_post("/discussions/".$discussion_id."/search", $data);
  }
  else {
    return dialogue_api_post("/search", $data);    
  }
}

/********************************************/
/* Authenticated methods (need __ac cookie) */
/********************************************/

function get_authenticated_user() {
  return dialogue_api_get("/users/authenticated");
}

function change_password($old_password, $new_password) {
  $data = array('current'=>$old_password, 'new'=>$new_password);
  return dialogue_api_post("/users/password/change", $data);
}

function get_user_profile($username) {
  return dialogue_api_get("/users/profiles/" . $username);
}

function add_idea($discussion_id, $title, $what, $why) {
  $data = array('title'=>$title, 'what'=>$what, 'why'=>$why);
  return dialogue_api_post("/discussions/ideas/".$discussion_id, $data);
}

function add_tags($discussion_id, $idea_id, $tags) {
  // $tags is a comma-separated string
  $data = array('tags'=>$tags);
  return dialogue_api_post("/discussions/".$discussion_id."/ideas/".$idea_id."/tags", $data);
}

function add_rating($discussion_id, $idea_id, $rating) {
  // $tags is a comma-separated string
  $data = array('rating'=>$rating);
  return dialogue_api_post("/discussions/".$discussion_id."/ideas/".$idea_id."/ratings", $data);
}

function add_comment($discussion_id, $idea_id, $comment) {
  $data = array('body'=>$comment);
  return dialogue_api_post("/discussions/".$discussion_id."/ideas/".$idea_id."/comments", $data);
}

function logout_user() {
  // get raw response (for testing purposes only)
  // because we want to check that the cookie has been unset
  $raw_response = dialogue_api_post("/users/logout", array(), True);
  return $raw_response;
}

/******************************************************************/
/* Admin-only methods (need to be logged in as a site admin user) */
/******************************************************************/

function add_discussion($title, $summary, $body) {
  $data = array('title'=>$title, 'summary'=>$summary, 'body'=>$body);
  return dialogue_api_post("/discussions", $data);
}

?>

