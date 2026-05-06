##  Architecture Overview

The project follows a layered architecture:

- **Controller** → Handles HTTP requests  
- **Service** → Business logic  
- **Repository** → Database queries  
- **Model** → Data structures  
- **Connection** → Database connection (PDO)  

##  Architecture Overview
### Start the application (build + run in background)
docker exec -it chatbot_v0-app-1 bash
### Start containers (without rebuild)
docker-compose up -d
docker-compose up --build -d
docker-compose down
docker-compose restart
docker exec -it chatbot_v0-db-1 mysql -u user -ppassword
docker exec chatbot_v0-db-1 mysqldump -u user -ppassword appdb > database/init.sql
type database\init.sql
###  remote container
docker ps -a
docker exec -it chatbot_v1-app-1 bash

### API Test (PowerShell)
Invoke-RestMethod -Uri "http://localhost:8080/api/user/fast" `
-Method POST `
-ContentType "application/json" `
-Body '{"email":"john@test.com"}'
