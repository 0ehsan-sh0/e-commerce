@extends('admin.layouts.admin')
@section('title', 'Edit product category')

@section('content')
    <!-- Content Row -->
    <div class="row">

        <div class="col-xl-12 col-md-12 p-4 mb-4 bg-white">
            <div class="mb-4 text-center text-md-right">
                <h5 class="font-weight-bold">ویرایش دسته بندی و ویژگی های محصول : {{ $product->name }}</h5>
            </div>
            @include('admin.sections.errors')
            <form action="{{ route('admin.products.category.update', ['product' => $product->id]) }}" method="POST">
                @method('PUT')
                @csrf

                <div class="form-row">
                    {{-- Category and Attributes Section --}}

                    <div class="col-md-12">
                        <hr>
                        <p>دسته بندی و ویژگی ها :</p>
                    </div>

                    <div class="col-md-12">
                        <div class="row justify-content-center">
                            <div class="form-group col-md-3">
                                <label for="category_id">دسته بندی</label>
                                <select id="categorySelect" name="category_id" class="form-control" data-live-search="true">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"  {{ $category->id === $product->category_id ? 'selected' : ''}}>{{ $category->name }} -
                                            {{ $category->parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="attributesContainer">
                        <div class="row" id="attributes"></div>
                        <div class="col-md-12">
                            <hr>
                            <p>افزودن قیمت و موجودی برای متغیر <span class="font-weight-bold" id="variationName"></span> :
                            </p>
                        </div>
                        <div id="czContainer">
                            <div id="first">
                                <div class="recordset">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="value">نام</label>
                                            <input class="form-control" name="variation_values[value][]" type="text">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="price">قمیت</label>
                                            <input class="form-control" name="variation_values[price][]" type="text">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="quantity">تعداد</label>
                                            <input class="form-control" name="variation_values[quantity][]" type="text">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="sku">شناسه انبار</label>
                                            <input class="form-control" name="variation_values[sku][]" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-outline-primary mt-5" type="submit">ویرایش</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-dark mt-5 mr-3">بازگشت</a>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#categorySelect').selectpicker({
            'title': 'انتخاب دسته بندی'
        });
        $('#attributesContainer').hide();
        $('#categorySelect').on('changed.bs.select', function() {
            let categoryId = $(this).val();

            $.get(`{{ url('/admin-panel/management/category-attributes/${categoryId}') }}`, function(response,
                status) {
                if (status == 'success') {
                    //    console.log(response);

                    $('#attributesContainer').fadeIn();
                    // remove container attributes
                    $('#attributes').find('div').remove();

                    // create attributes input
                    response.attributes.forEach(attribute => {
                        let attributeFormGroup = $('<div/>', {
                            class: 'form-group col-md-3'
                        });
                        attributeFormGroup.append($('<label/>', {
                            for: attribute.name,
                            text: attribute.name
                        }));

                        attributeFormGroup.append($('<input/>', {
                            type: 'text',
                            class: 'form-control',
                            id: attribute.name,
                            name: `attribute_ids[${attribute.id}]`,
                        }));

                        $('#attributes').append(attributeFormGroup);
                    });

                    $('#variationName').text(response.variation.name);
                }
            }).fail(function() {

            });
        });
        $("#czContainer").czMore();
    </script>
@endsection
