<?php

namespace Tests\Feature;

use App\Http\Controllers\Users\UserController;
use App\Models\Users\User;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    /** @var UserController $controller */
    private $controller;

    public function setUp(): void
    {
        $this->controller = $this->getMockBuilder(UserController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authorize'])
            ->getMock();
    }

    /** @test */
    public function show_with_user_param()
    {
        $user = $this->createMock(User::class);

        $this->controller->expects(self::once())->method('authorize')->with($user);

        $result = $this->controller->show($user);

        $this->assertEquals($user, $result);
    }

}
