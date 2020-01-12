@if ( isset($page->tags_ary) && !empty($page->tags_ary) )
<div class="row mb-4 nav-tags">
    <div class="col-md text-right">
        <span class="text-secondary text-tags">{{$helper->t('tags')}} :</span>
        @foreach( $page->tags_ary as $tag )
        <a href="{{route("LaravelCmsPages.show",$helper->s('system.reserved_slugs.search'), false)}}?tag={{$tag }}"
            class="btn btn-sm btn-light ml-1">{{$tag }}</a>
        @endforeach
    </div>
</div>
@endif
