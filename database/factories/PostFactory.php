<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::get()->toArray('id');
        return [
            'title'=>$this->faker->name(),
            'user_id'=>User::pluck('id')[$this->faker->numberBetween(1,User::count()-1)]
        ];
    }
}
