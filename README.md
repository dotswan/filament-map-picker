# Filament V3 Map Picker

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]][link-license]


A custom field for Filament that allows you to effortlessly select a location on a map and retrieve geographical coordinates.


![270298161-46b97f72-518b-40c5-963b-8e9d39d77d67](https://github.com/dotswan/map-picker/assets/20874565/a5dbda7b-b5c1-4038-9bf9-7a0a4c8ff632)

![image](https://github.com/user-attachments/assets/53d9de27-7e1f-4638-8c71-3b2c6b5d68ef)

## Introduction

Map Picker is a Filament custom field designed to simplify the process of choosing a location on a map and obtaining its geo-coordinates.

* Features include:
   * A Field for Filament-v3 with OpenStreetMap Integration
   * Receive Real-time Coordinates Upon Marker Movement Completion
   * Tailor Controls and Marker Appearance to Your Preferences
   * GeoMan Integration for Advanced Map Editing Capabilities

* Latest versions of PHP and Filament
* Best practices applied:
  * [`README.md`][link-readme] (badges included)
  * [`LICENSE`][link-license]
  * [`composer.json`][link-composer-json]
  * [`.gitignore`][link-gitignore]
  * [`pint.json`][link-pint]


## GeoMan Integration

This package now includes integration with GeoMan, a powerful tool for creating and editing geometries on maps. GeoMan allows users to draw various shapes, edit existing geometries, and perform advanced map editing tasks.

### GeoMan Features:

- Draw markers, polygons, polylines, and circles
- Edit existing geometries
- Cut polygons
- Rotate shapes
- Drag mode for easy shape manipulation
- Delete layers

## Supported Maps

Map Picker currently supports the following map:

1. Open Street Map (OSM)

Additional map options will be added to the package as needed and tested.

## Installation

You can easily install the package via Composer:

```bash
composer require dotswan/filament-map-picker
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
                        ->defaultLocation(latitude: 40.4168, longitude: -3.7038)
                        ->afterStateUpdated(function (Set $set, ?array $state): void {
                            $set('latitude',  $state['lat']);
                            $set('longitude', $state['lng']);
                            $set('geojson',   json_encode($state['geojson']));
                        })
                        ->afterStateHydrated(function ($state, $record, Set $set): void {
                            $set('location', [
                                    'lat'     => $record->latitude,
                                    'lng'     => $record->longitude,
                                    'geojson' => json_decode(strip_tags($record->description))
                                ]
                            );
                        })
                        ->extraStyles([
                            'min-height: 150vh',
                            'border-radius: 50px'
                        ])
                        ->liveLocation(true, true, 5000)
                        ->showMarker()
                        ->markerColor("#22c55eff")
                        ->showFullscreenControl()
                        ->showZoomControl()
                        ->draggable()
                        ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                        ->zoom(15)
                        ->detectRetina()
                        ->showMyLocationButton()
                        ->geoMan(true)
                        ->geoManEditable(true)
                        ->geoManPosition('topleft')
                        ->drawCircleMarker()
                        ->rotateMode()
                        ->clickable() //click to move marker
                        ->drawMarker()
                        ->drawPolygon()
                        ->drawPolyline()
                        ->drawCircle()
                        ->dragMode()
                        ->cutPolygon()
                        ->editPolygon()
                        ->deleteLayer()
                        ->setColor('#3388ff')
                        ->setFilledColor('#cad9ec')
           ]);
    }
    ...
}
```

If you wish to update the map location and marker either through an action or after altering other input values, you can trigger a refresh of the map using the following approach:

```php

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\VerticalAlignment;

Actions::make([
    Action::make('Set Default Location')
        ->icon('heroicon-m-map-pin')
        ->action(function (Set $set, $state, $livewire): void {
            $set('location', ['lat' => '52.35510989541003', 'lng' => '4.883422851562501']);
            $set('latitude', '52.35510989541003');
            $set('longitude', '4.883422851562501');
            $livewire->dispatch('refreshMap');
        })
])->verticalAlignment(VerticalAlignment::Start);

```

### clickable Option

This will allow you to set the point on the map with a click. Default behaviour has the marker centered as the map is
dragged underneath. You could, with this, keep the map still and lock the zoom and choose to click to place the marker.
```php
```
Map::make('location')
  ->defaultLocation(latitude: 40.4168, longitude: -3.7038)
  ->showMarker(true)
  ->clickable(true)
  ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
  ->zoom(12)
```
```

### rangeSelectField Option

The rangeSelectField Option allows you to specify another field on your form which specifies a range from the point
identified by the marker.  That field must be in meters. So for example you could do this:

```php
Fieldset::make('Location')
    ->schema([
        Select::make('membership_distance')
            ->enum(MembershipDistance::class)
            ->options(MembershipDistance::class)
            ->required(),

        Map::make('location')
            ->defaultLocation(latitude: 40.4168, longitude: -3.7038)
            ->showMarker(true)
            ->showFullscreenControl(false)
            ->showZoomControl()
            ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
            ->zoom(12)
            ->detectRetina()
            ->rangeSelectField('membership_distance')
            ->setFilledColor('#cad9ec'),
    ])
    ->columns(1),
```

In this case, as you change the value on the Select a circle of that radius centered on the marker will
change to match your drop down.


#### `liveLocation` Option

The `liveLocation` method accepts three parameters:

1. **`bool $send`:** Determines if the user's live location should be sent.
2. **`bool $realtime`:** Controls whether the live location should be sent to the server periodically.
3. **`int $milliseconds`:** Sets the interval (in milliseconds) at which the user's location is updated and sent to the server.

Example:

```php
Map::make('location')
    ->liveLocation(true, true, 10000)  // Updates live location every 10 seconds
    ->showMarker()
    ->draggable()
```

## Options Table

Here's a table describing all available options and their default values:

| Option | Description | Default Value |
|--------|-------------|---------------|
| draggable | Allow map dragging | true |
| showMarker | Display marker on the map | true |
| tilesUrl | URL for map tiles | 'http://tile.openstreetmap.org/{z}/{x}/{y}.png' |
| attribution | Map attribution text | null |
| zoomOffset | Zoom offset | -1 |
| tileSize | Tile size | 512 |
| detectRetina | Detect and use retina tiles | true |
| minZoom | Minimum zoom level | 0 |
| maxZoom | Maximum zoom level | 28 |
| zoom | Default zoom level | 15 |
| markerColor | Color of the marker | '#3b82f6' |
| liveLocation | Enable live location updates | [false, false, 5000] |
| showMyLocationButton | Show "My Location" button | false |
| default | Default location | ['lat' => 0, 'lng' => 0] |
| geoMan.show | Enable GeoMan | false |
| geoMan.editable | Allow editing with GeoMan | true |
| geoMan.position | Position of GeoMan controls | 'topleft' |
| geoMan.drawCircleMarker | Allow drawing circle markers | true |
| geoMan.rotateMode | Enable rotate mode | true |
| geoMan.drawMarker | Allow drawing markers | true |
| geoMan.drawPolygon | Allow drawing polygons | true |
| geoMan.drawPolyline | Allow drawing polylines | true |
| geoMan.drawCircle | Allow drawing circles | true |
| geoMan.dragMode | Enable drag mode | true |
| geoMan.cutPolygon | Allow cutting polygons | true |
| geoMan.editPolygon | Allow editing polygons | true |
| geoMan.deleteLayer | Allow deleting layers | true |
| geoMan.color | Stroke color for drawings | '#3388ff' |
| geoMan.filledColor | Fill color for drawings | '#cad9ec' |

### Usage As Infolist Field

The MapEntry Infolist field displays a map.

```php

use Dotswan\MapPicker\Infolists\MapEntry;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                MapEntry::make('location')
                    ->extraStyles([
                        'min-height: 50vh',
                        'border-radius: 50px'
                    ])
                    ->state(fn ($record) => ['lat' => $record?->latitude, 'lng' => $record?->longitude])
                    ->showMarker()
                    ->markerColor("#22c55eff")
                    ->showFullscreenControl()
                    ->draggable(false)
                    ->zoom(15),

                .....
            ]);
    }

```
<hr/>


## Usage Guide for Handling Map Locations

This section explains how to handle and display map locations within your application using this package.

**Step 1: Define Your Database Schema**

Ensure your database table includes latitude and longitude columns.
This is essential for storing the coordinates of your locations. You can define your table schema as follows:

```php
$table->double('latitude')->nullable();
$table->double('longitude')->nullable();
```

**Step 2: Retrieve and Set Coordinates**

When loading a record, ensure you correctly retrieve and set the latitude and longitude values.
Use the following method within your form component:

```php
->afterStateHydrated(function ($state, $record, Set $set): void {
    $set('location', ['lat' => $record?->latitude, 'lng' => $record?->longitude]);
})
```

**Step 3: Add Form Fields for Latitude and Longitude**

Add hidden form fields for latitude and longitude to your form. This ensures the values are present but not visible to the user:

```php
TextInput::make('latitude')
    ->hiddenLabel()
    ->hidden(),

TextInput::make('longitude')
    ->hiddenLabel()
    ->hidden()
```

If you prefer to display these values in a read-only format, replace `hidden()` with `readOnly()`.

### Alternative Approach: Using a Single Location Attribute

If you prefer to handle the location as a single field, you can define a custom attribute in your model. This method avoids the need for separate latitude and longitude columns:

```php
class YourModel extends Model
{
    protected function location(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => [
                'latitude' => $attributes['latitude'],
                'longitude' => $attributes['longitude']
            ],
            set: fn (array $value) => [
                'latitude' => $value['latitude'],
                'longitude' => $value['longitude']
            ],
        );
    }
}
```

This approach encapsulates both latitude and longitude within a single location attribute, streamlining your code.



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


[ico-version]: https://img.shields.io/packagist/v/dotswan/filament-map-picker.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dotswan/filament-map-picker.svg?style=flat-square

[link-workflow-test]: https://github.com/dotswan/filament-map-picker/actions/workflows/ci.yml
[link-packagist]: https://packagist.org/packages/dotswan/filament-map-picker
[link-license]: https://github.com/dotswan/filament-map-picker/blob/master/LICENSE.md
[link-downloads]: https://packagist.org/packages/dotswan/filament-map-picker
[link-readme]: https://github.com/dotswan/filament-map-picker/blob/master/README.md
[link-github-issue]: https://github.com/dotswan/filament-map-picker/issues
[link-docs]: https://github.com/dotswan/filament-map-picker/blob/master/docs/openapi.yaml
[link-composer-json]: https://github.com/dotswan/filament-map-picker/blob/master/composer.json
[link-gitignore]: https://github.com/dotswan/filament-map-picker/blob/master/.gitignore
[link-pint]: https://github.com/dotswan/filament-map-picker/blob/master/pint.json
[link-author]: https://github.com/dotswan
