<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $gender_arr = ["男", "女", "其他"];
        $gender_key = array_rand($gender_arr, 1);
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'ID_num' => $this->faker->unique()->numerify('E#########'),
            'gender' => $gender_arr[$gender_key],
            'birth' => $this->faker->date(),
            'address_1' => $this->faker->address(),
            'address_2' => 'address_1',
            'phone' => $this->faker->numerify('09########'),
            'TEL' => $this->faker->numerify('0#-#######'),
            'bureau_id' => 0,

            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
