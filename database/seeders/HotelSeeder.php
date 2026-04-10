<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Hôtel Terrou-Bi',
                'address' => 'Boulevard Martin Luther King Dakar, 11500',
                'email' => 'contact@terroubi.sn',
                'phone' => '+221 33 821 10 10',
                'price' => '25000',
                'currency' => 'F XOF',
                'image' => 'terrou bi.jpg',
            ],
            [
                'name' => 'King Fahd Palace',
                'address' => 'Rte des Almadies, Dakar',
                'email' => 'info@kingfahdpalace.sn',
                'phone' => '+221 33 869 69 69',
                'price' => '20000',
                'currency' => 'F XOF',
                'image' => 'king fahd palace.png',
            ],
            [
                'name' => 'Radisson Blu Hotel',
                'address' => 'Rte de la Corniche 0, Dakar 16868',
                'email' => 'dakar@radissonblu.com',
                'phone' => '+221 33 869 33 33',
                'price' => '22000',
                'currency' => 'F XOF',
                'image' => 'radisson blue.jpg',
            ],
            [
                'name' => 'Pullman Dakar Teranga',
                'address' => 'Place de l\'Indépendance, Dakar',
                'email' => 'dakar@pullmanhotels.com',
                'phone' => '+221 33 823 84 84',
                'price' => '30000',
                'currency' => 'F XOF',
                'image' => 'pullman.jpg',
            ],
            [
                'name' => 'Hôtel Lac Rose',
                'address' => 'Lac Rose, Dakar',
                'email' => 'info@hotellacrose.sn',
                'phone' => '+221 33 957 51 51',
                'price' => '25000',
                'currency' => 'F XOF',
                'image' => 'lac rose.jpg',
            ],
            [
                'name' => 'Hôtel Saly',
                'address' => 'Mbour, Sénégal',
                'email' => 'reservation@hotelsaly.sn',
                'phone' => '+221 33 957 52 52',
                'price' => '20000',
                'currency' => 'F XOF',
                'image' => 'saly mbour.jpg',
            ],
            [
                'name' => 'Palm Beach Resort & Spa',
                'address' => 'BP64, Saly 23000',
                'email' => 'info@palmbeachresort.sn',
                'phone' => '+221 33 957 53 53',
                'price' => '22000',
                'currency' => 'F XOF',
                'image' => 'palm beach.jpg',
            ],
            [
                'name' => 'Pullman Dakar Teranga',
                'address' => 'Place de l\'Indépendance, Dakar',
                'email' => 'dakar2@pullmanhotels.com',
                'phone' => '+221 33 823 85 85',
                'price' => '30000',
                'currency' => 'F XOF',
                'image' => 'pullman hôtel.jpg',
            ],
        ];

        foreach ($hotels as $hotel) {
            Hotel::create($hotel);
        }
    }
}
