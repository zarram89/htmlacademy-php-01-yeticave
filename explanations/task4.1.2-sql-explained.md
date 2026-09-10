Давай. Следующий шаг — **поднять отдельный MySQL для YetiCave в Docker**, не трогая твой установленный MySQL 8.0 на Windows.

### 1. Создай `compose.yaml`

В корне:

`D:\htmlacademy\htmlacademy-php-01-yeticave\compose.yaml`

```yaml
services:
  db:
    image: mysql:8.0
    container_name: yeticave-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: yeticave
    ports:
      - "3307:3306"
    volumes:
      - yeticave_mysql_data:/var/lib/mysql

volumes:
  yeticave_mysql_data:
```

Здесь главное понять:

```text
Windows
   │
   │ localhost:3307
   ▼
Docker
   │
   ▼
MySQL :3306
```

То есть:

* **3306** внутри контейнера — стандартный порт MySQL;
* **3307** на Windows — порт, через который мы обращаемся к этому контейнеру;
* твой старый MySQL продолжает занимать **3306**;
* поэтому они друг другу не мешают.

---

### 2. Запусти контейнер

В терминале PhpStorm:

```bash
docker compose up -d
```

Потом:

```bash
docker compose ps
```

Должно быть примерно:

```text
NAME              IMAGE       PORTS
yeticave-mysql    mysql:8.0   0.0.0.0:3307->3306/tcp
```

Если статус `Up` — MySQL работает.

---

### 3. Подключимся к нему

Можно прямо из терминала:

```bash
mysql -h 127.0.0.1 -P 3307 -u root -p
```

Пароль:

```text
root
```

Обрати внимание на отличие от твоего глобального MySQL:

```text
глобальный MySQL
127.0.0.1:3306

YetiCave MySQL
127.0.0.1:3307
```

---

### 4. Теперь загружаем твой `schema.sql`

Мы **не изменяем его**.

Находясь в корне проекта:

```bash
mysql -h 127.0.0.1 -P 3307 -u root -p < schema.sql
```

Вводишь:

```text
root
```

После этого схема должна создать:

```text
yeticave
├── categories
├── users
├── lots
└── bets
```

Проверить:

```bash
mysql -h 127.0.0.1 -P 3307 -u root -p
```

и внутри MySQL:

```sql
SHOW DATABASES;
```

затем:

```sql
USE yeticave;
SHOW TABLES;
```

Получишь:

```text
+-------------------+
| Tables_in_yeticave|
+-------------------+
| bets              |
| categories        |
| lots              |
| users             |
+-------------------+
```

---

### 5. А дальше — PhpStorm

И вот здесь Docker становится особенно удобным.

В PhpStorm можно подключить:

```text
Host:     127.0.0.1
Port:     3307
User:     root
Password: root
Database: yeticave
```

То есть PhpStorm будет работать с **тем же MySQL-контейнером**, а не с установленным в Windows MySQL.

Получается нормальная современная схема:

```text
PhpStorm
   │
   │ SQL / Database tool
   ▼
127.0.0.1:3307
   │
   ▼
Docker container
yeticave-mysql
   │
   ▼
MySQL 8.0
   │
   ▼
yeticave
 ├── users
 ├── categories
 ├── lots
 └── bets
```

**phpMyAdmin здесь пока вообще не нужен.** PhpStorm уже умеет быть GUI для базы. Если позже захочешь именно phpMyAdmin — добавим его вторым контейнером, не меняя саму БД.

### Сейчас сделай только это

```bash
docker compose up -d
docker compose ps
```

И пришли вывод `docker compose ps`. Дальше проверим контейнер и уже спокойно подключим к нему `schema.sql`.


#######################

Отлично — **контейнер MySQL запущен и порт проброшен правильно**. ✅

Вот эта строка:

```text
0.0.0.0:3307->3306/tcp
```

означает:

```text
Windows: 127.0.0.1:3307
          │
          ▼
Docker:   MySQL :3306
```

Теперь следующий шаг — **загрузить твой существующий `schema.sql`**, не изменяя его.

Из корня проекта выполни:

```bash
mysql -h 127.0.0.1 -P 3307 -u root -p < schema.sql
```

Когда спросит пароль:

```text
root
```

Если команда завершится без ошибок — схема создана.

После этого сразу проверим:

```bash
mysql -h 127.0.0.1 -P 3307 -u root -p
```

и внутри:

```sql
SHOW DATABASES;
```

затем:

```sql
USE yeticave;
SHOW TABLES;
```

