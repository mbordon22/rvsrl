<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // return [
        //     'first_name' => fake()->name(),
        //     'email' => fake()->unique()->safeEmail(),
        //     'email_verified_at' => now(),
        //     'password' => static::$password ??= Hash::make('password'),
        //     'remember_token' => Str::random(10),
        // ];


        // return [
        //     'first_name' => fake()->name(),
        //     'last_name' => fake()->name(),
        //     'email' => fake()->unique()->safeEmail(),
        //     // 'email_verified_at' => now(),
        //     'password' => static::$password ??= Hash::make('password'),
        //     'country_code' => fake()->randomElement(['93','91']),
        //     'phone' => fake()->randomNumber(),
        //     'dob' => fake()->date($format = 'Y-m-d', $max = 'now'),
        //     'gender' => fake()->randomElement(['male', 'female']),
        //     'status' => fake()->randomElement(['0', '1']),
        //     'country_id' => fake()->randomElement(['356','12']),
        //     'state_id' => fake()->randomElement(['12','25']),
        //     'location' => fake()->city(),
        //     'postal_code' => fake()->postcode(),
        //     'about_me' => fake()->text(),
        //     'bio' => fake()->text(),
        //     'system_reserve' => fake()->numberBetween('1'),
        //     'remember_token' => Str::random(10),
        // ];

        return [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'country_code' => fake()->randomElement(['93','91']),
            'phone' => fake()->randomNumber(),
            'dob' => fake()->date($format = 'Y-m-d', $max = 'now'),
            'gender' => fake()->randomElement(['male', 'female']),
            'status' => fake()->randomElement(['0', '1']),
            'country_id' => fake()->randomElement(['356','12']),
            'state_id' => fake()->randomElement(['12','25']),
            'location' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'about_me' => fake()->text(),
            'bio' => fake()->text(),
            'system_reserve' => fake()->numberBetween('1'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
