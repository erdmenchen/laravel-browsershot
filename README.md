<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400">
</p>

Save screenshots of a given set of websites with an Artisan CLI command. 

E.g. can be used to record screenshots of some dashboards on a daily base and combine them to an animated gif in order to show change over time.

## Requirements
Enable PHP extensions in php.ini:
* extension=gd2
* extension=exif

## Usage
Laravel based tool for taking snapshots of any url provided in config file `snapshot.json`. 

Execute `php artisan snapshot:take` for generating images and saving them to the `output` directory.

Sample of config file `snapshot.json`:
```
{
    "Title1": "https://url1.link",
    "Title2": "https://url2.link",
    ...
}
```
Images are saved to the `output` folder with their url as file names and the provided title as prefix (e.g. for pattern matching).

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
