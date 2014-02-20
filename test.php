<?php
include("api.php");

function print_heading($str) {
  print "<h2>".$str."</h2>";
}

function print_pre($str) {
  print "<pre>";
  print_r($str);
  print "</pre>";
}
?>

<h1>Table of Contents</h1>
<a href="#creating-users">Creating users</a><br/>
<a href="#password-reset">Resetting passwords</a><br/>
<a href="#idea-info">Getting info on individual ideas</a><br/>
<a href="#getting-tags">Getting tags</a><br/>
<a href="#logging-in">Logging in</a><br/>
<a href="#changing-password">Changing password</a><br/>
<a href="#user-profiles">User profiles</a><br/>
<a href="#adding-ideas">Adding ideas</a><br/>
<a href="#adding-comments">Adding comments</a><br/>
<a href="#tagging-rating">Tagging and rating</a><br/>
<a href="#logging-out">Logging out</a><br/>
<a href="#more-ratings">More ratings</a><br/>
<a href="#creating-discussions">Creating discussions</a><br/>
<a href="#listing-discussions">Listing discussions</a><br/>
<a href="#searching-ideas">Listing/searching ideas</a><br/>

<?php
/*******************************************************/
print '<hr/><h1 id="creating-users">Creating users</h1>';
  
print_heading("creating a couple of users");
print_pre(register_user('Jess', '12345', 'jess@example.com'));
print_pre(register_user('Bob', '12345', 'bob@example.com'));

print_heading("Creating a user with a blank password");
print_pre(register_user('Jess2', '', 'jess@delib.net'));

print_heading("Creating a user that already exists");
print_pre(register_user('Jess', '12345', 'jess@delib.net'));

print_heading("Creating a user with a blank username");
print_pre(register_user('', '12345', 'jess@delib.net'));

print_heading("Creating a user with a blank email");
print_pre(register_user('Jess3', '12345', ''));

/*******************************************************/
print '<hr/><h1 id="password-reset">Resetting passwords</h1>';

// Ask for token to reset Jess's password.
// This should be sent to the user's registered email address
// They can then use this to request a password reset
print_heading("Requesting a password reset token");
$resp = request_password_reset_token('Jess');
print_pre($resp);

// Now reset the password using the token returned
print_heading("Using the password reset token");
print_pre(reset_password('Jess', $resp['response body']->token, 'something'));

// Try it again, to make sure you can only use each token once
print_heading("Trying to reuse the password reset token");
print_pre(reset_password('Jess', $resp['response body']->token, 'something'));

print_heading("Requesting a password reset token for a non-existant user");
print_pre(request_password_reset_token('ZZZ'));

print_heading("Attempting to change (as opposed to reset) password when not logged in");
print_pre(change_password('something', 'something_new'));

/*******************************************************/
print '<hr/><h1 id="idea-info">Getting info on individual ideas</h1>';

print_heading("Getting idea info");
print_pre(get_idea('ideas', 'it-worked'));

print_heading("Getting idea info for a non-existant idea");
print_pre(get_idea('ideas', 'non-existant'));

print_heading("Getting idea info for a non-existant discussion");
print_pre(get_idea('non', 'non-existant'));

/*******************************************************/
print '<hr/><h1 id="getting-tags">Getting tags</h1>';

print_heading("Getting tags for site");
print_pre(get_tags_for_site());

print_heading("Getting tags for discussion");
print_pre(get_tags_for_discussion('ideas'));

print_heading("Getting tags for non-existant discussion");
print_pre(get_tags_for_discussion('hhh'));

print_heading("Getting tags for blank discussion");
print_pre(get_tags_for_discussion(''));

/*******************************************************/
print '<hr/><h1 id="logging-in">Logging in</h1>';

print_heading("Logging in user Jess with wrong password");
print_pre(login_user('Jess', 'hhh'));

print_heading("Logging in non-existant user");
print_pre(login_user('aaa', 'hhh'));

print_heading("Logging in user Jess");
print_pre(login_user('Jess', 'something'));

/*******************************************************/
print '<hr/><h1 id="changing-password">Changing password</h1>';

print_heading("Changing a password with the wrong old password");
print_pre(change_password('wrong', 'something_new'));

print_heading("Changing a password if you know the old password");
print_pre(change_password('something', 'something_new'));

print_heading("Changing it back again");
print_pre(change_password('something_new', 'something'));

/*******************************************************/
print '<hr/><h1 id="user-profiles">User profiles</h1>';

print_heading("Find out who I am logged in as");
print_pre(get_authenticated_user());

print_heading("Getting my own user profile");
print_pre(get_user_profile('Jess'));

print_heading("Getting somebody else's user profile");
print_pre(get_user_profile('Bob'));

print_heading("Getting a non-existant user's profile");
print_pre(get_user_profile('zzz'));

/*******************************************************/
print '<hr/><h1 id="adding-ideas">Adding ideas</h1>';

print_heading("Adding an idea");
//print_pre(add_idea('ideas', 'Jess\'s idea', 'This is what the idea is', 'This is why it\'s important'));

print_heading("Adding an idea to an invalid discussion");
print_pre(add_idea('zzz', 'Jess\'s idea', 'This is what the idea is', 'This is why it\'s important'));

print_heading("Adding an idea with a missing Title");
print_pre(add_idea('ideas', '', 'This is what the idea is', 'This is why it\'s important'));

print_heading("Adding an idea with a missing description");
print_pre(add_idea('ideas', 'Jess\'s idea', '', 'This is why it\'s important'));

