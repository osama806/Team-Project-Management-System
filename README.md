# Team Project Management System

The **Team-Project-Management-System** is a RESTful web service built with PHP and MySQL that allows managers to manage a collection of projects and their tasks.  

## Table of Contents

- [Team Project Management System](#team-project-management-system)
  - [Table of Contents](#table-of-contents)
  - [Features](#features)
  - [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
    - [Postman Collection](#postman-collection)

## Features

1. Projects
  - Add new projects to storage
  - Retrieve details of a specific project or all projects
  - Update projects information
  - Delete projects from storage
  - Retrieve deleted project ***(soft-delete)*** 

2. Tasks
  - Add new tasks to projects
  - Retrieve details of a specific task or all tasks
  - Update tasks information
  - Delete tasks from projects
  - Retrieve deleted task ***(soft-delete)*** 
  - Assignee tasks to users (manager/developer/tester) 

3. Project_User
  - Store shared info between projects and users

4. Authorization (users)
  - Registration for new user
  - Login user
  - Create refresh token
  - Display user info
  - Logout user
  - Delete user account
  - Retrieve deleted user
  - Delivery task to admin 
  - Modify task status (in-progress -> done) by developer
  - Add notes to tasks by tester

## Getting Started

These instructions will help you set up and run the Team Project Management System on your local machine for development and testing purposes.

### Prerequisites

- **PHP** (version 7.4 or later)
- **MySQL** (version 5.7 or later)
- **Apache** or **Nginx** web server
- **Composer** (PHP dependency manager, if you are using any PHP libraries)


### Installation

1. **Clone the repository**:

   ```
   git clone https://github.com/osama806/Team-Project-Management-System.git
   cd Team-Project-Management-System
   ```

2. **Set up the environment variables:**:

  Create a .env file in the root directory and add your database configuration:
  ```
  DB_HOST=localhost
  DB_PORT=3306
  DB_DATABASE=team-project-management-system
  DB_USERNAME=root
  DB_PASSWORD=password
  ```

3. **Set up the MySQL database:**:

  - Create a new database in MySQL:
    ```
    CREATE DATABASE team-project-management-system;
    ```
  - Run the provided SQL script to create the necessary tables:
    ```
    mysql -u root -p team-project-management-system < database/schema.sql
    ```

4. **Configure the server**:  
  - Ensure your web server (Apache or Nginx) is configured to serve PHP files.
  - Place the project in the appropriate directory (e.g., /var/www/html for Apache on Linux).

5. **Install dependencies (if using Composer)**:
  ```
  composer install
  ```

6. **Start the server:**:
  - For Apache or Nginx, ensure the server is running.
  - The API will be accessible at http://localhost/Team-Project-Management-System.


### Postman Documentation
- Link:
    ```
    https://documenter.getpostman.com/view/32954091/2sAXqqchct
    ```
