<?php
session_start();
//print_r($_SESSION);
    
$_SESSION['last_referrer_url'] = $_SERVER['HTTP_REFERER'];
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Tweed Twitter Feed Research </title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="css/survey.css">
	</head>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-8">

            <div class="wrap">
            <h1 class="likert-header">Survey For Previous Interface</h1>
            <form action="">
            <label class="statement">This tool was easy to use.</label>
            <ul class='likert'>
                <li>
                <input type="radio" name="q1" value="strong_agree">
                <label>Strongly agree</label>
                </li>
                <li>
                <input type="radio" name="q1" value="agree">
                <label>Agree</label>
                </li>
                <li>
                <input type="radio" name="q1" value="neutral">
                <label>Neutral</label>
                </li>
                <li>
                <input type="radio" name="q1" value="disagree">
                <label>Disagree</label>
                </li>
                <li>
                <input type="radio" name="q1" value="strong_disagree">
                <label>Strongly disagree</label>
                </li>
            </ul>

            <label class="statement">I saw what I wanted in the News Feed</label>
            <ul class='likert'>
                <li>
                <input type="radio" name="q2" value="strong_agree">
                <label>Strongly agree</label>
                </li>
                <li>
                <input type="radio" name="q2" value="agree">
                <label>Agree</label>
                </li>
                <li>
                <input type="radio" name="q2" value="neutral">
                <label>Neutral</label>
                </li>
                <li>
                <input type="radio" name="q2" value="disagree">
                <label>Disagree</label>
                </li>
                <li>
                <input type="radio" name="q2" value="strong_disagree">
                <label>Strongly disagree</label>
                </li>
            </ul>


            <div class="buttons">
                <button class="clear">Clear</button>
                <button class="kristen">Submit</button>
            </div>
            </form>
            </div>

			</div>

				<div class="col-xs-4 totop">

<?php echo $_GET["q1"]; echo "<br>"; echo $_GET["q2"]; echo "<br>";
    
    
    

    
    
    ?>




				</div>
    		</div>
    	</div>
			<script>
				jQuery(window).scroll(function() {
		   			jQuery('.totop').stop().animate({ right: '0px' });
				});

//                $("button").click(function() {
//                    $("#newpost").toggle();
//                });

//            $("#btn").click(function())
//            {
//                if($_SESSION['sentiment_positive'].value==false){
//                    $_SESSION['sentiment_positive'].value=true;}
//
//                else {
//                    $_SESSION['sentiment_positive'].value=false;}
//            };
//            $("div#changeButton").on('click', 'button.astext', function() {
//                         console.log('is this working');
//                         alert("is this even working?");
//                         });


            $("button.kristen").on('click', function( event ) {
            
                           //This is for Internet Explorer
                           var target = (event.target) ? event.target : event.srcElement;
                           var elem = $( this );
                           var dataString = "currentVal=" + elem.attr("id");
                           
                           alert("test");
                                   
                           $.ajax({
                                type: "POST",
                                url: "saveResponses.php",
                                data: dataString,
                                dataType: 'json',
                                cache: false,
                                success: function(response) {
                                
                                alert(response.message);
                                
                                }
                                });
                                   
                                   });
                           
                           //need to create pass_value.php
                           //should update session variables
                           // dataStrng here should be in json format
                           // just update the one element ID
                           // maybe within or maybe in update_tweets.php (below)
                           // should create long string of SQL query and
                           // update the tweet list div
                           // to update the div you want this line::
                           // document.getElementById("txtHint").innerHTML=xmlhttp.responseText;  Pretty sure this is the AJAX
                           // this will update whatever is inside of the the div with ID equal to "txtHint"
                           // and will replace it with (hopefully correctly formatted html) from responseText
                           // so ideally responseText will already be formatted right (ie.
                           // the output of printTweet)
//                           $.ajax({
//                                  
//                                  type: "POST",
//                                  url: "pass_value.php",
//                                  data: dataString,
//                                  dataType: 'json',
//                                  cache: false,
//                                  success: function(response) {
//                                  
//                                  alert(response.message);
//                                  
//                                  }
//                                  });
//            
//                           }


</script>
</html>











