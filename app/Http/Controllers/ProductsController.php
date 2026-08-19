<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ProductsController extends Controller
{
    public function create()
    {
        return view('products.create');
    }

    public function saveProduct(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'price' => 'required|numeric',
            'is_negotiable' => 'nullable',
            'login_source' => 'required',
            'login_username' => 'required',
            'login_password' => 'required',
            'media.*' =>
            [
                'required',
                File::types(['wav', 'mp4', 'jpg', 'png', 'jpeg'])
                    ->max('100mb'),
            ],
            'mythic_items' => 'nullable|array',
        ]);


        DB::beginTransaction();

        $product =    auth()->user()->products()->create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'is_negotiable' => $request->is_negotiable ? true : false,
            'login_source' => $request->login_source,
            'login_username' => $request->login_username,
            'login_password' => $request->login_password,
        ]);

        foreach ($request->media as $file) {
            $path =   $file->storePublicly('media', 's3');

            $url = Storage::disk('s3')->url($path);


            $product->media()->create([
                'path' => $url,
                'type' => $file->getMimeType(),
            ]);
        }

        if ($request->has('mythic_items')) {
            $product->mythicItems()->sync($request->mythic_items);
        }

        $product->setStatus(Product::STATUS_PENDING, "Product is awaiting approval");

        DB::commit();



        session()->flash('success', 'Product created successfully.');

        return redirect()->route('profile');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data =    $request->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'price' => 'required|numeric',
            'is_negotiable' => 'boolean',
            'login_source' => [Rule::requiredIf($product->status !== Product::STATUS_ACTIVE)],
            'login_username' => [Rule::requiredIf($product->status !== Product::STATUS_ACTIVE)],
            'login_password' => [Rule::requiredIf($product->status !== Product::STATUS_ACTIVE)],
            'media.*' => 'required|file',
            'mythic_items' => 'nullable|array',

        ]);





        DB::beginTransaction();

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'is_negotiable' => $request->is_negotiable ? true : false,

        ];

        if ($request->login_source && $request->login_username && $request->login_password) {
            $updateData['login_source'] = $request->login_source;
            $updateData['login_username'] = $request->login_username;
            $updateData['login_password'] = $request->login_password;
        }

        $product->update($updateData);

        if ($request->has('media')) {

            $product->media()->delete();

            foreach ($request->media as $file) {
                $path =   $file->storePublicly('media', 'public');

                $product->media()->create([
                    'path' => $path,
                    'type' => $file->getMimeType(),
                ]);
            }
        }

        if ($request->has('mythic_items')) {
            $product->mythicItems()->sync($request->mythic_items);
        }


        DB::commit();


        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.products');
        }

        if ($product->status !== Product::STATUS_ACTIVE) {
            $product->setStatus(Product::STATUS_PENDING, "Product is awaiting approval");
        }


        session()->flash('success', 'Product created successfully.');

        return redirect()->route('profile');
    }
}
