# EduGhar – Online Learning System

EduGhar is a **web-based e-learning platform** built using **PHP, MySQL, and Python (Flask)**.
It allows administrators to manage courses and learners, while learners can enroll in courses and receive AI-based course recommendations using a **cosine similarity machine learning algorithm**.

This project is developed as part of the **BCA Project II**.

### Admin Dashboard

Admin users can manage the system through a dashboard. They need to login using their credentials, and can create new courses, upload course images and videos, upload course chapters/content, Update and delete course information and lastly view learners info.

### Learner Dashboard

Learners can interact with the system through their dashboard. They can login, insert their interests, view recommended courses,
browse all available courses, enroll in courses, view enrolled courses, generate course certificates, and update profile information. 

### Course Recommendation

We have included Machine Learning for course recommendation using Python and flask. It recommends courses to learners based on their interests using the Cosine Similarity algorithm.

#### How it works?

1. Learners select interests during registration.

2. Interests are stored in the learnerinterests table.

3. The ML service compares:

     - learner interests

     - course categories

4. Cosine similarity is calculated between them.

5. Courses with the highest similarity are recommended.


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

