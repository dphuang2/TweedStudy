function boldButton (id){

  var pairs = [];
  pairs["only_text"] = "no_text";
  pairs["no_text"] = "only_text";
  pairs["only_pics"] = "no_pics";
  pairs["no_pics"] = "only_pics";
  pairs["only_retweets"] = "no_retweets";
  pairs["no_retweets"] = "only_retweets";
  pairs["tweet_popular"] = "tweet_unpopular";
  pairs["tweet_unpopular"] = "tweet_popular";
  pairs["poster_frequent"] = "poster_infrequent";
  pairs["poster_infrequent"] = "poster_frequent";
  pairs["verified"] = "unverified";
  pairs["unverified"] = "verified";
  pairs["sentiment_positive"] = "sentiment_negative";
  pairs["sentiment_negative"] = "sentiment_positive";
  pairs["close_friends"] = "distant_friends";
  pairs["distant_friends"] = "close_friends";
  pairs["only_videos"] = "no_videos";
  pairs["no_videos"] = "only_videos";
  var button = document.getElementById(id);
  var buttonPair = document.getElementById(pairs[id]);

  if(id == "refresh"){
    button.style.fontWeight = "bold";
    setTimeout(function() {button.style.fontWeight = "normal";},300);
    return;
  }

  var isTrend;
  for(var filter in pairs){
      if(id == filter){
          isTrend = false;
          break;
      } else{
          isTrend = true;
      }
  }

    if(isTrend){
        console.log("isTrend is true");

        if(button.style.fontWeight != "bold"){
              console.log("fontWeight was normal");
            $(".trend").css("font-weight", "normal");
            button.style.fontWeight = "bold";
        } else {
                console.log("fontWeight was bold");
              button.style.fontWeight = "normal";
        }
        if(id == "alloff"){
            console.log("alloff was pressed");
            $(".astext").css("font-weight", "normal");
        }

    } else {
        console.log("isTrend is false");


      if (button.style.fontWeight != "bold"){
        button.style.fontWeight = "bold";
        buttonPair.style.fontWeight = "normal";
      }
      else{
        button.style.fontWeight = "normal";
      }

    }
}
