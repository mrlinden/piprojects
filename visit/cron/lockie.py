#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2016
# Script that reads the GPIO inputs and counts the number of GPIO sensor openings

import MySQLdb as mdb
import sys
import RPi.GPIO as GPIO
import datetime
import time
import os
try:
    from configparser import ConfigParser
except ImportError:
    from ConfigParser import ConfigParser  # ver. < 3.0

# Some constants
ALL_GPIO_IN = [16, 26, 20, 21]
#ALL_GPIO_IN = [16]
DATE_TIME_FORMAT = "%Y-%m-%d %H:%M:%S"
DATE_FORMAT = "%Y-%m-%d"

logging = True

# Some global data
done = False
con = False
sensorCnt = [0, 0, 0, 0]

# Some configuration
config = ConfigParser()
config.read('../config.ini')
db_host     = config.get('database','db_host')
db_name     = config.get('database','db_name')
db_user     = config.get('database','db_user')
db_password = config.get('database','db_password')

# Some functions
def log(message):
    if (logging):
        print(message + "\n")

try:
        intervalStart = datetime.datetime.now()

        intervalStop = datetime.datetime.now()
        
        con = mdb.connect(db_host, db_user, db_password, db_name)
        cur = con.cursor(mdb.cursors.DictCursor)
        
        # get current stored value for day (re-read it instead of incrementing in this script 
        # to avoid getting tables out of sync if this script crashes)
        sensorDayTotal = 0
        cur.execute("SELECT * from `visits`.`daytable` WHERE `date` = %s", intervalStop.strftime(DATE_FORMAT))
        row = cur.fetchone()
        if (row != None):
            sensorDayTotal = row['visits']
            log("Exist in daytable. Nr was " + str(sensorDayTotal))

        sensorDayTotal = sensorDayTotal + 1

        sql_insert_day = "INSERT INTO `visits`.`daytable` (`date`, `visits`, `complete`) VALUES (%s,%s,0) ON DUPLICATE KEY UPDATE visits='%s', complete=0"
        addedLines = cur.execute(sql_insert_day, (intervalStart.strftime(DATE_FORMAT), sensorDayTotal, sensorDayTotal))
        if (addedLines != 1):
            log("ERROR. Did not update database as expected. \nSQL was " + sql_insert_day + " \nResult was " + str(addedLines) + "...")
        
        con.commit()
        log ("written " + str(sensorDayTotal) + " to database ")
        sensorCnt = [0, 0, 0, 0]
        intervalStart = intervalStop
    
except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()

