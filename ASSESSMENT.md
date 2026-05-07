# Technical Assessment - Full-Stack Debugging & Code Review

## Overview

You are given a small e-commerce application built with **Laravel 12** (REST API backend) and **React** (frontend). The application includes user authentication, product browsing, shopping cart, checkout with a simulated payment flow, and order history.

The codebase has been written by a developer who shipped it quickly. It works on the surface, but contains **several bugs, security issues, and design flaws** that need to be identified and fixed.

---

## Your Task

### 1. Set Up the Project

Follow the instructions in `SETUP.md` to get both the backend API and React frontend running locally.

### 2. Explore the API

Swagger documentation is available at:
```
http://localhost:8000/api/docs
```

Use it to understand the API endpoints, request/response formats, and authentication flow.

### 3. Identify & Fix Bugs

Review the **entire codebase** (both backend and frontend) and find bugs across the following categories:

#### Backend (Laravel)
- **Security issues** - Are there any authorization or data access problems?
- **Performance issues** - Are there any queries that would scale poorly?
- **Validation gaps** - Are all endpoints properly validating input?
- **REST design** - Are HTTP methods used correctly across all routes?
- **Database design** - Are there any missing optimizations?

#### Configuration & Environment
- **Environment settings** - Are `.env` and config files properly configured for security?
- **Security hardening** - Are encryption, hashing, CORS, and session settings appropriate?

#### Authentication
- **Token lifecycle** - Is token expiry handled correctly on both sides (server config + application code)?

#### API Design
- **Response consistency** - Do all endpoints follow a consistent response structure?

#### Frontend (React)
- **State management** - Are React state updates done correctly?
- **Error handling** - Are API failures handled gracefully?
- **Auth flow** - What happens when a token expires or becomes invalid?
- **HTTP methods** - Are the correct HTTP methods used for each API call?
- **Response handling** - Does the frontend properly verify API responses before acting?

#### Integration
- **Payment flow** - Is the checkout/payment flow correctly implemented end-to-end?

---

## Deliverables

Please provide the following:

### A. Bug Report (document)

Create a file called `BUG_REPORT.md` with your findings. For each bug found, include:

```markdown
### Bug #[number]: [Short title]

**Category:** [Backend / Frontend / Auth / API Design / Integration]
**Severity:** [Critical / High / Medium / Low]
**File(s):** [file path(s)]

**Description:**
[What the bug is and why it's a problem]

**Steps to Reproduce:**
[How to trigger the bug]

**Fix:**
[Your proposed or implemented fix]
```

### B. Code Fixes

Apply your fixes directly to the codebase. Make sure the application still runs correctly after your changes.

### C. Summary

At the bottom of your `BUG_REPORT.md`, include a brief summary:
- Total number of bugs found
- How you prioritized them
- Any additional improvements you would suggest if this were a real production system

---

## Evaluation Criteria

| Criteria | Weight |
|---|---|
| **Bug identification** - How many real issues did you find? | 30% |
| **Fix quality** - Are your fixes correct, clean, and minimal? | 25% |
| **Security awareness** - Did you catch the security-critical issues? | 20% |
| **Code understanding** - Do your explanations show deep understanding? | 15% |
| **Communication** - Is your bug report clear and well-structured? | 10% |

---

## Rules

- You may use any tools, IDE, or debugger to help you
- You may reference documentation (Laravel, React, etc.)
- Focus on **real bugs** - don't report style preferences or nitpicks as bugs
- Quality over quantity: well-explained critical bugs are worth more than a long list of minor issues

### IMPORTANT - Debugging Evidence

**Do NOT remove any `console.log()`, `dd()`, `dump()`, `Log::info()`, or any other debugging statements you add while investigating issues.** Leave them in the code exactly where you placed them.

This is intentional. We want to see:
- **How you traced the problem** - which files and variables you inspected
- **Your debugging thought process** - what you checked first, what led you to the root cause
- **The path from symptom to fix** - your logs tell the story of how you diagnosed each bug

Your debugging logs are part of the evaluation. They show us your real-world troubleshooting skills — not just whether you found the bug, but *how* you found it. Clean code is important, but in this assessment your investigation trail matters more.

---

## Time Limit

You have **1 hour** to complete this assessment.

Suggested time allocation:
- 20 min: Setup & exploration
- 30 min: API testing with Swagger
- 60 min: Code review & bug identification
- 40 min: Implementing fixes
- 20 min: Writing the bug report
- 10 min: Final verification

---

## Project Structure

```
technical-assessment/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # API controllers (Auth, Products, Cart, Checkout, Orders)
│   │   └── Middleware/         # CORS middleware
│   └── Models/                 # Eloquent models (User, Product, Category, Cart, Order, etc.)
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Seed data (users, products, categories)
├── routes/
│   └── api.php                 # API route definitions
├── frontend/
│   └── src/
│       ├── components/         # Shared components (Navbar)
│       ├── context/            # React context (Auth, Cart)
│       ├── pages/              # Page components (Login, Register, Products, Cart, Checkout, Orders)
│       └── services/           # API service layer (Axios config)
├── SETUP.md                    # Setup instructions
└── ASSESSMENT.md               # This file
```

---

## Test Accounts

| Email | Password |
|---|---|
| user1@ikonicdev.com | password123 |
| user2@ikonicdev.com | password123 |
| admin@ikonicdev.com | admin12345 |

---

Good luck!
