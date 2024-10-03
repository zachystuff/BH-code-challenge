<?php

use AS\infra\page;

class home extends page {

    protected function request() {      
        $this->set_template('home');
        if (!empty($_COOKIE['cart'])) {
            $this->data['cart'] = json_decode($_COOKIE['cart'], true);
        } else if($this->is_post) {
            $this->data['cart'] = json_decode($_POST['cart'], true);
            setcookie('cart', $_POST['cart'], time() + 3600, '/', '', false, true);
        }
        $this->data['products'] = [
            [
                'img' => 'https://via.placeholder.com/150',
                'name' => 'Product 1',
                'price' => 10,
                'description' => 'This is a description of product 1',
            ],
            [
                'img' => 'https://via.placeholder.com/150',
                'name' => 'Product 2',
                'price' => 20,
                'description' => 'This is a description of product 2',
            ],
            [
                'img' => 'https://via.placeholder.com/150',
                'name' => 'Product 3',
                'price' => 30,
                'description' => 'This is a description of product 3',
            ],
        ];

        if (!empty($_GET['error'])) {
            $this->data['error'] = strip_tags($_GET['error']);
        } else {
            $this->data['error'] = 'Product not found';
        }
    }
}