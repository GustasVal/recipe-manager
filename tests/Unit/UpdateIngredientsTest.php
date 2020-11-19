<?php

namespace Tests\Feature;

use App\Http\Controllers\Ingredients\IngredientController;
use App\Http\Requests\Ingredients\Ingredient\Store;
use App\Http\Requests\Ingredients\Ingredient\Update;
use App\Models\Ingredients\Ingredient;
use App\Models\Recipes\Recipe;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UpdateIngredientsTest extends TestCase
{
    /** @var IngredientController $controller */
    private $controller;

    public function setUp(): void
    {
        $this->controller = $this->getMockBuilder(IngredientController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authorize'])
            ->getMock();
    }

    /** @test */
    public function update_with_ingredient_attributes()
    {
        $ingredient = $this->createMock(Ingredient::class);
        $request = $this->createMock(Update::class);
        $collection = $this->createMock(Collection::class);

        $relationship = $this->createMock(BelongsToMany::class);

        $this->controller->expects(self::once())->method('authorize')->with($ingredient);

        $request
            ->expects(self::once())
            ->method('validated')
            ->willReturn([
                'position' => 1,
                'ingredient_attributes' => $collection
            ])
        ;


        $ingredient->expects(self::once())->method('ingredientAttributes')->willReturn($relationship);
        $relationship->expects(self::once())->method('sync')->with($collection)->willReturn([]);

        $ingredient
            ->expects(self::once())
            ->method('update')
            ->with(['ingredient_attributes' => $collection])
            ->willReturn(true)
        ;

        $ingredient
            ->expects(self::once())
            ->method('updatePosition')
            ->with(1)
        ;

        $this->controller->update($request, $ingredient);
    }

    /** @test */
    public function update_without_ingredient_attributes()
    {
        $ingredient = $this->createMock(Ingredient::class);
        $request = $this->createMock(Update::class);

        $this->controller->expects(self::once())->method('authorize')->with($ingredient);

        $request
            ->expects(self::once())
            ->method('validated')
            ->willReturn([
                'position' => 1,
            ])
        ;

        $ingredient->expects(self::never())->method('ingredientAttributes');

        $ingredient
            ->expects(self::once())
            ->method('update')
            ->with([])
            ->willReturn(true)
        ;

        $ingredient
            ->expects(self::once())
            ->method('updatePosition')
            ->with(1)
        ;

        $this->controller->update($request, $ingredient);
    }

    /** @test */
    public function update_without_position_and_attributes()
    {
        $ingredient = $this->createMock(Ingredient::class);
        $request = $this->createMock(Update::class);

        $this->controller->expects(self::once())->method('authorize')->with($ingredient);

        $request
            ->expects(self::once())
            ->method('validated')
            ->willReturn([])
        ;

        $ingredient->expects(self::never())->method('ingredientAttributes');

        $ingredient
            ->expects(self::once())
            ->method('update')
            ->with([])
            ->willReturn(true)
        ;

        $ingredient
            ->expects(self::once())
            ->method('updatePosition')
            ->with(null)
        ;

        $this->controller->update($request, $ingredient);
    }

}
