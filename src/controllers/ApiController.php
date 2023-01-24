<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $start = microtime(true) * 1000;
        $params = \Yii::$app->request->get();
        $sql = <<<SQL
select h.name as hotel_name, h.address, tmp.price_one, tmp.price_common, t.name room_name, r.name from (
	select 
		distinct on (r.hotel_id) hotel_id, 
		first_value(room_id) over (partition by hotel_id, room_id order by value) room_id,
		sum(value) over(partition by hotel_id, room_id, tariff_id) as price_common , 
		min(value) over (partition by hotel_id, room_id, tariff_id order by value) as price_one, 
		first_value(tariff_id) over (partition by hotel_id, room_id, tariff_id order by value) as tariff_id 
	from 
		rooms_prices rp 
	join 
		rooms r on r.id = rp.room_id 
	where 
		room_id in (select distinct room_id from rooms_avl ra where :date_from >= date_from  and :date_to <= date_to and count > 0) 	
		and
		"date" between :date_from and :date_to
		and guest_count = :guest_count
) tmp 
join rooms r on r.id = tmp.room_id
join tariffs t on t.id = tmp.tariff_id
join hotels h on h.id = t.hotel_id
order by price_one ASC
SQL;
        $data = \Yii::$app->db->createCommand($sql)
            ->bindValue(':date_from', $params['date_from'])
            ->bindValue(':date_to', $params['date_to'])
            ->bindValue(':guest_count', $params['guest_count'])
            ->queryAll();
        $end = microtime(true) * 1000;
        \Yii::info("request time " . ($end - $start), 'app');
        return $this->asJson($data);
    }
}
