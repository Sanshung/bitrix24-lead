<?
AddEventHandler("sale", "OnOrderAdd", "CreateLead");

function CreateLead($id , $arFields) {
    Log::fullclear();
    Log::writeArray($arFields);

    $address = '';
    \Bitrix\Main\Loader::includeModule('sale');
    $city = \Bitrix\Sale\Location\LocationTable::getList(array('filter' => array('CODE' => $arFields['ORDER_PROP'][6]), 'select' => array('*', 'NAME_RU' => 'NAME.NAME')))->fetch();
    $address = 'город '.$city['NAME_RU'].', ул '.$arFields['ORDER_PROP'][10].', дом '.$arFields['ORDER_PROP'][11].', строение '.$arFields['ORDER_PROP'][12]
        .', квартира '.$arFields['ORDER_PROP'][13];
    $postData = array(
        'TITLE' => 'Создан заказ '.$id.'. '.$arFields['ORDER_PROP'][1],
        'NAME' => $arFields['ORDER_PROP'][1],
        'EMAIL_OTHER' => $arFields['ORDER_PROP'][2],
        'PHONE_OTHER' => $arFields['ORDER_PROP'][3],
        'ADDRESS' => $address,
        'SOURCE_ID' => 'WEB',
        'OPPORTUNITY' => $arFields['PRICE'],
        'CURRENCY_ID' => $arFields['CURRENCY']
    );
    bitrix24Lead($postData);

}
?>
