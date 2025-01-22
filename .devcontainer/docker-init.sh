#!/bin/sh
set -e

# Ensure docker socket and folder have correct permissions
DOCKER_SOCKET=/var/run/docker.sock
DOCKER_FOLDER=/var/run/docker

# Create docker folder if it doesn't exist
if [ ! -d $DOCKER_FOLDER ]; then
    mkdir -p $DOCKER_FOLDER
fi

# Start docker daemon with specific socket permissions
dockerd -H unix://$DOCKER_SOCKET > /dev/null 2>&1 &

# Wait for docker daemon to be ready
timeout=30
while ! docker info > /dev/null 2>&1; do
    timeout=$((timeout - 1))
    if [ $timeout -le 0 ]; then
        echo "Timed out waiting for docker daemon to start"
        exit 1
    fi
    sleep 1
done

# Ensure socket has correct permissions
if [ -e $DOCKER_SOCKET ]; then
    chmod 666 $DOCKER_SOCKET
fi

# Add current user to docker group if it exists
if getent group docker > /dev/null; then
    sudo usermod -aG docker $(whoami)
fi

exec "$@"