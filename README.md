# Software Engineer Coding Challenge

This repository contains the source code for the Software Engineer Coding Challenge project. The application demonstrates a full-stack environment for managing products and their categories using Laravel and Vue.js, along with Redis, Elasticsearch, and automated testing via GitHub Actions.

---

## Table of Contents

- [Features & Progress](#features--progress)
- [Repository Structure](#repository-structure)
- [Getting Started](#getting-started)
  - [Backend Setup (API)](#backend-setup-api)
  - [Frontend Setup (Client)](#frontend-setup-client)
- [Usage](#usage)
  - [Creating a Product](#creating-a-product)
  - [Synchronizing with Elasticsearch](#synchronizing-with-elasticsearch)
  - [Seeding Large Data](#seeding-large-data)
- [Running Tests & CI](#running-tests--ci)

---

## Features & Progress

### Part 1
- [x] Entities / Models (Product, Category)
- [x] Create a product from the web interface
- [x] Create a product from the CLI
- [x] List products with sorting by price and filtering by category
- [x] Validation layer

### Part 2
- [x] Build a REST API
- [x] Command to seed the DB with 1k categories & 1M products
- [x] Cache layer in front of the DB
- [x] Logger that logs inline in development and in JSON in production

### Part 3
- [x] Optimize the DB
- [x] Write Unit tests
- [x] Implement CI (GitHub Actions)
- [x] Install & implement Elasticsearch (with full testing and partial front-end integration)
- [x] Integrate Elasticsearch with the front-end

### Documentation
- [x] README file

---

## Repository Structure

- **api/**  
  Contains the backend application built with Laravel. It uses Laravel Sail (Docker) for a consistent development environment, along with Redis for caching and Elasticsearch for advanced search capabilities.

- **client/**  
  Contains the frontend application built with Vue.js. This interface provides product and category management, including a dynamic search modal for categories.

---

## Getting Started

### Backend Setup (API)

1. **Navigate to the API directory:**

    ```bash
    cd api
    ```

2. **Install PHP dependencies using Composer:**

    ```bash
    composer install
    ```

3. **Start Docker containers using Laravel Sail:**

    ```bash
    ./vendor/bin/sail up -d
    ```

4. **Run database migrations:**

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

5. **Setup Elasticsearch indices:**  
   Create the necessary Elasticsearch indices for both categories and products by running:

    ```bash
    ./vendor/bin/sail artisan elasticsearch:setup
    ```

### Frontend Setup (Client)

1. **Navigate to the client directory:**

    ```bash
    cd ../client
    ```

2. **Install Node.js dependencies:**

    ```bash
    npm install
    ```

3. **Run the development server:**

    ```bash
    npm run dev
    ```

4. **Environment Variables:**  
   - Copy the `env.example` file to `.env` and update the backend API URL accordingly.  
   - In the backend, update the environment variable for the frontend panel URL if necessary.

---

## Usage

### Creating a Product

You can create a product via the web interface or the CLI. To create a product from the command line (within the Sail environment), run:

```bash
php artisan product:create "Awesome Product" 29.99 \
    --description="A high-quality, awesome product" \
    --image="https://example.com/images/awesome-product.jpg" \
    --categories="Electronics" \
    --categories="Gadgets"
```
> **Note:**  
> - If a specified category does not exist, it will be created automatically.  
> - The image URL will be used as the product image.

## Synchronizing with Elasticsearch

**Web Interface:**  
Products created via the web interface automatically sync with Elasticsearch.

**CLI or Seeded Data:**  
After creating products using the command line or seeding the database, update Elasticsearch by running:

```bash
./vendor/bin/sail artisan elastic:reindex
```
## Seeding Large Data:
To seed the database with 1,000 categories and 1,000,000 products, execute:

```bash
./vendor/bin/sail artisan db:seed --class=LargeDataSeeder
```
> **Note:**  
> - The seeding process may take up to 2 minutes to complete.
> - 
## Running Tests & CI:
To run the test suite, execute:

```bash
./vendor/bin/sail artisan test
```
Tests are also automatically run on every push to the main branch via GitHub Actions.


