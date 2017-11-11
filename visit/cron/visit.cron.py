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
import math
from logging.handlers import TimedRotatingFileHandler
from datetime import timedelta
try:
    from configparser import ConfigParser
except ImportError:
    from ConfigParser import ConfigParser  # ver. < 3.0

# Constants
DATE_TIME_FORMAT = "%Y-%m-%d %H:%M:%S"
DATE_FORMAT = "%Y-%m-%d"
MINUTES_PER_INTERVAL = 1
SECONDS_PER_INTERVAL = (MINUTES_PER_INTERVAL * 60)
SAVE_EMPTY_INTERVALS = True

# Configuration
config = ConfigParser()
config.read('../config.ini')
db_host     = config.get('database','db_host')
db_name     = config.get('database','db_name')
db_user     = config.get('database','db_admin')
db_password = config.get('database','db_password')
logMode     = config.get('logging','log_mode')
logPath     = config.get('logging','log_path')
sensorIds   = [ config.get('sensor','id_A'), config.get('sensor','id_B'), config.get('sensor','id_C'), config.get('sensor','id_D') ]
allGpios    = [ config.getint('sensor','gpio_A'), config.getint('sensor','gpio_B'), config.getint('sensor','gpio_C'), config.getint('sensor','gpio_D') ]

# Functions
def log(message):
    if (logMode == "console"):
        print(message + "\n")
    elif (logMode == "file"):
        if (not hasattr(log, "logger")):
            log.logger = logging.getLogger("Visit Log")
            log.logger.setLevel(logging.INFO)
            handler = TimedRotatingFileHandler(logPath,
                                               when="midnight",
                                               interval=1,
                                               backupCount=10)
            log.logger.addHandler(handler)
        log.logger.info(message)
        
def actOnSensor(gpioIn):
    global sensorCnt

    try:
        sensorNr = allGpios.index(gpioIn)
        sensorCnt[sensorNr] = sensorCnt[sensorNr] + 1
        log ("Act on sensor %d: Total counts this interval: %d." % (sensorNr, sensorCnt[sensorNr]))

    except ValueError:
        log ("Unexpected sensor %d " % gpioIn)

def intervalStartTime(tm):
    return tm - datetime.timedelta(minutes=tm.minute % MINUTES_PER_INTERVAL,
                                   seconds=tm.second,
                                   microseconds=tm.microsecond)
    
    
# Program start
log(str(datetime.datetime.now()) + " Started Visit sensor service\n\n")
GPIO.setmode(GPIO.BCM)
con = False
done = False
    
for gpioInNr in allGpios:
    if (gpioInNr != 0):
        log ("Setup GPIO nr %s as input" % gpioInNr)
        GPIO.setup(gpioInNr, GPIO.IN, pull_up_down = GPIO.PUD_OFF )
        GPIO.add_event_detect(gpioInNr, GPIO.FALLING, callback=actOnSensor, bouncetime=500)

try:
    sensorCnt = [0, 0, 0, 0]
    
    while not done:
        currentTime = datetime.datetime.now()
        previousIntervalStart = intervalStartTime(currentTime);

        # Sleep until next Interval shall start
        nextIntervalStart = previousIntervalStart + timedelta(minutes=MINUTES_PER_INTERVAL)
        sleepSeconds = (nextIntervalStart - currentTime).total_seconds() +1; # +1 just to be sure to pass border for MINUTES_PER_INTERVAL
        
        log ("sleep " + str(sleepSeconds) + " seconds to next interval start at " + str(nextIntervalStart) + ".\n")
        time.sleep(sleepSeconds)

        # Transfer to local variables to not be interfered by new events
        sCnt = sensorCnt
        sensorCnt = [0, 0, 0, 0]
        sCntTot = sum(sCnt)
        
        con = mdb.connect(db_host, db_user, db_password, db_name)
        cur = con.cursor(mdb.cursors.DictCursor)
        
        log (str(nextIntervalStart) + " sensor A: %d sensor B: %d sensor C: %d sensor D: %d" % (sCnt[0], sCnt[1], sCnt[2], sCnt[3]))       
        sql_insert_sensor = "INSERT INTO `visits`.`sensordata` (`timestamp`, `sensorId`, `count`) VALUES (%s,%s,%s)"
            
        for sensorNr in range(0,4):
            if (sensorIds[sensorNr] != "0"):
                if ((sCnt[sensorNr] > 0) or (SAVE_EMPTY_INTERVALS)):
                    addedLines = cur.execute(sql_insert_sensor, (nextIntervalStart.strftime(DATE_TIME_FORMAT), sensorIds[sensorNr], sCnt[sensorNr]))
                    if (addedLines != 1):
                        log("ERROR. Did not add to database as expected. \nSQL was " + sql_insert_sensor + " \nResult was " + str(addedLines) + "...")

        con.commit()

except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()
    GPIO.cleanup()
