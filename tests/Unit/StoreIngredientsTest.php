<?php

namespace Tests\Feature;

use App\Http\Controllers\Ingredients\IngredientController;
use App\Http\Requests\Ingredients\Ingredient\Store;
use App\Models\Ingredients\Ingredient;
use App\Models\Recipes\Recipe;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class StoreIngredientsTest extends TestCase
{
    private $controller;

    public function setUp(): void
    {
        $this->controller = $this->getMockBuilder(IngredientController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authorize', 'responseCreated'])
            ->getMock();
    }

    /** @test */
    public function store_without_ingredient_attributes()
    {
        $recipe = $this->createMock(Recipe::class);
        $request = $this->createMock(Store::class);
        $recipeRelationship = $this->createMock(HasMany::class);
        $ingredient = $this->getMockBuilder(Ingredient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updatePosition'])
            ->getMock();
        $ingredient->id = 1;

        $this->controller->expects(self::once())->method('authorize')->with([Ingredient::class, $recipe]);
        $request->expects(self::once())->method('validated')->willReturn([]);

        $recipe->expects(self::once())->method('ingredients')->willReturn($recipeRelationship);
        $recipeRelationship->expects(self::once())->method('create')->willReturn($ingredient);

        $ingredient->expects(self::once())->method('updatePosition')->with(NULL);

        $this->controller
            ->expects(self::once())
            ->method('responseCreated')
            ->with('ingredients.show', 1)
        ;

        $this->controller->store($request, $recipe);
    }

    /** @test */
    public function store_with_ingredient_attributes()
    {
        $recipe = $this->createMock(Recipe::class);
        $request = $this->createMock(Store::class);
        $recipeRelationship = $this->createMock(HasMany::class);
        $ingredientRelationship = $this->createMock(BelongsToMany::class);
        $ingredient = $this->getMockBuilder(Ingredient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updatePosition', 'ingredientAttributes'])
            ->getMock();
        $ingredient->id = 1;

        $this->controller->expects(self::once())->method('authorize')->with([Ingredient::class, $recipe]);
        $request->expects(self::once())->method('validated')->willReturn(['ingredient_attributes' => [1, 2]]);

        $recipe->expects(self::once())->method('ingredients')->willReturn($recipeRelationship);
        $recipeRelationship->expects(self::once())->method('create')->willReturn($ingredient);

        $ingredient->expects(self::once())->method('updatePosition')->with(NULL);
        $ingredient->expects(self::once())->method('ingredientAttributes')->willReturn($ingredientRelationship);

        $ingredientRelationship->expects(self::once())->method('sync')->with([1, 2])->willReturn([1]);

        $this->controller
            ->expects(self::once())
            ->method('responseCreated')
            ->with('ingredients.show', 1)
        ;

        $this->controller->store($request, $recipe);
    }


}
