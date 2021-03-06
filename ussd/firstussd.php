<?php
//1. Ensure ths code runs only after a POST from AT
if(!empty($_POST) && !empty($_POST['phoneNumber'])){
	require_once('connection.php');
	require_once('AfricasTalkingGateway.php');
	require_once('config.php');
	//2. receive the POST from AT
	$sessionId     =$_POST['sessionId'];
	$serviceCode   =$_POST['serviceCode'];
	$phoneNumber   =$_POST['phoneNumber'];
	$text          =$_POST['text'];
	//3. Explode the text to get the value of the latest interaction - think 1*1
	$textArray=explode('*', $text);
	$userResponse=trim(end($textArray));
	//4. Set the default level of the user
	$level=0;
	//5. Check the level of the user from the DB and retain default level if none is found for this session
	$sql = "select level from session_levels where session_id ='".$sessionId." '";
	$levelQuery = $db->query($sql);
	if($result = $levelQuery->fetch_assoc()) {
  		$level = $result['level'];
	}	
	//6. Create an account and ask questions later
	$sql6 = "SELECT * FROM account WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
	$acQuery=$db->query($sql6);
	if(!$acAvailable=$acQuery->fetch_assoc()){
		$sql1A = "INSERT INTO account (`phoneNumber`) VALUES('".$phoneNumber."')";
		$db->query($sql1A); 
	}
	//7. Check if the user is in the db
	$sql7 = "SELECT * FROM users WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
	$userQuery=$db->query($sql7);
	$userAvailable=$userQuery->fetch_assoc();
	//8. Check if the user is available (yes)->Serve the menu; (no)->Register the user
	if($userAvailable && $userAvailable['city']!=NULL && $userAvailable['name']!=NULL){
		//9. Serve the Services Menu (if the user is fully registered, 
		//level 0 and 1 serve the basic menus, while the rest allow for financial transactions)
		if($level==0 || $level==1){
			//9a. Check that the user actually typed something, else demote level and start at home
			switch ($userResponse) {
			    case "":
			        if($level==0){
			        	//9b. Graduate user to next level & Serve Main Menu
			        	$sql9b = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
			        	$db->query($sql9b);
			        	//Serve our services menu
							$response = "CON Welcome to Kwagric " . $userAvailable['name']  . ". Choose the product you want to order.\n";
						$response .= " 1. Cassava.\n";
						$response .= " 2. Yam\n";
						$response .= " 3. Egg\n";
						$response .= " 4. Maize\n";
						$response .= " 5. Update your Delivery address\n";
			  			// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;						
			        }
			        break;
			    case "0":
			        if($level==0){
			        	//9b. Graduate user to next level & Serve Main Menu
			        	$sql9b = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
			        	$db->query($sql9b);
			        	//Serve our services menu
						$response = "CON Welcome to Kwagric " . $userAvailable['name']  . ". Choose the product you want to order.\n";
						$response .= " 1. Cassava.\n";
						$response .= " 2. Yam\n";
						$response .= " 3. Egg\n";
						$response .= " 4. Maize\n";
						$response .= " 5. Update your Delivery address\n";
			  			// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;						
			        }
			        break;			        
			    case "1":
			        if($level==1){
			            //9e. Ask how much and Launch the Mpesa Checkout to the user
						$response = "CON How many tons of Cassava do you want?\n";
						$response .= " 1. A ton of cassava.\n";
						$response .= " 2. Two tons of cassava.\n";
						$response .= " 3. Three tons of cassava.\n";							
						//Update sessions to level 9
				    	$sqlLvl9="UPDATE `session_levels` SET `level`=9 where `session_id`='".$sessionId."'";
				    	$db->query($sqlLvl9);
			  			// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;	 			    
			    
			        }
			        break;
			    case "2":
			    	if($level==1){
			    		//9e. Ask how much and Launch the Mpesa Checkout to the user
						$response = "CON How many tuber of yam do you want?\n";
						$response .= " 1. 500pcs.\n";
						$response .= " 2. 1000pcs.\n";
						$response .= " 3. 1500pcs.\n";							
						//Update sessions to level 9
				    	$sqlLvl9="UPDATE `session_levels` SET `level`=10 where `session_id`='".$sessionId."'";
				    	$db->query($sqlLvl9);
			  			// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;	 			    		
			    	}
			        break;	
			    case "3":
			    	if($level==1){
			    		//9e. Ask how much and Launch B2C to the user
						$response = "CON How many crate of egg do you want?\n";
						$response .= " 1. 100 Crate of egg.\n";
						$response .= " 2. 200 Crate of egg\n";
						$response .= " 3. 300 Crate of egg.\n";							
						//Update sessions to level 10
				    	$sqlLvl10="UPDATE `session_levels` SET `level`=11 where `session_id`='".$sessionId."'";
				    	$db->query($sqlLvl10);
			  			// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;	 			    		
			    	}
			        break;	
			    case "4":
			    	if($level==1){
			    		//9g. Send Another User Some Money
						$response = "CON How mang bags of maize do you want?\n";
						$response .= " 1. 50 Bags of Maize.\n";
						$response .= " 2. 100 Bags of Maize\n";
						$response .= " 3. 150 Bags of Maize\n";		
						//Update sessions to level 11
				    	$sqlLvl11="UPDATE `session_levels` SET `level`=12 where `session_id`='".$sessionId."'";
				    	$db->query($sqlLvl11);
				    	// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;
 			    		
			    	}
			        break;
			    case "5":
			    	if($level==1){
				    	$response = "CON Enter Your New Address.\n";
			  			// Print the response onto the page so that our gateway can read it
			  			header('Content-type: text/plain');
 			  			echo $response;	
						//Update sessions to level 11
				    	$sqlLvl11="UPDATE `session_levels` SET `level`=13 where `session_id`='".$sessionId."'";
				    	$db->query($sqlLvl11);					    						
							
					    
 			    		
			    	}
			    break;		        				        				        			        		        
			    default:
			    	if($level==1){
				        // Return user to Main Menu & Demote user's level
				    	$response = "CON You have to choose a service.\n";
				    	$response .= "Press 0 to go back.\n";
				    	//demote
				    	$sqlLevelDemote="UPDATE `session_levels` SET `level`=0 where `session_id`='".$sessionId."'";
				    	$db->query($sqlLevelDemote);
	
				    	// Print the response onto the page so that our gateway can read it
				  		header('Content-type: text/plain');
	 			  		echo $response;	
			    	}
			}
		}else{
			// Financial Services Delivery
			switch ($level){
			    case 9:
			    	//9a. Collect Deposit from user, update db
					switch ($userResponse) {
					    case "1":
						    //End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="A ton of cassava";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 	        			       	
				        break;	
					    case "2":
					        // End session
					    	$response = "END made successfully \n Kindly wait some minutes while we process your order\n";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="Two tons of cassava";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 		        	       	
					    break;
					    case "3":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order\n";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="Three tons of cassava";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 			        		       	
					    break;
					    default:
						$response = "END Apologies, something went wrong... \n";
					  		// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
					  		echo $response;	
					    break;
					}				
		        	break;
			    case 10: 
			    	//Withdraw fund from account
					switch ($userResponse) {
					    case "1":
						    //End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="500pcs of yam";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 	        			       	
				        break;	
					    case "2":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="1000pcs of Yam";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 		        	       	
					    break;
					    case "3":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="1500 pcs of Yam";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 			        		       	
					    break;
					    default:
						$response = "END Apologies, something went wrong... \n";
					  		// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
					  		echo $response;	
					    break;
					}		        	
			    	break;	
			    case 11:
	
						switch ($userResponse) {
					    case "1":
						    //End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="100 Crate of eggs";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 	        			       	
				        break;	
					    case "2":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="200 Crate of eggs";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 		        	       	
					    break;
					    case "3":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="300 Crate of egg";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 			        		       	
					    break;
					    default:
						$response = "END Apologies, something went wrong... \n";
					  		// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
					  		echo $response;	
					    break;
					}		        		
		        	break;
		         case 12:
	
						switch ($userResponse) {
					    case "1":
						    //End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="50 bags of Maize";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 	        			       	
				        break;	
					    case "2":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="100 bags of Maize";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 		        	       	
					    break;
					    case "3":
					        // End session
					    	$response = "END Order made successfully \n Kindly wait some minutes while we process your order.\n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="150 bags of Maize";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 			        		       	
					    break;
					    default:
						$response = "END Apologies, something went wrong... \n";
					  		// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
					  		echo $response;	
					    break;
					}		        		
		        	break;
		        case 13:
	
					
					    	$response = "END  Delivery Address changed successfully \n Cheers \n Kwagric";
					    	// Print the response onto the page so that our gateway can read it
					  		header('Content-type: text/plain');
		 			  		echo $response;	
		 			  		$amount="50 bags of Maize";
							//Create pending record in checkout to be cleared by cronjobs
				        	$sql9aa = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
				        	$db->query($sql9aa); 	        			       	
				       
						        		
		        	break;
			    default:
			    	//11g. Request for city again
					$response = "END Apologies, something went wrong... \n";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
				  	echo $response;	
			    	break;		        
			}
		}
	} else{
		//10. Check that user response is not empty
		if($userResponse==""){
			//10a. On receiving a Blank. Advise user to input correctly based on level
			switch ($level) {
			    case 0:
				    //10b. Graduate the user to the next level, so you dont serve them the same menu
				     $sql10b = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."', 1)";
				     $db->query($sql10b);
				     //10c. Insert the phoneNumber, since it comes with the first POST
				     $sql10c = "INSERT INTO users(`phonenumber`) VALUES ('".$phoneNumber."')";
				     $db->query($sql10c);
				     //10d. Serve the menu request for name
				     $response = "CON Please enter your name";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
 			  		echo $response;	
			        break;
			    case 1:
			    	//10e. Request again for name - level has not changed...
        			$response = "CON Name not supposed to be empty. Please enter your name \n";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
 			  		echo $response;	
			        break;
			    case 2:
			    	//10f. Request for city again --- level has not changed...
					$response = "CON Role not supposed to be empty. Please reply with your Delivery Address \n";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
 			  		echo $response;	
			        break;
			 case 3:
			    	//10f. Request for city again --- level has not changed...
					$response = "CON Address not supposed to be empty. Please reply with your Delivery Address \n";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
 			  		echo $response;	
			        break;
			    default:
			    	//10g. End the session
					$response = "END Apologies, something went wrong... \n";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
 			  		echo $response;	
			        break;
			}
		}else{
			//11. Update User table based on input to correct level
			switch ($level) {
			    case 0:
				    //10b. Graduate the user to the next level, so you dont serve them the same menu
				     $sql10b = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."', 1)";
				     $db->query($sql10b);
				     //10c. Insert the phoneNumber, since it comes with the first POST
				     $sql10c = "INSERT INTO users (`phonenumber`) VALUES ('".$phoneNumber."')";
				     $db->query($sql10c);
				     //10d. Serve the menu request for name
				     $response = "CON Please enter your name";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
				  		echo $response;	
			    	break;		    
			    case 1:
			    	//11b. Update Name, Request for city
			        $sql11b = "UPDATE users SET `name`='".$userResponse."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
			        $db->query($sql11b);
			        //11c. We graduate the user to the city level
			        $sql11c = "UPDATE `session_levels` SET `level`=2 WHERE `session_id`='".$sessionId."'";
			        $db->query($sql11c);
			        //We request for the city
			        $response = "CON Please enter your Delivery Address";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
				  		echo $response;	
			    	break;
			    case 2:
			    	//11d. Update city
			        $sql11d = "UPDATE users SET `city`='".$userResponse."' WHERE `phonenumber` = '". $phoneNumber ."'";
			        $db->query($sql11d);
			    	//11e. Change level to 0
		        	$sql11e = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
		        	$db->query($sql11e);  
					//11f. Serve the menu request for name
					$response = "END You have been successfully registered. Dial *384*315# to make your orders.";	        	   	
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
				  	echo $response;	
			    	break;			        		        		        
			    default:
			    	//11g. Request for city again
					$response = "END Apologies, something went wrong... \n";
			  		// Print the response onto the page so that our gateway can read it
			  		header('Content-type: text/plain');
				  	echo $response;	
			    	break;
			}	
		}		
	} 
}
?>