<h2>Frequently Asked Questions</h2>
@foreach ($faqs as $faq)
  <details class="faq-item">
    <summary>{{ $faq['q'] }}</summary>
    <p>{{ $faq['a'] }}</p>
  </details>
@endforeach

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn ($faq) => [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a'],
        ],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
