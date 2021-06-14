<html>
	<head>
		<title>Unban User</title>
		<link rel="stylesheet" href="style.css"/>
	</head>
	
	<body>
		
		
		<div class="smain">
<?php
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../incl/lib/mainLib.php";
$gs = new mainLib();
if(!empty($_POST["userName"]) AND !empty($_POST["password"]) AND !empty($_POST["userID"])){
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$userID = $ep->remove($_POST["userID"]);
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
		$query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
		$accountID = $query->fetchColumn();
		if($gs->checkPermission($accountID, "toolLeaderboardsban")){
			if(!is_numeric($userID)){
				exit("Invalid userID");
			}
			$query = $db->prepare("UPDATE users SET isBanned = 0 WHERE userID = :id");
			$query->execute([':id' => $userID]);
			if($query->rowCount() != 0){
				echo "Unbanned succesfully.";
			}else{
				echo "Unban failed.";
			}
			$query = $db->prepare("INSERT INTO modactions  (type, value, value2, timestamp, account) 
													VALUES ('15',:userID, '0',  :timestamp,:account)");
			$query->execute([':userID' => $userID, ':timestamp' => time(), ':account' => $accountID]);
		}else{
			exit("You do not have the permission to do this action. <a href='leaderboardsUnban.php'>Try again</a>");
		}
	}else{
		echo "Invalid password or nonexistant account. <a href='leaderboardsUnban.php'>Try again</a>";
	}
}else{
	echo '<form action="leaderboardsUnban.php" method="post">Your Username: <input type="text" name="userName">
		<br>Your Password: <input type="password" name="password">
		<br>Target UserID: <input type="text" name="userID">
		<br><input type="submit" value="unBan"></form>';
}
?>
			</table>
		</div>
	</body>
</html>