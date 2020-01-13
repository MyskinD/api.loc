1. git clone https://github.com/MyskinD/api.loc.git .
2. go to the project folder & run composer
3. configure the virtual host on the folder web (/web)
4. configure access to the database (/config/db.php)
5. create tables for databases (in yii2 basic no migration mechanism)
6. for test api use Postman program

<pre>
CREATE TABLE projects (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) DEFAULT NULL,
  code varchar(10) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  budget int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

CREATE TABLE contacts (
  id int(11) NOT NULL AUTO_INCREMENT,
  project_id int(11) NOT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  phone varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE contacts
ADD CONSTRAINT FK_contacts_project_id FOREIGN KEY (project_id)
REFERENCES projects (id) ON DELETE NO ACTION;
</pre>

<h2>Systems PHP Yii2 Test Task</h2>

<h3>Description</h3>

<p>We have 2 models "Project" and "Contact" in our Application. We already have some data in our storage.</p>

<p>Project has next properties:</p>

<ul>
    <li>id;</li>
    <li>name - string 50 only space and latin letters in any case, min length - 5;</li>
    <li>code - string 10 only latin letters in lower case, min length - 3, can not be changed;</li>
    <li>url - only valid urls from domain example.com;</li>
    <li>budget - BYN amount,</li>
    <li>contacts - collection of Contact, at least one is required.</li>
</ul>

<p>Contact has next properties:</p>

<ul>
<li>id;</li>
<li>firstName - string 50;</li>
<li>lastName - string 50;</li>
<li>phone - mask +xxx (xx) xxx-xx-xx;</li>
</ul>

<p>We need to create API to perform CRUD for Project:</p>

<ul>
<li>Endpoints:</li>
<li>
<ul>
<li>List:</li>
<li>
<ul>
<li>Method: GET</li>
<li>URI: /projects</li>
<li>Allow filtering by code with partial match and budget between</li>
<li>
Response:<br>
<pre>
[
  {
    "id": 1,
    "name": "Project",
    "code": "project",
    "url": "http://example.com/my-page",
    "budget": 100,
    "contacts": [
      {
        "id": 1,
        "firstName": "John",
        "lastName": "Doe",
        "phone": "+012 (34) 567-89-10"
      }
    ]
  }
]
</pre>
</li>
</ul>
</li>
<li>Single Item:</li>
<li>
<ul>
<li>Method: GET</li>
<li>URI: /projects/[id]</li>
<li>
Response: <br>
<pre>
{
  "id": 1,
  "name": "Project",
  "code": "project",
  "url": "http://example.com/my-page",
  "budget": 100,
  "contacts": [
    {
      "id": 1,
      "firstName": "John",
      "lastName": "Doe",
      "phone": "+012 (34) 567-89-10"
    }
  ]
}
</pre>
</li>
<li></li>
</ul>
</li>
<li>Create:</li>
<li>
<ul>
<li>Method: POST</li>
<li>URI: /projects</li>
<li>
Request: <br>
<pre>
{
  "name": "New Project",
  "code": "np",
  "url": "http://example.com/new-page",
  "budget": 100,
  "contacts": [
    {
      "firstName": "Jane",
      "lastName": "Doe",
      "phone": "+012 (34) 567-89-10"
    }
  ]
}

</pre>
</li>
<li>
Response: <br>
<pre>
{
  "id": 2,
  "name": "New Project",
  "code": "np",
  "url": "http://example.com/new-page",
  "budget": 100,
  "contacts": [
    {
      "id": 2,
      "firstName": "Jane",
      "lastName": "Doe",
      "phone": "+012 (34) 567-89-10"
    }
  ]
}

</pre>
</li>
</ul>
</li>
<li>Update:</li>
<li>
<ul>
<li>Method: PATCH</li>
<li>URI: /projects/1</li>
<li>
Request: <br>
<pre>
{
  "name": "New Name"
}
</pre>
</li>
<li>
Response: <br>
<pre>
{
  "id": 1,
  "name": "New Name",
  "code": "project",
  "url": "http://example.com/my-page",
  "budget": 100,
  "contacts": [
    {
      "id": 1,
      "firstName": "John",
      "lastName": "Doe",
      "phone": "+012 (34) 567-89-10"
    }
  ]
}

</pre>
</li>
</ul>
</li>
<li>Delete:</li>
<li>
<ul>
<li>Method: DELETE</li>
<li>URI: /projects/1</li>
</ul>
</li>
</ul>
</li>
</ul>

<h3>Requirements</h3>

<ul>
<li>Authentication by secret key in URL;</li>
<li>PHP 7.*;</li>
<li>Symfony >= 4 (preferable) or plain PHP/any other framework;</li>
<li>any RDBMS;</li>
<li>add some unit tests to the application (no need to cover all, only show the principles).</li>
</ul>