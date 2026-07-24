# ELSS (Employee Leave Management System)

Production-ready Employee Leave Management System (B…OS) for a mid-sized enterprise.

## Repository Structure

- `backend/` - Spring Boot REST API (JWT auth, RBAC, Flyway, PostgreSQL)
- `frontend/` - React + Vite web app
- `docker-compose.yml` - Local PostgreSQL environment for development

## Prerequisites

- Java 21+
- Maven 3.9+
- Node.js 22+
- Docker (optional, for local PostgreSQL)

## Local Development

### 1) Start Database

```bash
docker compose up -d
```

### 2) Run Backend

```bash
cd backend
mvn spring-boot:run
```

Backend will run on `http://localhost:8080`.

- Swagger UI: `http://localhost:8080/swagger-ui/index.html`

### 3) Run Frontend

```bash
cd frontend
npm install
npm run dev
```

Frontend will run on `http://localhost:5173`.

### 4) Auth API

- Register: `POST /api/v1/auth/register`
- Login: `POST /api/v1/auth/login`
- Me: `GET /api/v1/auth/me`

## Configuration

Backend configuration is in `backend/src/main/resources/application.yml`.

Important env vars:
- `ELMS_DB_URL`
- `ELMS_DB_USER`
- `ELMS_DB_PASSWORD`
- `ELMS_JWT_SECRET` (must be changed in production)

## Production Notes

- Use a managed PostgreSQL with backups and point-in-time recovery.
- Rotate JWT secrets using a secret manager.
- Run behind TLS ingress/WAF.
- Enable centralized logging + metrics.