Должны появиться:

```text
bets
categories
lots
users
```

**Пока не подключай PhpStorm и не добавляй phpMyAdmin** — сначала убедимся, что сам `schema.sql` нормально применился к контейнерному MySQL.


###############

Конечно. Подключим **именно Docker-базу `yeticave`**, которая слушает `127.0.0.1:3307`.

### 1. Открой Database Tool

В PhpStorm справа найди вкладку:

**Database**

Если её нет:

**View → Tool Windows → Database**

---

### 2. Создай подключение

В окне Database нажми:

**`+` → Data Source → MySQL**

Откроется форма подключения.

Заполни:

| Поле         | Значение    |
| ------------ | ----------- |
| **Host**     | `127.0.0.1` |
| **Port**     | `3307`      |
| **User**     | `root`      |
| **Password** | `root`      |
| **Database** | `yeticave`  |

То есть главное — **порт `3307`**, а не стандартный `3306`.

---

### 3. Driver

PhpStorm, скорее всего, покажет:

> **Download missing driver files**

Нажми **Download**.

Это просто JDBC-драйвер, который PhpStorm использует для подключения к MySQL.

---

### 4. Проверь соединение

Нажми:

**Test Connection**

Должно появиться что-то вроде:

> **Successful**

После этого нажми **OK** / **Apply**.

---

### 5. Что ты должен увидеть

В Database появится источник примерно такого вида:

```text
localhost:3307
└── yeticave
    ├── Tables
    │   ├── bets
    │   ├── categories
    │   ├── lots
    │   └── users
    └── ...
```

Можно раскрыть, например:

```text
lots
```

и посмотреть:

* Columns
* Keys
* Foreign Keys
* Indexes

Это особенно полезно сейчас, потому что ты как раз изучаешь, **как SQL-схема превращается в реальную структуру БД**.

### Важный момент

Не выбирай:

```text
Port: 3306
```

Это будет твой **старый глобальный MySQL Windows**.

Для YetiCave:

```text
Host:     127.0.0.1
Port:     3307
Database: yeticave
User:     root
Password: root
```

После подключения можем сделать следующий очень полезный шаг: **в PhpStorm открыть каждую из 4 таблиц и разобрать, как именно `PRIMARY KEY`, `FOREIGN KEY` и связи из твоего `schema.sql` отображаются в Database Tool.**


##### PHPStorm Database

Да, ты правильно понял. Но я бы **не считал это задание полностью законченным только потому, что таблицы появились**. Сейчас у тебя хороший момент, чтобы один раз нормально разобраться с Database Tool, потому что дальше он будет постоянно встречаться.

Я бы не пытался изучить весь PhpStorm Database Tool. Для YetiCave тебе сейчас достаточно освоить **6 вещей**.

### 1. Сначала посмотреть структуру таблиц

Открой:

```text
yeticave
└── Tables
    ├── users
    ├── categories
    ├── lots
    └── bets
```

На каждой таблице раскрой **Columns**.

Например, `lots`:

```text
id
date_creation
title
lot_description
img
start_price
date_finish
step
user_id
winner_id
category_id
```

И попробуй сопоставить это с:

```sql
CREATE TABLE lots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ...
    user_id INT,
    winner_id INT,
    category_id INT,
    FOREIGN KEY (...)
);
```

Вот здесь начинает формироваться главное понимание:

> **SQL-код создаёт структуру, а Database Tool показывает уже созданную структуру базы.**

---

### 2. Посмотреть Primary Key и Foreign Key

Это я бы обязательно сделал.

У `lots` найди **Keys / Foreign Keys**.

Там ты должен увидеть примерно такие связи:

```text
lots.user_id
    ↓
users.id

lots.winner_id
    ↓
users.id

lots.category_id
    ↓
categories.id
```

А у `bets`:

```text
bets.user_id
    ↓
users.id

bets.lot_id
    ↓
lots.id
```

И вот это очень важная вещь:

**`lots.user_id` и `lots.winner_id` — два разных поля, но оба ссылаются на одну таблицу `users`.**

То есть база фактически знает:

```text
Пользователь
   │
   ├── создал → Лот
   │
   └── выиграл → Лот
```

---

### 3. Посмотреть данные, а не только структуру

Двойной клик по таблице `users`.

PhpStorm покажет табличное представление:

```text
id | date_registration | email | user_name | ...
```

То же самое для:

```text
categories
lots
bets
```

Сейчас таблицы, скорее всего, пустые.

Это нормально.

Но попробуй **сам выполнить INSERT через SQL Console**.

