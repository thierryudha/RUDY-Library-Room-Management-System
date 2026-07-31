# 📚 RUDY (Ruang Study)

> **Currently under active redevelopment.**

RUDY (Ruang Study) is a modern web-based room reservation system designed to streamline the process of borrowing study rooms in a library. This project is currently being rebuilt from scratch using a modern API-first architecture to improve scalability, maintainability, security, and developer experience.

The previous version was developed using PHP Native (MVC). This repository contains the next generation of RUDY powered by **Laravel as a Standalone REST API** and **Next.js** as the frontend application.

---

## 🚧 Project Status

**Current Phase:** Rebuilding (Work in Progress)

This project is under active development. Features, APIs, and documentation will continue to evolve throughout the development process.

---

## 🎯 Project Goals

- Build a scalable library room reservation system.
- Implement a clean RESTful API architecture.
- Separate frontend and backend responsibilities.
- Follow modern software engineering best practices.
- Improve maintainability and code quality.
- Build a production-ready portfolio project.

---

## ✨ Planned Features

### 🔐 Authentication

- User Registration
- Login & Logout
- Email Verification
- Forgot Password & Password Reset
- JWT Authentication
- Role-Based Access Control (RBAC)

### 👤 User Features

- Browse Available Rooms
- View Room Details
- Book a Study Room
- Booking History
- Booking Status Tracking
- User Profile Management
- Feedback Submission

### 🛠️ Administrator Features

- Dashboard Analytics
- User Management
- Account Verification
- Room Management (CRUD)
- Booking Approval & Rejection
- Booking Monitoring
- Feedback Management
- Export Reports

---

## 👥 User Roles

- Student
- Lecturer
- Educational Staff
- Administrator

---

## 🏗️ System Architecture & Tech Stack

![System Architecture](./docs/RUDY-High-level-System-Architecture.excalidraw.svg)

## 🛠️ Tech Stack

### Frontend

- Next.js
- React
- TypeScript
- Tailwind CSS
- TanStack Query
- Axios

### Backend

- Laravel
- REST API
- Eloquent ORM
- Laravel Sanctum *(or JWT - TBD)*

### Database

- PostgreSQL

### Development Tools

- Docker 
- Git
- GitHub
- Composer
- npm
- Postman

---

## 🗄️ Database Architecture & ERD Highlights

![Database Architecture & ERD](./docs/RUDY-ERD-for-Laravel-Standalone-API.png)

This project follows strict relational database design principles and enterprise-grade security standards. Below are the key architectural decisions implemented in the database design:

### 1. Dynamic Role-Based Access Control (RBAC)
- **Centralized Authentication:** Decoupled generic user credentials (`accounts`) from authorization levels using a master `roles` table (`role_id`).
- **Granular Authorization:** Supports multi-tier roles (`STUDENT`, `LECTURER`, `STAFF`, `ADMIN`, `SUPER_ADMIN`) seamlessly, enabling dynamic permission checks without hardcoded values.

### 2. Normalized Identity & Access Management (IAM)
- **Decoupled Identity Schemas:** Applied 3NF normalization by splitting domain-specific profile data (`students`, `lecturers`, `staffs`) from core authentication (`accounts`) using 1-to-1 relationships.
- **Zero Data Sparsity:** Eliminates sparse tables and unnecessary `NULL` columns while retaining strict schema integrity for varying academic identities.
- **Verification Workflow:** Integrated `activation_proof_path` for manual/automated credential verification prior to grant room booking privileges.

### 3. Role-Agnostic Group Booking System
- **Requester vs Participant Separation:** Distinguishes the primary applicant (`created_by_account_id`) from group members using a dedicated junction table (`booking_members`).
- **Cross-Role Collaboration:** The `booking_members` junction table links directly to `accounts`, allowing flexible group bookings between Students, Lecturers, and Staff without schema redundancy.

### 4. Verified Reviews & Audit Trails
- **Anti-Spam Feedback System:** The `feedbacks` table strictly enforces 1-to-1/1-to-N relationships bound to `booking_id` and `account_id`, guaranteeing that only users with a verified booking history can submit room ratings and comments.
- **Data Integrity & Retention:** Crucial tables (`accounts`, `rooms`, `feedbacks`) feature `deleted_at` timestamps for **Soft Delete** support, preserving historical booking analytics and audit trails.

---

## 📁 Project Structure

```text
rudy/
│
├── backend/              # Laravel Standalone API
│
├── frontend/             # Next.js Application
│
├── docs/
│   ├── PRD.md
│   ├── ERD.md
│   ├── API.md
│   └── ARCHITECTURE.md
│
└── README.md
```

---

## 📋 Planned Business Rules

- Maximum booking duration is **3 hours**.
- One booking per user per day.
- Prevent overlapping room reservations.
- Validate room capacity.
- Booking requires administrator approval.
- Users can track booking status.
- Account verification is required before making reservations.

---

## 📖 Documentation

The following documentation will be added during development:

- Product Requirements Document (PRD)
- Entity Relationship Diagram (ERD)
- API Documentation
- Database Schema
- Software Architecture
- Deployment Guide

---

## 🚀 Development Roadmap

- [x] Project Planning
- [ ] Product Requirements Document (PRD)
- [ ] Database Design (ERD)
- [ ] API Design
- [ ] Laravel Backend Development
- [ ] Authentication & Authorization
- [ ] Next.js Frontend Development
- [ ] Integration Testing
- [ ] Dockerization
- [ ] Deployment

---

## 📸 Preview

Application screenshots will be added after the first functional version is completed.

---

## 🤝 Contributing

This project is currently under active development.

Suggestions, issues, and feedback are always welcome.

---

## 📄 License

This project is developed for educational and portfolio purposes.

---

## 👨‍💻 Author

**Thierry Yudha Diantha** & **Muhammad Hanif Zidan**

Student of Applied Informatics Engineering

Politeknik Negeri Jakarta