#!/usr/bin/env python
import MySQLdb
import csv
import inspect
from screen import screen
from boto.mturk.connection import MTurkConnection
import time

def HIT_to_console(hit):
    print "Title: %s" % hit.Title
    print "HITId: %s" % hit.HITId
    print "  HITTypeId: %s" % hit.HITTypeId
    # print "  HITGroupId: %s" % hit.HITGroupId
    # print "  HITLayoutId: %s" % hit.HITLayoutId
    print "  CreationTime: %s" % hit.CreationTime
    # print "  HITStatus: %s" % hit.HITStatus
    print "  Expiration: %s" % hit.Expiration
    print "  NumberOfAssignmentsAvailable: %s" % hit.NumberOfAssignmentsAvailable
    print "  NumberOfAssignmentsCompleted: %s" % hit.NumberOfAssignmentsCompleted
    print "  NumberOfAssignmentsPending: %s" % hit.NumberOfAssignmentsPending

# Credentials should be here
# https://console.aws.amazon.com/iam/home#/security_credential
ACCESS_ID = 'YOUR ACCESSS ID'
SECRET_KEY = 'YOUR SECRET KEY'
HOST = 'mechanicalturk.sandbox.amazonaws.com'

# Make a connection to MTurk using your own credentials
mtc = MTurkConnection(aws_access_key_id=ACCESS_ID,
                      aws_secret_access_key=SECRET_KEY,
                      host=HOST)

# Make a connection to our MySQL database
db = MySQLdb.connect("engr-cpanel-mysql.engr.illinois.edu", "twitterf_user", "IIA@kT$7maLt", "twitterf_tweet_store" )

# hit_ids = mtc.get_all_hits()

print "<<<<<<<<<------Processing Hits------>>>>>>>>>"
loopMe = True

while loopMe:
    time.sleep(5)
    hit_ids = mtc.get_all_hits()
    print "Grabbing HITS"
    for hit in hit_ids:
        HIT_to_console(hit)
        extend_hit_count = 0

        assignments = mtc.get_assignments(hit.HITId)
        if not assignments:
            print "*No assignments for this HIT*"

        print "Processing %i assignments:" % len(assignments)
        for assignment in assignments:
            workerId = assignment.WorkerId
            print "  AssignmentId: %s" % assignment.AssignmentId
            print "  AssignmentStatus: %s" % assignment.AssignmentStatus
            # print "  AutoApprovalTime: %s" % assignment.AutoApprovalTime
            print "  WorkerId: %s" % workerId


            if (assignment.AssignmentStatus != "Rejected") and assignment.AssignmentStatus != "Approved":
                print "Answers from the WorkerID: %s" % assignment.WorkerId
                for question_form_answer in assignment.answers[0]:
                    for answer in question_form_answer.fields:
                        print "Screening:----------------------->"
                        if screen(db, workerId, answer):
                            mtc.approve_assignment(assignment.AssignmentId)
                            print "Approved"
                        else:
                            mtc.reject_assignment(assignment.AssignmentId)
                            extend_hit_count += 1
                            print "Rejected"
        if extend_hit_count > 0:
            mtc.extend_hit(hit.HITId, assignments_increment=extend_hit_count)
        print "-------------------------------------------------------------"
