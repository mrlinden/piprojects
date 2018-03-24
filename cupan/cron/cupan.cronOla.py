#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2018
# Script that listens to UDP on port 5555 and sends artnet DMX messages

import json
import socket
import logging
import datetime
import array
import threading
import signal
import time
from logging.handlers import TimedRotatingFileHandler
from ola.ClientWrapper import ClientWrapper



class UdpReceivingThread (threading.Thread):
    def __init__(self, threadID, name):
        threading.Thread.__init__(self)
        self.threadID = threadID
        self.name = name
        self.done = False
    def stop(self):
        self.done = True
    def run(self):
        udpIp = config["udp_ip"]
        udpPort = config["udp_port"]
        udpBufferSize = config["udp_buffer_size"]
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        sock.setblocking(0)
        sock.bind((udpIp, udpPort))

        while not self.done:
            try:
                data, addr = sock.recvfrom(udpBufferSize)
                scene = data.strip()
                setScene(scene)
            except socket.error:
                time.sleep(0.5)


# Configuration
with open('../config.json') as json_data_file:
    config = json.load(json_data_file)


# Global data
dmxUniverseAndValues = {}
threadLock = threading.Lock()
udpReceivingThread = UdpReceivingThread(1, "UdpReceivingThread")
wrapper = ClientWrapper()
client = wrapper.Client()


# Functions
def log(message):
    logMode = config["log_mode"]
    logPath = config["log_path"]

    if (logMode == "console"):
        print(message + "\n")
    elif (logMode == "file"):
        if (not hasattr(log, "logger")):
            log.logger = logging.getLogger("Cupan Log")
            log.logger.setLevel(logging.INFO)
            handler = TimedRotatingFileHandler(logPath,
                                               when="midnight",
                                               interval=1,
                                               backupCount=10)
            log.logger.addHandler(handler)
        log.logger.info(message)


def breakHandler(signum, frame):
    wrapper.Stop()
    udpReceivingThread.stop()


def getScenePreset(sceneName):
    if (sceneName in config["scene_presets"]):
        return config["scene_presets"][sceneName]
    return None


def getLampChannel(lampName):
    return config["lamps"][lampName]["dmx_channel"]


def getLampUniverse(lampName):
    return config["lamps"][lampName]["dmx_universe"]


def getLampPreset(presetName):
    return config["lamp_presets"][presetName]


def setScene(sceneName):
    if (sceneName == "scen_av"):
        clearScene()
        sendAllUniverseValues()
        return

    preset = getScenePreset(sceneName)
    if (preset == None):
        log("Found no preset with name " + sceneName + ".")
        return

    clearScene()
    for scenePresetCommand in preset:
        lampUniverse = getLampUniverse(scenePresetCommand["lamp"])
        lampChannel = getLampChannel(scenePresetCommand["lamp"])
        lampPreset = getLampPreset(scenePresetCommand["preset"])
        updateUniverseValues(lampUniverse, lampChannel, lampPreset)

    sendAllUniverseValues()


def clearScene():
    for universe in dmxUniverseAndValues:
        initValues = [0] * 512
        dmxUniverseAndValues[universe] = initValues


def updateUniverseValues(universe, startingChannel, values):
    if (universe not in dmxUniverseAndValues):
        initValues = [0] * 512
        dmxUniverseAndValues[universe] = initValues

    for i in range(len(values)):
        dmxUniverseAndValues[universe][startingChannel + i] = values[i]


def sendAllUniverseValues():
    for universe in dmxUniverseAndValues:
        data = array.array('B', dmxUniverseAndValues[universe])
        log (str(universe) + ":" + str(dmxUniverseAndValues[universe]))
        threadLock.acquire()
        client.SendDmx(universe, data)
        threadLock.release()


def sendAllUniverseValuesPeriodic():
    if (config["periodic_transmission_interval"] > 0):
        wrapper.AddEvent(config["periodic_transmission_interval"], sendAllUniverseValuesPeriodic)
        sendAllUniverseValues()



# Program start
log(str(datetime.datetime.now()) + "\nStarted Cupan stage light service\n\n")
signal.signal(signal.SIGINT, breakHandler)

sendAllUniverseValuesPeriodic()
udpReceivingThread.start()
try:
    wrapper.Run() # Blocking call that handles the DMX sending
except:
    udpReceivingThread.stop()

# Wait for UDP thread to complete
udpReceivingThread.join()

log("Cupan stage light service ends")
