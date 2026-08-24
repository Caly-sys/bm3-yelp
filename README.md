# 🎓 BM3 Yelp — SMK Bina Mandiri Multimedia Teacher Review Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js" />
  <img src="https://img.shields.io/badge/PixiJS-8.x-E72264?style=for-the-badge&logo=pixijs&logoColor=white" alt="PixiJS" />
  <img src="https://img.shields.io/badge/Tests-36%20Passing-brightgreen?style=for-the-badge&logo=githubactions&logoColor=white" alt="Tests" />
</p>

A modern, full-featured **Teacher Review & Rating Platform** tailored for **SMK Bina Mandiri Multimedia (BM3)**. Designed with a **Pixiv-inspired visual identity**, it provides students with a voice to rate and review their teachers across multiple academic dimensions, while giving school administrators robust tools to manage faculty profiles and moderate content safely.

---

## 📸 Overview & Key Features

### 🎨 1. Pixiv-Inspired Design System & Dark Mode
- **Pixiv Aesthetic**: Vibrant Pixiv Electric Blue (`#0096fa`), like coral pink (`#ff4060`), gold badges (`#ffaa00`), soft rounded cards (`16px`), and clean tag chips (`#Pemrograman Web`).
- **Interactive Dark Mode**: Full dark theme support (`#121519` base, `#1b1f24` surfaces) with a zero-flash theme switch (☀️ / 🌙) persisted in `localStorage`.
- **Canvas Particle Background**: Powered by **PixiJS**, dynamic floating particle orbs animate smoothly behind the hero section.
- **Micro-Animations**: Animated rating counters, interactive star hover fill/bounce, and pulse effects on voting.

