<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function removeImage($image)
    {
        if (File::exists(public_path(env('BANNER_IMAGES_UPLOAD_PATH') . $image))) {
            File::delete(public_path(env('BANNER_IMAGES_UPLOAD_PATH') . $image));
        } else {
            return redirect()->back()->withErrors(['imageError' => 'تصویر مورد نظر یافت نشد']);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::latest()->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpg,jpeg,png,svg|max:3072',
            'priority' => 'required|integer',
            'type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $fileNameImage = generateFileName($request->image);
        $request->image->move(public_path(env('BANNER_IMAGES_UPLOAD_PATH')), $fileNameImage);

        Banner::create([
            'image' => $fileNameImage,
            'title' => $request->title,
            'text' => $request->text,
            'priority' => $request->priority,
            'is_active' => $request->is_active,
            'type' => $request->type,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'button_icon' => $request->button_icon,
        ]);

        alert()->success('موفق', 'بنر با موفقیت ثبت شد');
        return redirect()->route('admin.banners.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|mimes:jpg,jpeg,png,svg|max:3072',
            'priority' => 'required|integer',
            'type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        
        if ($request->has('image')) {
            $this->removeImage($banner->image);
            $fileNameImage = generateFileName($request->image);
            $request->image->move(public_path(env('BANNER_IMAGES_UPLOAD_PATH')), $fileNameImage);
        }

        $banner->update([
            'image' => $request->has('image') ? $fileNameImage : $banner->image,
            'title' => $request->title,
            'text' => $request->text,
            'priority' => $request->priority,
            'is_active' => $request->is_active,
            'type' => $request->type,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'button_icon' => $request->button_icon,
        ]);

        alert()->success('موفق', 'بنر با موفقیت ویرایش شد');
        return redirect()->route('admin.banners.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        alert()->success('موفق', 'بنر با موفقیت حذف شد');
        return redirect()->route('admin.banners.index');
    }
}
