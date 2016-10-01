#!/bin/sh
ps -ef | grep "sudo python" | grep -v grep | awk '{print $2}' | xargs kill
