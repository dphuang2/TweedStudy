$(document).on 'turbolinks:load', ->
  $('.loading').hide()

  data = document.getElementById('data-object')
  num_of_tweets = data.getAttribute('data-numOfTweets')
  tweets_step = num_of_tweets / 5

  handleLow = (low, high) ->
    if low == high
      ret = low-(tweets_step/2)
      if ret < 0
        ret = 0
      return ret
    else
      return low

  handleHigh = (low, high) ->
    if low == high
      ret = high+(tweets_step/2)
      if ret > num_of_tweets
        ret = num_of_tweets
      return ret
    else
      return high

  $('#sent-slider').slider
    range: true
    step: tweets_step
    min: 0
    max: num_of_tweets
    values: [
      0
      num_of_tweets
    ]
    stop: (event, ui) ->
      $.ajax
        url: '/filter/sentiment'
        type: 'GET'
        data:
          low: handleLow ui.values[0], ui.values[1]
          high: handleHigh ui.values[0], ui.values[1]
        dataType: 'script'
        success: (response) ->
      return
    change: (event, ui) ->
      $('#tweet_sent1').val ui.values[0]
      $('#tweet_sent2').val ui.values[1]
      return

  $('#close-slider').slider
    range: true
    step: tweets_step
    min: 0
    max: num_of_tweets
    values: [
      0
      num_of_tweets
    ]
    stop: (event, ui) ->
      $.ajax
        url: '/filter/closeness'
        type: 'GET'
        data:
          low: handleLow ui.values[0], ui.values[1]
          high: handleHigh ui.values[0], ui.values[1]
        dataType: 'script'
        success: (response) ->
      return
    change: (event, ui) ->
      $('#tweet_close1').val ui.values[0]
      $('#tweet_close2').val ui.values[1]
      return

  $('#post-slider').slider
    range: true
    step: tweets_step
    min: 0
    max: num_of_tweets
    values: [
      0
      num_of_tweets
    ]
    stop: (event, ui) ->
      $.ajax
        url: '/filter/poster_frequency'
        type: 'GET'
        data:
          low: handleLow ui.values[0], ui.values[1]
          high: handleHigh ui.values[0], ui.values[1]
        dataType: 'script'
        success: (response) ->
      return
    change: (event, ui) ->
      $('#tweet_post1').val ui.values[0]
      $('#tweet_post2').val ui.values[1]
      return

  $('#pop-slider').slider
    range: true
    step: tweets_step
    min: 0
    max: num_of_tweets
    values: [
      0
      num_of_tweets
    ]
    stop: (event, ui) ->
      $.ajax
        url: '/filter/popularity'
        type: 'GET'
        data:
          low: handleLow ui.values[0], ui.values[1]
          high: handleHigh ui.values[0], ui.values[1]
        dataType: 'script'
        success: (response) ->
      return
    change: (event, ui) ->
      $('#tweet_pop1').val ui.values[0]
      $('#tweet_pop2').val ui.values[1]
      return

  $('#celeb-slider').slider
    range: true
    step: tweets_step
    min: 0
    max: num_of_tweets
    values: [
      0
      num_of_tweets
    ]
    stop: (event, ui) ->
      $.ajax
        url: '/filter/celebrity'
        type: 'GET'
        data:
          low: handleLow ui.values[0], ui.values[1]
          high: handleHigh ui.values[0], ui.values[1]
        dataType: 'script'
        success: (response) ->
      return
    change: (event, ui) ->
      $('#tweet_celeb1').val ui.values[0]
      $('#tweet_celeb2').val ui.values[1]
      return

  resetSliders = ->
    $('*[id*=slider]:visible').each ->
      options = $(this).slider('option')
      $(this).slider 'values', [
        options.min
        options.max
      ]
      return
    $.ajax
      url: '/reset'
      type: 'GET'
      dataType: 'script'
      success: (response) ->
        console.log 'success'
        return
    return

  $('#reset-button').click ->
    resetSliders()
    return

  return


$(document).ajaxStart(->
  $('.loading').show()
  return
)

$(document).ajaxStop(->
  $('.loading').hide()
  return
)
