<?php

namespace Tests\Feature;

use App\Models\Rota;
use App\Models\Shift;
use App\Models\Shop;
use App\Models\Staff;
use App\SingleManning;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SingleManningTestOld extends TestCase
{
    //use RefreshDatabase;

    /**
     * @var $singleManning
     */
    protected $singleManning;


    public function setUp(): void
    {

        parent::setUp(); //important
        //https://laracasts.com/discuss/channels/testing/how-can-i-get-id-of-created-row-when-testing

        $this->singleManning = new SingleManning(1, Carbon::parse('2022-05-19'));

    }


    function a_single_manning_has_a_shop_id()
    {
        $this->assertEquals(1,$this->singleManning->getShopId());
    }


    function a_single_manning_has_a_date_commencing()
    {
        $this->assertEquals( '2022-05-19',$this->singleManning->getDateCommencing());
    }


    function a_shop_is_set_up()
    {
        $shop = $this->set_up_a_shop();

        $this->assertEquals('shop',$shop->name);

        $rota = $this->set_up_a_rota($shop->id);

        $this->assertEquals('2022-05-19',$rota->week_commence_date);

        $staff = $this->set_up_staff(4,$shop->id);
        $this->assertEquals(4,$staff->count());
        $this->assertEquals(1,$staff->first()->shop_id);

        $this->set_up_shifts($staff,$rota->id);
        $shifts = Shift::all();
        $this->assertEquals(4,$shifts->count());
        $this->assertEquals('2022-05-19 09:00:00',$shifts[0]['start_time']);

    }


    function a_single_manning_has_shifts()
    {
        //$this->refreshDatabase();
        $this->assertTrue(true);
    }




    function mock_shifts()
    {
        $shop = $this->set_up_a_shop();
        $rota = $this->set_up_a_rota($shop->id);
        $staff = $this->set_up_staff(4,$shop->id);
        $this->set_up_shifts($staff,$rota->id);
    }

    function set_up_shifts($staff,$rotaid)
    {
        $start_time = [
            '2022-05-19 09:00:00',
            '2022-05-19 10:00:01',
            '2022-05-19 10:00:01',
            '2022-05-19 13:00:01'
        ];
        $end_time = [
            '2022-05-19 10:00:00',
            '2022-05-19 11:00:00',
            '2022-05-19 13:00:00',
            '2022-05-19 17:00:00'
        ];

        foreach ($staff as $employee) {
            Shift::factory()->create([
                'rota_id' => $rotaid,
                'staff_id' => $employee->id,
                'start_time' => $start_time[$employee->id-1],
                'end_time' => $end_time[$employee->id-1],
            ]);
        }

    }

    function set_up_staff($number,$shopid)
    {
            return Staff::factory($number)->create([
                'shop_id' => $shopid,
                'first_name' => Str::random(5),
                'surname' => Str::random(7)
            ]);
    }

    function set_up_a_rota($shopid)
    {
        return  Rota::factory()->create([
            'shop_id' => $shopid,
            'week_commence_date' => '2022-05-19'
        ]);

    }

    function set_up_a_shop()
    {
        return Shop::factory()->create([
            'name' => 'shop'
        ]);
    }
}
