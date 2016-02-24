function boldButton (id){
  var pairs = [];
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

  if (button.style.fontWeight !== "bold"){
    button.style.fontWeight = "bold";
    buttonPair.style.fontWeight = "normal";
  }
  else{
    button.style.fontWeight = "normal";
  }
}
