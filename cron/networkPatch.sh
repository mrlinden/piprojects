#!/bin/sh
# script that disabbles power management for wifi in case it is not already disabled.

if grep -q wireless-power "/etc/network/interfaces"; then
	echo "nothing to be done"
else
	echo "to be done"
	echo "wireless-power off" >> /etc/network/interfaces
	/etc/init.d/networking restart
fi
