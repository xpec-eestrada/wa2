<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Xpectrum\Wa2\Api;


/**
 * Wa2Interface
 */
interface Wa2Interface{
    /**
     * Return el stock de un sku.
     *
     * @api
     * @param string $sku Es el identificador unico del producto.
     * @return string Obtiene el stock del producto en el inventario del e-commerce .
     */
    public function getStock($sku);

    /**
     * Return el stock de un sku.
     *
     * @api
     * @param string $sku Es el identificador unico del producto.
     * @param int $stock La cantidad del pruducto en el inventario.
     * @return string Se obtiene una respuesta JSON.
     */
    public function setStock($sku,$stock);

    /**
     * Actualizar varios Stocks
     *
     * @api
     * @param string $param JSON con la relación de skus y stocks a modificar.
     * @return string Se obtiene una respuesta de la transacción
     */
    public function setStocks($param);
}