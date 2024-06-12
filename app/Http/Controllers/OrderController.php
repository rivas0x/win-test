<?php

namespace App\Http\Controllers;

use App\Models\Order;
use GuzzleHttp\Client;

class OrderController extends Controller
{
    public function show(int $id) // Requerimiento (A)
    {
        try {
            $client = new Client();

            $user = array(
                'email' => 'jhon@win.investments',
                'password' => 'password',
            );

            $response = $client->request('GET', 'https://rocky-beyond-58885-df0762919b44.herokuapp.com/login?email=' . $user['email'] . '&password=' . $user['password']);

            $loginData = json_decode($response->getBody()->getContents());

            $headers = [
                'Authorization' => 'Bearer ' . $loginData->access_token,
                'Accept' => 'application/json',
            ];

            $response = $client->request('GET', 'https://rocky-beyond-58885-df0762919b44.herokuapp.com/orders/'.$id, [
                'headers' => $headers,
            ]);

            $orderData = json_decode($response->getBody()->getContents())->order;

            if($orderData->status == 'processing'){
                $order = Order::updateOrCreate(['id' => $orderData->id], (array) $orderData);  // Requerimiento (B)
            }

            return array('status' => true, 'order' => $orderData);

        } catch (\Exception $e) {
            return response(array('status' => false, 'message' => $e->getMessage()), 400)->header('Content-Type', 'application/json');
        }
    }
}
