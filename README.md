1. git clone https://github.com/MyskinD/api.loc.git .
2. go to the project folder & run composer
2. configure the virtual host on the folder web (/web)
3. configure access to the database (/config/db.php)
4. create tables for databases (in yii2 basic no migration mechanism)

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

5. for test api use Postman program