Например:

```sql
INSERT INTO categories (character_code, name_category)
VALUES ('boards', 'Доски');
```

Потом:

```sql
SELECT * FROM categories;
```

И ты увидишь созданную запись непосредственно в Database Tool.

Это очень полезное упражнение: ты увидишь полный путь:

```text
SQL
 ↓
INSERT
 ↓
MySQL
 ↓
таблица categories
 ↓
Database Tool показывает результат
```

---

### 4. Научиться пользоваться SQL Console

Вот это, пожалуй, **самая полезная часть Database Tool**.

На базе нажимаешь:

**Right click → New → Query Console**

И можешь писать:

```sql
SELECT * FROM users;
```

или:

```sql
SELECT *
FROM lots;
```

или:

```sql
SELECT *
FROM lots
WHERE start_price > 1000;
```

То есть Database Tool для тебя — это не только «красивый просмотр таблиц».

Фактически это:

> **терминал для работы с SQL + GUI для просмотра структуры и данных.**

---

### 5. Очень рекомендую посмотреть Diagram

Вот это как раз сильно поможет тебе **понять связи визуально**.

Выдели:

```text
categories
users
lots
bets
```

и выбери что-то вроде:

**Diagrams → Show Visualization**

PhpStorm построит схему примерно:

```text
              ┌──────────────┐
              │    users     │
              │──────────────│
              │ id           │
              │ email        │
              │ ...          │
              └──────┬───────┘
                     │
             ┌───────┴────────┐
             │                │
          user_id          winner_id
             │                │
             ▼                ▼
          ┌─────────────────────┐
          │        lots         │
          │─────────────────────│
          │ id                  │
          │ title               │
          │ category_id         │
          │ user_id             │
          │ winner_id           │
          └──────────┬──────────┘
                     │ lot_id
                     ▼
               ┌───────────┐
               │   bets    │
               │───────────│
               │ id        │
               │ user_id   │
               │ lot_id    │
               │ price_bet │
               └───────────┘

          categories
               │
          category_id
               │
               ▼
              lots
```

Для первого знакомства с реляционными БД это **очень полезная визуализация**.

---

### 6. И ещё одна вещь — попробовать `DESCRIBE`

В Query Console:

```sql
DESCRIBE users;
```

Потом:

```sql
DESCRIBE lots;
```

MySQL покажет:

```text
Field
Type
Null
Key
Default
Extra
```

Например:

```text
id
INT
NO
PRI
NULL
auto_increment
```

И здесь ты увидишь уже непосредственно то, что означает объявление:

```sql
id INT AUTO_INCREMENT PRIMARY KEY
```

То есть:

```text
INT              → тип данных
PRIMARY KEY      → PRI
AUTO_INCREMENT   → auto_increment
```

---

## А что из всего остального пока НЕ надо изучать

В Database Tool огромное количество возможностей:

* DataGrip-функции
* миграции
* introspection
* stored procedures
* triggers
* explain plan
* indexes
* экспорт/импорт
* генерация SQL
* рефакторинг
* SSH tunnels
* Docker-интеграция
* разные схемы и datasource
* и т.д.

**Сейчас тебе это вообще не нужно.**

Для текущего курса я бы сформировал такой минимальный набор:

```text
DATABASE TOOL
│
├── 1. Tables
│      └── понимать структуру таблиц
│
├── 2. Columns
│      └── поле + тип
│
├── 3. Primary Key
│      └── идентификатор записи
│
├── 4. Foreign Key
│      └── связь между таблицами
│
├── 5. Data
│      └── реальные записи
│
├── 6. SQL Console
│      └── писать SQL
│
└── 7. Diagram
       └── видеть связи целиком
```

**Вот эти семь вещей я бы сейчас реально потрогал руками.**

И особенно важный момент: не надо просто смотреть на интерфейс. Сделай маленький эксперимент:

1. посмотреть `users`;
2. посмотреть `lots`;
3. открыть Foreign Keys;
4. построить Diagram;
5. через Console сделать `INSERT` одной категории;
6. сделать `SELECT`;
7. открыть `categories` и увидеть эту запись через GUI;
8. удалить её через SQL.

После этого Database Tool перестанет выглядеть как огромная непонятная панель, потому что ты поймёшь его основную модель:

> **Database Tool не является самой базой. Это интерфейс PhpStorm для подключения к MySQL: смотреть структуру, данные и отправлять SQL-команды.**

И именно **SQL остаётся главным**. GUI лишь делает работу с БД удобнее.
