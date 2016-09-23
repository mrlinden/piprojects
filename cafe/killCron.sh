#!/bin/sh
ps -ef | grep "sudo python cafe" | grep -v grep | awk '{print $2}' | xargs kill
