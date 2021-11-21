### Back End(Laravel/Lumen) Bike Shop Inventory (For Educational Purpose)
This Is API bike shop inventory (CRUD). It has several features like login, register, logout, edit user, create product, edit product, delete product, show all list product, and edit quantity. Especially for edit quantity, you increase quantity products as supplier and decrease quantity products as buyer. You also can see Inventory quantity flow buyer and supplier.

### Programming Language
- Laravel/Lumen (7.0)

### First Config
- Install Laragon
- Download/pull this project to C:\laragon\www
- Open this project with cd BikeShopInventoryLumen
- Install composer
- Run composer install
- Download phpmyadmin and extract
- copy folder phpmyadmin to C:\laragon\etc\apps and rename it as phpMyAdmin
- Start Laragon
- Create new Database in http://localhost/phpmyadmin/
- Migrate Database with php artisan migrate
- Copy file .env.example and rename it to .env
- Edit value DB_DATABASE, DB_USERNAME, DB_PASSWORD

### Run Development Server
Make sure different PORT with Front End and same port with API_URL in .env frontend
php -S localhost:PORT -t public 

### Run In Broswer
http://localhost:PORT/

### Author 
Firbert Oktariko 

### Big Thanks 
I am using Template HTML this github project.
https://github.com/davidtmiller
https://github.com/StartBootstrap/startbootstrap-sb-admin-2 

### Hopefully this project can help people to understand basic CRUD using Back End(Laravel/Lumen)
