<?php

namespace Tests\Integration\Model;

use App\Models\Shop;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ShopTest extends TestCase
{
    //use RefreshDatabase;

    /** @test */
    function it_fetches_active_shops()
    {
        $this->truncate();
        //Given i have these records in the db
        Shop::factory(3)->create();
        $activeShop = Shop::factory()->active()->create();

        //when i execute this action - get active shops
        //$articles = Article::trending();
        $shop = Shop::active();

        //then what do I expect to happen - assertions
        //$this->assertEquals($mostPopular->id,$articles->first()->id);
        //expected / actual
        $this->assertEquals($activeShop->active, $shop->first()->active);

    }

    /** @test */
    function it_fetches_shop_with_staff()
    {
        $this->truncate();
        //Given i have these records in the db
        //https://laravel.com/docs/8.x/database-testing#factory-relationships
/*        Shop::factory()
            ->has(Staff::factory()->count(5))
            ->create();*/

        $shop= Shop::factory()
                        ->hasStaff(5)
                        ->create();

        //when i execute this action - get staff for shop
        $shop = Shop::find($shop->first()->id);
        $staff = $shop->staff()->get();

        //then what do I expect to happen - assertions
        $this->assertEquals(5,$staff->count());

    }

}
