<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
        User::create(array(
            'name'     => 'Christopher Paslawski',
            'username' => 'salaman',
            'email'    => 'chris@paslawski.me',
            'password' => Hash::make('password'),
        ));
	}

}