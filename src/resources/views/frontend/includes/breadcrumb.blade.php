<nav aria-label="breadcrumb" class="mb-4 nav-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item app-name"><i class="fas fa-home text-secondary mr-1"></i><a
                href="/">{{$helper->s('site_name')}}</a>
        </li>
        @php
        foreach( $page->parent_flat_ary as $parent ){
        $item = (object) $parent;
        echo '<li class="breadcrumb-item">
            <a href="'  . $helper->url($item) . '" title="' . $item->title .'">' . ($item->menu_title ?? $item->title) .
                '</a>
        </li>';
        }
        @endphp


        <li class="breadcrumb-item active" aria-current="page">{{$page->title}}</li>
    </ol>
</nav>
