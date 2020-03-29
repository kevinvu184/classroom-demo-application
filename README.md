# P2PMarkingSystem

Kevin Vu 2020

© Kevin Vu 2020. Disclaimer: This repo is an asset of Kevin Vu & Ted Vu.

- Link: https://p2p-marking-system.appspot.com/

## Team member
- Kevin Vu
- Ted Vu

## Project Brief
Classroom Student Software Demo is wonderful, but who should be the evaluator? Some argue it should be lecturer, but there are two inherent problems with this argument:

- To make the process of evaluation become fair, lecturer should come up with a set of criteria or rubric, however, this Demo Activity is not rigorous, its sole purpose is to allow student to engage more with hands-on programming experience and be employable. To make this activity interesting students should be allow to be creative and go wild with their imagination. Plus, it will be a burden on lecturer having to come up with rubric everytime a student wants to pitch his/her ideas since this activity is not mandatory in the course.

- On the other hand, not having a rubric can lead to biases in evaluation.

Ok how about students marking each other's work, that sounds like a great solution! Let's create a Peer-to-Peer Marking Application, the software will be subject to but not limited to these following requirements:

1. Student:
- Register their account.
- Change their name/password.
- Mark other team and not their own (fairness).
- Making only one evaluation ( fairness+security).
2. Network Admin/Lecturer:
- See the mark for demoed team.
- Reset Database for the next demo session.

## Possible Future Enhancement
- Since we do not have RMIT Student Database, one students can register many accounts with their friend's student number, however we only allow one student number to be registered so we can still ensure fairness.

- Network Admin currently has to manually add each team to database on GCP (technically, not having a UI yet).

- Student can not register their own team but only send the mail to Lecturer for registration (it sounds like another project though maybe StudentDemoRegistration App).

## Account Enquiry and Usage
- For students: Note that since we do not have RMIT Database for password hash comparison, we stored the password as your student number without the 's'.

- For Network Admin: If you want to see what Network Admin UI looks like (sneak peak pics are provided below), feel free reach out to me at [Kevin Vu & Ted Vu](mailto:kevinvu184@gmail.com,tedvu184@gmail.com?subject=[GitHub]%20P2PMarkingSystem%20Enquiry) for Network Admin account and password  .

## Stack
- Front end: **HTML/CSS/JS - Bootstrap**
- Back end: **PHP** (Only pure PHP)
- DB: **Google Cloud Datastore** (NoSQL cloud based db)
- Deploy Platform: **Google App Engine Flexible**
- Other Cloud Services:
  - **Google Cloud Shell + Text Editor**

## Recreate the App
**Attention**: Since this project is built with Google Cloud Platform (GCP), please register an account on GCP, and install Google SDK before following these steps:
1. Clone the project `git clone https://github.com/kevinvu184/P2PMarkingSystem`
2. Change directory into git repo: `cd P2PMarkingSystem`
3. Install dependencies: `composer install`
4. For development on localhost: `php -S localhost:8080 -t www/`
5. For Deploying and publish your `project: gcloud app deploy`

## UI Network Admin 

<img width="863" alt="UISneakPeak" src="https://user-images.githubusercontent.com/36873497/77839126-d5578480-71c5-11ea-9c73-5c1607c42de4.PNG">



