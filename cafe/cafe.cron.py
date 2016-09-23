#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2015

import MySQLdb as mdb
import sys
import RPi.GPIO as GPIO
import time
import os

# Some constants
BUTTON_1 = 0
BUTTON_2 = 1
BUTTON_3 = 2
PRESET_CAFE_1 = 1
PRESET_CAFE_2 = 2
PRESET_CAFE_3 = 3 # 1+2
PRESET_CAFE_FOAJE = 4
PRESET_MIXER = 5
PRESET_OFF = 6
ALL_GPIO_OUT = [6, 12, 13, 19, 16, 26, 20, 21, 22, 23, 24]
ALL_GPIO_IN = [17, 18, 27]
logging = True

# Some global data
allButtonsPressed = False
allButtonsPressedCnt = 0
preset = PRESET_OFF
newPreset = PRESET_OFF
done = False
con = False
wakeUpCnt = 0
wakeUpImmediatelly = 0

# Some functions
def log(message):
    if (logging):
        logFile.write(message + "\n")
        logFile.flush()

def buttonPressed(gpioIn):
    global newPreset
    global allButtonsPressed
    global allButtonsPressedCnt
    global wakeUpImmediatelly

    try:
        time.sleep(0.05) # Needed because sometimes GPIO.input did not return True for rising events 
        button = ALL_GPIO_IN.index(gpioIn)

 
        if (GPIO.input(gpioIn)):

            # calculate action
            newPreset = preset

            if (button == BUTTON_1):
                if (preset == PRESET_OFF):
                    newPreset = PRESET_CAFE_1
                elif (preset == PRESET_CAFE_1):
                    newPreset = PRESET_OFF
                elif (preset == PRESET_CAFE_2):
                    newPreset = PRESET_CAFE_3
                elif (preset == PRESET_CAFE_3):
                    newPreset = PRESET_CAFE_2
                elif (preset == PRESET_CAFE_FOAJE):
                    newPreset = PRESET_OFF
            elif (button == BUTTON_2):
                if (preset == PRESET_OFF):
                    newPreset = PRESET_CAFE_2
                elif (preset == PRESET_CAFE_1):
                    newPreset = PRESET_CAFE_3
                elif (preset == PRESET_CAFE_2):
                    newPreset = PRESET_OFF
                elif (preset == PRESET_CAFE_3):
                    newPreset = PRESET_CAFE_1
                elif (preset == PRESET_CAFE_FOAJE):
                    newPreset = PRESET_OFF
            elif (button == BUTTON_3):
                if (preset == PRESET_OFF):
                    newPreset = PRESET_CAFE_FOAJE
                elif (preset == PRESET_CAFE_1):
                    newPreset = PRESET_CAFE_FOAJE
                elif (preset == PRESET_CAFE_2):
                    newPreset = PRESET_CAFE_FOAJE
                elif (preset == PRESET_CAFE_3):
                    newPreset = PRESET_CAFE_FOAJE
                elif (preset == PRESET_CAFE_FOAJE):
                    newPreset = PRESET_OFF

            log ("Pressed button %s. Change from preset %s to preset %s" % (button, preset, newPreset))

            # Check if all buttons are pressed
            tmp = True
            for gpioInNr in ALL_GPIO_IN:
                if (GPIO.input(gpioInNr) == 0):
                    tmp = False
            allButtonsPressed = tmp # update global variable

            # Trigger store to database at next iteration in main loop
            if (newPreset != preset):
                wakeUpImmediatelly = 1 

        else:
            #log ("Button %s released!" % button)
            allButtonsPressed = False

        if (allButtonsPressed == False):
            allButtonsPressedCnt = 0 # Reset counter all buttons not pressed

    except ValueError:
        log ("Bad button %s pressed!" % gpioIn)


# Program start
logFile = open("/var/cafe/cafe.log", "w")
log("Started Cafe button and relay controller\n\n")

GPIO.setmode(GPIO.BCM)

for gpioOutNr in ALL_GPIO_OUT:
    log ("Setup GPIO nr %s as output" % gpioOutNr)
    GPIO.setup(gpioOutNr, GPIO.OUT)

for gpioInNr in ALL_GPIO_IN:
    log ("Setup GPIO nr %s as input" % gpioInNr)
    GPIO.setup(gpioInNr, GPIO.IN, pull_up_down = GPIO.PUD_OFF )
    GPIO.add_event_detect(gpioInNr, GPIO.BOTH, callback=buttonPressed, bouncetime=20)

try:
    while not done:

        # Take actions if time to wake up. database read not performed every time to reduce load.
        if ((wakeUpCnt == 0) or (wakeUpImmediatelly > 0)):

            wakeUpCnt = 20
            wakeUpImmediatelly = 0

            # open database
            con = mdb.connect('localhost', 'gpio', 'gpiodata', 'cafe')
            cur = con.cursor(mdb.cursors.DictCursor)
            
            # Store to database if preset has changed
            if (newPreset != preset):
                sql_insert = "INSERT INTO `cafe`.`action` (`id`, `time`, `preset`, `userid`) VALUES (NULL,CURRENT_TIMESTAMP,'%s','2')"            
                addedLines = cur.execute(sql_insert, (newPreset))
                if (addedLines != 1):
                    log("ERROR. Did not add 1 line to database as expected. Result was " + str(addedLines) + "...")
                con.commit()
                
            # get latest selected preset
            cur.execute("SELECT * from preset INNER JOIN action ON preset.id = action.preset ORDER BY action.time DESC LIMIT 1")
            row = cur.fetchone()
            preset = row["preset"]
            newPreset = preset
            activeGpioOutList = row["activeGpioOut"].split(",")
        
            for gpioOutNr in ALL_GPIO_OUT:

                if (str(gpioOutNr) in activeGpioOutList):
                    state = 1
                else:
                    state = 0   
                    
                if (GPIO.input(gpioOutNr) != state):
                    GPIO.output(gpioOutNr, state)
                        
            if (allButtonsPressed):
                allButtonsPressedCnt = allButtonsPressedCnt + 1
        
            if (allButtonsPressedCnt > 2):
                log("Shutting down")
                done = True
                os.system("sudo shutdown -h now")

            con.close()

        else:
            wakeUpCnt = wakeUpCnt - 1

        time.sleep(0.5)

except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()
    GPIO.cleanup()
