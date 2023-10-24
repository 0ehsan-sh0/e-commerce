@extends('admin.layouts.admin')
@section('title', 'Categories')
@section('content')
    <div class="row">

        <div class="col-xl-12 col-md-12 p-md-5 mb-4 bg-white">
            <div class="d-flex justify-content-between mb-4">
                <h5 class="font-weight-bold">لیست دسته بندی ها ({{ $categories->total() }})</h5>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-outline-primary">ایجاد دسته بندی
                    <i class="fa fa-plus"></i>
                </a>
            </div>

            <div>
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>نام انگلیسی</th>
                            <th>والد</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $key => $category)
                            <tr>
                                <th>
                                    {{ $categories->firstItem() + $key }}
                                </th>

                                <th>
                                    {{ $category->name }}
                                </th>

                                <th>
                                    {{ $category->slug }}
                                </th>

                                <th>
                                    @if ($category->parent_id === 0)
                                        بدون والد
                                    @else
                                        {{ $category->parent->name }}
                                    @endif
                                </th>

                                <th>
                                    <span class="{{ $category->getRawOriginal('is_active') ? 'text-success' : 'text-danger' }}">
                                        {{ $category->is_active }}
                                    </span>
                                </th>

                                <th>
                                    <a href="{{ route('admin.categories.show', ['category' => $category->id]) }}"
                                        class="btn btn-sm btn-outline-success">نمایش</a>
                                    <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}"
                                        class="btn btn-sm btn-outline-info mr-3">ویرایش</a>
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
