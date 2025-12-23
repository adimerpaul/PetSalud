<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MascotaFactory extends Factory
{
    public function definition(): array
    {
        $especies = ['Perro', 'Gato', 'Conejo', 'Ave', 'Pez', 'Reptil', 'Otro'];
        $colores  = ['Negro','Blanco','Gris','Marrón','Beige','Dorado','Rojo','Canela','Crema','Tricolor','Manchado','Atigrado'];

        $sexo = $this->faker->randomElement(['Macho', 'Hembra']);
        $fecha = $this->faker->dateTimeBetween('-15 years', '-2 months')->format('Y-m-d');
        $edadAnios = \Carbon\Carbon::parse($fecha)->diffInYears(now());

        return [
            // mascota
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->optional(0.4)->lastName(),
            'especie' => $this->faker->randomElement($especies),
            'raza' => $this->faker->optional(0.2)->word(), // simple
            'sexo' => $sexo,
            'fecha_nac' => $fecha,
            'edad' => $edadAnios . ' años',
            'senas_particulares' => $this->faker->optional(0.6)->sentence(3),
            'color' => $this->faker->randomElement($colores),
            'photo' => $this->faker->randomElement(['defaultPet.jpg','defaultPet.jpg','defaultPet.jpg']),

            // propietario
            'propietario_nombre' => $this->faker->name(),
            'propietario_ci' => (string)$this->faker->numberBetween(1000000, 99999999),
            'propietario_direccion' => $this->faker->address(),
            'propietario_telefono' => $this->faker->phoneNumber(),
            'propietario_ciudad' => $this->faker->randomElement(['Oruro','La Paz','Cochabamba','Santa Cruz','Potosí','Tarija']),
            'propietario_celular' => $this->faker->optional(0.7)->phoneNumber(),

            // veterinaria_id lo setearás en el seeder (o aquí si quieres)
            // 'veterinaria_id' => null,
        ];
    }
}
