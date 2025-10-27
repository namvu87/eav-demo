#!/bin/bash
# Docker wrapper script that uses sg (set group) to run docker commands
cd /home/namvc/Public/Project/eav-demo
sg docker -c "docker $*"
