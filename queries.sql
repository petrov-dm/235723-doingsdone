
-- Заполняем таблицу users данными 2 пользователей

INSERT INTO `users`(`date_reg`, `email`, `name`, `pwd`) VALUES ('2017-01-01','ivan@mail.ru','Ivan','111');
INSERT INTO `users`(`date_reg`, `email`, `name`, `pwd`) VALUES ('2017-02-02','petr@mail.ru','Petr','222');  

-- Заполняем таблицу projects, закрепляем за проектами пользователей по user_id

INSERT INTO `projects`(`name`) VALUES ('Входящие');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'ivan@mail.ru') WHERE projects.name = 'Входящие';

INSERT INTO `projects`(`name`) VALUES ('Учеба');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'ivan@mail.ru') WHERE projects.name = 'Учеба';

INSERT INTO `projects`(`name`) VALUES ('Работа');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'ivan@mail.ru') WHERE projects.name = 'Работа';

INSERT INTO `projects`(`name`) VALUES ('Домашние дела');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'petr@mail.ru') WHERE projects.name = 'Домашние дела';

INSERT INTO `projects`(`name`) VALUES ('Авто');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'petr@mail.ru') WHERE projects.name = 'Авто';

-- Заполняем таблицу tasks, закрепляем за задачей проект project_id и пользователя user_id

INSERT INTO `tasks`(`date_created`, `done`, `name`, `date_planned`) VALUES ('2018-01-01', 0, 'Собеседование в IT компании', '2019-12-01');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Работа') WHERE tasks.name='Собеседование в IT компании';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='ivan@mail.ru') WHERE tasks.name='Собеседование в IT компании';

INSERT INTO `tasks`(`date_created`, `done`, `name`, `date_planned`) VALUES ('2018-02-01', 0, 'Выполнить тестовое задание', '2019-12-25');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Работа') WHERE tasks.name='Выполнить тестовое задание';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='ivan@mail.ru') WHERE tasks.name='Выполнить тестовое задание';

INSERT INTO `tasks`(`date_created`, `done`, `name`, `date_planned`) VALUES ('2018-03-01', 1, 'Сделать задание первого раздела', '2019-12-21');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Учеба') WHERE tasks.name='Сделать задание первого раздела';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='ivan@mail.ru') WHERE tasks.name='Сделать задание первого раздела';    
    
INSERT INTO `tasks`(`date_created`, `done`, `name`, `date_planned`) VALUES ('2018-04-01', 0, 'Встреча с другом', '2019-12-22');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Входящие') WHERE tasks.name='Встреча с другом';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='ivan@mail.ru') WHERE tasks.name='Встреча с другом';     


INSERT INTO `tasks`(`date_created`, `done`, `name`) VALUES ('2018-05-01', 0, 'Купить корм для кота');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Домашние дела') WHERE tasks.name='Купить корм для кота';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='petr@mail.ru') WHERE tasks.name='Купить корм для кота';         
    
INSERT INTO `tasks`(`date_created`, `done`, `name`) VALUES ('2018-06-01', 0, 'Заказать пиццу');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Домашние дела') WHERE tasks.name='Заказать пиццу';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='petr@mail.ru') WHERE tasks.name='Заказать пиццу';         
    

