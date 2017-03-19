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
sensorIds   = [ config.get('sensor','id_A'), config.get('sensor','id_B'), config.get('sensor','id_C'), config.get('sensor','id_D') ]

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
log(str(datetime.datetime.now()) + " Started Visit sensor service\n\n")
GPIO.setmode(GPIO.BCM)
con = False
done = False
    
for gpioInNr in ALL_GPIO_IN:
    log ("Setup GPIO nr %s as input" % gpioInNr)
    GPIO.setup(gpioInNr, GPIO.IN, pull_up_down = GPIO.PUD_OFF )
    GPIO.add_event_detect(gpioInNr, GPIO.RISING, callback=actOnSensor, bouncetime=20)

try:
    sensorCnt = [0, 0, 0, 0]
    intervalStart = datetime.datetime.now()
 
    while not done:
        sleepSeconds = 600-intervalStart.second
        
        log (str(intervalStart) + "sleep " + str(sleepSeconds) + " (seconds of interval start: %d)...\n" % (intervalStart.second))
        
        time.sleep(sleepSeconds)
        intervalStop = floor(datetime.datetime.now()/600)*600   # Round to floor 10 minute interval
        # Transfer to local variables to not be interferred by new events
        sCnt = sensorCnt
        sensorCnt = [0, 0, 0, 0]
        sCntTot = sum(sCnt)
        
        con = mdb.connect(db_host, db_user, db_password, db_name)
        cur = con.cursor(mdb.cursors.DictCursor)
        
        log (str(intervalStop) + " sensor A: %d sensor B: %d sensor C: %d sensor D: %d" % (sCnt[0], sCnt[1], sCnt[2], sCnt[3]))       
        if (sCntTot > 0):
            sql_insert_sensor = "INSERT INTO `visits`.`sensordata` (`timestamp`, `id`, `count`) VALUES (%s,%s,%s)"
            
            for sensorNr in range(0,3):
                if (sensorIds[sensorNr] != "0"):
                    addedLines = cur.execute(sql_insert_sensor, (intervalStop.strftime(DATE_TIME_FORMAT), 
                                                                 sensorIds[sensorNr], 
                                                                 sCnt[sensorNr]))
                    if (addedLines != 1):
                        log("ERROR. Did not add to database as expected. \nSQL was " + sql_insert_sensor + " \nResult was " + str(addedLines) + "...")

        con.commit()
        
        intervalStart = intervalStop
        
except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()
    GPIO.cleanup()
