#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2018
# Script that listens to UDP port for commands from cretron and sends artnet DMX messages

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

# Classes

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


class PeriodicArtNetSendingThread (threading.Thread):
    def __init__(self, threadID, name):
        threading.Thread.__init__(self)
        self.threadID = threadID
        self.name = name
        self.done = False
        self.nrTransmissionsOfSameScene = 0
    def stop(self):
        self.done = True
    def run(self):
        if (config["periodic_transmission_interval"] > 0):
            while not self.done:
                if (self.nrTransmissionsOfSameScene < config["periodic_max_nr_retransmissions"]):
                    sendAllUniverseValues()
                    self.nrTransmissionsOfSameScene += 1
                time.sleep(config["periodic_transmission_interval"])
    def newSceneHasBeenSet(self):
        self.nrTransmissionsOfSameScene = 0


class ArtNetPacket(LittleEndianStructure):
    _fields_ = [("id", c_char * 8),
                ("opcode", c_ushort),
                ("protverh", c_ubyte),
                ("protver", c_ubyte),
                ("sequence", c_ubyte),
                ("physical", c_ubyte),
                ("universe", c_ushort),
                ("payloadLength", c_ushort),
                ("payload", c_ubyte * 512)]
    def __init__(self, universe, payload):
        self.id = b"Art-Net"
        self.opcode = 0x5000
        self.protver = 14
        self.universe = universe
        self.payloadLength = 512
        self.payload = (ctypes.c_ubyte * len(payload))(*payload)


# Configuration
with open('../config.json') as json_data_file:
    config = json.load(json_data_file)


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


def getSceneSubgroup(sceneName):
    for subgroup in config["scene_subgroups"]:
        if (sceneName.startswith(subgroup)):
            return subgroup
    return None


def getScenePreset(sceneName):
    if (sceneName in config["scene_presets"]):
        return config["scene_presets"][sceneName]
    return None


def isSceneOffCommand(sceneName):
    return sceneName.endswith("Av")


def getLampChannel(lampName):
    return config["lamps"][lampName]["dmx_channel"]


def getLampUniverse(lampName):
    return config["lamps"][lampName]["dmx_universe"]


def getLampPreset(presetName):
    return config["lamp_presets"][presetName]


def setSceneByDMXValuesInSceneName(sceneName):
    subgroup = "dmx"
    dmxValues = sceneName[3:].split(",")
    dmxValues = [int(i) for i in dmxValues]
    startingChannel = dmxValues.pop(0)
    universe = 5

    updateSubgroupUniverseValues(subgroup, universe, startingChannel, dmxValues)
    sendAllUniverseValues()
    return


def setScene(sceneName):
    log(sceneName)

    subgroup = getSceneSubgroup(sceneName)
    preset = getScenePreset(sceneName)

    if (subgroup == "dmx"):
        setSceneByDMXValuesInSceneName(sceneName);
        return

    if ((subgroup == None) or (preset == None)):
        log("Found no preset or subgroup for scene name " + sceneName + " (" + str(preset) + " " + str(subgroup) + ")")
        return

    clearSceneSubgroup(subgroup)

    if (not isSceneOffCommand(sceneName)):
        for scenePresetCommand in preset:
            lampUniverse = getLampUniverse(scenePresetCommand["lamp"])
            lampChannel = getLampChannel(scenePresetCommand["lamp"])
            lampPreset = getLampPreset(scenePresetCommand["preset"])
            updateSubgroupUniverseValues(subgroup, lampUniverse, lampChannel, lampPreset)

    sendAllUniverseValues()
    periodicArtNetSendingThread.newSceneHasBeenSet()


def clearSceneSubgroup(subgroup):
    if (subgroup not in subgroupDmxUniverseAndValues):
	return

    for universe in subgroupDmxUniverseAndValues[subgroup]:
        initValues = [0] * 512
        subgroupDmxUniverseAndValues[subgroup][universe] = initValues


def updateSubgroupUniverseValues(subgroup, universe, startingChannel, values):
    if (subgroup not in subgroupDmxUniverseAndValues):
        subgroupDmxUniverseAndValues[subgroup] = {}

    if (universe not in subgroupDmxUniverseAndValues[subgroup]):
        initValues = [0] * 512
        subgroupDmxUniverseAndValues[subgroup][universe] = initValues

    #log("Starting ch :" +  str(startingChannel) + " and length is " + str(len(values)))
    startingIndex = startingChannel - 1;
    for i in range(len(values)):
        #log ("update index " + str(startingIndex + i) + " from pos " + str(i) + " with value " + str(values[i]))
        subgroupDmxUniverseAndValues[subgroup][universe][startingIndex + i] = values[i]


def getMergedDmxUniverseAndValues():
    mergedDmxUniverseAndValues = {}

    for subgroup in config["scene_subgroups"]:
        if (subgroup in subgroupDmxUniverseAndValues):
            for universe in subgroupDmxUniverseAndValues[subgroup]:
                if (universe not in mergedDmxUniverseAndValues):
                    initValues = [0] * 512
                    mergedDmxUniverseAndValues[universe] = initValues

                for i in range(len(mergedDmxUniverseAndValues[universe])):
                    if (subgroupDmxUniverseAndValues[subgroup][universe][i] > mergedDmxUniverseAndValues[universe][i]):
                        mergedDmxUniverseAndValues[universe][i] = subgroupDmxUniverseAndValues[subgroup][universe][i];

    return mergedDmxUniverseAndValues


def sendAllUniverseValues():
    mergedDmxUniverseAndValues = getMergedDmxUniverseAndValues()
    for universe in mergedDmxUniverseAndValues:
        packet = ArtNetPacket(universe, mergedDmxUniverseAndValues[universe])
        #log(str(binascii.hexlify(packet)))
        artNetSocket.sendto(packet, (config["artnet_ip"], config["artnet_port"]))


# Program start
subgroupDmxUniverseAndValues = {}

artNetSocket = socket.socket(socket.AF_INET,socket.SOCK_DGRAM)
periodicArtNetSendingThread = PeriodicArtNetSendingThread(2, "PeriodicArtNetSendingThread")
periodicArtNetSendingThread.start()

udpReceivingThread = UdpReceivingThread(1, "UdpReceivingThread")
udpReceivingThread.start()

log(str(datetime.datetime.now()) + "\nStarted Cupan stage light service\n\n")

# Wait for threads to complete
periodicArtNetSendingThread.join()
udpReceivingThread.join()

log("Cupan stage light service ends")

