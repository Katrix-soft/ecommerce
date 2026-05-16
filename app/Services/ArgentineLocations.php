<?php

namespace App\Services;

class ArgentineLocations
{
    public static function getZipCode($province, $locality)
    {
        $cps = [
            'Capital Federal' => '1000',
            'Buenos Aires' => [
                'La Plata' => '1900',
                'Mar del Plata' => '7600',
                'Bahía Blanca' => '8000',
                'Lanús' => '1824',
                'Lomas de Zamora' => '1832',
                'Quilmes' => '1878',
                'Pilar' => '1629',
                'Tigre' => '1648',
                'Avellaneda' => '1870',
                'Morón' => '1708',
                'San Isidro' => '1642',
                'Vicente López' => '1602',
            ],
            'Córdoba' => [
                'Córdoba' => '5000',
                'Río Cuarto' => '5800',
                'Villa María' => '5900',
                'Carlos Paz' => '5152',
            ],
            'Santa Fe' => [
                'Rosario' => '2000',
                'Santa Fe' => '3000',
                'Rafaela' => '2300',
                'Venado Tuerto' => '2600',
            ],
            'Mendoza' => [
                'Mendoza' => '5500',
                'San Rafael' => '5600',
                'Godoy Cruz' => '5501',
            ],
            'Tucumán' => [
                'San Miguel de Tucumán' => '4000',
                'Yerba Buena' => '4107',
            ],
            'Salta' => [
                'Salta' => '4400',
            ],
            'Entre Ríos' => [
                'Paraná' => '3100',
                'Concordia' => '3200',
            ],
            'Misiones' => [
                'Posadas' => '3300',
                'Iguazú' => '3370',
            ],
            'Chaco' => [
                'Resistencia' => '3500',
            ],
            'Corrientes' => [
                'Corrientes' => '3400',
            ],
            'Santiago del Estero' => [
                'Santiago del Estero' => '4200',
            ],
            'San Juan' => [
                'San Juan' => '5400',
            ],
            'Jujuy' => [
                'San Salvador de Jujuy' => '4600',
            ],
            'Río Negro' => [
                'Bariloche' => '8400',
                'Viedma' => '8500',
            ],
            'Neuquén' => [
                'Neuquén' => '8300',
            ],
            'Formosa' => [
                'Formosa' => '3600',
            ],
            'Chubut' => [
                'Rawson' => '9103',
                'Comodoro Rivadavia' => '9000',
                'Puerto Madryn' => '9120',
            ],
            'San Luis' => [
                'San Luis' => '5700',
            ],
            'Catamarca' => [
                'Catamarca' => '4700',
            ],
            'La Rioja' => [
                'La Rioja' => '5300',
            ],
            'La Pampa' => [
                'Santa Rosa' => '6300',
            ],
            'Santa Cruz' => [
                'Río Gallegos' => '9400',
            ],
            'Tierra del Fuego' => [
                'Ushuaia' => '9410',
                'Río Grande' => '9420',
            ],
        ];

        if ($province === 'Capital Federal') {
            return '1000';
        }

        if (isset($cps[$province])) {
            if (is_array($cps[$province]) && isset($cps[$province][$locality])) {
                return $cps[$province][$locality];
            }
        }

        return '';
    }
}
