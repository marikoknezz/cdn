#!/bin/bash -

CURRENT_DIR=$(dirname $BASH_SOURCE)

sed -i "/.*listen 8080;.*/a \            add_header \"Access-Control-Allow-Origin\" \"\*\";" "${CONFIGURATION_TARGET_PATH}/configmap/nginx.yaml"
sed -i "/.*location @app {.*/a \                add_header \"Access-Control-Allow-Origin\" \"\";" "${CONFIGURATION_TARGET_PATH}/configmap/nginx.yaml"
