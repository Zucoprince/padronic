# padronic

Padronic

To install:



composer require zucoprince/padronic



Developed by: Pedro Zucolo

Library for: Laravel (Compatible with versions from 7.x ^)

What is Padronic?

Padronic is a library designed for the Laravel framework, aimed at simplifying the creation of essential files in Laravel projects. These files are commonly needed for operations such as database creation, models, controllers, and routes, among others.

Key Features:

Automatic File Creation: With just one command in the terminal, Padronic automatically creates the following files:

- Migration
- Model
- Controller
- Resource
- Request
- Route
- Repository

Laravel CRUD Pattern: Some files are generated with the Laravel CRUD pattern, providing an organized and consistent structure.
Automatic Trait (Version 0.0.3+): Starting from version 0.0.3, Padronic also creates a Trait file automatically. This file contains two functions with scripts to return CRUD function responses in a standardized JSON format.


Important Notes:

- Route Structure Modification: The library modifies the default API route consumption pattern. It alters the routes/api.php file so that routes are read from within the routes/api folder, which is created automatically by the library. In the future, an option will be implemented for users to choose whether or not to modify the route structure.

- Existing File Check: The script checks if the requested files already exist in the project. If any of the files already exist, the script will ask if you want to overwrite them.

- Be Careful When Deleting Files: The command to delete files does not request confirmation. It removes all files from the CRUD structure with the provided name. Therefore, use it with caution.


How to Use:

After installing the package, you will have access to new commands.



Command to Create Files:

The command to create files accepts one or more parameters, which are the names of the desired classes or models. Parameters should be passed with the first letter capitalized and not in plural form. For example:



php artisan make:all Drink

You can also pass multiple parameters separated by space:

php artisan make:all Drink Food



Command to Delete Files:

The command to delete files removes all files related to the provided models. For example, to delete files from the Drink model:



php artisan app:rmall Drink

To delete files from multiple models:

php artisan app:rmall Drink Food

This will remove all files related to the Drink and Food models.




With Padronic, creating and managing files in Laravel projects becomes more efficient and standardized.
