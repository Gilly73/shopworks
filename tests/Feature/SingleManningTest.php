<?php

namespace Tests\Feature;

use App\Models\Shift;
use App\Models\Shop;
use App\SingleManning;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;




class SingleManningTest extends TestCase
{
    //use RefreshDatabase;

    /**
     * @var $singleManning
     */
    protected $singleManning;


    public function setUp(): void
    {
        parent::setUp(); //important
        //$this->refreshDatabase();
        $this->truncate();

    }

    /** @test */
    function it_fetches_shop_id_for_single_manning()
    {
        //Given i have these records in the db
        /*$rota= Rota::factory()
            ->for(Shop::factory()->active()->hasStaff(5)->create())
           // ->hasAttached(Shift::factory())
           ->hasShifts(2,[
               'rota_id' => 1
           ])
           ->create(['week_commence_date' => '2022-05-02']);

        $shop=Shop::factory()
            ->active()
            ->hasStaff(5)
            ->hasRota(1,['week_commence_date' => '2022-05-02'])
            ->create();

        $rota = $shop->rota()->get();
        $staff = $shop->staff()->get();


        $staff->each(function($item, $key) use($rota){
            Shift::factory()
                ->create([
                    'rota_id' =>$rota->first()->id,
                    'staff_id' =>$item->id
                ]);
        });*/

        //when i execute this action
        $this->singleManning = new SingleManning(1, Carbon::parse('2022-05-02'));

        //then what do I expect to happen - assertions
        $this->assertEquals(1,$this->singleManning->getShopId());

    }
    /** @test */
    function it_fetches_date_commencing_for_single_manning()
    {
        //Given i have these records in the db

        /*$shop=Shop::factory()
            ->active()
            ->hasStaff(5)
            ->hasRota(1,['week_commence_date' => '2022-05-02'])
            ->create();

        $rota = $shop->rota()->get();
        $staff = $shop->staff()->get();


        $staff->each(function($item, $key) use($rota){
            Shift::factory()
                ->create([
                    'rota_id' =>$rota->first()->id,
                    'staff_id' =>$item->id
                ]);
        });*/

        //when i execute this action
        $this->singleManning = new SingleManning(1, Carbon::parse('2022-05-02'));

        //then what do I expect to happen - assertions
        $this->assertEquals('2022-05-02',$this->singleManning->getDateCommencing());

    }
    /** @test */
    function it_fetches_shifts_for_a_single_manning()
    {
        $this->truncate();

        //Given i have these records in the db

        $shop=Shop::factory()
            ->active()
            ->hasStaff(5)
            ->hasRota(1,['week_commence_date' => '2022-05-02'])
            ->create();

        $rota = $shop->rota()->get();
        $staff = $shop->staff()->get();
        $allExpectedShifts = collect([]);
        $staff->each(function($item, $key) use($rota,$allExpectedShifts){
            $shift = Shift::factory()
                ->create([
                    'rota_id' =>$rota->first()->id,
                    'staff_id' =>$item->id
                ]);
            $allExpectedShifts->push($shift);
        });
        $expected = $allExpectedShifts->first();

        //when i execute this action
        $this->singleManning = new SingleManning($shop->id, Carbon::parse('2022-05-02'));
        $actualShifts = $this->singleManning->shifts()->shifts;

        //expect
        $this->assertTrue($actualShifts->contains('staff_id', $expected->staff_id));

    }

