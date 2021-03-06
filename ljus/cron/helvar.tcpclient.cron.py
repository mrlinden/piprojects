#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Linden 2019
# Script that periodically send TCP commands to Helvar system
import os
import sys
import time
import re
import json
import socket
import logging
import datetime
import array
import threading
import signal
import time
from logging.handlers import TimedRotatingFileHandler
import ctypes
from ctypes import *
import binascii
from threading import Thread
import socket, time

# Global variables
sock = None
isConnected = False
previousStatusTimestamp = 0
timestamp_file = '../status/statusRequested.txt'


# Classes

class Receiver(Thread):
    def run(self):
        debug("Receiver thread started")
        while True:
            try:
                rxData = self.readServerData()
            except:
                debug("Exception in Receiver.run()")
                isReceiverRunning = False
                closeConnection()
                break
        debug("Receiver thread terminated")

    def readServerData(self):
        debug("Calling readResponse")
        bufSize = config["tcp_buffer_size"]
        data = ""
        while data[-1:] != "#": # reply with end-of-message indicator
            try:
                blk = sock.recv(bufSize)
            except:
                raise Exception("Exception from blocking sock.recv()")
            data += blk
        multipleReplies = data.strip().split("#")
        for reply in multipleReplies:
            parseAndStore(reply)

# Configuration
with open('helvar.config.json') as json_data_file:
    config = json.load(json_data_file)


# Functions
def parseAndStore(reply):
    if (reply == ""): return
    debug('Reply ' + reply)
    onOffLampGroups = ["130","131","132","133","500","501"]
    onOffLampRegexp = re.compile(r'.*?V:1,C:103,G:(?P<group>\d*),.*\=(?P<value>\d*)$')
    onOffLampMatch = onOffLampRegexp.match(reply)
    storeIfValidGroup(onOffLampMatch, onOffLampGroups, 'onOffLamp')

    dimmerLampGroups = ["129","900"]
    dimmerLampRegexp = re.compile(r'.*?V:1,C:103,G:(?P<group>\d*),.*=(?P<value>\d*)$')
    #dimmerLampRegexp = re.compile(r'.*V:1,C:13,G:(?P<group>\d*),.*L:(?P<value>\d*),')
    dimmerLampMatch = dimmerLampRegexp.match(reply)
    storeIfValidGroup(dimmerLampMatch, dimmerLampGroups, 'dimmerLamp')

def storeIfValidGroup(match, validGroups, filePrefix):
    if (match == None): return
    m = match.groupdict()
    #print(json.dumps(m))
    if (m['group'] in validGroups):
        oldValue = ""
        fileName = '../status/' + filePrefix + '.' + m['group']
        if (os.path.exists(fileName)):
            fr = open(fileName, "r")
            oldValue = fr.read()
            fr.close()
        if (oldValue != m['value']):
            log('Stored Group ' + m['group'] + ' with value ' + m['value'])
            f = open(fileName, "w")
            f.write(m['value'])
            f.close()

def debug(message):
    do_debug = config["debug"]
    if do_debug > 0: log(message)

def log(message):
    log_mode = config["log_mode"]
    log_path = config["log_path"]
    log_level = config["log_level"]
    if log_level == 0: return

    if log_mode == "console":
        print(message + "\n")
    elif log_mode == "file":
        if not hasattr(log, "logger"):
            log.logger = logging.getLogger("Helvar TCP Client Log")
            log.logger.setLevel(logging.INFO)
            handler = TimedRotatingFileHandler(log_path,
                                               when="midnight",
                                               interval=1,
                                               backupCount=10)
            log.logger.addHandler(handler)
        log.logger.info(message)

def startReceiver():
    debug("Starting Receiver thread")
    receiver = Receiver()
    receiver.start()

def sendCommand(cmd):
    debug("sendCommand() with cmd = " + cmd)
    try:
        # append \0 as end-of-message indicator
        sock.sendall(cmd)
    except:
        debug("Exception in sendCommand()")
        closeConnection()

def closeConnection():
    global isConnected
    debug("Closing socket")
    sock.close()
    isConnected = False

def connect():
    global sock
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    debug("Connecting...")
    try:
        tcpIp = config["tcp_ip"]
        tcpPort = config["tcp_port"]
        sock.connect((tcpIp, tcpPort))
    except:
        debug("Connection failed.")
        return False
    startReceiver()
    return True

def isStatusRecentlyRequested():
    global previousStatusTimestamp
    global timestamp_file

    try:
        modification_time = os.path.getmtime(timestamp_file)
        if (previousStatusTimestamp != modification_time):
            previousStatusTimestamp = modification_time
            return True
    except OSError:
        previousStatusTimestamp = 0
    return False

def touch(path):
    open(path, 'a').close()
    os.chmod(path, 0o666)
    os.utime(path, None)

# Program start
log(str(datetime.datetime.now()) + "\nStarted Helvar TCP Client service\n\n")
touch(timestamp_file)

while True:
    if connect():
        isConnected = True
        log("Connection established")
        time.sleep(1)
        while isConnected:
            if isStatusRecentlyRequested():
                sendCommand(">V:1,C:103,G:130,B:1#")
                sendCommand(">V:1,C:103,G:131,B:1#")
                sendCommand(">V:1,C:103,G:132,B:1#")
                sendCommand(">V:1,C:103,G:133,B:1#")
                sendCommand(">V:1,C:103,G:129,B:1#")
                sendCommand(">V:1,C:103,G:900,B:1#")
                sendCommand(">V:1,C:103,G:500,B:1#")
                sendCommand(">V:1,C:103,G:501,B:1#")
            time.sleep(1)
    else:
        log("Connection failed")
    log("Helvar TCP Client reconnect")

# Wait for threads to complete
log("Helvar TCP Client service ends")

