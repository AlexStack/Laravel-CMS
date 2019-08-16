@extends('laravel-cms::backend.backend-layout')

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">
            <ul id="sortableList" class="list-group">
                @forelse ($all_pages as $item)
                    <li class="list-group-item list-group-item-action">
                        <i class="fas fa-arrows-alt text-light handle"></i>
                        @php
                            if ( $item->depth ){
                                echo str_repeat("⎯⎯⎯", $item->depth);
                            }
                            if ( trim($item->redirect_url) != '' ) {
                                $color_class = 'text-success';
                            } else {
                                $color_class = 'text-secondary';
                            }
                            if ( $item->slug == 'homepage'){
                                $color_class = 'text-primary';
                            }
                            if ( $item->menu_enabled) {
                                if ( $item->depth == 0 ){
                                    $icon =  '<i class="fas fa-list-alt ml-1 ' . $color_class . ' "></i>';
                                } elseif ( $item->depth == 1 ){
                                    $icon =  '<i class="fas fa-list-ul ml-1 ' . $color_class . ' "></i>';
                                } else {
                                    $icon =  '<i class="fas fa-stream ml-1 ' . $color_class . ' "></i>';
                                }

                            } else {
                                $icon =  '<i class="far fa-file ml-1 ' . $color_class . ' "></i>';
                            }
                            if ( $item->slug == 'homepage'){
                                $icon = '<i class="fas fa-home ml-1 ' . $color_class . ' "></i>';
                            }
                        @endphp

                        {!! $icon !!}
                        <a href="./pages/{{$item->id}}/edit" class="text-dark">
                            @if ( $item->menu_title)
                                [ {{$item->menu_title}} ] -
                            @endif
                            {{$item->title}}
                        </a>
                        <span class="text-secondary">
                            {{-- - pid{{$item->parent_id}}/id{{$item->id}} --}}
                            @if ( $item->sort_value)
                                -SortValue {{$item->sort_value}}
                            @endif
                        </span>
                        <a href="./pages/{{$item->id}}/edit" class="text-secondary"><i class="far fa-edit ml-3"></i></a>

                        <a href="{{$controller->url($item)}}" class="{{$color_class}}" target="_blank"><i class="far fa-eye ml-3"></i></a>

                        @if ( $item->menu_enabled)
                            <a href="{{ route('LaravelCmsAdminPages.create', ['parent_id' => $item->id, 'menu_enabled'=>0]) }}" class="text-secondary" ><i class="far fa-plus-square ml-3"></i></a>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item list-group-item-action">No Page yet, <a href="{{ route('LaravelCmsAdminPages.create') }}">Create a new page</a> </a>
                @endforelse
            </ul>

        </div>
    </div>
</div>

@endsection
