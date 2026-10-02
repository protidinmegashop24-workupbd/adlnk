<aside class="blog-sidebar">
  <div class="blog-side-card">
    <h3>Latest Posts</h3>
    @foreach ($latest as $item)
      <a class="blog-side-post" href="{{ route('blog.show', $item['slug']) }}">
        <img src="{{ asset($item['thumbnail']) }}" alt="" width="56" height="42"/>
        <span>
          <span class="bsp-title">{{ $item['title'] }}</span>
          <span class="bsp-date">{{ \Carbon\Carbon::parse($item['date'])->format('M j, Y') }}</span>
        </span>
      </a>
    @endforeach
  </div>

  <div class="blog-side-card">
    <h3>Categories</h3>
    <ul class="blog-cat-list">
      <li>
        <a href="{{ route('blog.index') }}" class="{{ ($activeCategory ?? '') === '' ? 'active' : '' }}">All Posts</a>
      </li>
      @foreach ($categories as $name => $count)
        <li>
          <a href="{{ route('blog.index', ['category' => $name]) }}" class="{{ ($activeCategory ?? '') === $name ? 'active' : '' }}">
            {{ $name }} <span class="bcl-count">{{ $count }}</span>
          </a>
        </li>
      @endforeach
    </ul>
  </div>
</aside>
