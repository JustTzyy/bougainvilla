<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# Cloud Deployment Guide

This document describes the deployment architecture and step-by-step process for deploying the Bougainvilla Hotel Management System to the cloud.

## Architecture Overview

* **Application:** Laravel 12 — deployed on Render via Docker
* **Web Server:** Nginx + PHP-FPM (inside the container)
* **Process Manager:** Supervisor (keeps Nginx and PHP-FPM running)
* **Database:** Amazon Aurora MySQL (AWS ap-southeast-2)
* **File Storage:** Amazon S3

---

# System Architecture

```
User (Browser)
  ↓
Render.com (Docker Container)
  ↓
Nginx → PHP-FPM (Laravel App)
  ↓                    ↓
Aurora MySQL          Amazon S3
```

---

# 1. Deploying the Application (Laravel → Render)

## Step 1: Push Project to GitHub

```bash
git add .
git commit -m "Initial commit"
git push origin main
```

## Step 2: Create Web Service on Render

1. Go to [https://render.com](https://render.com)
2. Click **New** → **Web Service**
3. Connect your GitHub repository
4. Set **Environment** to **Docker** and branch to `main`

## Step 3: Set Environment Variables

In the Render dashboard → **Environment** tab, add your app, database, and AWS credentials:

```
APP_NAME=Bougainvilla
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=your-aurora-endpoint.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=bougainvilla
DB_USERNAME=admin
DB_PASSWORD=your-password

AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=ap-southeast-2
AWS_BUCKET=bougainvilla

SESSION_DRIVER=database
CACHE_STORE=database
LOG_CHANNEL=stderr
```

> Generate APP_KEY locally: `php artisan key:generate --show`

## Step 4: Deploy

Click **Manual Deploy** → **Deploy latest commit**.

Render will automatically:

* Build the Docker image
* Install PHP and Node.js dependencies
* Compile frontend assets with Vite
* Start Nginx + PHP-FPM via Supervisor
* Run database migrations and seeders

---

# 2. Setting Up AWS Aurora MySQL

## Step 1: Create Aurora MySQL Cluster

1. Go to **AWS RDS Console** → **Create database**
2. Choose **Amazon Aurora** with **MySQL compatibility**
3. Set region to `ap-southeast-2` and enable **Publicly accessible**

## Step 2: Configure VPC and Security Group

1. In **VPC Console** → **Subnets**, ensure each subnet in the DB's availability zone has a route table with:

   * `0.0.0.0/0` → your Internet Gateway

2. In the Aurora **Security Group**, add an inbound rule:

   * **Type:** MySQL/Aurora, **Port:** 3306, **Source:** `0.0.0.0/0`

## Step 3: Verify Connectivity

```powershell
Test-NetConnection -ComputerName your-aurora-endpoint.rds.amazonaws.com -Port 3306
```

Confirm `TcpTestSucceeded: True` before proceeding.

---

# 3. Configuring Amazon S3 for File Storage

## Step 1: Create S3 Bucket

1. Go to **AWS S3** → **Create bucket**
2. Set bucket name and region to `ap-southeast-2`

## Step 2: Create IAM User

1. Go to **IAM** → **Users** → **Create user**
2. Attach a policy granting `s3:PutObject`, `s3:GetObject`, `s3:DeleteObject`, and `s3:ListBucket` on your bucket
3. Create **Access Keys** and add them to your Render environment variables

---

# 4. Post-Deployment Verification

* Visit your Render URL — the login page should load with full styling
* Check Render **Logs** for: `==> Setup complete! Waiting on Supervisor...`
* Login with seeded credentials to confirm database connectivity
* Upload a receipt or generate a report to confirm S3 is working
