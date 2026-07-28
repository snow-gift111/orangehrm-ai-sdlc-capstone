FROM eclipse-temurin:17-jre-alpine

RUN addgroup -S orangehrm && adduser -S orangehrm -G orangehrm
WORKDIR /app
COPY target/leave-balance-alert-*.jar /app/leave-balance-alert.jar
USER orangehrm

EXPOSE 8080
ENTRYPOINT ["java", "-XX:MaxRAMPercentage=75.0", "-jar", "/app/leave-balance-alert.jar"]
