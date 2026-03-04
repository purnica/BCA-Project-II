create database project6;

alter table course
add admin_id int,
ADD CONSTRAINT fk_admin
foreign key (admin_id) references admin(id);

alter table learnerlogin
add admin_id int,
ADD CONSTRAINT fk_learner_admin
foreign key (admin_id) references admin(id);

-- course table
create table course (
id int primary key auto_increment,
category varchar(50),
title varchar(100),
primary_description varchar(1000),
learning_outcomes varchar(500),
image varchar(255)
);
select * from course;

-- content table
create table course_content(
content_id int primary key auto_increment,
course_id int,
content_title varchar(100),
filepath varchar(255),
foreign key(course_id) references course(id)
);
select * from course_content;

-- learners login
create table learnerlogin(
learner_id int primary key auto_increment,
firstname varchar(30),
lastname varchar(30),
email varchar(30),
password varchar(100)
);
select * from learnerlogin;

-- learner interest
create table learnerinterests(
learnerinterest_id int primary key auto_increment,
learner_id int not null,
interests varchar(50),
foreign key(learner_id) references learnerlogin(learner_id)
);
SHOW CREATE TABLE learnerinterests;
ALTER TABLE learnerinterests
DROP FOREIGN KEY learnerinterests_ibfk_1;
ALTER TABLE learnerinterests
ADD CONSTRAINT fk_learner FOREIGN KEY (learner_id) REFERENCES learnerlogin(learner_id) ON DELETE CASCADE;

select * from learnerinterests;
SELECT l.learner_id,l.firstname,l.lastname,l.email,
GROUP_CONCAT(i.interests ORDER BY i.interests SEPARATOR ', ') AS interests
FROM learnerlogin l LEFT JOIN learnerinterests i ON l.learner_id = i.learner_id
GROUP BY l.learner_id, l.firstname, l.lastname, l.email;

-- for admin login
create table admin(
id int primary key auto_increment,
email varchar(30),
password varchar(100)
);
insert into admin (email, password) values("purnika@gmail.com","purnika"),("upasana@gmail.com", "upasana");
insert into admin (email, password) values("admin@gmail.com","admin123");
alter table admin
add name varchar(10);
update admin set name= 'Purnika' where id=1;
update admin set name= 'Upasana' where id=2;
update admin set name= 'Admin' where id=3;
select * from admin;

-- enrollment table
CREATE TABLE enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    learner_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_learner_course (learner_id, course_id),   -- prevents duplicate enrollments at DB level
    FOREIGN KEY (learner_id) REFERENCES learnerlogin(learner_id)    ON DELETE CASCADE,
    FOREIGN KEY (course_id)  REFERENCES course(id)     ON DELETE CASCADE
);
select * from enrollments;

