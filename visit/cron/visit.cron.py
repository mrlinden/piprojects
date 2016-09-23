#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2016
# Script that reads the GPIO inputs and counts the number of GPIO changes

#import MySQLdb as mdb
import sys
import RPi.GPIO as GPIO
import time
import os

# Some constants
ALL_GPIO_IN = [16, 26, 20, 21]
logging = True

# Some global data
done = False
con = False
sensorCnt = [0, 0, 0, 0]

# Some functions
def log(message):
    if (logging):
        logFile.write(message + "\n")
        logFile.flush()

def actOnSensor(gpioIn):
    global sensorCnt

    try:
        time.sleep(0.05) # Needed because sometimes GPIO.input did not return True for rising events 
        sensorNr = ALL_GPIO_IN.index(gpioIn)

        if (GPIO.input(gpioIn)):
            sensorCnt[sensorNr] = sensorCnt[sensorNr] + 1
            log ("Sensor %d opened! Total counts this interval: %d." % sensorNr, sensorCnt[sensorNr])
        else:
            log ("Sensor %d closed!" % sensorNr)

    except ValueError:
        log ("Unexpected sensor %d " % gpioIn)


# Program start
logFile = open("/var/visit.log", "w")
log("Started Visit sensor service\n\n")

GPIO.setmode(GPIO.BCM)

for gpioInNr in ALL_GPIO_IN:
    log ("Setup GPIO nr %s as input" % gpioInNr)
    GPIO.setup(gpioInNr, GPIO.IN, pull_up_down = GPIO.PUD_OFF )
    GPIO.add_event_detect(gpioInNr, GPIO.BOTH, callback=actOnSensor, bouncetime=20)

try:
    while not done:
        time.sleep(10)
        log ("Store counts for interval. sensor A: %d sensor B: %d sensor C: %d sensor D: %d" % (sensorCnt[0], sensorCnt[1], sensorCnt[2], sensorCnt[3]))
        
except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()
    GPIO.cleanup()
