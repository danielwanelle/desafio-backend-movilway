<?php

namespace App\Modules;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Products module
 *
 * @package App\Modules
 */
class ProductsModule
{
    /**
     * Products request URL
     *
     * @var string
     */
    private const REQUEST_URL = 'https://api.redeconekta.com.br/mockprodutos/produtos';

    /**
     * Make a request to get products
     *
     * @return Response
     */
    private static function _makeRequest() : Response
    {
        return Http::get(self::REQUEST_URL);
    }

    /**
     * Get products
     *
     * @return array
     */
    public static function getProducts()
    {
        try {
            $productsRequest = self::_makeRequest();

            return self::_parseProducts($productsRequest);
        } catch (\Exception $e) {
            throw new \Exception('Failed to get products');
        }
    }

    /**
     * get selected products
     *
     * @param array $productsIds Products ids
     *
     * @return array
     */
    public static function getSelectedProducts(array $productsIds) : array
    {
        $products = self::getProducts();

        $selectedProducts = [];

        foreach ($products as $product) {
            if (in_array($product['id'], $productsIds)) {
                $selectedProducts[] = $product;
            }
        }

        if (count($selectedProducts) !== count($productsIds)) {
            throw new \Exception('Some products were not found');
        }

        return $selectedProducts;
    }

    /**
     * Get product by id
     *
     * @param integer $id Product id
     *
     * @return void
     */
    public static function getProductById(int $id)
    {
        try {
            $products = self::getProducts();

            foreach ($products as $product) {
                if ($product['id'] === $id) {
                    return $product;
                }
            }

            throw new \Exception('Product not found');
        } catch (\Exception $e) {
            throw new \Exception('Failed to get product');
        }
    }

    /**
     * Parse products
     *
     * @param  Response $productsRequest Products request
     *
     * @return array
     */
    private static function _parseProducts(Response $productsRequest) : array
    {
        $products = [];

        foreach ($productsRequest->json() as $product) {
            $products[] = [
                'id' => $product['id'],
                'name' => $product['nome'],
                'price' => $product['preco'],
            ];
        }

        return $products;
    }

    /**
     * Calculate total
     *
     * @param array $products Products
     *
     * @return float
     */
    public static function calculateTotal(array $products) : float
    {
        $total = 0;

        foreach ($products as $product) {
            $total += $product['valor'];
        }

        return $total;
    }
}