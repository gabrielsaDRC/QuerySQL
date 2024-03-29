<?php
$response = array();
include 'db/db_connect.php';
include 'db/funcions.php';
 
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); 
 
if(isset($input['username']) && isset($input['password'])){
	$username = $input['username'];
	$password = $input['password'];
	$query    = "SELECT full_name,password_hash, salt FROM member WHERE username = ?";
 
	if($stmt = $con->prepare($query)){
		$stmt->bind_param("s",$username);
		$stmt->execute();
		$stmt->bind_result($fullName,$passwordHashDB,$salt);
		if($stmt->fetch()){
			if(password_verify(concatPasswordWithSalt($password,$salt),$passwordHashDB)){
				$response["status"] = 0;
				$response["message"] = "Login successful";
				$response["full_name"] = $fullName;
			}
			else{
				$response["status"] = 1;
				$response["message"] = "Invalid username and password combination";
			}
		}
		else{
			$response["status"] = 1;
			$response["message"] = "Invalid username and password combination";
		}
		
		$stmt->close();
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "Missing mandatory parameters";
}
echo json_encode($response);