<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductCrudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', [
            'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::latest()->get();

        return view('products.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|numeric|exists:categories,id',
            'price' => 'required|numeric|min:2000',
            'stok' => 'required|numeric|min:1'
        ]);

        Product::create($validateData);

        return redirect()->route('products.index')->with('success', 'Product has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Category::latest()->get();

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $product = Product::find($id);

        $validateData = $request->validate([
            'name' => 'max:255',
            'category_id' => 'required|numeric|exists:categories,id',
            'price' => 'nullable|numeric|min:2000',
            'stok' => 'nullable|numeric|min:1'
        ]);

        if ($request->name == "") {
            $validateData['name'] = $product->name;
        }

        if ($request->price == null) {
            $validateData['price'] = $product->price;
        }

        if ($request->stok == null) {
            $validateData['stok'] = $product->stok;
        }

        $product->update($validateData);

        return redirect()->route('products.index')->with('success', 'Product has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product has been deleted');
    }
}
