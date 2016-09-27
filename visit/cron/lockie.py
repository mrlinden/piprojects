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
        cur.execute("SELECT * from `visits`.`daytable` WHERE `date` = '%s'", intervalStop.strftime(DATE_FORMAT))
        row = cur.fetchone()
        if (row != None):
            log("Exist in daytable %d" + row['visits'])
            sensorDayTotal = row['visits']
        
except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()

