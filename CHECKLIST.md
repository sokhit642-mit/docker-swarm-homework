# Submission Checklist — Docker & Docker Swarm Homework

- [x] docker-compose.yml defines two correctly connected services with a named volume.
- [x] Application was confirmed reachable locally with docker compose up.
- [x] The application was deployed as a Swarm stack with docker stack deploy.
- [x] The application service was scaled to 3 replicas, confirmed with docker service ps.
- [x] A killed task was confirmed to be automatically replaced by Swarm.
- [x] The image was built, tagged with my Docker Hub username, and pushed as v1 and v2 (also v3).
- [x] The Docker Hub repository is set to Public and was pulled successfully from a separate machine (EC2) and after a local docker rmi.
- [x] An EC2 instance is running Docker with a security group allowing HTTP on the application's port.
- [x] The application was confirmed reachable from a browser at the EC2 instance's public IP address.
- [x] The EC2 instance was terminated after capturing evidence, to avoid ongoing charges.
- [x] All required screenshots and the README are included in the submission.

**Repository:** https://github.com/sokhit642-mit/docker-swarm-homework
**Docker Hub:** https://hub.docker.com/r/ksk6898/homework-app
