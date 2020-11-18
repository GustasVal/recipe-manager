<?php

namespace Tests\Feature;

use App\Models\Recipes\Recipe;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipePhotosTest extends TestCase
{
    private $recipe;

    public function setUp(): void
    {
        $this->recipe = $this->getMockBuilder(Recipe::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['update', 'generatePhotoUuid'])
            ->getMock();
    }


    /** @test */
    public function add_photo()
    {
        $photo = $this->createMock(UploadedFile::class);
        $this->recipe->id = 1;
        $this->recipe->slug = 'test';
        $this->recipe->photos = [];

        $photo->expects(self::once())->method('getClientOriginalExtension')->willReturn('png');
        $this->recipe->expects(self::once())->method('generatePhotoUuid')->willReturn('123');
        $photo->expects(self::once())->method('storeAs')->with('1', '123-test.png', 'recipe_images');
        $this->recipe->expects(self::once())->method('update')->with(['photos' => ['123-test.png']]);

        $this->recipe->addPhoto($photo);
    }

    /** @test */
    public function remove_photo()
    {
        $filesystem = $this->createMock(Filesystem::class);
        $this->recipe->id = 1;
        $this->recipe->photos = ['test123.png'];

        $this->recipe->expects(self::once())->method('update')->with(['photos' => []]);

        Storage::shouldReceive('disk')
            ->once()
            ->with('recipe_images')
            ->andReturn($filesystem);

        $filesystem->expects(self::once())->method('delete')->with('1/test123.png');

        $this->recipe->removePhoto('test123.png');
    }

    /** @test */
    public function remove_not_found_photo()
    {
        $filesystem = $this->createMock(Filesystem::class);
        $this->recipe->id = 1;
        $this->recipe->photos = [];

        Storage::shouldReceive('disk')
            ->once()
            ->with('recipe_images')
            ->andReturn($filesystem);

        $filesystem->expects(self::once())->method('delete')->with('1/test123.png');

        $this->recipe->removePhoto('test123.png');
    }
}
