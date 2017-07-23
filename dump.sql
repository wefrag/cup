USE `wefrag_cup`;

CREATE TABLE `config` (
  `name` VARCHAR(255),
  `description` VARCHAR(255),
  `backstory` TEXT,
  `rules` TEXT,
  `admin` VARCHAR(255),
  `forum` VARCHAR(255),
  `name_team_first` VARCHAR(255),
  `name_team_second` VARCHAR(255),
  CONSTRAINT id PRIMARY KEY (id)
) DEFAULT CHARSET=utf8mb4;

INSERT INTO `config` (
    'WeFrag Cup 2012',
    'Description ...',
    'Backstory ...',
    'Rules ...',
    'MxR',
    'https://forum.nofrag.com/forums/jouer/topics/1078518',
    'BNNF',
    'CCCM'
);

CREATE TABLE `admin` (
    `id` INT,
    `name` VARCHAR(255),
    CONSTRAINT id PRIMARY KEY (id)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE `game` (
    `id` INT,
    `name` VARCHAR(255),
    CONSTRAINT id PRIMARY KEY (id)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE `play` (
    `id` INT,
    `id_member` INT,
    `id_game` INT,
    CONSTRAINT id PRIMARY KEY (id)
);

CREATE TABLE `planet` (
    `id` INT,
    `id_game` INT,
    `name` VARCHAR(255),
    `team` TINYINT DEFAULT NULL,
    `level` TINYINT,
    CONSTRAINT id PRIMARY KEY (id)
) DEFAULT CHARSET=utf8mb4;
