
# Payroll Report - Company Payroll Report Generator

***

## How To Install Locally
To clone and run this application, you'll need [Git](https://git-scm.com), [Docker](https://docs.docker.com/engine/install) and [Docker Compose](https://docs.docker.com/compose/install).

```bash
# Clone repository
$ git clone git@github.com:Deodathus/PayrollReport.git payroll_report

# Go into the repository
$ cd payroll_report

# Build docker containers and install dependencies
$ cp .env.example .env 
$ make build
$ make up
$ make install

# Tests (optional) 
$ make pu
```

If you can not use Makefile for some reasons you can run all the commands manually:
```bash
# Build docker containers and install dependencies
$ docker-compose build
$ docker-compose up -d
$ docker exec -it payroll_report-php composer install
$ docker exec -it payroll_report-php php bin/console d:m:m

# Tests (optional)
$ docker exec -it payroll_report-php composer phpunit
```

## How To Use
```bash
# Run from the repository folder
$ make bash
```

### Department
#### Create Department
```bash
# From bash
$ php bin/console department:create <name> <salaryBonus> (<salaryBonusType>[percentage, fixed_amount])

# Example
$ php bin/console department:create IT 500 fixed_amount
```

#### Get All Departments
```bash
# From bash
$ php bin/console department:get-all
```

#### Example response:

| ID  | Name | Salary Bonus Type | Salary Bonus |
| --- |------| ----------------- |--------------|
| f25cd0db-8688-4f7c-b7a4-34e74831a642 | IT   | fixed_amount | 500          |
| 8a7bb328-ce9a-4c50-aaba-c9d550a34a15 | HR   | fixed_amount | 300          |

#### Get Department By ID
```bash
# From bash
$ php bin/console department:get-by-id <id>

# Example
$ php bin/console department:get-by-id f25cd0db-8688-4f7c-b7a4-34e74831a642
```

#### Example response:
| ID  | Name | Salary Bonus Type | Salary Bonus |
| --- |------| ----------------- |--------------|
| f25cd0db-8688-4f7c-b7a4-34e74831a642 | IT   | fixed_amount | 500          |

### Employee
#### Create Employee
```bash
# From bash
$ php bin/console employee:create <departmentId> <firstName> <lastName> <salary> (<hiredAt>[Ymd])

# Example
$ php bin/console employee:create f25cd0db-8688-4f7c-b7a4-34e74831a642 Jan Kowalski 10000 20170729
```

#### Get All Employees
```bash
# From bash
$ php bin/console employee:get-all
```

#### Example response:

| ID  | Department ID                         | First Name | Last Name | Hired At | Salary | 
| --- |---------------------------------------|------------|-----------|----------|--------|
| 1242178a-578e-48fe-a7d0-8813e7fc4ca0 | f25cd0db-8688-4f7c-b7a4-34e74831a642  | Jan        | Kowalski  | 2017-07-29 00:00:00 | 10000 |
| 8f2e6fcc-8fb1-45b8-bfd1-2a44d7f6cc32 | f25cd0db-8688-4f7c-b7a4-34e74831a642  | Jakub      | Kowalski  | 2015-07-29 00:00:00 | 5000  |

### Report
#### Generate Report
```bash
# From bash
$ php bin/console report:generate
```

#### Get Reports Data
```bash
# From bash
$ php bin/console report:get-all-data
```

#### Example response:
| Report ID | Generated At |
| --------- | ------------ |
| 2be980ea-4457-499c-9bd1-9119e23994ba | 2022-08-29 16:10:14 |

#### Get Report By ID
```bash
# From bash
$ php bin/console report:get-by-id <id> 
  [(<--filter|-f>[employee_first_name, employee_last_name, department_name]) (optional, multiple values allowed)] 
  [(<--sortBy>[employee_first_name, employee_last_name, department_name, base_salary, salary_bonus, salary_bonus_type, total_salary])(optional)]
  [(<--sortOrder[ASC, DESC]>)(optional)]

# Example
$ php bin/console report:get-by-id 2be980ea-4457-499c-9bd1-9119e23994ba

# Example with filter
$ php bin/console report:get-by-id 2be980ea-4457-499c-9bd1-9119e23994ba -f employee_first_name=jan

# Example with multiple filters
$ php bin/console report:get-by-id 2be980ea-4457-499c-9bd1-9119e23994ba -f employee_first_name=jan -f employee_last_name=owal

# Example with sorting
$ php bin/console report:get-by-id 2be980ea-4457-499c-9bd1-9119e23994ba --sortBy=employee_first_name --sortOrder=ASC

# Complex example
$ php bin/console report:get-by-id 2be980ea-4457-499c-9bd1-9119e23994ba -f employee_first_name=jan -f employee_last_name=owal --sortBy=employee_first_name --sortOrder=ASC
```

You can combine multiple filters: it will be `OR` logic operator between them.

If you are using --sortBy option you have to use --sortOrder option as well and vice versa.

#### Example response:
| First Name | Last Name | Department Name | Base Salary | Salary Bonus | Salary Bonus Type | Total Salary |
| ---------- | --------- | --------------- | ----------- | ------------ | ----------------- | ------------ |
| Jakub | Kowalski | IT | 5000 | 3500 | fixed_amount | 8500 |
| Jan | Kowalski | IT | 10000 | 2500 | fixed_amount | 12500 |