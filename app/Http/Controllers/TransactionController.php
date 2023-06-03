<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Product;
use Carbon\Carbon;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validRequest = $request->hasAny(['product_id', 'customer_id', 'quantity']);
        if ($validRequest){
            $product = Product::find($request->input('product_id'));
            if($product->stock >= 0){
                $transaction = Transaction::create([
                    'product_id' => $request->input('product_id'),
                    'customer_id' => $request->input('customer_id'),
                    'total' => $product->price * $request->input('quantity'),
                    'quantity' => $request->input('quantity'),
                    'product_stock' => $product->stock,
                    'product_name' => $product->name,
                    'date_transaction' => Carbon::now()->format('d-m-Y')
                ]);
                $count = $product->stock - $transaction->quantity;
                if($count <= 0){
                    return response()->json('product stock mines');
                }else{
                    $product->update(['stock' => $count]);
                    $resultResponse = [
                        'data' => $transaction,
                        'message' => 'success created data',
                        'status' => 200
    
                    ];
                    return response()->json($resultResponse);
                }
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
        if($request->hasAny(['search_product'])){
            $searchProduct = $request->input('search_product');
            $transactions = Transaction::where('product_name','ilike','%'.$searchProduct.'%')
            ->orderBy('product_name','desc')
            ->get();
        }else if($request->hasAny(['order_by'])){
            $orderBy = $request->has('order_by') ? $request->input('order_by') : 'date_transaction';
            $sortBy = $request->has('sort_by') ? $request->input('sort_by') : 'desc';
            $transactions = Transaction::orderBy($orderBy,$sortBy)->get();
        }else{
            $transactions = Transaction::all();
        }

        foreach ($transactions as $key => $value) 
        {
            array_push( $data, 
                [
                    "id" => $value->id,
                    "product_name" => $value->product_name,
                    "stock" => $value->product_stock,
                    "quantity" => $value->quantity,
                    "date_transaction" => $value->date_transaction,
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
