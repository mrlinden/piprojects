#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2015

import MySQLdb as mdb
import sys
import time
import os

# Some constants
logging = False

# Some global data
newPreset = 0
con = False

# Some functions
def log(message):
    if (logging):
        logFile.write(message + "\n")
        logFile.flush()


# Input handling
if (len(sys.argv) != 2):
	print 'Invalid number of arguments. Usage: scriptName 1|2|3|4|5|6'
	exit()

if (sys.argv[1].isdigit() == False):
	print 'Invalid arguments. Usage: scriptName 1|2|3|4|5|6'
	exit()

argInt = int(sys.argv[1])
if ((argInt < 1) or (argInt > 6)):
	print 'Invalid arguments. Usage: scriptName 1|2|3|4|5|6'
	exit()

newPreset = argInt
	
# Program start
if (logging):
	logFile = open("/var/cafe/changePreset.log", "w")
	log("Change Preset for cafe speakers\n\n")

try:
        # open database
        con = mdb.connect('localhost', 'gpio', 'gpiodata', 'cafe')
        cur = con.cursor(mdb.cursors.DictCursor)
            
        # Store to database
        sql_insert = "INSERT INTO `cafe`.`action` (`id`, `time`, `preset`, `userid`) VALUES (NULL,CURRENT_TIMESTAMP,'%s','2')"            
        addedLines = cur.execute(sql_insert, (newPreset))
        if (addedLines != 1):
        	log("ERROR. Did not add 1 line to database as expected. Result was " + str(addedLines) + "...")
        con.commit()                
        con.close()

except mdb.Error, e:

    log ("Error %d: %s" % (e.args[0],e.args[1]))
    sys.exit(1)
    
finally:    
        
    if con:
        if con.open:
            con.close()
