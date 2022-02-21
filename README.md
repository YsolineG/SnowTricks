# SnowTricks

SnowTricks is a Symfony project for my Openclassrooms training.

## Technologies

-   PHP (Symfony framework)
-   [Twig](https://twig.symftwigony.com/) - Template engine for PHP
-   JavaScript
-   CSS (Bootstrap)

## Prerequisite

- PHP 8.0 minimum
- MySQL 5.7
- Composer

## Installation

1.  Clone the repository : `git clone git@github.com:YsolineG/SnowTricks.git`


2.  Install composer packages : `composer install`


3.  Duplicate file `.env` to `.env.local`


4.  Configure the SQL DATABASE_URL connection string so that it can communicate with your local SQL database


4.  Create a database for the project : `php bin/console doctrine:schema:create`


5.  Run Doctrine migrations : `php bin/console doctrine:migrations:migrate`


6.   To start the project : `symfony server:start`