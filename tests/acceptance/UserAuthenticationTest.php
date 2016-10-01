<?php

require_once __DIR__.'/Base.php';

class UserAuthenticationTest extends Base
{

    public function validLoginInputsProvider()
    {
        $inputs[] = [
            [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ];

        return $inputs;
    }

    public function invalidLoginInputsProvider()
    {
        $inputs[] = [
            [
                'username' => 'wrong_username',
                'password' => 'wrong_password',
            ]
        ];

        return $inputs;
    }

    /**
     * @dataProvider validLoginInputsProvider
     */
    public function testValidLogin(array $inputs)
    {
        $this->url('/');
        $this->assertContains('Login', $this->title());

        $form = $this->byTag('form');
        foreach ($inputs as $input => $value) {
            $form->byName($input)->value($value);
        }
        $form->submit();

        $content = $this->byClassName('sidebar')->text();
        $this->assertContains($inputs['username'], $content);
    }

    /**
     * @dataProvider invalidLoginInputsProvider
     */
    public function testInvalidLogin(array $inputs)
    {
        $this->url('/');

        // Test wrong username with correct password
        $form = $this->byTag('form');
        $form->byName('username')->value($inputs['username']);
        $form->byName('password')->value('admin');
        $form->submit();

        $content = $this->byTag('body')->text();
        $this->assertContains('Bad username or password', $content);

        // Test wrong password with correct username
        $form = $this->byTag('form');
        $form->byName('username')->value('admin');
        $form->byName('password')->value($inputs['password']);
        $form->submit();

        $content = $this->byTag('body')->text();
        $this->assertContains('Bad username or password', $content);

        // Test wrong username and password
        $form = $this->byTag('form');
        $form->byName('username')->value($inputs['username']);
        $form->byName('password')->value($inputs['password']);
        $form->submit();

        $content = $this->byTag('body')->text();
        $this->assertContains('Bad username or password', $content);


    }
}
