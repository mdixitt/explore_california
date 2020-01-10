<?php
require 'config.php';
require 'database.php';
$g_title = BLOG_NAME . ' - Register';
$g_page = 'register';
require 'header.php';
require 'menu.php';
require('databaseconnection.php');

ob_start();
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

$tbl_name="members"; // Table name if you wish to use a variable

$myusername= !empty($_POST['myusername']) ? $_POST['myusername'] : '';
$myemail= !empty($_POST['myemail']) ? $_POST['myemail'] : '';
$mypassword= !empty($_POST['mypassword']) ? $_POST['mypassword'] : '';
$mypassword2= !empty($_POST['mypassword2']) ? $_POST['mypassword2'] : '';


$errors = array();

if(isset($_SESSION['username'])){
$message = 'You are already logged in.';
echo "<script type='text/javascript'>
alert('$message');
</script>";
header("Refresh:0; url=index.php", true, 303);
}

?>


<?php

//if (!isset($_POST['mypassword']) || count($errors) > 0)
{
?>
<div class="w3-content w3-light-grey w3-padding-64 w3-center" style="max-width:1500px">

<img src="./w3images/boy.jpeg" class="w3-image w3-padding-32" width="300" height="300">
<form name="form1" method="post" action="register.php" style="margin:auto;width:60%" >
<p>Just me, myself and I, exploring the universe of unknownment. I have a heart of love and an interest of lorem ipsum and mauris neque quam blog. I want to share my world with you. Praesent tincidunt sed tellus ut rutrum. Sed vitae justo condimentum, porta lectus vitae, ultricies congue gravida diam non fringilla. Praesent tincidunt sed tellus ut rutrum. Sed vitae justo condimentum, porta lectus vitae, ultricies congue gravida diam non fringilla.</p>

<p class="w3-large w3-text-pink">Member register</p>
    <div class="w3-section">
      <label><b>Username</b></label>
      <input class="w3-input w3-border" name="myusername" type="text" id="myusername" value="<?=$myusername?>" required alt="Username" ></td>
   <label><b> Email ID</b></label>
<input class="w3-input w3-border" name="myemail" type="text" id="myemail" value="<?=$myemail?>" required>
<label><b>Password</b></label>
<input class="w3-input w3-border" name="mypassword" type="password" id="mypassword" required>
<label><b>Verify Password</b></label>
<input class="w3-input w3-border" name="mypassword2" type="password" id="mypassword2" required>

<input type="submit" name="Submit" value="Register">



<?php
}
if (isset($_POST['mypassword']))
{

if (empty($myusername)) { array_push($errors, "Username required!"); }
if (empty($myemail)) { array_push($errors, "Email is required!"); }
if (empty($mypassword)) { array_push($errors, "Password required!"); }
if (empty($mypassword2)) { array_push($errors, "Please confirm password!"); }

if (!filter_var($myemail, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Email is not valid!"); }
if ($mypassword != $mypassword2) { array_push($errors, "The two passwords do not match!"); }

$user_check_query = "SELECT username, email FROM members WHERE username=:myusername OR email=:myemail LIMIT 1";
$statement = $db->prepare($user_check_query);
$statement->bindParam(':myusername',$myusername);
$statement->bindParam(':myemail',$myemail);
$statement->execute() or die(print_r($statement->errorInfo(), true));
$user = $statement->fetch();

if ($user) { // if user exists, which field?
if ($user['username'] == $myusername) {array_push($errors, "Username already exists!");}
if ($user['email'] == $myemail) {array_push($errors, "Email already exists!");}
}

if (count($errors) == 0) {
// salting adds uniqueness to each entry
$salt=uniqid() ;
$salted_password=$salt.$mypassword;
$encrypted_password = hash("sha512", $salted_password);

$insert_sql="insert into members (username,password,salt,email) values (:myusername,:encrypted_password,:salt,:myemail)";
$statement = $db->prepare($insert_sql);
$statement->bindParam(':myusername',$myusername);
$statement->bindParam(':encrypted_password',$encrypted_password);
$statement->bindParam(':salt',$salt);
$statement->bindParam(':myemail',$myemail);
$statement->execute() or die(print_r("User already registered. Please login.", true));
$pass = $statement->fetch();

echo 'You are registered.';
header("Refresh:2; url=main_login.php", true, 303);
}
else{
foreach ($errors as $error) {
echo "<p>$error</p>";
}
}

}
?>
</td>
</tr>
</table>
</div>
<?php

ob_end_flush();
require 'footer.php';
?>

