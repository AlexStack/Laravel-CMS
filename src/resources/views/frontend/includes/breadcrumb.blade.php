<nav aria-label="breadcrumb" class="mb-4 nav-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item app-name"><a href="/">{{config('app.name')}}</a></li>
    @php
        foreach( $page->parent_flat_ary as $parent ){
            $item = (object) $parent;
            echo '<li class="breadcrumb-item">
                    <a href="'  . $helper->url($item) . '">' . ($item->menu_title ?? $item->title) . '</a>
                  </li>';
        }
    @endphp


  <li class="breadcrumb-item active" aria-current="page">{{$page->title}}</li>
  </ol>
</nav>
