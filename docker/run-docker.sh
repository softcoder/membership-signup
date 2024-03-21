#!/bin/bash

sudo docker run -p "8080:80" -v ${PWD}/app:/app -e APP_SMTP_OutboundUsername='markvejvoda@gmail.com' -e APP_SMTP_OutboundPassword='ruxnfeaflnyalmwf' -e APP_SMTP_OutboundFromAddress='markvejvoda@gmail.com' softcoder/membership-signup:latest
