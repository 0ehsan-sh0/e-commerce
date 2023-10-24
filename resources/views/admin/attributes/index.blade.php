@extends('admin.layouts.admin')
@section('title', 'Attributes')
@section('content')
    <div class="row">

        <div class="col-xl-12 col-md-12 p-md-5 mb-4 bg-white">
            <div class="d-flex justify-content-between mb-4">
                <h5 class="font-weight-bold">لیست ویژگی ها ({{$attributes->total()}})</h5>
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-sm btn-outline-primary">ایجاد ویژگی
                    <i class="fa fa-plus"></i>
                </a>
            </div>
            
            <div>
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attributes as $key => $attribute)
                            <tr>
                                <th>
                                    {{ $attributes->firstItem() + $key}}
                                </th>

                                <th>
                                    {{ $attribute->name}}
                                </th>

                                <th>
                                    <a href="{{ route('admin.attributes.show', ['attribute' => $attribute->id]) }}" class="btn btn-sm btn-outline-success">نمایش</a>
                                    <a href="{{ route('admin.attributes.edit', ['attribute' => $attribute->id]) }}" class="btn btn-sm btn-outline-info mr-3">ویرایش</a>
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
