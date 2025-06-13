<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Перелік ринків для парсингу
    |--------------------------------------------------------------------------
    | Ключ — slug ринку (наприклад, 'atb'), значення — базова URL для каталогу
    | Якщо потрібно парсити кілька категорій, можна вказати масив URL
    */

    'markets' => [
        'atb' => [
            // Приклад: сторінка категорії "Овочі та фрукти"
            '287-ovochi-ta-frukti',
            '285-bakaliya',
            '292-alkogol-i-tyutyun',
            '294-napoi-bezalkogol-ni',
            '299-konditers-ki-virobi',
            '353-riba-i-moreprodukti',
            '325-khlibobulochni-virobi',
            '322-zamorozheni-produkti',
            '360-kovbasa-i-m-yasni-delikatesi',
            '339-dityache-kharchuvannya',
            '415-yapons-ka-kukhnya',

            // За потреби додавайте інші URL-адреси категорій:
            // 'https://www.atbmarket.com/catalog/288-miaso-ta-gostyna',
            // 'https://www.atbmarket.com/catalog/289-molochni-produkty',
        ],

        // Якщо додаватимете інші ринки, аналогічно додавайте сюди:
        // 'silpo' => [
        //     'https://silpo.ua/…/moloko',
        //     ...
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CSS-селектори для різних сайтів
    |--------------------------------------------------------------------------
    | Якщо структура HTML різниться — можна рознести селектори за ринком.
    */
    'selectors' => [
        'atb' => [
            // Кореневий контейнер товару (один елемент на сторінці)
            'item_container' => '.catalog-item.js-product-container',

            // Селектор для назви
            'name'           => '.catalog-item__title a',

            // Селектор для блоку з ціною (вартість за вагу/шт. у <data value="…">)
            'price_weighted' => '.catalog-item__product-price.product-price--weight data',
            'price_unit'     => '.catalog-item__product-price.product-price--unit data',

            // Атрибут, у якому лежить числове значення ціни
            'price_value_attr' => 'value',

            // Атрибут з ідентифікатором товару (data-productid)
            'product_id_attr'  => 'data-productid',
        ],

        // Аналогічно, якщо додаєте інші ринки:
        // 'silpo' => [ ... ],
    ],
];
