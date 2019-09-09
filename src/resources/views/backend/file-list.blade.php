@extends('laravel-cms::' . $helper->s('template.backend_dir')  .  '.includes.layout')

@section('content')
<div class="row">
        <div class="col-md-2 img-item">
            <img class="col-md-12 thumbnail" src="https://via.placeholder.com/150x150?text=001" alt="a galerie test" />
            <i class="fa fa-check"></i>
        </div>
        <div class="col-md-2 img-item">
            <img class="col-md-12 thumbnail" src="https://via.placeholder.com/150x150?text=002" alt="a galerie test" />
            <i class="fa fa-check"></i>
        </div>
        <div class="col-md-2 img-item">
            <img class="col-md-12 thumbnail" src="https://via.placeholder.com/150x150?text=003" alt="a galerie test" />
            <i class="fa fa-check"></i>
        </div>
        <div class="col-md-2 img-item">
            <img class="col-md-12 thumbnail" src="https://via.placeholder.com/150x150?text=004" alt="a galerie test" />
            <i class="fa fa-check"></i>
        </div>
    </div>


FILE FILE
<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">

<!-- Nav tabs -->
<ul class="nav nav-tabs mb-0" role="tablist">
    @foreach ($settings->pluck('param_name', 'category') as $category => $param_name)
<li class="nav-item">
        <a class="nav-link {{ isset($_GET['category']) ? ($_GET['category']== $category ? 'active' : '' ) : ($loop->first ? 'active' : '') }}" data-toggle="tab" href="#{{$category}}" role="tab">
            @if ( isset($categories[$category]) )
                {!! $categories[$category] !!}
            @else
                <i class="fas fa-cube mr-1"></i>{{ucfirst($category)}}
            @endif
        </a>
    </li>
    @endforeach
</ul>


<!-- Tab panes start -->
<style>
.list-group-item:first-child {
	border-top-left-radius: 0;
	border-top-right-radius: 0;
    border-top:0;
}
</style>
<div class="tab-content">

@foreach ($settings->pluck('param_name', 'category') as $category => $param_name)
<div class="tab-pane mb-3 {{isset($_GET['category']) ? ($_GET['category']== $category ? 'active' : '' ) : ($loop->first ? 'active' : '') }}" id="{{$category}}" role="tabpanel">
    @foreach ($settings->filter(function ($v, $k) use($category) {
                    return $v->category == $category;
                }) as $item)
        <li class="list-group-item list-group-item-action">
            @php
                if ( $item->enabled) {
                    $icon =  '<i class="fas fa-wrench ml-1  "></i>';
                } else {
                    $icon =  '<i class="fas fa-hammer ml-1 "></i>';
                }
            @endphp

            {!! $icon !!}
            <a href="./settings/{{$item->id}}/edit" class="{{$item->enabled ? 'text-dark font-weight-bold' : 'text-secondary'}}"  title="Sort Value: {{$item->sort_value??0}}">
                {{$item->category}}.{{$item->param_name}}
                    @if ( $item->page_id)
                        - PageID:{{$item->page_id}}
                    @endif
            </a>

        <a href="./settings/{{$item->id}}/edit" class="{{$item->enabled ? 'text-dark' : 'text-secondary'}}"><i class="far fa-edit ml-1 mr-1" title="Sort Value: {{$item->sort_value??0}}"></i></a>

            <span class="abstract">
                    ({!! \Illuminate\Support\Str::words($item->abstract, 20,'...')  !!})
            </span>

            <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => $item->category, 'page_id'=>$item->page_id, 'input_attribute'=>$item->input_attribute, 'sort_value'=>($item->sort_value-1)]) }}" class="text-secondary" ><i class="far fa-plus-square ml-1"></i></a>

            <div class="param-value {{$item->enabled ? 'text-success' : 'text-secondary'}}">
                <i class="far fa-arrow-alt-circle-right ml-1 "></i> {{ str_limit($item->param_value, 100, '...')}}
            </div>
        </li>
    {{-- @empty
        <li class="list-group-item list-group-item-action">No Setting yet, <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => 'global', 'page_id'=>null, 'input_attribute'=>'{"rows":1,"required":"required"}', 'sort_value'=>1000]) }}">Create a new Setting</a> </a> --}}
    @endforeach
</div>
@endforeach
</div>
<!-- Tab panes end -->


{{--
            <ul id="sortableList" class="list-group">
                @foreach ($settings as $item)
                    <li class="list-group-item list-group-item-action">
                        @php
                            if ( $item->enabled) {
                                $icon =  '<i class="fas fa-wrench ml-1  "></i>';
                            } else {
                                $icon =  '<i class="fas fa-hammer ml-1 "></i>';
                            }
                        @endphp

                        {!! $icon !!}
                        <a href="./settings/{{$item->id}}/edit" class="{{$item->enabled ? 'text-dark font-weight-bold' : 'text-secondary'}}"  title="Sort Value: {{$item->sort_value??0}}">
                            [ {{$item->category}}
                                @if ( $item->page_id)
                                    PageID:{{$item->page_id}}
                                @endif
                            ] -
                            {{$item->param_name}}
                        </a>

                    <a href="./settings/{{$item->id}}/edit" class="{{$item->enabled ? 'text-dark' : 'text-secondary'}}"><i class="far fa-edit ml-1 mr-1" title="Sort Value: {{$item->sort_value??0}}"></i></a>

                        <span class="abstract">
                             ({!! \Illuminate\Support\Str::words($item->abstract, 20,'...')  !!})
                        </span>

                        <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => $item->category, 'page_id'=>$item->page_id, 'input_attribute'=>$item->input_attribute, 'sort_value'=>($item->sort_value-1)]) }}" class="text-secondary" ><i class="far fa-plus-square ml-1"></i></a>

                        <div class="param-value {{$item->enabled ? 'text-success' : 'text-secondary'}}">
                            <i class="far fa-arrow-alt-circle-right ml-1 "></i> {{ str_limit($item->param_value, 100, '...')}}
                        </div>
                    </li>
                @empty
                    <li class="list-group-item list-group-item-action">No Setting yet, <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => 'global', 'page_id'=>null, 'input_attribute'=>'{"rows":1,"required":"required"}', 'sort_value'=>1000]) }}">Create a new Setting</a> </a>
                @endforeach
            </ul> --}}

        </div>
    </div>
</div>

@endsection
