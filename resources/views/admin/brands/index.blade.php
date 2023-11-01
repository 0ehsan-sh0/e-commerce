@extends('admin.layouts.admin')
@section('title', 'Brands')
@section('content')
    <div class="row">

        <div class="col-xl-12 col-md-12 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row text-center justify-content-md-between mb-4">
                <h5 class="font-weight-bold mb-3 mb-0">لیست برند ها ({{$brands->total()}})</h5>
                <div>
                    <a href="{{ route('admin.brands.create') }}" class="btn btn-sm btn-outline-primary">ایجاد برند
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $key => $brand)
                            <tr>
                                <th>
                                    {{ $brands->firstItem() + $key}}
                                </th>

                                <th>
                                    {{ $brand->name}}
                                </th>

                                <th>
                                    <span class="{{ $brand->getRawOriginal('is_active') ? 'text-success' : 'text-danger' }}">
                                        {{ $brand->is_active }}
                                    </span>
                                </th>

                                <th>
                                    <a href="{{ route('admin.brands.show', ['brand' => $brand->id]) }}" class="btn btn-sm btn-outline-success">نمایش</a>
                                    <a href="{{ route('admin.brands.edit', ['brand' => $brand->id]) }}" class="btn btn-sm btn-outline-info mr-3">ویرایش</a>
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $brands->render() }}
            </div>
        </div>
    </div>
@endsection