print_heading("Adding an idea with a missing 'why'");
print_pre(add_idea('ideas', 'Jess\'s idea', 'This is what the idea is', ''));

/*******************************************************/
print '<hr/><h1 id="adding-comments">Adding comments</h1>';

print_heading("Adding comments to my idea");
//print_pre(add_comment('ideas', 'jesss-idea', 'I like bees'));
//print_pre(add_comment('ideas', 'jesss-idea', 'I don\'t like spiders'));

print_heading("Adding comments to a non-existant idea");
print_pre(add_comment('ideas', 'zzz', 'I like bees'));

print_heading("Adding comments to a non-existant discussion");
print_pre(add_comment('hhh', 'zzz', 'I like bees'));

print_heading("Adding a blank comment");
print_pre(add_comment('ideas', 'jesss-idea', ''));

/*******************************************************/
print '<hr/><h1 id="tagging-rating">Tagging and rating</h1>';

print_heading("Adding tags to my idea");
print_pre(add_tags('ideas', 'jesss-idea', 'bees, honey, bananas'));

print_heading("Attempting to add rating to my own idea");
print_pre(add_rating('ideas', 'jesss-idea', '5'));

/*******************************************************/
print '<hr/><h1 id="logging-out">Logging out</h1>';

print_heading("Logging out");
print_pre(logout_user());

print_heading("Show that I'm not logged in as anyone anymore");
print_pre(get_authenticated_user());

print_heading("Attempting to add an idea after logging out");
print_pre(add_idea('ideas', 'Jess\'s idea', 'This is what the idea is', 'This is why it\'s important'));

/*******************************************************/
print '<hr/><h1 id="more-ratings">More ratings</h1>';

print_heading("Logging in user Bob");
print_pre(login_user('Bob', '12345'));

print_heading("Getting my own user profile");
print_pre(get_user_profile('Bob'));

print_heading("Getting somebody else's user profile");
print_pre(get_user_profile('Jess'));

print_heading("Attempting to rate Jess's idea with something invalid");
print_pre(add_rating('ideas', 'jesss-idea', 'cheese'));

print_heading("Rating Jess's idea");
print_pre(add_rating('ideas', 'jesss-idea', 2));

print_heading("Clearing my rating");
print_pre(add_rating('ideas', 'jesss-idea', ''));

print_heading("Changing my rating (this time using a string instead of an int)");
print_pre(add_rating('ideas', 'jesss-idea', '3'));

print_heading("Attempting to create a discussion (not allowed)");
print_pre(add_discussion('new-discussion', 'summary', 'body'));

print_heading("Logging out");
print_pre(logout_user());


/*******************************************************/
print '<hr/><h1 id="creating-discussions">Creating discussions</h1>';

print_heading("Logging in user admin");
print_pre(login_user('admin', 'admin'));

print_heading("Creating a discussion");
print_pre(add_discussion('new-discussion', 'summary', 'body'));

print_heading("Creating a discussion with blank title");
print_pre(add_discussion('', 'summary', 'body'));

print_heading("Creating a discussion with blank summary");
print_pre(add_discussion('new-discussion', '', 'body'));

print_heading("Creating a discussion with blank body");
print_pre(add_discussion('new-discussion', 'summary', ''));

print_heading("Logging out");
print_pre(logout_user());

/*******************************************************/
print '<hr/><h1 id="listing-discussions">Listing discussions</h1>';

print_heading("Listing discussions");
print_pre(get_discussion_list());

print_heading("Getting info about 'ideas' discussion");
print_pre(get_discussion('ideas'));

print_heading("Getting info about private discussion xzifqoawlz");
print_pre(get_discussion('xzifqoawlz'));

print_heading("Getting info about non-existant discussion");
print_pre(get_discussion('fff'));

/*******************************************************/
print '<hr/><h1 id="searching-ideas">Listing/searching ideas</h1>';

print_heading("Listing all ideas");
print_pre(search_ideas());

print_heading("Listing all ideas in 'ideas' discussion");
print_pre(search_ideas('ideas'));

print_heading("Searching for ideas containing the word 'test'");
print_pre(search_ideas('', 'test'));

print_heading("Searching for ideas tagged with 'bananas'");
print_pre(search_ideas('', '', 'bananas'));

print_heading("Searching for ideas tagged with 'apples'");
print_pre(search_ideas('', '', 'apples'));

print_heading("Searching for ideas tagged with 'bananas' and 'bees'");
print_pre(search_ideas('', '', 'bananas, bees'));

print_heading("Searching for ideas tagged with 'bananas' and 'apples'");
print_pre(search_ideas('', '', 'bananas, apples'));

print_heading("Searching for ideas sorted by rating (lowest first)");
print_pre(search_ideas('', '', '', 'rating', 'ascending'));

print_heading("Searching for ideas sorted by rating (highest first)");
print_pre(search_ideas('', '', '', 'rating', 'descending'));

print_heading("Searching for ideas sorted by created date (oldest first)");
print_pre(search_ideas('', '', '', 'created', 'ascending'));

print_heading("Searching for ideas sorted by created date (newest first)");
print_pre(search_ideas('', '', '', 'created', 'descending'));

print_heading("Searching for ideas sorted by number of comments (fewest first)");
print_pre(search_ideas('', '', '', 'comments', 'ascending'));

print_heading("Searching for ideas sorted by number of comments (most first)");
print_pre(search_ideas('', '', '', 'comments', 'descending'));

?>
