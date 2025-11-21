<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBomLine;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $query = Product::query();

        if($r->filled('type')){
            $query->where('type',$r->type);
        }

        if($r->filled('status')){
            $query->where('is_active',$r->status==='active');
        }

        if($r->filled('search')){
            $s = $r->search;
            $query->where(function($qq) use ($s){
                $qq->where('name','like',"%$s%")
                   ->orWhere('sku','like',"%$s%");
            });
        }

        $products = $query->latest('id')->paginate(15)->withQueryString();
        return view('admin/products/index', compact('products'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get(['id','name','base_unit','is_active']);
        $categories = Category::where('is_active',true)->orderBy('sort_order')->get(['id','name']);
        return view('admin/products/create', compact('items','categories'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'=>'required|string|max:255|unique:products,name',
            'sku'=>'nullable|string|max:64|unique:products,sku',
            'type'=>'required|in:simple,composite',
            'selling_price'=>'required|numeric|min:0',

            'category_id'=>'nullable|exists:categories,id',
            'image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'linked_item_id'=>'nullable|exists:items,id|required_if:type,simple',
            'per_sale_qty'=>'nullable|numeric|gt:0|required_if:type,simple',

            'bom.item_id.*'=>'nullable|exists:items,id',
            'bom.qty.*'=>'nullable|numeric|gt:0',
        ]);

        $imagePath = null;
        if($r->hasFile('image')){
            $imagePath = $r->file('image')->store('products',config('filesystems.default'));
        }

        $product = Product::create([
            'name'=>$data['name'],
            'sku'=>$data['sku'] ?? null,
            'type'=>$data['type'],
            'category_id'=>$data['category_id'] ?? null,
            'selling_price'=>$data['selling_price'],
            'is_active'=>$r->has('is_active'),
            'image_path'=>$imagePath,
            'linked_item_id'=>$data['type']==='simple' ? $data['linked_item_id'] : null,
            'per_sale_qty'=>$data['type']==='simple' ? $data['per_sale_qty'] : null,
        ]);

        if($product->isComposite() && $r->has('bom.item_id')){
            $this->syncBom($product,$r);
        }

        if($product->isComposite() && $product->is_active && $product->bomLines()->count()===0){
            $product->update(['is_active'=>false]);
            return redirect()->route('admin.products.show',$product)
                ->with('error','Composite product deactivated: add at least one BOM line first.');
        }

        return redirect()->route('admin.products.show',$product)->with('ok','Product created');
    }

    public function show(Product $product)
    {
        $product->load('linkedItem','bomLines.item','category');
        $estimatedCost = $product->estimatedCost();
        return view('admin/products/show', compact('product','estimatedCost'));
    }

    public function edit(Product $product)
    {
        $product->load('linkedItem','bomLines.item','category');
        $items = Item::orderBy('name')->get(['id','name','base_unit','is_active']);
        $categories = Category::where('is_active',true)->orderBy('sort_order')->get(['id','name']);
        $estimatedCost = $product->estimatedCost();

        return view('admin/products/edit', compact('product','items','categories','estimatedCost'));
    }

    public function update(Request $r, Product $product)
    {
        $data = $r->validate([
            'name'=>'required|string|max:255|unique:products,name,' . $product->id,
            'sku'=>'nullable|string|max:64|unique:products,sku,' . $product->id,
            'type'=>'required|in:simple,composite',
            'selling_price'=>'required|numeric|min:0',

            'category_id'=>'nullable|exists:categories,id',
            'image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'linked_item_id'=>'nullable|exists:items,id|required_if:type,simple',
            'per_sale_qty'=>'nullable|numeric|gt:0|required_if:type,simple',

            'bom.item_id.*'=>'nullable|exists:items,id',
            'bom.qty.*'=>'nullable|numeric|gt:0',
        ]);

        $imagePath = $product->image_path;

        if($r->hasFile('image')){
            if($imagePath){
                Storage::delete($imagePath);
            }
            $imagePath = $r->file('image')->store('products',config('filesystems.default'));
        }

        $product->update([
            'name'=>$data['name'],
            'sku'=>$data['sku'] ?? null,
            'type'=>$data['type'],
            'category_id'=>$data['category_id'] ?? null,
            'selling_price'=>$data['selling_price'],
            'is_active'=>$r->has('is_active'),
            'image_path'=>$imagePath,
            'linked_item_id'=>$data['type']==='simple' ? $data['linked_item_id'] : null,
            'per_sale_qty'=>$data['type']==='simple' ? $data['per_sale_qty'] : null,
        ]);

        if($product->isComposite()){
            $this->syncBom($product,$r);
        } else {
            $product->bomLines()->delete();
        }

        if($product->isComposite() && $product->is_active && $product->bomLines()->count()===0){
            $product->update(['is_active'=>false]);
            return back()->with('error','Composite product requires at least one BOM line. Deactivated.');
        }

        if($product->isSimple() && $product->is_active && optional($product->linkedItem)->is_active===false){
            $product->update(['is_active'=>false]);
            return back()->with('error','Linked Item is inactive. Product deactivated.');
        }

        return redirect()->route('admin.products.show',$product)->with('ok','Product updated');
    }

    public function toggle(Product $product)
    {
        if(!$product->is_active){
            if($product->isComposite() && $product->bomLines()->count()===0){
                return back()->with('error','Cannot activate: composite product has no BOM lines.');
            }

            if($product->isSimple() && (!$product->linkedItem || !$product->per_sale_qty || $product->linkedItem->is_active===false)){
                return back()->with('error','Cannot activate: simple product needs an active linked Item and per_sale_qty.');
            }
        }

        $product->update(['is_active'=>!$product->is_active]);
        return back()->with('ok','Product status updated');
    }

    public function editBom(Product $product)
    {
        abort_unless($product->isComposite(),404);

        $product->load('bomLines.item');
        $items = Item::orderBy('name')->get(['id','name','base_unit','is_active']);
        $estimatedCost = $product->estimatedCost();

        return view('admin/products/bom', compact('product','items','estimatedCost'));
    }

    public function updateBom(Request $r, Product $product)
    {
        abort_unless($product->isComposite(),404);

        $this->syncBom($product,$r);

        if($product->is_active && $product->bomLines()->count()===0){
            $product->update(['is_active'=>false]);
            return back()->with('error','Product deactivated: BOM is empty.');
        }

        return back()->with('ok','BOM updated');
    }

    private function syncBom(Product $product, Request $r)
    {
        $itemIds = $r->input('bom.item_id',[]);
        $qtys = $r->input('bom.qty',[]);

        $rows = [];

        for($i=0; $i<count($itemIds); $i++){
            $iid = $itemIds[$i];
            $q = $qtys[$i] ?? null;

            if($iid && $q && $q>0){
                $rows[] = ['item_id'=>(int)$iid, 'qty'=>(float)$q];
            }
        }

        $merged = [];
        foreach($rows as $row){
            $id = $row['item_id'];

            if(isset($merged[$id])){
                $merged[$id]['qty'] += $row['qty'];
            } else {
                $merged[$id] = $row;
            }
        }

        $rows = array_values($merged);

        $product->bomLines()->delete();

        foreach($rows as $rrow){
            ProductBomLine::create([
                'product_id'=>$product->id,
                'item_id'=>$rrow['item_id'],
                'qty'=>$rrow['qty'],
            ]);
        }
    }
}
