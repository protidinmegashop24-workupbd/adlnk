<nav class="breadcrumb" aria-label="Breadcrumb">
  @foreach ($crumbs as $i => $crumb)
    @if ($i > 0)
      <span aria-hidden="true"> &rsaquo; </span>
    @endif
    @if (! empty($crumb['url']) && $i < count($crumbs) - 1)
      <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
    @else
      <span aria-current="page">{{ $crumb['label'] }}</span>
    @endif
  @endforeach
</nav>
