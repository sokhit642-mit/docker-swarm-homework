# Docker & Docker Swarm Homework

A PHP + MySQL visit-counter application, built to demonstrate:
- Multi-container orchestration with Docker Compose
- Deployment as a Docker Swarm stack with replicas and self-healing
- Publishing a custom image to Docker Hub
- Running the published image on a live AWS EC2 instance

**Docker Hub repository:** https://hub.docker.com/r/ksk6898/homework-app
(public, tags `v1`, `v2`, `v3`)

## What the app does

Each time you load the page, it connects to MySQL, records a visit in a
`visits` table, and displays the total number of visits along with the
hostname of the container that served the request (useful for seeing
load-balancing across replicas). `v3` adds a styled card-based interface
on top of the same functionality.

## Project files

- `Dockerfile` — builds the PHP application image
- `index.php` — the application code (styled interface, `v3`)
- `docker-compose.yml` — Part 1: local multi-container setup
- `docker-stack.yml` — Part 2: Swarm stack definition
- `README.md` — this file
- `screenshots/` — evidence for each part of the assignment

## Part 1 — Run locally with Docker Compose

```bash
docker compose up -d
```

Visit `http://localhost:8080`. The visit counter increments on each
refresh.

To confirm the database data survives a restart:

```bash
docker compose down
docker compose up -d
```

Reload the page — the visit count continues from where it left off
(confirmed: count was 7 before restart, continued from 7 after), proving
the named volume (`db_data`) persisted the MySQL data. See
`screenshots/1-compose-local.png` and `screenshots/1b-compose-persistence.png`.

## Part 2 — Run as a Docker Swarm stack

Initialize Swarm (skip if already initialized):

```bash
docker swarm init
```

Deploy the stack:

```bash
docker stack deploy -c docker-stack.yml homework
```

Check status:

```bash
docker service ls
docker service ps homework_app
```

Scale the app service to 3 replicas:

```bash
docker service scale homework_app=3
docker service ps homework_app
```

See `screenshots/2-swarm-3-replicas.png`.

Self-healing was tested by force-removing one running container directly:

```bash
docker ps --filter "name=homework_app"
docker rm -f <container_id>
docker service ps homework_app
```

Swarm automatically started a replacement task within seconds, with no
manual recovery command run. See `screenshots/3-selfheal.png` — the
`docker service ps` output shows the killed task marked `Shutdown` /
`Failed ... non-zero exit (137)`, alongside a new task already `Running`.

## Part 3 — Build, tag, and push to Docker Hub

```bash
docker login

docker build -t ksk6898/homework-app:v1 .
docker push ksk6898/homework-app:v1
```

After making a visible change to `index.php` (updated the page title/
heading), rebuilt and pushed a second version:

```bash
docker build -t ksk6898/homework-app:v2 .
docker push ksk6898/homework-app:v2
```

A third version was later pushed with a redesigned, styled interface
(card layout, status indicator, styled counter) while keeping the same
underlying PHP/MySQL logic:

```bash
docker build -t ksk6898/homework-app:v3 .
docker push ksk6898/homework-app:v3
```

Repository: https://hub.docker.com/r/ksk6898/homework-app — set to
**Public** visibility, confirmed reachable from a separate, unauthenticated
account. See `screenshots/4-dockerhub-tags.png`.

## Part 4 — Deploy to AWS EC2

On a fresh EC2 instance (Ubuntu 26.04, t3.micro, free tier):

```bash
sudo apt update
sudo apt install docker.io -y
sudo systemctl enable --now docker

sudo docker run -d \
  --name homework_db \
  -e MYSQL_DATABASE=homeworkdb \
  -e MYSQL_USER=appuser \
  -e MYSQL_PASSWORD=apppassword \
  -e MYSQL_ROOT_PASSWORD=rootpassword \
  mysql:8.0

sudo docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' homework_db
# used to get the db container's internal IP for the app container below

sudo docker pull ksk6898/homework-app:v3

sudo docker run -d \
  --name homework_app \
  -p 80:80 \
  -e DB_HOST=<homework_db internal IP> \
  -e DB_USER=appuser \
  -e DB_PASSWORD=apppassword \
  -e DB_NAME=homeworkdb \
  -e APP_VERSION=v3 \
  ksk6898/homework-app:v3
```

Confirmed reachable from a laptop browser (not from inside the EC2
instance) at the instance's public IP. See `screenshots/5-ec2-live.png`.

Security group used: inbound TCP 22 (SSH, from my IP) and inbound TCP 80
(HTTP, open to 0.0.0.0/0) on the EC2 instance.

The EC2 instance was terminated after capturing evidence, to avoid
ongoing charges.

## Notes

- Part 1 (Compose) and Part 2 (Swarm) were run locally via Docker
  Desktop on Windows.
- Part 4 used a plain `docker run` (not Compose/Swarm) on a single fresh
  EC2 instance, per the assignment's Part 4 instructions.
- MySQL 8.0 was used in place of 5.7 for local/EC2 runs; functionality
  is unaffected for this application.
