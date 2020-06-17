<?php

use Illuminate\Database\Seeder;

class FuelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fuel_types = ['petrol95', 'petrol98', 'diesel', 'gas'];
        $fuel_prices_1l = ['0.91', '0.95', '0.81', '0.41'];

        $makes = ['BMW', 'BMW', 'Audi', 'BMW', 'Audi', 'Audi', 'Audi', 'Citroen', 'Citroen', 'Ford', 'Ford'];
        $models = ['X5', 'X3', 'A8', 'X6', 'A6', '80', 'A6', 'C3', 'Xsara Picasso', 'Econoline', 'Focus'];
        $fuelCost = [8.70, 8.70, 9.70, 11.10, 4.80, 6.00, 9.60, 4.60, 7.70, 16.60, 6.60];
        $car_fuel_types = [0, 1, 2, 1, 2, 2, 1, 0, 0, 3, 3];

        for ($i = 0; $i < 4; $i++) {
            $fuel = new \App\Fuel();
            $fuel->kuroPavadinimas = $fuel_types[$i];
            $fuel->kaina = $fuel_prices_1l[$i];

            $fuel->save();
        }
        for ($i = 0; $i < 11; $i++) {
            $car = new \App\Car();
            $car->marke = $makes[$i];
            $car->modelis = $models[$i];
            $car->kuroSanaudos = $fuelCost[$i];
            $car->kuroId = $car_fuel_types[$i];

            $car->save();
        }
    }
}
