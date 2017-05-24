<?php
	/*
	 * PSO password change form.
	 * Created by gatchi (github.com/gatchi) (christen.got@gmail.com)
	 * Inspired by Soly's PSO registration form. (github.com/Solybum)
	 */
	
	$error = false;
	$dberror = false;
	$passChanged = false;
	$errorString = $dberrorString = $username = $newpass = $oldpass = "";
	$regtime = 0;
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"]))
	{
		if(empty($_POST["username"]))
		{
			$error = true;
			$errorString .= "<br />Enter your username.";
		}
		if(empty($_POST["oldpass"]))
		{
			$error = true;
			$errorSttring .= "<br />Enter your old password.";
		}
		else if(empty($_POST["newpass"]))
		{
			$error = true;
			$errorString .= "<br />Enter a new password.";
		}
		else if(empty($_POST["passcheck"]))
		{
			$error = true;
			$errorString .= "<br />Retype your new password.";
		}
		else if($_POST["newpass"] !== $_POST["passcheck"])
		{
			$error = true;
			$errorString .= "<br />New passwords do not match.";
		}
		
		if($error == false)
		{
			require_once("mysql.php");
			$username = db_escape($_POST["username"]);
			$oldpass = db_escape($_POST["oldpass"]);
			$newpass = db_escape($_POST["newpass"]);
			
			// Obtain regtime for hash
			$query = sprintf("SELECT regtime FROM account_data WHERE username='%s'", $username);
			$result = db_query($query);
			
			if($result == false)
			{
				$dberror = true;
				$dberrorString .= "<br />There was an error connecting to the database.";
				echo db_error();
			}
			else
			{
				$row = mysqli_fetch_assoc($result);
				$regtime = $row["regtime"];
				$md5str = "%s_%u_salt";
				$md5password = md5(sprintf($md5str, $newpass, $regtime));
			}
			
			// Obtain the account hash and compare it with a hash of the submitted old password
			$query = sprintf("SELECT password FROM account_data WHERE username='%s'", $username);
			$result = db_query($query);
			
			if($result == false)
			{
				$dberror = true;
				$dberrorString .= "<br />There was an error connecting to the database.";
				echo db_error();
			}
			else
			{
				$row = mysqli_fetch_assoc($result);
				$accountmd5 = $row["password"];
			}
		
			$oldmd5 = md5(sprintf($md5str, $oldpass, $regtime));
			if($dberror == false && $oldmd5 !== $accountmd5)
			{
				$error = true;
				$errorString .= "<br />Account verification failed -- wrong password.";
			}
			
			// change password
			if($error == false && $dberror == false)
			{
				$query = sprintf("UPDATE account_data SET password = '%s' WHERE username = '%s'", $md5password, $username);
				$result = db_query($query);
				if($result == false)
				{
					$dberror = true;
					$dberrorString .= "<br />Password change failed.  Contact the administrator.";
					echo db_error();
				}
				else
				{
					$passChanged = true;
				}
			}
		}
	}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>Change Password</title>
  <meta name="description" content="Change your player account password here." />
  <meta name="keywords" content="" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="style/style.css" />
</head>

<body>
  <div id="main">
    <div id="site_content">
      <div id="content">
		<?php
			if($_SERVER["REQUEST_METHOD"] == "POST" && $error == false && $dberror == false && $passChanged == true)
			{
				?>
				<h2>Change your password</h2>
				<p>Password changed.</p>
				<?php
			}
			else
			{
				?>
				<h2>Change your password</h2>
				<p>If you know your password, you can supply it below to change it.</p>
				<?php
					if($dberror == true)
					{
						?>
						<p>Please contact the administrators with the following message:
						<?php echo $dberrorString; ?>
						</p>
						<?php
					}
					else if($error == true)
					{
						?>
						<p>Please solve the following errors:
						<?php echo $errorString; ?>
						</p>
						<?php
					}
			}
		?>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
		<div class="form_settings">
			<p>
				<span>Username</span>
				<input type="text" name="username" title="username" placeholder="" value="<?php echo $username;?>" />
			</p>
			<p>
				<span>Old password</span>
				<input type="password" name="oldpass" title="password" placeholder="" value="" />
			</p>
			<p>
				<span>New password</span>
				<input type="password" name="newpass" title="password" placeholder="" value="" />
			</p>
			<p>
				<span>Re-type new password</span>
				<input type="password" name="passcheck" title="password" placeholder="" value="" />
			</p>
			<p>
				<span>&nbsp;</span>
				<input class="submit" type="submit" name="submit" title="submit" value="Submit"/>
			</p>
		</div>
		</form>
      </div>
    </div>
  </div>
</body>
</html>
