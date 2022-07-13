<?php
session_start();

require_once "config.php";

$username = $password = "";
$usernameErr = $passwordErr = $loginErr ="";

if($_SERVER["REQUEST_METHOD"] == "POST") {

  if(isset($_POST['login'])) {
    

    $username = trim($_POST["Uname"]);
    $password = trim($_POST["Pass"]);

    $sql = "SELECT id, username, password FROM users WHERE username = ?";

    if($stmt = mysqli_prepare($link,$sql)) {
      mysqli_stmt_bind_param($stmt, "s", $param_username);
      $param_username = $username;

      $options = ['cost' => 10,];
      $rehashed = password_hash($password, PASSWORD_DEFAULT, $options);

      if(mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
          if (mysqli_stmt_fetch($stmt)) {
          if(password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["username"] = $username;

            header("location: .");

          } else {
            $login_err = "Invalid username or password.";
          }
        }
        
      } else { 
        echo "Something went wrong";
      
      }
        mysqli_stmt_close($stmt);
      }
    } mysqli_close($link);

  } elseif (isset($_POST['submit'])) {

    $id = ($_POST['idvalue']);
    $body = ($_POST['postBody']);
    
    $sql = ("INSERT INTO posts (dateandtime, replyTo, username, body) VALUES (?,?,?,?);");    
    $date = getCurDate();

    altermysqli($link, $sql, array($date, $id, trim($_SESSION["username"]),$body));
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fishr</title>
  <link rel="stylesheet" href="main.css">
  <!--<script defer src="login.js"></script> -->
</head>

<body class="light-theme">
  <h1>Fishr</h1>
 
<?php if(isset($_SESSION["loggedin"]) === false) { ?>

  <h2>For tracking all of your fishing data</h2>
  <p id="msg">What this application is capable of:</p>
  <ul>
    <li class="list">Returns weather and wave information for you favourite fishing locations</li>
    <li class="list">Recommends best fishing days for you based on fishing history</li>
    <li class="list">Records your fishing trips for you and the conditions</li><br>
  <ul>
  <noscript>You need to enable JavaScript to view the full site.</noscript>

<html>
<div class ="login">
<h2>Login</h2></br></br>
<form id ="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  <label><b>
    User Name : 
  </b></label>
  <input type='text' name='Uname' id ='Uname' placeholder='Username' required>
  <br><br>

  <label><b>
    Password : 
  </b></label>
    <input type='Password' name='Pass' id='Pass' placeholder='Password' required>
  <br><br>

  <input type='submit' name='login' id='log' value='login'>

  <input type='checkbox' id='check'>
  <span>Remember me</span>
  <br><br>

  Forgot <a href='user/resetpass'>Password</a>
  <br><br>

  Don't have an account? 
  <a href='user/register.php'>
    Click Here
  </a>
  <br><br>
</form>
</div>    

<?php } else { ?>
  
  <div>

  <h2>Welcome <?php echo($_SESSION["username"]) ?></h2></br>

  <a href="logout.php" class="btn btn-danger ml-3">Sign out</a>

  <ul class="nav">
    <li><a href = "user/fishforecast.php">Fishing Forecast</a></li>
    <li><a href = "user/fishhistory.php">Fishing History</a></li>
    <li><a href = "user/shouldigo.php">Should I go fishing?</a></li>
    <li><a href = "user/friends.php">Friends</a></li>
    <li><a href = "user/fishingtrip.php">Record a fishing trip</a></li>
    <li><a href = "user/newpost.php">New Generic Post</a></li>
    <li><a href = "user/posts.php">View my Posts</a></li>
    <li><a href = "user/usercontrol.php">User Control Panel</a><li>
  </ul>


  <br></br>
</div></br>

    <h1>Recent Posts and fishing trips from friends</h1>
<!--  Creating a method to collect a certain number of posts from the database from friends and own user 
      This could be harder to do because of the multiple database
      Use a combine function to add them all together?-->
  <?php 

  $fndFriends = "SELECT accUser, intUser FROM relationships WHERE (intUser = ? OR accUser = ?) AND friends = 1;";
  $stmt = mysqli($link, $fndFriends, array($_SESSION["username"],$_SESSION["username"]));

  //$fndNames = "'".$_SESSION["username"]."' ";
  $fndString = "username = ? ";
  $fndNames = array($_SESSION["username"]);


    if(mysqli_stmt_num_rows($stmt) > 0) {
      mysqli_stmt_bind_result($stmt, $accUser, $intUser);
      while(mysqli_stmt_fetch($stmt)) {

        $friendName = str_ireplace($_SESSION["username"],"",$intUser.$accUser)."";

        array_push($fndNames, $friendName);
        $fndString = $fndString."OR username = ? ";
        //$fndNames = $fndNames."OR '".$friendName."' ";
      }
    } 

  mysqli_stmt_close($stmt);

  $sql = "SELECT * FROM `posts` WHERE $fndString ORDER BY dateandtime DESC;";

  $stmt = mysqli($link, $sql, $fndNames);
  //echo mysqli_num_rows($rpst);


    // if(mysqli_stmt_num_rows($rpst) > 0) {
    //   mysqli_stmt_bind_result($rpst, $dateandtime, $username, $body);

    //   while(mysqli_stmt_fetch($rpst)) {
    //     echo "<div class = 'reply'><I>$username posted on $dateandtime</I></br><p>$body</p>";
    //   }   

    // }



  if(mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $id, $dateandtime, $username, $replys, $title, $body, $tripDate, $totalFish, $bigFish, $conditions, $images);

    while(mysqli_stmt_fetch($stmt)) {

      if(strcmp($username, trim($_SESSION["username"])) == 0) {
        $username = "You";
      }

      if($replys == NULL) {
        echo "<div id='$id' class ='post'>";
        echo "<h3>$title</h3><I>$username posted on $dateandtime</I>";
        echo "<p>$body</p>";      

        if ($tripDate != NULL) {
          echo "<p>Trip was taken on $tripDate, where $totalFish fish were caught</br>";
          if ($bigFish != NULL) {
            echo "These included $bigFish</p></br>";
          }
        }

        echo "</div>";

        //can this $id part here be used for sql injection as it it not a user input? Could easily bind it possibly.
        $rpsql = "SELECT `dateandtime`,`username`, `replyTo`, `body` FROM `posts` WHERE `replyTo` = $id ORDER BY dateandtime ASC";
        $rpst = mysqli_query($link, $rpsql);  

        while($row = mysqli_fetch_assoc($rpst)) {
          $replyer = $row['username'];
          $replyDT = $row['dateandtime'];
          $replyBody = $row['body'];
          echo "<div class = 'reply'><I>$replyer replied on $replyDT</I></br><p>$replyBody</p></div>";         
        }
        mysqli_free_result($rpst);

        //create reply area
        echo "<div class = 'reply'><form name='replyto$id' id='replyto$id' method='post' action=".htmlspecialchars($_SERVER["PHP_SELF"])."><input type='hidden' name='idvalue' value='$id'><textarea rows='3' cols='50' name='postBody' id ='pBody' placeholder='Enter your reply here' required></textarea><br><input type='submit' name='submit' value='submit$id'></form>";      
        echo "</div>";
        echo "</br></br>";  

      }

    }  

  }
  mysqli_stmt_close($stmt);
}   



?>

<br><br>
  
</body>

</html>