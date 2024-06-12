<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Order;
use GuzzleHttp\Client;

use Barryvdh\DomPDF\Facade\Pdf;

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

            $order = Order::updateOrCreate(['id' => $orderData->id], (array) $orderData);  // Requerimiento (F)

            return array('status' => true, 'order' => $orderData);

        } catch (\Exception $e) {
            return response(array('status' => false, 'message' => $e->getMessage()), 400)->header('Content-Type', 'application/json');
        }
    }

    public function index(FilterRequest $request) // Requerimiento (C)
    {
        try {
            $orders = Order::query();

            if(count($request->all()) > 0){
                if(isset($request->status)){
                    $orders->orderBy('status', $request->status);
                }
                if(isset($request->group_id)){
                    $orders->orderBy('group_id', $request->group_id);
                }
                if(isset($request->amount)){
                    $orders->orderBy('amount', $request->amount);
                }
            }

            $orders = $orders->paginate();

            return array('status' => true, 'orders' => $orders);

        } catch (\Exception $e) {
            return response(array('status' => false, 'message' => $e->getMessage()), 400)->header('Content-Type', 'application/json');
        }
    }

    public function totals() // Requerimiento (D)
    {
        $orders = Order::selectRaw('COUNT(id) as count, SUM(amount) as total')->get();

        return array('status' => true, 'orders' => current($orders->toArray()));
    }

    public function pdf() // Requerimiento (E)
    {
        $orders = Order::all();

        $pdf = Pdf::loadView('pdf', array('orders' => $orders));
        return $pdf->stream();
    }
}
