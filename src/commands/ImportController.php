<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Hotel;
use app\models\Tariff;
use app\models\Room;
use app\models\RoomAvl;
use app\models\RoomPrice;

class ImportController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $file the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($file = 'hotels-list.xml')
    {

        $path = __DIR__ . '/../_mock/' . $file;
        $xmlData = file_get_contents($path);
        $document = new \DOMDocument("1.0", "utf-8");
        $document->loadXML($xmlData);
        $hotels = $document->getElementsByTagName('hotel');
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach($hotels as $h) {
                $hotelId = $h->attributes->getNamedItem('id')->value;
                [,$name,,$address,,$tariffs,,$rooms] = $h->childNodes;
                $hotelName = $name->nodeValue;
                $hotelAddress = $address->nodeValue;
    
                $hModel = new Hotel([
                    'vendor_id' => $hotelId,
                    'name' => $hotelName,
                    'address' => $hotelAddress
                ]);
                $hModel->save();
                $tariffsMap = [];
                foreach($tariffs->childNodes as $t) {
                    if ($t->attributes) {
                        $tariffId = $t->attributes->getNamedItem('id')->value;
                        [, $name, , $hasBreakfast] = $t->childNodes;
                        $tariffName = $name->nodeValue;
                        $tariffHasBreakfast = trim($hasBreakfast->nodeValue) === 'true' ? true : false;
                        $tModel = new Tariff([
                            'hotel_id' => $hModel->id,
                            'vendor_id' => $tariffId,
                            'name' => $tariffName,
                            'hasBreakfast' => $tariffHasBreakfast ? 1 : 0
                        ]);
                        $tModel->save();
                        $tariffsMap[$tariffId] = $tModel->id;
                    }
                }
                foreach($rooms->childNodes as $r) {
                    if ($r->attributes) {
                        $roomId = $r->attributes->getNamedItem('id')->value;
                        [, $name,, $availability,, $rates] = $r->childNodes;
                        $roomName = $name->nodeValue;
                        $rModel = new Room([
                            'vendor_id' => $roomId,
                            'hotel_id' => $hModel->id,
                            'name' => $roomName,
                        ]);
                        $rModel->save();
                        $prevDate = null;
                        $prevCount = null;
                        $startDate = null;
                        $aDate = null;
                        foreach($availability->childNodes as $a) {
                            if ($a->attributes) {
                                $aDate = $a->attributes->getNamedItem('date')->value;
                                $aDate = new \DateTimeImmutable($aDate);
                                $aCount = $a->nodeValue;
                                if (!$prevDate) {
                                    $prevDate = $aDate;
                                }
                                if (!$startDate) {
                                    $startDate = $aDate;
                                }
                                if (!$prevCount) {
                                    $prevCount = $aCount;
                                }
                                if ($aDate->diff($prevDate)->d <= 1 && $prevCount === $aCount) {
                                    $prevDate = $aDate;
                                    continue;
                                }
                                $aModel = new RoomAvl([
                                    'room_id' => $rModel->id,
                                    'date_from' => $startDate->format('Y-m-d'),
                                    'date_to' => $prevDate->format('Y-m-d'),
                                    'count' => $prevCount,
                                ]);
                                $aModel->save();
                                $startDate = $aDate;
                                $prevDate = $aDate;
                                $prevCount = $aCount;
                            }
                        }
                        $aModel = new RoomAvl([
                            'room_id' => $rModel->id,
                            'date_from' => $startDate->format('Y-m-d'),
                            'date_to' => $aDate->format('Y-m-d'),
                            'count' => $prevCount,
                        ]);
                        $aModel->save();
                        $prevDate = null;
                        $prevPrice = null;
                        $startDate = null;
                        $priceDate = null;
                        foreach($rates->childNodes as $rate) {
                            if ($rate->attributes) {
                                $rateRoomId = $rate->attributes->getNamedItem('roomId')->value;
                                $rateTariffId = $rate->attributes->getNamedItem('tariffId')->value;
                                $rateGuestCount = $rate->attributes->getNamedItem('guestCount')->value;
                                [, $prices] = $rate->childNodes;
                                if ($prices->childNodes) {
                                    $bulk = [];
                                    foreach($prices->childNodes as $price) {
                                        
                                        if ($price->attributes) {
                                            $priceDate = $price->attributes->getNamedItem('date')->value;
                                            $priceValue = (float) $price->nodeValue;
                                            $bulk[] = [
                                                'room_id' => $rModel->id,
                                                'tariff_id' => $tariffsMap[$rateTariffId],
                                                'date_from' => $priceDate,
                                                'guest_count' => $rateGuestCount,
                                                'value' => $priceValue,
                                            ];
                                            if (count($bulk) >= 50) {
                                                \Yii::$app->db->createCommand()->batchInsert('rooms_prices', ['room_id', 'tariff_id', 'date_from','guest_count', 'value'], $bulk)->execute();
                                                $bulk = [];
                                            }
                                            // $priceDate = $price->attributes->getNamedItem('date')->value;
                                            // $priceValue = (float) $price->nodeValue;
                                            // $pModel = new RoomPrice([
                                            //     'room_id' => $rModel->id,
                                            //     'tariff_id' => $tariffsMap[$rateTariffId],
                                            //     'date_from' => $priceDate,
                                            //     'guest_count' => $rateGuestCount,
                                            //     'value' => $priceValue,
                                            // ]);
                                            // $pModel->save();
                                            // unset($pModel);
                                            // $priceDate = new \DateTimeImmutable($priceDate);
                                            // if (!$prevDate) {
                                            //     $prevDate = $priceDate;
                                            // }
                                            // if (!$startDate) {
                                            //     $startDate = $priceDate;
                                            // }
                                            // if (!$prevPrice) {
                                            //     $prevPrice = $priceValue;
                                            // }
                                            // if ($priceDate->diff($prevDate)->d <= 1 && $prevPrice === $priceValue) {
                                            //     $prevDate = $priceDate;
                                            //     continue;
                                            // }
                                            // $pModel = new RoomPrice([
                                            //     'room_id' => $rModel->id,
                                            //     'tariff_id' => $tariffsMap[$rateTariffId],
                                            //     'date_from' => $startDate->format('Y-m-d'),
                                            //     'date_to' => $prevDate->format('Y-m-d'),
                                            //     'guest_count' => $rateGuestCount,
                                            //     'value' => $priceValue,
                                            // ]);
                                            // $pModel->save();
                                            // $startDate = $priceDate;
                                            // $prevDate = $priceDate;
                                            // $prevPrice = $priceValue;
                                        }
                                    }
                                    \Yii::$app->db->createCommand()->batchInsert('rooms_prices', ['room_id', 'tariff_id', 'date_from','guest_count', 'value'], $bulk)->execute();
                                    // $pModel = new RoomPrice([
                                    //     'room_id' => $rModel->id,
                                    //     'tariff_id' => $tariffsMap[$rateTariffId],
                                    //     'date_from' => $startDate->format('Y-m-d'),
                                    //     'date_to' => $priceDate->format('Y-m-d'),
                                    //     'guest_count' => $rateGuestCount,
                                    //     'value' => $priceValue,
                                    // ]);
                                    // $pModel->save();
                                }
                            }
                        }
                    }
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            print_r($e->getMessage());
            $transaction->rollback();
        }
        
        return ExitCode::OK;
    }
}
