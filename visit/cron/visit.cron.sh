#!/bin/sh
# script that is called by crontab to start the python script that loops and controls GPIO output

SERVICE='visit.cron.py'
 
if ps ax | grep -v grep | grep $SERVICE > /dev/null
then
    # Ony for troubleshooting. Log file size will increase a lot if this is uncommented
    echo "$SERVICE service running, everything is fine"
    cd /home/pi/piprojects/visit/cron
else
    echo "Starting $SERVICE ..."
    cd /home/pi/piprojects/visit/cron
    sudo python $SERVICE
fi