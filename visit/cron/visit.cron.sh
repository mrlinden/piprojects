#!/bin/sh
# script that is called by crontab to start the python script that loops and controls GPIO input

SERVICE='visit.cron.py'

if ps ax | grep -v grep | grep $SERVICE > /dev/null
then
    # Log file size will increase a lot if next line is uncommented. Use for troubleshooting only
    # echo "$SERVICE service running, everything is fine"
    cd /home/pi/piprojects/visit/cron
else
    date
    echo "Starting $SERVICE ..."
    cd /home/pi/piprojects/visit/cron
    sudo python $SERVICE
fi
