## University Management Dashboard System (UMDS)

> **Status:** Work in Progress

This system is a comprehensive web-based platform designed to streamline university operations, connecting students, lecturers, and administrators through a centralized management dashboard.

---

### Core Functions

The system is built to manage the complex workflows of a modern university environment. It provides role-based access to ensure that users only interact with data relevant to them.

* **Student Management:** Handles registration, profiles, and academic tracking using unique student numbers.
* **Academic Structure:** Organizes the institution into specific Faculties and Programmes (e.g., BSc in Computer Science).
* **Authentication & Security:** Uses secure password hashing and role-specific login portals to protect sensitive campus data.
* **User Profiles:** Synchronized data across multiple tables ensures that personal, faculty, and contact information remains consistent.

---

### User Roles

The platform caters to three distinct types of users:

1. **Administrators:**
* Oversee total system health.
* Manage user accounts across all departments.
* Access the master dashboard for institutional oversight.


2. **Lecturers:**
* Manage specific course content and student interactions within their assigned faculties.


3. **Students:**
* Access personal dashboards and profiles.
* View academic information specific to their registered programme.



---

### Technical Overview

* **Backend:** PHP (Session-based authentication).
* **Database:** MySQL (Relational structure with `users`, `students`, `lecturers`, and `faculties` tables).
* **Frontend:** Bootstrap 5 (Responsive UI) and FontAwesome icons.
* **Validation:** Dynamic registration forms with faculty dropdowns and unique identifier checks.

---

### Current Development Focus

The system is currently **under active development**. Recent updates include:

* Transitioning to secure `password_hash` and `password_verify` protocols.
* Implementing dynamic database-driven dropdowns for faculty selection.
* Refining the user registration logic to support flexible email formats (e.g., `@nust.na` or `@gmail.com`).

**Next Steps:** Developing the internal dashboard modules for each specific user role and building out the real-time interaction features.
