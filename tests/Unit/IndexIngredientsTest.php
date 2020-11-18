<?php

namespace Tests\Feature;

use App\Http\Controllers\Ingredients\IngredientController;
use App\Http\Requests\Ingredients\Ingredient\Store;
use App\Models\Ingredients\Ingredient;
use App\Models\Recipes\Recipe;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tests\TestCase;

class IndexIngredientsTest extends TestCase
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
    public function do_not_group()
    {
        $recipe = $this->createMock(Recipe::class);
        $request = $this->createMock(Request::class);
        $recipeRelationship = $this->createMock(HasMany::class);
        $recipeQueryBuilder = $this->createMock(Builder::class);

        $this->controller->expects(self::once())->method('authorize')->with([Ingredient::class, $recipe]);
        $recipe->expects(self::once())->method('ingredients')->willReturn($recipeRelationship);
        $recipeRelationship->expects(self::once())->method('getQuery')->willReturn($recipeQueryBuilder);

        $recipeQueryBuilder
            ->expects(self::exactly(2))
            ->method('orderBy')
            ->withConsecutive(
                [$this->equalTo('ingredient_group_id')],
                [$this->equalTo('position')]
            )
            ->willReturnOnConsecutiveCalls($recipeQueryBuilder, $recipeQueryBuilder)
        ;

        $recipeQueryBuilder->expects(self::once())->method('get')->willReturn(new Collection);

        $this->controller->index($request, $recipe);
    }

    /** @test */
    public function group_the_result()
    {
        $recipe = $this->createMock(Recipe::class);
        $request = $this->createMock(Request::class);
        $request->grouped = true;
        $recipeRelationship = $this->createMock(HasMany::class);
        $recipeQueryBuilder = $this->createMock(Builder::class);

        $this->controller->expects(self::once())->method('authorize')->with([Ingredient::class, $recipe]);
        $recipe->expects(self::once())->method('ingredients')->willReturn($recipeRelationship);
        $recipeRelationship->expects(self::once())->method('getQuery')->willReturn($recipeQueryBuilder);

        $recipeQueryBuilder
            ->expects(self::exactly(2))
            ->method('orderBy')
            ->withConsecutive(
                [$this->equalTo('ingredient_group_id')],
                [$this->equalTo('position')]
            )
            ->willReturnOnConsecutiveCalls($recipeQueryBuilder, $recipeQueryBuilder)
        ;

        $recipeQueryBuilder->expects(self::exactly(2))->method('get')->willReturn(new Collection);

        $recipeQueryBuilder
            ->expects(self::once())
            ->method('groupBy')
            ->with('ingredient_group_id')
            ->willReturn($recipeQueryBuilder);

        $this->controller->index($request, $recipe);
    }


}
