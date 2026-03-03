<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;

class ProductController extends Controller
{
    public function index(Request $request) {
		$search = $request->query('q');
		$products = Product::withCount('audits')
			->when($search, function($q, $search) {
				$q->where('name', 'like', "%$search%")
				->orWhere('sku', 'like', "%$search%");
			})
			->paginate(15);
		return view('products.index', compact('products', 'search'));
	}

	public function store(Request $request) {
		$request->validate([
			'name' => 'required|max:255', 'sku' => 'required|unique:products,sku|max:50',
			'stock' => 'required|integer|min:0', 'price' => 'required|numeric|min:0|max:99999999.99|regex:/^\d{1,10}(\.\d{1,2})?$/'
		]);
		Product::create($request->all());
		return redirect()->route('products.index')->with('success', 'Added');
	}

	public function update(Request $request, Product $product) {
		$request->validate([
			'name' => 'required|max:255', 
			'sku' => 'required|unique:products,sku,'.$product->id,
			'stock' => 'required|integer|min:0|max:2147483647', 'price' => 'required|numeric|min:0|max:999999.99'
		]);
		$product->update($request->all());
		return redirect()->route('products.index')->with('success', 'Updated');
	}

	public function destroy(Product $product) {
		$product->delete();
		return redirect()->route('products.index')->with('success', 'Deleted');
	}

	// CSV Import (Test invalid rows)
	public function import(Request $request) {
		$request->validate(['csv' => 'required|file|mimes:csv,txt']);
		$badRows = [];
		Excel::import(new ProductsImport($badRows), $request->file('csv'));
		return back()->with('badRows', $badRows); // Reports bad rows
	}

	// Sale (Test insufficient stock)
	public function sale(Request $request, Product $product) {
		$request->validate(['quantity' => 'required|integer|min:1']);
		try {
			auth()->check() ? $product->adjustStock(-$request->quantity, auth()->id()) : 
							 $product->adjustStock(-$request->quantity);
			return back()->with('success', 'Sale recorded');
		} catch (\Exception $e) {
			return back()->withErrors(['quantity' => $e->getMessage()]);
		}
	}

	public function create()
	{
		return view('products.create');
	}

	public function edit(Product $product)
	{
		return view('products.edit', compact('product'));
	}



}
