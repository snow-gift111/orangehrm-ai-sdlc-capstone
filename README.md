# Leave Balance Alert — Sprint 1 (Spring Boot Module)

Spring Boot module for Sprint 1 of the **Leave Balance Alert** initiative. This service exposes APIs to evaluate leave balances and produce alerts, with integrations intentionally stubbed for local development and early sprint validation.

---

## Features (Sprint 1)

- **Leave balance evaluation API**
  - Fetches (stubbed) leave balance data for an employee.
  - Evaluates thresholds and determines whether an alert should be raised.
- **Alert generation**
  - Creates alert records/messages when balances cross configured thresholds.
- **Configurable thresholds**
  - Thresholds configurable via `application.yml` / environment variables.
- **Provider abstraction**
  - Leave balance and employee directory integrations are behind interfaces.
  - Sprint 1 ships with **stub providers** for local/demo usage.
- **In-app channel logging**
  - “In-app” alert channel is implemented as application logging (no external notification system yet).
- **Health endpoints**
  - Basic health check endpoints to validate service is up.

---

## Requirements

- Java 17+
- Maven 3.8+
- (Optional) Docker, if you want to containerize locally

---

## Local Run

### 1) Configure application properties

Default configuration is intended to work out-of-the-box with stub providers.

Common environment variables (if supported in your setup):

- `SERVER_PORT` (default: `8080`)
- `LEAVE_ALERT_THRESHOLD_DAYS` (example: `5`)
- `LEAVE_PROVIDER` (default: `stub`)
- `EMPLOYEE_PROVIDER` (default: `stub`)
- `ALERT_CHANNEL` (default: `in-app`)

If your project uses Spring profiles, you can run with a local profile:

- `SPRING_PROFILES_ACTIVE=local`

### 2) Run with Maven

From the Spring Boot module directory:

- Build:
  - `mvn clean package`
- Run:
  - `mvn spring-boot:run`

Or run the packaged jar (path may vary by module name):

- `java -jar target/*.jar`

### 3) Verify service is running

- Health:
  - `curl -s http://localhost:8080/actuator/health`

If Actuator isn’t enabled in your build, use the module’s health endpoint if provided (commonly `/health`).

---

## Demo Users (Stub Data)

Sprint 1 uses stub providers that return deterministic example employees and balances. Use these sample identities in requests.

Example demo users:

- `E1001` — Typical employee with a moderate leave balance
- `E1002` — Low balance user (likely to trigger alert depending on threshold)
- `E1003` — Edge case / zero balance user

If your local stub dataset differs, check the stub provider implementation/classes in the module and update the IDs accordingly.

---

## Sample cURL Commands

> Replace endpoint paths below with the exact controller mappings in your module if they differ. The commands illustrate typical usage for Sprint 1 flows.

### 1) Health check

curl -s http://localhost:8080/actuator/health

### 2) Get leave balance (example)

curl -s "http://localhost:8080/api/leave/balance?employeeId=E1001"

### 3) Evaluate balance and generate alert (example)

curl -s -X POST "http://localhost:8080/api/leave/alerts/evaluate" \
  -H "Content-Type: application/json" \
  -d '{
    "employeeId": "E1002",
    "asOfDate": "2026-07-25"
  }'

### 4) List generated alerts (example)

curl -s "http://localhost:8080/api/leave/alerts?employeeId=E1002"

---

## Notes

### Stub Providers (Sprint 1)

Sprint 1 integrations are **stubbed** by design:

- **Employee provider**: returns a small in-memory list of employees.
- **Leave balance provider**: returns predefined balances per employee.

This enables:
- Fast local development without external dependencies
- Stable demo behavior for sprint reviews
- Clear seams for replacing with real implementations in future sprints

To switch to real providers later, implement the provider interfaces and change configuration to select the implementation.

### In-App Channel = Logging (Sprint 1)

The in-app alert channel is implemented as **application logs** rather than a UI notification feed or a messaging provider.

Expected behavior:
- When an alert is generated, the service logs an “in-app notification” entry.
- These logs serve as the sprint’s verification mechanism.

Tips:
- For local development, set log level to `INFO` or `DEBUG` to view alert emission details.
- Tail logs while testing:
  - If running via Maven: watch the console output.
  - If running from a jar: `java -jar ... | tee app.log`

### Error Handling / Validation

- Invalid `employeeId` should return a 4xx response (behavior depends on implementation).
- Ensure date formats follow ISO-8601 (e.g., `YYYY-MM-DD`) when included.

---

## Next Steps (Beyond Sprint 1)

Planned enhancements in future sprints typically include:
- Real HR/leave system integration
- Persisting alerts to a datastore
- Actual in-app notifications (UI/API feed) and/or external channels (email/Teams/Slack)
- Admin-configurable thresholds per policy/employee group