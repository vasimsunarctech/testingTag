<?php
	$dblocal = 1;
	
	if ($dblocal<=1){
	$host = "dummylocalhost";
	$user = "dummycarymedicine";
	$password = "dummyzSh3e3&0";
	$db = "carymedicine";
	
	} else{
		$host = "localhost";
		$user = "pukhraj";
		$password = "pukhraj123";
		$db = "harendar";
	
	}
	$con = @mysqli_connect("$host","$user","$password","$db");
	// Check connection
	if (mysqli_connect_errno())
  	{
  		echo "." . mysqli_connect_error();
  	}
	else
		{
			echo "..". "<br>";
		}
	$sql = "INSERT INTO
 			 clientinfo(fname, lname, address1, city, state, zip, email, pan, pn, amount, cardtype, nameoncard, transid, corelationid )
			VALUES('".$firstName."', '".$lastName."', '".$address1."', '".$city."', '".$state."', '".$zip."', '".$email."', '".$pan."', '".$pn."', '".$amount."', '".$creditCardType."', '".$nameCard."', '".$trans."', '".$corel."')
			"; 
	// Execute query
	if (@mysqli_query($con,$sql))
	  {
  		echo ".";
 	 }
	else
  	{
  		echo "." . @mysqli_error($con);
  	}

	@mysqli_close($con);
?>

