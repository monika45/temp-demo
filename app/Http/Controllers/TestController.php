<?php

namespace App\Http\Controllers;

use App\Model\Carimg;
use App\Model\StpMonitorData;
use App\Model\StpUser;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{

    static $mailExts = [
        '@gmail.com',
        '@aol.com',
        '@gmx.com',
        '@yahoo.com',
        '@mail.com',
        '@outlook.com'
    ];

    static $ethnic_list = [
        'White',
        'Black',
        'Asian',
        'Other'
    ];

    static $cities = [
        ['London',51.517877517010334, -0.13181607944201346],
        ['Brighton', 50.82345395014663, -0.13720937036925138],
        ['Birmingham', 52.491215949581964, -1.8928151775194355],
        ['Manchester', 53.4865270613932, -2.2416601697772616],
        ['Glasgow', 55.86969758364373, -4.251342493087311],
        ['Carlisle', 54.89484441345045, -2.9340211126726254],
    ];

    private $faker;



    public function index()
    {
        echo 'success';
    }


    // 生成种子数据
    public function seedData()
    {

        $this->faker = Factory::create();
        $genders = [
            0 => 1,//male
            1 => 2 //female
        ];


//        var_dump($this->generateRandomPoint([29.0921, 106.333], 4));
//        die;
//        echo $this->faker->firstNameFemale;
//        echo $this->faker->firstNameMale;
        $i = 5;
        $mailExtLen = count(self::$mailExts);
        $ethnicLen = count(self::$ethnic_list);
        $cityLen = count(self::$cities);
        $blood_list = ['A', 'B', 'O', 'AB'];
        while (--$i >= 0) {
            $user = new StpUser();
            $gender = $genders[(int)$this->faker->boolean];
            $name = $gender == 2 ? $this->faker->firstNameFemale : $this->faker->firstNameMale;
            $email = $name . self::$mailExts[rand(0, $mailExtLen - 1)];
            while (StpUser::where('email', $email)->exists()) {
                $name = $gender ? $this->faker->firstNameFemale : $this->faker->firstNameMale;
                $email = $name . self::$mailExts[rand(0, $mailExtLen - 1)];
            }
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make('uys*s32+');
            $user->gender = $gender;
            $user->birthday = $this->faker->dateTimeBetween('-50 years', '-16 years', 'UTC')->format('Y-m-d');
            $user->ethnic_bg = self::$ethnic_list[rand(0, $ethnicLen - 1)];
            $user->height = $this->randHeight($gender);
            $user->weight = $this->randWeight($gender, $user->height);
            $user->blood_type = $blood_list[rand(0, 3)];
            $user->save();
            // 给每个用户生成最近3天的数据
            $user_id = $user->id;
            $days = $this->days(3);
            foreach ($days as $day) {
                $times = $this->times($day);
                $lastTemperature = null;
                foreach ($times as $time) {
                    $stpData = new StpMonitorData();
                    $stpData->user_id = $user_id;
                    $stpData->day = $day;
                    $stpData->time = $time;
                    $stpData->type = 'temperature';
//                    $stpData->data = round($this->randFloat(35, 39), 1);
//                    $stpData->data = round($this->randFloat(38, 40), 1);
                    $stpData->data = round($this->randFloat(36, 37), 1);
                    if (!$lastTemperature) {
                        $stpData->variation = '';
                    } else {
                        $stpData->variation = StpMonitorData::calVariation($stpData->data, $lastTemperature);
                    }
                    $lastTemperature = $stpData->data;
                    // 城市、坐标
                    $city = self::$cities[rand(0, $cityLen - 1)];
                    $loc = $this->generateRandomPoint([$city[1], $city[2]], 8);
                    $stpData->location = $city[0];
                    $stpData->lat = $loc[0];
                    $stpData->lng = $loc[1];
                    $stpData->created_at = $day . ' ' . $time;
                    $stpData->save();
                }
            }
        }

        echo 'success';
    }

    function generateRandomPoint($centre, $radius) {
        $radius_earth = 3959; //miles

        //Pick random distance within $distance;
        $distance = lcg_value()*$radius;

        //Convert degrees to radians.
        $centre_rads = array_map( 'deg2rad', $centre );

        //First suppose our point is the north pole.
        //Find a random point $distance miles away
        $lat_rads = (pi()/2) -  $distance/$radius_earth;
        $lng_rads = lcg_value()*2*pi();


        //($lat_rads,$lng_rads) is a point on the circle which is
        //$distance miles from the north pole. Convert to Cartesian
        $x1 = cos( $lat_rads ) * sin( $lng_rads );
        $y1 = cos( $lat_rads ) * cos( $lng_rads );
        $z1 = sin( $lat_rads );


        //Rotate that sphere so that the north pole is now at $centre.

        //Rotate in x axis by $rot = (pi()/2) - $centre_rads[0];
        $rot = (pi()/2) - $centre_rads[0];
        $x2 = $x1;
        $y2 = $y1 * cos( $rot ) + $z1 * sin( $rot );
        $z2 = -$y1 * sin( $rot ) + $z1 * cos( $rot );

        //Rotate in z axis by $rot = $centre_rads[1]
        $rot = $centre_rads[1];
        $x3 = $x2 * cos( $rot ) + $y2 * sin( $rot );
        $y3 = -$x2 * sin( $rot ) + $y2 * cos( $rot );
        $z3 = $z2;


        //Finally convert this point to polar co-ords
        $lng_rads = atan2( $x3, $y3 );
        $lat_rads = asin( $z3 );

        return array_map( 'rad2deg', array( $lat_rads, $lng_rads ) );
    }


    /**
     * 添加体温数据
     * 遍历所有用户，添加随机数据，给每个用户生成最近10天的数据
    */
    private function bodyTemperatureData()
    {

    }

    private function randFloat($st_num=0,$end_num=1,$mul=1000000)
    {
        if ($st_num>$end_num) return false;
        return mt_rand($st_num*$mul,$end_num*$mul)/$mul;
    }

    private function times($day)
    {
        $count = rand(10, 46);
        $times = [];
        $time = $this->randTime(5, 12);
        while (--$count > 0) {
            $times[] = $time;
            $last = $time;
            $intervalMinutes = rand(15, 120);
            $baseTimeStamp = strtotime($day . ' ' . $time);
            $time = date('H:i', strtotime("+$intervalMinutes minutes", $baseTimeStamp));
            if ($time <= $last) {
                break;
            }
        }
        return $times;
    }

    private function randTime($start_hour = 0, $end_hour = 23)
    {
        $hour = rand($start_hour, $end_hour);
        $minute = rand(0, 59);
        $second = rand(0, 59);
        if ($hour < 10) {
            $hour = '0' . $hour;
        }
        if ($minute < 10) {
            $minute = '0' . $minute;
        }
        return $hour . ':' . $minute;
    }

    private function days($num, $date = null)
    {
        $i = $num;
        $days = [];
        if (!$date) {
            $date = date('Y-m-d');
        }
        $baseTimeStamp = strtotime($date);
        while (--$i >= 0) {
            $days[] = date('Y-m-d', strtotime("-$i days", $baseTimeStamp));
        }
        return $days;
    }

    private function randHeight($gender)
    {
        // 男性
        if ($gender == 1) {
            return rand(168, 180);
        }
        // 女性
        return rand(152, 168);

    }

    private function randWeight($gender, $height)
    {
        // 男性
        if ($gender == 1) {
            return round(($height - 80) * 0.7) + rand(-6, 6);
        }
        // 女性
        return round(($height - 70) * 0.6) + rand(-6, 6);
    }


}
