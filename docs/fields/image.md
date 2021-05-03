# Image
The image field can be used to display an image inside a column. You can display an image by src or by your routings. If you use the package [laravel filemanager]([https://github.com/singlequote/laravel-filemanager](https://github.com/singlequote/laravel-filemanager)) you can call your images by route.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Display image by route
Use the laravel routing to display an image. The paramaters will be auto formatted to the results value.
If a parameter does not exists it will be left untouched.

```php
Image::make('...')->route('my-image-route', optional 'id')

// <img src="http://www.example.com/my-image-route/1">

Image::make('...')->route('my-image-route', optional [100, 100, 'id'])

// <img src="http://www.example.com/my-image-route/100/100/1">
```

### Display image by src
Display image by hardcoded src.

```php
Image::make('...')->src(`https://picsum.photos/200/300`)

// <img src="https://picsum.photos/200/300">

```