### 👨‍🏫 2. Teacher Directory & Leaderboard
- **Top Teacher Leaderboard**: Daily ranking podium badges (🥇 #1 Gold, 🥈 #2 Silver, 🥉 #3 Bronze) calculated dynamically from student reviews.
- **Instant Search & Filter**: Search teachers by name, subject (e.g. *Pemrograman Web*, *Desain Grafis*, *Matematika*), and sort by rating or review count.
- **Detailed Profiles**: Average ratings breakdown, teaching style overview, and verified student feedback.

### ⭐ 3. Multi-Dimension Review System
- **5 Evaluation Categories**:
  1. 🌟 **Overall Rating**
  2. 📖 **Teaching Quality**
  3. 💡 **Explanation Clarity**
  4. ⚖️ **Grading Fairness**
  5. 📝 **Assignment Workload**
- **Integrity Controls**: Duplicate review prevention (one review per teacher per student), authorization policies (`ReviewPolicy`), and editable submissions.

### 👍 4. Helpful Voting & Community Moderation
- **AJAX Vote Toggling**: Students can upvote helpful reviews with instant UI updates and CSRF-protected backend validation (self-voting is prevented).
- **Review Reporting**: Flag inappropriate content with pre-categorized reasons (*Spam*, *Harassment*, *Offensive Language*, *Personal Information*, *Fake Review*, or *Other*).

### 🛡️ 5. Comprehensive Admin Dashboard
- **Analytics Cards**: Total faculty count, registered students, published reviews, and pending reports.
- **Teacher Management (CRUD)**: Create, edit, and delete teacher profiles with photo upload or automatic avatar initials.
- **Report Moderation**: Review reported posts and safely resolve (hide) or dismiss flags.
- **User Moderation**: View student accounts, track submitted reviews, and toggle account suspensions.

---

## 🛠️ Tech Stack

- **Backend**: [Laravel 11](https://laravel.com), PHP 8.2+
- **Frontend**: [Blade Templates](https://laravel.com/docs/blade), [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Graphics / VFX**: [PixiJS 8](https://pixijs.com)
- **Database**: SQLite (default, zero-configuration) or MySQL / PostgreSQL
- **Asset Bundler**: [Vite](https://vitejs.dev)
- **Testing**: [PHPUnit](https://phpunit.de) & Laravel Feature Testing Suite

---

## 🚀 Getting Started & Installation Tutorial

Follow these step-by-step instructions to set up and run BM3 Yelp locally on your machine.

### Prerequisites

Ensure you have the following software installed:
- **PHP** >= 8.2 with SQLite, PDO, cURL, and Mbstring extensions
- **Composer** (PHP Package Manager)
- **Node.js** >= 18.x & **NPM**
- **Git**

---

### Step 1: Clone the Repository

```bash
git clone https://github.com/Caly-sys/bm3-yelp.git
cd bm3-yelp
```

---

### Step 2: Install Dependencies

Install both PHP and JavaScript dependencies:

```bash
# Install PHP packages
composer install

# Install NPM packages
npm install
```

---

### Step 3: Environment Setup

Duplicate the example environment configuration file and generate your application encryption key:

#### On Windows (PowerShell / Command Prompt):
```powershell
copy .env.example .env
php artisan key:generate
```

#### On Linux / macOS:
```bash
cp .env.example .env
php artisan key:generate
```

---

### Step 4: Database Setup & Seeding

The application uses **SQLite** by default, requiring no database server installation.

Run the migrations and populate the database with the pre-configured demo seeder:

```bash
# Run migrations and seed with realistic teachers, students, reviews, and votes
php artisan migrate:fresh --seed
```

---

### Step 5: Link Storage for Uploads

Create a symbolic link to make uploaded teacher profile photos publicly accessible:

```bash
php artisan storage:link
```

---

### Step 6: Compile Assets

Build the frontend assets for production, or run the development server for hot-reloading:

```bash
# Build production bundle
npm run build

# OR start live development server (optional)
npm run dev
```

---

### Step 7: Launch the Application

Start the local Laravel development server:

```bash
php artisan serve
```

🎉 Open your browser and navigate to: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Demo & Admin Credentials

The database seeder automatically populates sample accounts for immediate testing:

| Role | Username | Email | Password | Access Level |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin@bm3.sch.id` | `password` | Full Admin Dashboard (`/admin`), Teacher CRUD, Report Resolution, User Suspension |
| **Demo Student** | `student_demo` | `student@bm3.sch.id` | `password` | Write reviews, edit own reviews, vote helpful, report reviews, profile editing |

> 💡 **Note**: The seeder also creates **30 other student accounts** (all with password `password`) along with realistic multi-teacher review threads and helpful vote distributions.

---

## 🧪 Running Automated Tests

BM3 Yelp includes a comprehensive automated test suite covering all authentication flows, student journeys, rating constraints, vote integrity, and admin moderation rules.

Run the test suite with:

```bash
php artisan test
```

### Test Coverage Highlights:
- ✅ `test_guest_can_view_home_page`
- ✅ `test_guest_can_view_teacher_directory_and_filter`
- ✅ `test_guest_can_view_teacher_profile_with_ratings`
- ✅ `test_student_can_create_a_review`
- ✅ `test_student_cannot_submit_duplicate_review_for_same_teacher`
- ✅ `test_student_can_edit_own_review`
- ✅ `test_student_cannot_edit_another_users_review`
- ✅ `test_student_can_toggle_helpful_vote_on_review`
- ✅ `test_user_cannot_vote_on_own_review`
- ✅ `test_student_can_report_review`
- ✅ `test_student_cannot_access_admin_dashboard`
- ✅ `test_admin_can_access_dashboard_and_manage_teachers`
- ✅ `test_admin_can_resolve_reports`
- ✅ All Laravel Breeze authentication and profile management tests

**Total:** 36 passing tests (94 assertions).

---

## 📁 Project Architecture

```
bm3-yelp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Admin CRUD, Report & User moderation
│   │   │   ├── HomeController.php  # Landing page & top ranked teachers
│   │   │   ├── TeacherController.php# Teacher directory & profiles
│   │   │   ├── ReviewController.php # 5-star review submissions & edits
│   │   │   ├── ReviewVoteController.php # AJAX helpful vote toggling
│   │   │   └── ReportController.php # Content flagging
│   │   └── Middleware/
│   │       └── EnsureIsAdmin.php   # Admin route guard
│   ├── Models/                     # User, Teacher, Review, ReviewVote, Report
│   └── Policies/
│       └── ReviewPolicy.php        # Granular authorization for review management
├── database/
│   ├── factories/                  # Seed factories for users, teachers, reviews
│   ├── migrations/                 # Database schema definitions
│   └── seeders/DatabaseSeeder.php  # Sample data generator
├── resources/
│   ├── css/app.css                 # Custom Tailwind & Pixiv theme definitions
│   ├── js/
│   │   ├── app.js                  # Main bundle & Alpine.js initialization
│   │   ├── pixi-background.js      # PixiJS floating particle background
│   │   └── rating-input.js         # Interactive star rating component logic
│   └── views/
│       ├── admin/                  # Admin dashboard & management views
│       ├── components/             # Reusable UI components (cards, stars, modals)
│       ├── teachers/               # Directory & profile pages
│       └── home.blade.php          # Landing page
├── routes/
│   ├── web.php                     # Application route definitions
│   └── auth.php                    # Authentication routes
└── tests/
    └── Feature/
        └── Bm3PlatformTest.php     # Comprehensive feature test suite
```

---

## 🤝 Contributing

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
