<h2>{{ $title ?? 'Related Tools' }}</h2>
<div class="related-tools">
  @foreach ($tools as $tool)
    <a href="{{ $tool['url'] }}">{{ $tool['icon'] ?? '' }} {{ $tool['label'] }}</a>
  @endforeach
</div>
