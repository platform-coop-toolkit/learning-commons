@if(isset($_GET['s']) || $found_posts < App::totalPosts('lc_resource'))
<div class="current-filters">
  <p class="h3">{{ sprintf(__('Showing %1$s of %2$s resources for:', 'coop-library'), $found_posts, App::totalPosts('lc_resource')) }}</p>
  @if(isset($_GET['s']))
  <h2 class="h4">{{ __('Search term', 'coop-library') }}</h2>
  <p>&ldquo;{{ $_GET['s'] }}&rdquo;</p>
  @endif
  @if(!empty(array_filter(array_map('array_filter', $queried_resource_terms))))
    <h2 class="h4">{{ __('Applied filters', 'coop-library') }}</h2>
    <ul class="tags">
      @foreach($queried_resource_terms as $taxonomy => $terms)
        @foreach($terms as $term => $name)
        <li class="tag">
          <button class="button button--tag-button" data-checkbox="{{ $taxonomy }}-{{ $term }}"><span class="screen-reader-text">{{ __('Remove', 'coop-library') }} </span>{!! $name !!}<span class="screen-reader-text"> {{ __('from current filters', 'coop-library') }}</span> @svg('close', 'icon--close', ['focusable' => 'false', 'aria-hidden' => 'true'])</button>
        </li>
        @endforeach
      @endforeach
    </ul>
  <p><a href="{{ get_post_type_archive_link('lc_resource') }}">{{ __('Clear all', 'coop-library') }}</a></p>
  @endif
</div>
@endif
