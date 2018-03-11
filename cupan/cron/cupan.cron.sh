#!/bin/sh
# script that is called by crontab to start the python script

SERVICE='cupan.cron.py'

if ps ax | grep -v grep | grep $SERVICE > /dev/null
then
    # Log file size will increase a lot if next line is uncommented. Use for troubleshooting only
    # echo "$SERVICE service running, everything is fine"
    cd /home/pi/piprojects/cupan/cron
else
    date
    echo "Starting $SERVICE ..."
    cd /home/pi/piprojects/cupan/cron
    # The following line is blocking but that is intended since then crashing python will print to cupan.cron.log
    sudo python $SERVICE 
fi
