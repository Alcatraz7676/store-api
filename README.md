# store-api
## Установка
Прописать в config/database.php имя хоста, название бд, юзера и пароль.
## Использование
### Order-item
* Добавление товара к заказу

/order-item/create.php

body:
```javascript
{
  “order_id”: 505,
  “item_id”: 123,
  “quantity”: 3
}
```

* Получение всех товаров во всех заказах

/order-item/read.php

* Удаление товара из заказа

/order-item/delete.php

body: 
```javascript
{
  “order_item_id”: 1
}
```

* Изменение кол-ва товара в заказе

/order-item/update.php

body:
```javascript
{
  “order_item_id”: 1,
  “quantity”: 3
}
```

### Order

* Создание заказа

/order/create.php

body 
```javascript
{
  “promocode”: “ABCDEFG123”,
  “address”: “адрес”,
  “date”: “2019-10-31”
}
```

* Получение всех заказов

/order/read.php

* Удаление заказа

/order/delete.php

body:
```javascript
{
  “id”: 3
}
```

* Изменение заказа

/order/update.php

body 
```javascript
{
  “id”: 3,
  “promocode”: “ABCDEFG123”,
  “address”: “адрес”,
  “date”: “2019-10-31”
}
```

### Item

* Добавление товара в магазин

/item/create.php

body:
```javascript
{
  “id”: 124,
  “discount”: 20
}
```

* Получение всех товаров

/item/read.php

* Изменение скидки на товар

/item/update.php

body:
```javascript
{
  “id”: 124,
  “discount”: 20
}
```

* Удаление товара

/item/delete.php

body:
```javascript
{
  “id”: 124
}
```

### Бизнес-методы

* Получение товаров в заказе

/order-item/search.php?order_id=5

* Поиск заказа

/order/search.php?promocode=ABCDEFG123&address=адрес&date=2019-10-31

* Поиск скидок на товары
/order/search.php?from=30&to=70
