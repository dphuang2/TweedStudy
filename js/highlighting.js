function boldButton (id){
  var pairs = [];
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
  var button = document.getElementById(id);
  var buttonPair = document.getElementById(pairs[id]);


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
