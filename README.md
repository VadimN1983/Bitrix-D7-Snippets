# Bitrix-D7-Snippets
Сниппеты для парсеров каталогов

Для свойства типа список установить, либо добавить значение "с переплетом"
<исходный код >valuePropertyEnum(50, 'c переплетом');

Для SKU с ID 32154 обновить, либо установить стоимость 14.5USD
<исходный код>itemPrice(32154, 14.5, 'USD');

Для SKU с ID 32154 и склада с ID 3 обновить, либо установить остаток 500
<?itemStore(32154, 3, 500);?>
