<?php

namespace Tests\Integration\Model;

use App\Models\Rota;
use App\Models\Shop;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RotaTest extends TestCase
{
    //use RefreshDatabase;

    /** @test */
    function it_fetches_rota_with_shifts()
    {

        $this->truncate();
        //Given i have these records in the db
        //https://laravel.com/docs/8.x/database-testing#factory-relationships
        $rota= Rota::factory()
                        ->hasShifts(5)
                        ->create();

        //when i execute this action
        $rota = Rota::find($rota->first()->id);
        $shifts = $rota->shifts()->get();

        //then what do I expect to happen - assertions
        $this->assertEquals(5,$shifts->count());

    }
    /** @test */
    function it_fetches_rota_for_week_commencing_and_shop_id()
    {
        //$this->refreshDatabase();
        $this->truncate();

        //Given i have these records in the db
        $rota= Rota::factory()
            ->for(Shop::factory()->active())
            ->hasShifts(5)
            ->create(['week_commence_date' => '2022-05-02']);

        //when i execute this action
       $actionRota = (new Rota())->rotaWithShifts($rota->shop_id,'2022-05-02')->get();


         //then what do I expect to happen - assertions
        $this->assertEquals($rota->id,$actionRota->first()->id);
        $this->assertEquals($rota->shop_id,$actionRota->first()->shop_id);
    }

}
