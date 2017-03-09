# workerID should come from the AMT data -- their worker ID
# workerAnswer should come from the AMT data -- what they answered as the survey code


def screen(db, workerId, workerAnswer):

    payThem = False

    sql_workerExistsAndCompare = "SELECT code FROM `survey_responses_userinfo` WHERE `turkerID` = '" + workerId + "'"

    db.query(sql_workerExistsAndCompare)

    r = db.store_result()

    workerCode = r.fetch_row()

    # Exit from function if worker was not found
    if not workerCode:
        print "Worker was not found"
    else:
        # if rows exist, check next condition, that the two match
        # check that the code they answered in the survey matches what we have
        if workerCode[0][0] == workerAnswer:
            print "workerCode equals workerAnswer!"
            # get user_id to not have to do joins in the future
            sql_getTwitterId = "SELECT user_id FROM `survey_responses_userinfo` WHERE `turkerID` = '" + workerId + "'"
            db.query(sql_getTwitterId)
            r = db.store_result()
            twitterId = r.fetch_row()
            if twitterId:
                twitterId = twitterId[0][0]

                # check number of clicks per page
                sql_numberClicks = "SELECT SUM( survey_responses.`0.001` ) , SUM( survey_responses.`0.251` ) , SUM( survey_responses.`0.501` ), SUM( survey_responses.`0.751` ), SUM( survey_responses.`1.001` ), SUM( survey_responses.`1` ) , SUM( survey_responses.`0.25` ),  SUM( survey_responses.`0.5` ),  SUM( survey_responses.`0` ) FROM `survey_responses` WHERE user_id = '" + str(twitterId) + "'"
                db.query(sql_numberClicks)
                r = db.store_result()
                numClicks = r.fetch_row()
                if numClicks:
                    totalClicks = 0
                    for i in range(len(numClicks[0])):
                        totalClicks = totalClicks + numClicks[0][i]

                    if totalClicks > 12 * 2.5:
                        # do the verification question test
                        verificationTask = {1: "strong_agree", 2: "neutral", 3: "strong_disagree", 4: "disagree", 5: "neutral", 6: "agree", 7: "strong_agree", 8: "strong_disagree", 9: "disagree", 10: "agree", 11: "neutral", 12: "strong_agree", 13: "agree", 14: "strong_disagree", 15: "strong_agree", 16: "agree", 17: "disagree", 18: "neutral"}
                        numWrong = 0
                        numCorrect = 0
                        for i in range(1, 19):
                            print i
                            sql_getVerificationAnswer = "SELECT question3 FROM `survey_responses` WHERE user_id = '" + str(twitterId) + "' AND page = '" + str(i) + "'"
                            db.query(sql_getVerificationAnswer)
                            r = db.store_result()
                            verifyAns = r.fetch_row()[0][0]
                            if verifyAns == verificationTask[i]:
                                print verifyAns
                                print verificationTask[i]
                                numCorrect += 1
                            else:
                                numWrong += 1
                            print numCorrect
                            print numWrong
                        if numCorrect > 15:
                            payThem = True

    return payThem

    # PAY THEM



# Lookup table for verification questions
# Set verification question as third question on every page

# automically reject
# feedback :
# no record of survey
# not enough number of clicks
# did not interact with system as instructed
# you incorrectly answered too many of our verification questions (more than 15% wrong)
#
# 85% - 16 questions right
