<?php

//db credentials

$servername="localhost";
$username="root";
$password="";
$dbname="01_machine_test";

$conn= new mysqli($servername,$username,$password,$dbname);

if($conn->connect_error){
	die("db connection failed please check");
}

function Tokenn()
{
   return md5(uniqid(rand(), true));
}

//form insert code

if($_SERVER["REQUEST_METHOD"]=="POST")
{

   $name=$_POST['name'];
   $phone=$_POST['phone'];
   $email=$_POST['email'];
   $subject=$_POST['subject'];
   $message=$_POST['message'];
   $token=$_POST['token'];

   if(empty($token) || !isset($_POST['token']) || $_POST['token'] != $token)
   {
      $error[] = "Invalid Form Submission try again";
   }

   if(empty($name)|| empty($phone)|| empty($email)|| empty($subject)|| empty($message))
   {
      $error[] = "Please fill all fields and try again";
   }

   if(!filter_var($email, FILTER_VALIDATE_EMAIL))
   {
      $error[] = "Please enter valid email address";
   }

   if(!preg_match("/^[0-9]{10}$/", $phone))
   {
      $error[] = "Please enter valid phone";
   }

   if(empty($error))
   {
   	//here we use query binding to prevent sql injection
		$stmt = $conn->prepare("INSERT INTO contact_form (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
		
		if($stmt)
		{
			$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
	        $stmt->execute();
	        $stmt->close();
			$success[]="Form submited successfully";
		}else{
				echo "error stmt";
			}
       
        $token = Tokenn();

       // send mail

		try
		{
            $to = "admin@gmail.com";
			$subject = $subject;
			$txt = $message;
			$headers = "From: $email";

			mail($to,$subject,$txt,$headers);
		} catch (Exception $e){
			$errors[] = "Cant able to send mail";
		}

			
   } 

}



$conn->close();

?>


<!DOCTYPE html>
<html>
<head>
<title>Form submit with validation</title>
</head>
<body>

<h1>Form</h1>

<form action="" method="post">

<label>Full name</label>
<input type="text" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : "" ; ?>">

<input type="hidden" name="token" value="<?php echo $token; ?>">

<label>Phone</label>
<input type="text" name="phone" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ""; ?>">

<label>Email</label>
<input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>">

<label>Subject</label>
<input type="text" name="subject" value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ""; ?>">

<label>Message</label>
<input type="text" name="message" value="<?php echo isset($_POST['message']) ? $_POST['message'] : ""; ?>">

<button type="submit">Submit</button>

</form>

<?php 

     if(!empty($error)){
        echo "<ul>";
        foreach($error as $err)
        {
           echo "<li>$err</li>";
        }
        echo "</ul>";
     }

     if(!empty($success)){
     	foreach($success as $sss){
             echo "$sss";
     	}
     	
     }

?>

</body>
</html>