Local Docker development

Prerequisites

- Docker Desktop (or Docker + docker-compose)
- Optional: make, bash

Dev (quick)

1. Start dev stack (this runs Vite in the `node` container and exposes the app on port 8000):

```bash
docker compose -f docker-compose.dev.yml up --build
```

2. Open on your phone (same Wi-Fi) using your PC IP: http://<PC_IP>:8000

Production (build image + run)

```bash
# build the image locally
./scripts/deploy.sh
# run compose in foreground
docker compose up -d
```

Notes

- The Dockerfile is multi-stage: Node builds assets and the final image runs PHP-FPM.
- CI is provided at `.github/workflows/ci.yml` which runs asset builds, composer install and tests.
