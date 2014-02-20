<?php include("api.php");?>

<html>

<body>
  <h1>Sign up</h1>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  // TODO: check that password and confirmation match
  $response = register_user($_POST['username'], $_POST['password'], $_POST['email']);
  
  if($response['response status'] == '200') {
    print "<p>User created.</p>";
  }
  else {
    print "<p>Something went wrong: " . $response['response body']->message."</p>";
  }
}
?>

  <form action="" method="post">
    <!-- todo: repopulate these fields on error -->
    Username: <input type="text" name="username"/><br/>
    Password: <input type="password" name="password"/><br/>
    <!-- todo: password confirmation field -->
    Email: <input type="text" name="email"/><br/>
    <input type="submit"/>
  </form>
  
</body>
</html>

