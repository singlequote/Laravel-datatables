# Table Models
Table models are the controllers for your tables. Inside this file you can configure the behaviour of the tables

## Create
You can use the `artisan` command to generate a new `TableModel`. Where `Users` is the name of the table model.
```command
php artisan make:table-model Users
```

This command also has some options you can use to easily create a new table model.

| Option | Description | Demo |
| ------------- | ------------- | ------------- |
| --route={value} | Set the route for the 3 demo buttons | `php artisan make:table-model Name --route=my-route` |
| --class={value} | Set the table class | `php artisan make:table-model Name --class=table` |
| --buttons | Set a default amount of buttons (show, edit, destroy) | `php artisan make:table-model Name --buttons` |
| --translations | Set a translations method | `php artisan make:table-model Name --translations` | 
