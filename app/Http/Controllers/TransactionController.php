<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Product;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json($transactions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validRequest = $request->hasAny(['product_id', 'customer_id', 'total', 'quantity']);
        if ($validRequest){
            $product = Product::find($request->input('product_id'));
            if($product->stock >= 0){
                $transaction = Transaction::create([
                    'product_id' => $request->input('product_id'),
                    'customer_id' => $request->input('customer_id'),
                    'total' => $request->input('total'),
                    'quantity' => $request->input('quantity'),
                    'product_stock' => $product->stock,
                ]);
                $count = $product->stock - $transaction->quantity;
                $product->update(['stock' => $count]);
                $resultResponse = [
                    'data' => $transaction,
                    'message' => 'success created data',
                    'status' => 200

                ];
                return response()->json($resultResponse);
            }else{
                return response()->json('stock tidak tersedia');
            }
        }else{
            return response()->json('sorry parameter wrong');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::find($id);
        return response()->json($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validRequest = $request->hasAny(['product_id', 'customer_id', 'total', 'quantity']);
        if($validRequest){
            $transaction = Transaction::find($id);
            $transaction->update($request->all());
            return response()->json($transaction);
        }else{
            return response()->json('please check parameters');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::destroy($id);
        return response()->json('success delete');
    }

    public function summary(Request $request){
        $data = [];
        $transactions = Transaction::all();

        foreach ($transactions as $key => $value) 
        {
            array_push( $data, 
                [
                    "id" => $value->id,
                    "product_name" => $value->product->name,
                    "stock" => $value->product_stock,
                    "quantity" => $value->quantity,
                    "date" => $value->created_at,
                    "type" => $value->product->type
                ]);
        }
        $resultResponse = [
            'data' => $data,
            'status' => 200,
            'message' => 'success data'
        ];
        return response()->json($resultResponse);
    }
}
