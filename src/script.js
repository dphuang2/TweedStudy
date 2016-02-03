<html>
<script>
        // Keeping Control Panel on screen
          jQuery(window).scroll(function() {
              jQuery('.totop').stop().animate({ right: '0px' });
          });

//            $("#btn").click(function())
//            {
//                if($_SESSION['sentiment_positive'].value==false){
//                    $_SESSION['sentiment_positive'].value=true;}
//                
//                else {
//                    $_SESSION['sentiment_positive'].value=false;}
//            };

  //                function buttonChange() {
  ////                    var buttonVal
  //                
  //                }

      $("button").on('click', function( event ) {
  //                        alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");
               //                                             <?php
               //                                             if($_SESSION['sentiment_positive'].value==false){
               //                                                 $_SESSION['sentiment_positive'].value=true;}
               //
               //                                             else {
               //                                             $_SESSION['sentiment_positive'].value=false;} ?>
  //                               console.log( event.which );
  //                               console.log( event.target);
                     //This is for Internet Explorer
                     var target = (event.target) ? event.target : event.srcElement;
                     var elem = $( this );
                     var dataString = "currentVal=" + elem.attr("id");

                     alert(dataString);
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
                     $.ajax({
                            
                            type: "POST",
                            url: "src/pass_value.php",
                            data: dataString,
                            dataType: 'json',
                            cache: false,
                            success: function(response) {
                            
                            alert(response.message);
                            
                            }
                            });
                     
                     return false;
                     
                     
  //                  # PUT THE AJAX SESSION VARIABLE UPDATE HERE!
  //                               if ( elem.attr( "id" ).match("sentiment_positive")) {
  //                               #testString = "$test = ($_SESSION['" + elem.attr("id") + "'].value) ? 'true' : 'false'; echo $test;";
  //                               #alert(testString);
  //                               } else {
  //                                       alert("something not working");
  //                               }
                  
                     // alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");
               });


  //                $("div#changeButton").on('click', 'button.astext', function( eventObject ) {
  ////                                             <?php
  ////                                             if($_SESSION['sentiment_positive'].value==false){
  ////                                                 $_SESSION['sentiment_positive'].value=true;}
  ////                             
  ////                                             else {
  ////                                             $_SESSION['sentiment_positive'].value=false;} ?>
  //                                         var elem = $( this );
  //                                         if ( elem.attr( "id" ).match("pos_sen")) {
  //                                             alert("pos_sen");
  //                                         }
  ////                                         alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");
  //                                         });
                 // $("button").click(function() {
                 //     $("#newpost").toggle();
                 // });
</script>
</html>