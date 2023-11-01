@extends('admin.layouts.admin')
@section('title', 'Edit products')

@section('content')
    <!-- Content Row -->
    <div class="row">

        <div class="col-xl-12 col-md-12 p-4 mb-4 bg-white">
            <div class="mb-4 text-center text-md-right">
                <h5 class="font-weight-bold">ویرایش محصول {{ $product->name }}</h5>
            </div>
            <hr>
            @include('admin.sections.errors')
            <form action="{{ route('admin.products.update', ['product' => $product->id]) }}" method="POST">
                @csrf
                @method('put')
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="name">نام</label>
                        <input class="form-control" id="name" name="name" type="text"
                            value="{{ $product->name }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="brand_id">برند</label>
                        <select id="brandSelect" name="brand_id" class="form-control" data-live-search="true">
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ $product->brand->id === $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="is_active">وضعیت</label>
                        <select class="form-control" id="is_active" name="is_active">
                            <option value="1" {{ $brand->getRawOriginal('is_active') ? 'selected' : '' }}>فعال</option>
                            <option value="0" {{ $brand->getRawOriginal('is_active') ? '' : 'selected' }}>غیرفعال
                            </option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="tag_ids">تگ</label>
                        <select id="tagSelect" name="tag_ids[]" class="form-control" multiple data-live-search="true">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    {{ in_array($tag->id, $product->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="description">توضیحات</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
                    </div>

                    {{-- Delivery Section --}}

                    <div class="col-md-12">
                        <hr>
                        <p>هزینه ارسال :</p>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="delivery_amount">هزینه ارسال</label>
                        <input class="form-control" id="delivery_amount" name="delivery_amount" type="text"
                            value="{{ $product->delivery_amount }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="delivery_amount_per_product">هزینه ارسال به ازای محصول اضافی</label>
                        <input class="form-control" id="delivery_amount_per_product" name="delivery_amount_per_product"
                            type="text" value="{{ $product->delivery_amount_per_product }}">
                    </div>

                    {{-- Product attributes and variations --}}

                    <div class="col-md-12">
                        <hr>
                        <p>ویژگی ها :</p>
                    </div>

                    @foreach ($productAttributes as $productAttribute)
                        <div class="form-group col-md-3">
                            <label for="">{{ $productAttribute->attribute->name }}</label>
                            <input class="form-control" type="text" name="attribute_values[{{ $productAttribute->id }}]"
                                value="{{ $productAttribute->value }}">
                        </div>
                    @endforeach

                    @foreach ($productVariations as $productVariation)
                        <div class="col-md-12">
                            <hr>
                            <div class="d-flex">
                                <p class="mb-0"> قیمت و موجودی برای متغیر ( {{ $productVariation->value }} ) : </p>
                                <p class="mb-0 mr-3">
                                    <button class="btn btn-sm btn-primary" type="button" data-toggle="collapse"
                                        data-target="#collapse-{{ $productVariation->id }}">
                                        نمایش
                                    </button>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="collapse mt-2" id="collapse-{{ $productVariation->id }}">
                                <div class="card card-body">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label> قیمت </label>
                                            <input type="text" class="form-control"
                                                name="variation_values[{{ $productVariation->id }}][price]"
                                                value="{{ $productVariation->price }}">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label> تعداد </label>
                                            <input type="text" class="form-control"
                                                name="variation_values[{{ $productVariation->id }}][quantity]"
                                                value="{{ $productVariation->quantity }}">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label> sku </label>
                                            <input type="text" class="form-control"
                                                name="variation_values[{{ $productVariation->id }}][sku]"
                                                value="{{ $productVariation->sku }}">
                                        </div>

                                        {{-- Sale Section --}}
                                        <div class="col-md-12">
                                            <p> حراج : </p>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label> قیمت حراجی </label>
                                            <input type="text"
                                                name="variation_values[{{ $productVariation->id }}][sale_price]"
                                                value="{{ $productVariation->sale_price }}" class="form-control">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label> تاریخ شروع حراجی </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend order-2">
                                                    <span class="input-group-text" id="variationDateOnSaleFrom-{{ $productVariation->id }}">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" id="variationInputDateOnSaleFrom-{{ $productVariation->id }}"
                                                    name="variation_values[{{ $productVariation->id }}][date_on_sale_from]"
                                                    value="{{ $productVariation->date_on_sale_from ? verta($productVariation->date_on_sale_from) : null }}">
                                                    
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label> تاریخ پایان حراجی </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend order-2">
                                                    <span class="input-group-text" id="variationDateOnSaleTo-{{ $productVariation->id }}">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" id="variationInputDateOnSaleTo-{{ $productVariation->id }}"
                                                    name="variation_values[{{ $productVariation->id }}][date_on_sale_to]" 
                                                    value="{{ $productVariation->date_on_sale_to ? verta($productVariation->date_on_sale_to) : null }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-outline-primary mt-5" type="submit">ویرایش</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-dark mt-5 mr-3">بازگشت</a>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#brandSelect').selectpicker({
            'title': 'انتخاب برند'
        });
        $('#tagSelect').selectpicker({
            'title': 'انتخاب تگ'
        });

        let variations = @json($productVariations);
        variations.forEach(variation => {
            $(`#variationDateOnSaleTo-${variation.id}`).MdPersianDateTimePicker({
                targetTextSelector: `#variationInputDateOnSaleTo-${variation.id}`,
                englishNumber: true,
                enableTimePicker: true,
                textFormat: 'yyyy-MM-dd HH:mm:ss',
            });
    
            $(`#variationDateOnSaleFrom-${variation.id}`).MdPersianDateTimePicker({
                targetTextSelector: `#variationInputDateOnSaleFrom-${variation.id}`,
                englishNumber: true,
                enableTimePicker: true,
                textFormat: 'yyyy-MM-dd HH:mm:ss',
            });
        });
    </script>
@endsection
