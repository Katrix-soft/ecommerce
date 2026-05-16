<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Option;
use App\Models\Feature;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            [
                'name' => 'Talle',
                'type' => '1',
                'features'=> [
                    ['value' => 'S',
                    'description'=> 'Pequeño'],
                    ['value' => 'M',
                    'description'=> 'Mediano'],
                    ['value' => 'L',
                    'description'=> 'Grande'],
                    ['value' => 'XL',
                    'description'=> 'Extra Grande'],
                ]
            ],
            [
                'name' => 'Color',
                'type' => '2',
                'features'=> [
                    ['value' => '#FF0000',
                    'description'=> 'Rojo'],
                    ['value' => '#00FF00',
                    'description'=> 'Verde'],
                    ['value' => '#0000FF',
                    'description'=> 'Azul'],
                    ['value' => '#FFFF00',
                    'description'=> 'Amarillo'],
                ]
            ],
            [
                'name'=> 'Sexo',
                'type' => '1',
                'features'=> [
                    ['value' => 'M',
                    'description'=> 'Masculino'],
                    ['value' => 'F',
                    'description'=> 'Femenino'],
                ]
            ]
        ];

        foreach ($options as $option) {
           $optionModel = Option::firstOrCreate([
            'name'=> $option['name'],
            'type' => $option['type'],
           ]);
           foreach ($option['features'] as $feature) {
              $optionModel->features()->firstOrCreate([
                'value' => $feature['value'],
                'description'=> $feature['description'],
              ]);
           }
        }
    }
}
