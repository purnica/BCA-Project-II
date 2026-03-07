# EduGhar – Online Learning System

EduGhar is a **web-based e-learning platform** built using **PHP, MySQL, and Python (Flask)**.
It allows administrators to manage courses and learners, while learners can enroll in courses and receive AI-based course recommendations using a **cosine similarity machine learning algorithm**.

This project is developed as part of the **BCA Project II**.

### Admin Dashboard

Admin users can manage the system through a dashboard. They need to login using their credentials, and can create new courses, upload course images and videos, upload course chapters/content, Update and delete course information and lastly view learners info.

### Learner Dashboard

Learners can interact with the system through their dashboard. They can login, insert their interests, view recommended courses,
browse all available courses, enroll in courses, view enrolled courses, generate course certificates, and update profile information. 

## Project Architecture

```
/bca-project-II
│
├── admin
│   ├── upload
│   └── admin_dashboard_files
│
├── learner
│   └── learner_dashboard_files
│
├── flask_api
│   └── Python ML recommendation system
│
├── webpage
│   └── landing page, login, signup
|
├── styles
|   └── css, javascript, logo, images
|
├── Project.sql
|
└── README.md
```

## Technologies Used

### Frontend
- HTML
- CSS
- JavaScript

### Backend
- PHP
- MySQL

### Machine Learning
- Python
- Flask
- Scikit-Learn
- Cosine Similarity Algorithm

