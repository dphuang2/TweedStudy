# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema.define(version: 20170607234723) do

  create_table "friends", force: :cascade do |t|
    t.string   "nickname"
    t.string   "name"
    t.string   "image_url"
    t.string   "twitter_url"
    t.integer  "statuses_count"
    t.integer  "verified"
    t.integer  "followers_count"
    t.string   "location"
    t.string   "screen_name"
    t.string   "lang"
    t.integer  "friends_count"
    t.string   "description"
    t.string   "twitter_creation_date"
    t.string   "time_zone"
    t.float    "twitter_id"
    t.integer  "user_id"
    t.datetime "created_at",            null: false
    t.datetime "updated_at",            null: false
    t.float    "post_frequency"
    t.float    "fake_post_frequency"
    t.integer  "closeness"
    t.integer  "fake_closeness"
    t.integer  "fake_verified"
    t.integer  "celebrity"
    t.integer  "fake_celebrity"
    t.index ["user_id"], name: "index_friends_on_user_id"
  end

  create_table "messages", force: :cascade do |t|
    t.text     "text"
    t.integer  "user_id"
    t.float    "sender_id"
    t.string   "sender_name"
    t.string   "sent_date"
    t.integer  "sentiment"
    t.integer  "word_count"
    t.datetime "created_at",  null: false
    t.datetime "updated_at",  null: false
    t.float    "twitter_id"
    t.index ["user_id"], name: "index_messages_on_user_id"
  end

  create_table "tracks", force: :cascade do |t|
    t.string   "twitter_id"
    t.string   "screen_name"
    t.integer  "sentiment_low"
    t.integer  "closeness_low"
    t.integer  "poster_frequency_low"
    t.integer  "celebrity_low"
    t.integer  "popularity_low"
    t.datetime "created_at",            null: false
    t.datetime "updated_at",            null: false
    t.integer  "verified_low"
    t.integer  "sentiment_high"
    t.integer  "closeness_high"
    t.integer  "poster_frequency_high"
    t.integer  "celebrity_high"
    t.integer  "popularity_high"
    t.integer  "verified_high"
    t.string   "real_feed_position"
    t.string   "fake_feed_position"
    t.integer  "feed"
  end

  create_table "tweets", force: :cascade do |t|
    t.text     "text"
    t.integer  "user_id"
    t.datetime "created_at",               null: false
    t.datetime "updated_at",               null: false
    t.string   "retweet_user_screen_name"
    t.string   "retweet_user_profile_img"
    t.string   "retweet_user_url"
    t.string   "user_profile_img"
    t.string   "user_screen_name"
    t.string   "media_url"
    t.string   "hashtags"
    t.integer  "retweet_count"
    t.integer  "favorite_count"
    t.float    "tweet_id"
    t.string   "tweet_created_at"
    t.integer  "popularity"
    t.integer  "fake_popularity"
    t.string   "retweet_user_name"
    t.string   "user_name"
    t.string   "user_url"
    t.integer  "sentiment"
    t.integer  "fake_sentiment"
    t.float    "poster_frequency"
    t.float    "fake_poster_frequency"
    t.integer  "verified"
    t.integer  "fake_verified"
    t.integer  "closeness"
    t.integer  "fake_closeness"
    t.string   "pick"
    t.integer  "celebrity"
    t.integer  "fake_celebrity"
    t.index ["user_id"], name: "index_tweets_on_user_id"
  end

  create_table "users", force: :cascade do |t|
    t.string   "nickname"
    t.datetime "created_at",            null: false
    t.datetime "updated_at",            null: false
    t.string   "name"
    t.string   "image_url"
    t.string   "twitter_url"
    t.integer  "statuses_count"
    t.integer  "verified"
    t.integer  "followers_count"
    t.string   "location"
    t.string   "screen_name"
    t.string   "lang"
    t.integer  "friends_count"
    t.string   "description"
    t.string   "twitter_creation_date"
    t.string   "time_zone"
    t.float    "twitter_id"
    t.string   "real_feed_position"
    t.string   "fake_feed_position"
  end

end
