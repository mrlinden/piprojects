#!/bin/sh
# script that is called by crontab to start the php script

SERVICE='cupolen.automation.cron.php'
date
echo "Starting $SERVICE ..."
cd /home/pi/piprojects/ljus/cupolen/cron
# The following line is blocking
sudo php $SERVICE $1
