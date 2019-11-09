#!/bin/sh
# script that is called by crontab to start the php script

SERVICE='cupolen.automation.cron.php'
date
echo "Starting $SERVICE ..."
cd /home/pi/piprojects/cupolen/cron
# The following line is blocking but that is intended since then crashing python will print to cupolen.cron.log
sudo php $SERVICE $1
