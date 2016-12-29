#!/bin/sh
# script that disabbles power management for wifi in case it is not already disabled.
PWR_ALREADY_OFF = cat /etc/network/interfacesML | grep "wireless-power off"

if grep -q wireless-power "/etc/network/interfacesML"; then
	echo "nothing to be done"
else
	echo "to be done"
	sudo echo "wireless-power off" >> /etc/network/interfacesML
	sudo /etc/init.d/networking restart
fi
