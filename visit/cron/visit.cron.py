#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2016
# Script that reads the GPIO inputs and counts the number of GPIO sensor openings

import os
import sys
import MySQLdb as mdb
import RPi.GPIO as GPIO
import datetime
import time
import logging
from logging.handlers import TimedRotatingFileHandler
try:
    from configparser import ConfigParser
except ImportError:
    from ConfigParser import ConfigParser  # ver. < 3.0

# Constants
ALL_GPIO_IN = [16, 26, 20, 21]
DATE_TIME_FORMAT = "%Y-%m-%d %H:%M:%S"
DATE_FORMAT = "%Y-%m-%d"

# Configuration
config = ConfigParser()
config.read('../config.ini')
db_host     = config.get('database','db_host')
db_name     = config.get('database','db_name')
db_user     = config.get('database','db_user')
db_password = config.get('database','db_password')
logMode     = config.get('logging','log_mode')
logPath     = config.get('logging','log_path')

# Some functions
def log(message):
    if (logMode == "console"):
        print(message + "\n")
    elif (logMode == "file"):
        if not hasattr(log, "logger"):
            logger = logging.getLogger("Visit Log")
            logger.setLevel(logging.INFO)
            handler = TimedRotatingFileHandler(logPath,
                                               when="midnight",
                                               interval=1,
                                               backupCount=10)
            logger.addHandler(handler)
            #log.logFile = open(logPath, "w")  # it doesn't exist yet, so initialize it
        log.logger.info(message + "\n")
        #log.logFile.flush()

def actOnSensor(gpioIn):
    global sensorCnt

    try:
        time.sleep(0.05) # Needed because sometimes GPIO.input did not return True for rising events 
        sensorNr = ALL_GPIO_IN.index(gpioIn)

        if (GPIO.input(gpioIn)):
            sensorCnt[sensorNr] = sensorCnt[sensorNr] + 1
            log ("Sensor %d opened! Total counts this interval: %d." % (sensorNr, sensorCnt[sensorNr]))
        else:
            log ("Sensor %d closed!" % sensorNr)

    except ValueError:
        log ("Unexpected sensor %d " % gpioIn)


# Program start
log("Started Visit sensor service\n\n")
GPIO.setmode(GPIO.BCM)

for gpioInNr in ALL_GPIO_IN:
    log ("Setup GPIO nr %s as input" % gpioInNr)
    GPIO.setup(gpioInNr, GPIO.IN, pull_up_down = GPIO.PUD_OFF )
    GPIO.add_event_detect(gpioInNr, GPIO.RISING, callback=actOnSensor, bouncetime=20)

try:
    done = False
    sensorCnt = [0, 0, 0, 0]
    intervalStart = datetime.datetime.now()
 
    while not done:
        time.sleep(60)
        intervalStop = datetime.datetime.now()
        
        con = mdb.connect(db_host, db_user, db_password, db_name)
        cur = con.cursor(mdb.cursors.DictCursor)
        
        # get current stored value for day (re-read it instead of incrementing local variable
        # in this script to avoid getting tables out of sync if this script is stoped/restarted)
        sensorDayTotal = 0
        cur.execute("SELECT * from `visits`.`daytable` WHERE `date` = %s", intervalStop.strftime(DATE_FORMAT))
        row = cur.fetchone()
        if (row != None):
            sensorDayTotal = row['visits']
        
        sensorDayTotal = sensorDayTotal + sensorCnt[0] + sensorCnt[1] + sensorCnt[2] + sensorCnt[3]

        log (str(intervalStop) + " sensor A: %d sensor B: %d sensor C: %d sensor D: %d dayTotal %d" % (sensorCnt[0], sensorCnt[1], sensorCnt[2], sensorCnt[3], sensorDayTotal))
        
        sql_insert_minute = "INSERT INTO `visits`.`minutetable` (`intervalStart`, `intervalStop`, `doorA`, `doorB`, `doorC`, `doorD`) VALUES (%s,%s,%s,%s,%s,%s)"            
        addedLines = cur.execute(sql_insert_minute, (intervalStart.strftime(DATE_TIME_FORMAT), intervalStop.strftime(DATE_TIME_FORMAT), sensorCnt[0],  sensorCnt[1],  sensorCnt[2],  sensorCnt[3]))
        if (addedLines != 1):
            log("ERROR. Did not add to database as expected. \nSQL was " + sql_insert_minute + " \nResult was " + str(addedLines) + "...")

        sql_insert_day = "INSERT INTO `visits`.`daytable` (`date`, `visits`) VALUES (%s,%s) ON DUPLICATE KEY UPDATE visits='%s'"
        addedLines = cur.execute(sql_insert_day, (intervalStart.strftime(DATE_FORMAT), sensorDayTotal, sensorDayTotal))
        if (addedLines < 1):
            log("ERROR. Did not update database as expected. \nSQL was " + sql_insert_day + " \nResult was " + str(addedLines) + "...")
        
        con.commit()
        
        sensorCnt = [0, 0, 0, 0]
        intervalStart = intervalStop
        
except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()
    GPIO.cleanup()
