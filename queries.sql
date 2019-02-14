--  Выбираем БД
USE things_fine;

-- Заполняем таблицу users данными 2 пользователей

INSERT INTO `users`(`date_reg`, `email`, `name`, `pwd`) VALUES ('2017-01-01','ivan@mail.ru','Ivan','111');
INSERT INTO `users`(`date_reg`, `email`, `name`, `pwd`) VALUES ('2017-02-02','petr@mail.ru','Petr','222');  
INSERT INTO `users`(`date_reg`, `email`, `name`, `pwd`) VALUES ('2017-03-03','konst@mail.ru','Константин','333');

-- Заполняем таблицу projects, закрепляем за проектами пользователей по user_id

INSERT INTO `projects`(`name`) VALUES ('Входящие');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'ivan@mail.ru') WHERE projects.name = 'Входящие' and projects.user_id IS NULL;

INSERT INTO `projects`(`name`) VALUES ('Учеба');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'ivan@mail.ru') WHERE projects.name = 'Учеба' and projects.user_id IS NULL;

INSERT INTO `projects`(`name`) VALUES ('Работа');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'ivan@mail.ru') WHERE projects.name = 'Работа' and projects.user_id IS NULL;

INSERT INTO `projects`(`name`) VALUES ('Домашние дела');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'petr@mail.ru') WHERE projects.name = 'Домашние дела' and projects.user_id IS NULL;

INSERT INTO `projects`(`name`) VALUES ('Авто');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'petr@mail.ru') WHERE projects.name = 'Авто' and projects.user_id IS NULL;

INSERT INTO `projects`(`name`) VALUES ('Авто');
UPDATE `projects` SET `user_id`=(SELECT id FROM users WHERE users.email = 'konst@mail.ru') WHERE projects.name = 'Авто' and projects.user_id IS NULL;



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

INSERT INTO `tasks`(`date_created`, `done`, `name`) VALUES ('2018-12-01', 0, 'Отвезти машину на СТО');
UPDATE `tasks` SET `project_id`=(SELECT id FROM projects WHERE projects.name='Авто') WHERE tasks.name='Авто';
UPDATE `tasks` SET `user_id`= (SELECT id FROM users WHERE users.email='konst@mail.ru') WHERE tasks.name='Отвезти машину на СТО';  

     
-- Получаем список проектов для одного пользователя
SELECT users.name, projects.name FROM projects INNER JOIN users ON (projects.user_id = users.id) and (users.email='petr@mail.ru');

-- Получаем список всех задач для одного проекта
SELECT projects.name, tasks.name FROM tasks INNER JOIN projects ON (tasks.project_id = projects.id) and (projects.name = 'Работа');

-- Помечаем задачу как выделенную 
UPDATE tasks SET tasks.done = 1 WHERE (tasks.name = 'Выполнить тестовое задание') and (tasks.date_planned = '2019-12-25');

-- Обновление названия задачи по индетификатору
UPDATE tasks SET name = "Собеседование в IT-компании Microsoft" WHERE id = 1;

