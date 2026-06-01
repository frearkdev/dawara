#!/usr/bin/env bash
# Simple deploy script: build Docker image and push (requires DockerHub login or registry configured)
set -euo pipefail
IMAGE_NAME="dawara/app:latest"

echo "Building production image..."
docker build -t ${IMAGE_NAME} .

echo "Tagging and pushing (optional)..."
# docker push ${IMAGE_NAME}

echo "Create/Update services with docker-compose (production)"
# docker-compose -f docker-compose.yml up -d --remove-orphans

echo "Done."
