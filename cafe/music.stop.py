#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright Marcus Lindén 2015

import dbus

bus = dbus.SessionBus()
player = bus.get_object('org.mpris.MediaPlayer2.mopidy', '/org/mpris/MediaPlayer2')
player.stop()
