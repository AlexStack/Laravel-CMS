@extends($helper->bladePath('includes.layout','b'))

@inject('str', 'Illuminate\Support\Str')

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-0" role="tablist">

                @foreach ($categories as $category => $param_name)
                <li class="nav-item">
                    <a class="nav-link {{ isset($_GET['category']) ? ($_GET['category']== $category ? 'active' : '' ) : ($loop->first ? 'active' : '') }}"
                        data-toggle="tab" href="#{{$category}}" role="tab">
                        @if ( strpos($categories[$category],'class=') === false )
                        <i class="fas fa-cube mr-1"></i>
                        @endif
                        {!! $categories[$category] !!}
                    </a>
                </li>
                @endforeach
            </ul>


            <!-- Tab panes start -->
            <style>
                .list-group-item:first-child {
                    border-top-left-radius: 0;
                    border-top-right-radius: 0;
                    border-top: 0;
                }
            </style>
            <div class="tab-content all-settings">

                @foreach ($categories as $category => $param_name)
                <div class="tab-pane mb-3 {{isset($_GET['category']) ? ($_GET['category']== $category ? 'active' : '' ) : ($loop->first ? 'active' : '') }}"
                    id="{{$category}}" role="tabpanel">
                    <ul class="list-group {{$category}}">
                        @foreach ($settings->filter(function ($v, $k) use($category) {
                        return $v->category == $category;
                        }) as $item)
                        <li class="list-group-item list-group-item-action">
                            @php
                            if ( $item->enabled) {
                            $icon = '<i class="fas fa-wrench ml-1  "></i>';
                            } else {
                            $icon = '<i class="fas fa-hammer ml-1 "></i>';
                            }
                            @endphp

                            {!! $icon !!}
                            <a href="./settings/{{$item->id}}/edit"
                                class="{{$item->enabled ? 'font-weight-bold' : 'text-secondary'}}"
                                title="Sort Value: {{$item->sort_value??0}}">
                                @if ( $item->category == 'plugin' && trim($helper->s('plugin.' . $item->param_name .
                                '.plugin_name'))!= '')
                                {{$helper->s('plugin.' . $item->param_name . '.plugin_name')}}
                                @else
                                {{$item->category}}.{{$item->param_name}}
                                @endif

                            </a>
                            @if ( $item->category == 'plugin' && $helper->s('plugin.' . $item->param_name .
                            '.plugin_type') == 'standalone' && trim(strip_tags($helper->s('plugin.' . $item->param_name
                            .
                            '.tab_name'))) != '' )
                            <a href="./plugins/{{$item->param_name}}" class="text-primary ml-1 mr-1">{!!
                                strip_tags($helper->s('plugin.' .
                                $item->param_name . '.tab_name'), '<i>') !!}</a>
                            @endif

                            <a href="./settings/{{$item->id}}/edit"
                                class="{{$item->enabled ? 'text-secondary' : 'text-secondary'}}"><i
                                    class="far fa-edit ml-1 mr-1" title="Edit {{$item->param_name}}"></i></a>

                            <span class="abstract">
                                @if ( trim(strip_tags($item->abstract)) != '' )
                                ({!! $str->words(strip_tags($item->abstract,'<b><span>
                                        <div><i><a>
                                                    <font>'), 20,'...') !!})
                                                        @endif
                                    </span>

                                    <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => $item->category, 'page_id'=>$item->page_id, 'input_attribute'=>$item->input_attribute, 'sort_value'=>($item->sort_value-1)]) }}"
                                        class="text-secondary"><i class="far fa-plus-square ml-1"></i></a>

                                    <div class="param-value {{$item->enabled ? 'text-success' : 'text-secondary'}}">
                                        <i class="far fa-arrow-alt-circle-right ml-1 "></i>
                                        {{ $str->limit($item->param_value, 100, '...')}}
                                    </div>
                        </li>

                        @endforeach
                    </ul>
                    @if ( $category == 'plugin')
                    @include($helper->bladePath('includes.search-plugin','b'))
                    @endif
                </div>
                @endforeach

            </div>
            <!-- Tab panes end -->

        </div>
    </div>
</div>

@endsection
