@extends('laravel-cms::' . $helper->getCmsSetting('template_backend_dir')  .  '.includes.layout')

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">
            <ul id="sortableList" class="list-group">
                @forelse ($settings as $item)
                    <li class="list-group-item list-group-item-action">
                        @php
                            if ( $item->enabled) {
                                $icon =  '<i class="fas fa-list-alt ml-1  "></i>';
                            } else {
                                $icon =  '<i class="fas fa-list-ul ml-1 "></i>';
                            }
                        @endphp

                        {!! $icon !!}
                        <a href="./settings/{{$item->id}}/edit" class="text-dark">
                            [ {{$item->category}}
                                @if ( $item->page_id)
                                    PageID:{{$item->page_id}}
                                @endif
                            ] -
                            {{$item->param_name}}
                        </a>

                        <a href="./settings/{{$item->id}}/edit" class="text-dark"><i class="far fa-edit ml-1 mr-1"></i></a>

                        <span class="abstract">
                             ({!! \Illuminate\Support\Str::words($item->abstract, 20,'...')  !!})
                        </span>

                        <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => $item->category, 'page_id'=>$item->page_id]) }}" class="text-secondary" ><i class="far fa-plus-square ml-1"></i></a>

                        <div class="param-value text-success">
                            <i class="far fa-arrow-alt-circle-right ml-1 "></i> {{ str_limit($item->param_value, 100, '...')}}
                        </div>
                    </li>
                @empty
                    <li class="list-group-item list-group-item-action">No Setting yet, <a href="{{ route('LaravelCmsAdminSettings.create') }}">Create a new Setting</a> </a>
                @endforelse
            </ul>

        </div>
    </div>
</div>

@endsection
