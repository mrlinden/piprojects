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
DATE_FORMAT = "%Y-%m-%d %H:%M:%S"
logging = True

# Some global data
done = False
con = False
sensorCnt = [0, 0, 0, 0]

# Some configuration
config = ConfigParser()
config.read('../config.ini')
db_host     = config.get('db_host')
db_name     = config.get('db_name')
db_user     = config.get('db_user')
db_password = config.get('db_password')

# Some functions
def log(message):
    if (logging):
        print(message + "\n")
        logFile.write(message + "\n")
        logFile.flush()

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
logFile = open("/var/log/visit.log", "w")
log("Started Visit sensor service\n\n")

GPIO.setmode(GPIO.BCM)

for gpioInNr in ALL_GPIO_IN:
    log ("Setup GPIO nr %s as input" % gpioInNr)
    GPIO.setup(gpioInNr, GPIO.IN, pull_up_down = GPIO.PUD_OFF )
    GPIO.add_event_detect(gpioInNr, GPIO.RISING, callback=actOnSensor, bouncetime=20)

try:
    intervalStart = datetime.datetime.now()
    
    while not done:
        time.sleep(60)
        intervalStop = datetime.datetime.now()
        log ("Store the count for interval. sensor A: %d sensor B: %d sensor C: %d sensor D: %d" % (sensorCnt[0], sensorCnt[1], sensorCnt[2], sensorCnt[3]))
        
        con = mdb.connect(db_host, db_user, db_password, db_name)
        cur = con.cursor(mdb.cursors.DictCursor)
            
        sql_insert = "INSERT INTO `visits`.`minutetable` (`intervalStart`, `intervalStop`, `doorA`, `doorB`, `doorC`, `doorD`) VALUES (%s,%s,%s,%s,%s,%s)"            
        addedLines = cur.execute(sql_insert, (intervalStart.strftime(DATE_FORMAT ), intervalStop.strftime(DATE_FORMAT ), sensorCnt[0],  sensorCnt[1],  sensorCnt[2],  sensorCnt[3]))
        if (addedLines != 1):
            log("ERROR. Did not add 1 line to database as expected. \nSQL was " + sql_insert + " \nResult was " + str(addedLines) + "...")
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
