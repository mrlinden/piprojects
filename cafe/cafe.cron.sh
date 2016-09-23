#!/bin/sh
# script that is called by crontab to start the python script that loops and controls GPIO output

SERVICE='cafe.cron.py'
 
if ps ax | grep -v grep | grep $SERVICE > /dev/null
then
    # Ony for troubleshooting. Log file size wil increase a lot if this is uncommented
    # echo "$SERVICE service running, everything is fine"
    cd /var/cafe
else
    echo "Starting $SERVICE ..."
    cd /var/cafe
    sudo python $SERVICE
fi