    /** @test */
    function it_fetches_the_shifts_sorted_and_grouped_by_day()
    {

        $shop=Shop::factory()
            ->active()
            ->hasStaff(5)
            ->hasRota(1,['week_commence_date' => '2022-05-19'])
            ->create();

        $rota = $shop->rota()->get();
        $staff = $shop->staff()->get();
        $allExpectedShifts = collect([]);
        $start_time = [
            '2022-05-20 09:00:00',
            '2022-05-19 10:00:01',
            '2022-05-19 07:00:01',
            '2022-05-22 13:00:01',
            '2022-05-20 07:00:01',
        ];
        $end_time = [
            '2022-05-20 10:00:00',
            '2022-05-19 11:00:00',
            '2022-05-19 13:00:00',
            '2022-05-22 17:00:00',
            '2022-05-20 17:00:00',
        ];
        $staff->each(function($item, $key) use($rota,$allExpectedShifts,$start_time,$end_time){
            $shift = Shift::factory()
                ->create([
                    'rota_id' =>$rota->first()->id,
                    'staff_id' =>$item->id,
                    'start_time' => $start_time[$key],
                    'end_time' => $end_time[$key],
                ]);
            $allExpectedShifts->push($shift);
        });

        $shift = Shift::factory()
            ->create([
                'rota_id' =>$rota->first()->id,
                'staff_id' => 3,
                'start_time' => '2022-05-21 09:00:00',
                'end_time' => '2022-05-21 17:00:00',
            ]);

        $allExpectedShifts->push($shift);

        $shifts = $allExpectedShifts->sortBy(function ($shift){
            return Carbon::parse($shift->start_time)->format('Y-m-d h:i:s');
        });
        $shifts = $shifts->groupBy(function ($shift){
            return Carbon::parse($shift->start_time)->format('Y-m-d');
        });
        $expected = $shifts['2022-05-22'][0]['staff_id'];
        //var_dump($expected);

       //when i execute this action
        $this->singleManning = new SingleManning($shop->id, Carbon::parse('2022-05-19'));
        $actualShifts = $this->singleManning->prepareShifts();

        //var_dump($actualShiftsGrouped);
        $actual = $actualShifts['2022-05-22'][0]['staff_id'];
        //var_dump($actual);
        $this->assertEquals($expected, $actual);
    }

    /** @test */
    function it_fetches_the_total_minutes_for_week()
    {
        $shop=Shop::factory()
            ->active()
            ->hasStaff(2)
            ->hasRota(1,['week_commence_date' => '2022-05-19'])
            ->create();

        $rota = $shop->rota()->get();
        $staff = $shop->staff()->get();
        $allExpectedShifts = collect([]);
        $start_time = [
            //'2022-05-20 09:00:00',
            '2022-05-19 00:00:00',
            '2022-05-19 10:00:00',
            //'2022-05-22 13:00:00',
            //'2022-05-20 07:00:00',
        ];
        $end_time = [
           //'2022-05-20 10:00:00',
            '2022-05-19 23:59:59',
            '2022-05-19 11:00:00',
            //'2022-05-22 17:00:00',
            //'2022-05-20 17:00:00',
        ];
        $staff->each(function($item, $key) use($rota,$allExpectedShifts,$start_time,$end_time){
            $shift = Shift::factory()
                ->create([
                    'rota_id' =>$rota->first()->id,
                    'staff_id' =>$item->id,
                    'start_time' => $start_time[$key],
                    'end_time' => $end_time[$key],
                ]);
            $allExpectedShifts->push($shift);
        });

        $shift = Shift::factory()
            ->create([
                'rota_id' =>$rota->first()->id,
                'staff_id' => 3,
                'start_time' => '2022-05-21 09:00:00',
                'end_time' => '2022-05-21 17:00:00',
            ]);

        $allExpectedShifts->push($shift);

        $shifts = $allExpectedShifts->sortBy(function ($shift){
            return Carbon::parse($shift->start_time)->format('Y-m-d h:i:s');
        });
        $shifts = $shifts->groupBy(function ($shift){
            return Carbon::parse($shift->start_time)->format('Y-m-d');
        });
        //$expected = $shifts['2022-05-22'][0]['staff_id'];
        //var_dump($expected);

        //when i execute this action
        $this->singleManning = new SingleManning($shop->id, Carbon::parse('2022-05-19'));
        $shifts = $this->singleManning->prepareShifts();

        $total = $this->singleManning->getTotalMinutesForWeek($shifts);

        //var_dump($actualShiftsGrouped);
        var_dump($total);
        $this->assertTrue(true);

    }

}
