# Filament V3 Map Picker

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]][link-license]


A custom field for Filament that allows you to effortlessly select a location on a map and retrieve geographical coordinates.


![270298161-46b97f72-518b-40c5-963b-8e9d39d77d67](https://github.com/dotswan/map-picker/assets/20874565/a5dbda7b-b5c1-4038-9bf9-7a0a4c8ff632)


## Introduction 

Map Picker is a Filament custom field designed to simplify the process of choosing a location on a map and obtaining its geo-coordinates.

* Features include: 
   * A Field for Filament-v3 with OpenStreetMap Integration
   * Receive Real-time Coordinates Upon Marker Movement Completion
   * Tailor Controls and Marker Appearance to Your Preferences
* Latest versions of PHP and Filament
* Best practices applied:
  * [`README.md`][link-readme] (badges included)
  * [`LICENSE`][link-license]
  * [`composer.json`][link-composer-json]
  * [`.gitignore`][link-gitignore]
  * [`pint.json`][link-pint]

## Supported Maps

Map Picker currently supports the following map:

1. Open Street Map (OSM)

Additional map options will be added to the package as needed and tested.

## Installation

You can easily install the package via Composer:

```bash
composer require dotswan/map-picker
```

## Basic Usage

Resource file:

```php
<?php
namespace App\Filament\Resources;
use Filament\Resources\Resource;
use Filament\Resources\Forms\Form;
use Dotswan\MapPicker\Fields\Map;
...

class FilamentResource extends Resource
{
    ...
    public static function form(Form $form)
    {
        return $form->schema([
            Map::make('location')
                    ->label('Location')
                    ->columnSpanFull()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?array $state): void {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    })
                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                        $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                    })
                    ->liveLocation()
                    ->showMarker()
                    ->markerColor("#22c55eff")
                    ->showFullscreenControl()
                    ->showZoomControl()
                    ->draggable()
                    ->tilesUrl("http://tile.openstreetmap.de/{z}/{x}/{y}.png")
                    ->zoom(15)
                    ->extraTileControl([])
                    ->extraControl([
                        'zoomDelta'           => 1,
                        'zoomSnap'            => 2,
                    ])
           ]);
    }
    ...
}
```

## License

[MIT License](LICENSE.md) © Dotswan

## Security

We take security seriously. If you discover any bugs or security issues, please help us maintain a secure project by reporting them through our [`GitHub issue tracker`][link-github-issue]. You can also contact us directly at [tech@dotswan.com](mailto:tech@dotswan.com).

## Contribution

We welcome contributions! contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


[ico-version]: https://img.shields.io/packagist/v/dotswan/map-picker.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dotswan/map-picker.svg?style=flat-square

[link-workflow-test]: https://github.com/dotswan/map-picker/actions/workflows/ci.yml
[link-packagist]: https://packagist.org/packages/dotswan/map-picker
[link-license]: https://github.com/dotswan/map-picker/blob/master/LICENSE.md
[link-downloads]: https://packagist.org/packages/dotswan/map-picker
[link-readme]: https://github.com/dotswan/map-picker/blob/master/README.md
[link-github-issue]: https://github.com/dotswan/map-picker/issues
[link-docs]: https://github.com/dotswan/map-picker/blob/master/docs/openapi.yaml
[link-composer-json]: https://github.com/dotswan/map-picker/blob/master/composer.json
[link-gitignore]: https://github.com/dotswan/map-picker/blob/master/.gitignore
[link-pint]: https://github.com/dotswan/map-picker/blob/master/pint.json
[link-author]: https://github.com/dotswan
