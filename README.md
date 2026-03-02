# MTTS Learning Management System (LMS) Plugin

A premium, secure, and brand-consistent Learning Management System specialized for Mountain-Top Theological Seminary. This plugin transforms WordPress into a full-featured educational platform with dedicated portals for students, faculty, and administrative staff.

## 🎨 Brand Identity

The LMS features a custom **Institutional Purple** theme (`#6b21a8`) derived from the MTTS brand identity. Every interface component, from login portals to student ID cards, has been meticulously polished for a professional, premium aesthetic.

## 🚀 Key Features

### 🏛️ Dedicated Dashboards

- **Student Portal**: Access courses, track progress, and view institutional notifications.
- **Lecturer Portal**: Manage student performance and course materials.
- **Administrative Suite**: Custom frontend dashboards for:
  - **School Admin**: General oversight and system configuration.
  - **Registrar**: Admission processing and student record management.
  - **Accountant**: Financial tracking and tuition management.
  - **Campus Coordinator**: Localized campus oversight.

### 🛡️ Security First

- **SQL Injection Prevention**: All database queries are protected using `$wpdb->prepare` and strict sanitization.
- **Nonce Protection**: Every form submission and authentication request is secured with WordPress Nonces to prevent CSRF and unauthorized actions.
- **Role-Based Access Control**: Strict redirection logic ensuring users only access their authorized frontend dashboards.
- **Secure Authentication**: Integrated "Others Portal" for Alumni and Guest Ministerial access with separate secure handling.

### 🎓 Specialized Tools

- **Formal Student ID**: Automated generation of purple-themed vertical ID cards with QR code validation.
- **Alumni Network**: A dedicated community platform for graduated ministers to connect, share resources, and participate in events.
- **Notification Engine**: Brand-consistent email and SMS templates for institutional communications.

## 🛠️ Installation

1. Copy the `mtts-plugin` directory to your `/wp-content/plugins/` folder.
2. Activate the plugin through the WordPress 'Plugins' menu.
3. Configure the MTTS Settings in the admin sidebar.
4. Use the `[mtts_login_form]` shortcode on your landing page.

## 📖 Usage

### Redirection Logic

The system automatically detects the user's role upon login and redirects them:

- **MTTS Student** → `/student-dashboard`
- **MTTS Registrar** → `/registrar-dashboard`
- **Administrator** → `/wp-admin` (Super admins only)
- **Alumni/Guest** → `/alumni-network`

## 🔒 Security Best Practices

Developers should always:

1. Use `MttsLms\Core\Database\Model` for database operations.
2. Always verify nonces when handling `$_POST` data.
3. Use the global CSS variables defined in `mtts-lms.css` to maintain brand consistency.

---

_Developed for Mountain-Top Theological Seminary (MTTS) By BennieChat TechWealth Solutions Ltd. (benniechatsystems@gmail.com)_
