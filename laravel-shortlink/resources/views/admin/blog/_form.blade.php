@csrf
@if (isset($post))
  @method('PUT')
@endif

@if ($errors->any())
  <div class="error">{{ $errors->first() }}</div>
@endif

<div style="border:1px solid #d6e4ff;border-radius:8px;padding:14px 16px;margin:0 0 20px;background:#f5f9ff">
  <strong style="font-size:14px">✨ AI দিয়ে খসড়া লিখুন (ঐচ্ছিক)</strong>
  <div class="hint" style="margin:4px 0 12px">
    একটা বিষয় দিন — AI একটা নতুন, অরিজিনাল খসড়া লিখে Title/Excerpt/Post Content বক্সে বসিয়ে দিবে। এটা কোনো অন্য লেখা কপি/রিরাইট করে না — শুধু বিষয়টা নিয়ে নতুন করে লেখে।
    <strong>পাবলিশ করার আগে অবশ্যই নিজে পড়ে, ঠিক করে, নিজের ভাষায় এডিট করে নিবেন।</strong>
  </div>

  <label for="ai_topic" style="font-weight:normal">বিষয় (Topic)</label>
  <input id="ai_topic" type="text" style="margin-bottom:10px" placeholder="যেমন: কিভাবে ফ্রি QR কোড বানাবেন"/>

  <label for="ai_notes" style="font-weight:normal">নোট / পয়েন্ট <span class="hint" style="display:inline">(ঐচ্ছিক)</span></label>
  <textarea id="ai_notes" rows="2" style="margin-bottom:10px" placeholder="বিশেষ কিছু পয়েন্ট থাকলে লিখুন, না থাকলে ফাঁকা রাখুন"></textarea>

  <div style="display:flex;gap:18px;margin-bottom:12px">
    <label style="display:flex;align-items:center;gap:6px;font-weight:normal;margin:0">
      <input type="radio" name="ai_language" value="bn" checked style="width:auto"/> বাংলা
    </label>
    <label style="display:flex;align-items:center;gap:6px;font-weight:normal;margin:0">
      <input type="radio" name="ai_language" value="en" style="width:auto"/> English
    </label>
  </div>

  <button type="button" id="aiGenerateBtn" style="width:auto;padding:10px 18px;margin:0">✨ Generate Draft</button>
  <span id="aiGenerateStatus" class="hint" style="display:none;margin-left:10px"></span>
</div>

<label for="title">Title</label>
<input id="title" type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required/>

<label for="slug">URL Slug <span class="hint" style="display:inline">(optional — leave blank to auto-generate from the title)</span></label>
<input id="slug" type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}" placeholder="auto-generated-from-title"/>

<label for="category">Category</label>
<input id="category" type="text" name="category" list="categoryOptions" value="{{ old('category', $post->category ?? '') }}" required/>
<datalist id="categoryOptions">
  @foreach ($existingCategories as $cat)
    <option value="{{ $cat }}"></option>
  @endforeach
</datalist>

<label for="excerpt">Excerpt <span class="hint" style="display:inline">(short summary shown on the blog list and in search results)</span></label>
<textarea id="excerpt" name="excerpt" rows="2" required>{{ old('excerpt', $post->excerpt ?? '') }}</textarea>

<div style="border:1px solid #e5e7eb;border-radius:8px;padding:14px 16px;margin:18px 0;background:#fafbfc">
  <strong style="font-size:14px">Search engine (SEO) settings — optional</strong>
  <div class="hint" style="margin:4px 0 12px">Leave these blank to use the Title and Excerpt above automatically. Fill them in only if you want a different headline or summary to show up in Google search results.</div>

  <label for="meta_title">SEO Title</label>
  <input id="meta_title" type="text" name="meta_title" maxlength="255" value="{{ old('meta_title', $post->meta_title ?? '') }}" placeholder="{{ old('title', $post->title ?? 'Same as Title above') }}"/>

  <label for="meta_description" style="margin-top:10px">SEO Description</label>
  <textarea id="meta_description" name="meta_description" rows="2" maxlength="500" placeholder="Same as Excerpt above">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
</div>

<label for="thumbnail_upload">Thumbnail Image <span class="hint" style="display:inline">(optional — leave blank to keep the current one)</span></label>
@if (isset($post) && $post->thumbnail)
  <img src="{{ asset($post->thumbnail) }}" alt="" style="width:160px;height:100px;object-fit:cover;border-radius:6px;margin-bottom:8px;display:block"/>
@endif
<input id="thumbnail_upload" type="file" name="thumbnail_upload" accept="image/*"/>

<label for="body">Post Content</label>
<div style="display:flex;gap:18px;margin:4px 0 10px">
  <label style="display:flex;align-items:center;gap:6px;font-weight:normal;margin:0">
    <input type="radio" name="body_mode" value="text" class="body-mode-input" style="width:auto" {{ old('body_mode', $post->body_mode ?? 'text') === 'text' ? 'checked' : '' }}/>
    ✍️ সাধারণ লেখা (Plain Text)
  </label>
  <label style="display:flex;align-items:center;gap:6px;font-weight:normal;margin:0">
    <input type="radio" name="body_mode" value="html" class="body-mode-input" style="width:auto" {{ old('body_mode', $post->body_mode ?? 'text') === 'html' ? 'checked' : '' }}/>
    💻 HTML কোড (Advanced)
  </label>
</div>
<div class="hint" style="margin-bottom:6px;line-height:1.6">
  <strong>সাধারণ লেখা মোডে:</strong> যা লিখবেন বা অন্য জায়গা থেকে কপি করে পেস্ট করবেন, তাই লেখা হিসেবে দেখাবে — কোনো কোড ভাঙবে না। প্যারাগ্রাফের মাঝে একটা ফাঁকা লাইন ছাড়ুন। কিছু ঐচ্ছিক শর্টকাট:
  <code>## Heading</code>,
  <code>### Smaller Heading</code>,
  <code>**bold text**</code>,
  <code>[link text](https://example.com)</code>,
  বুলেটের জন্য <code>-</code> অথবা নাম্বারিং এর জন্য <code>1.</code> দিয়ে লাইন শুরু করুন।
  <br/><strong>HTML কোড মোডে:</strong> আপনি যা টাইপ করবেন তা সরাসরি HTML হিসেবে গণ্য হবে (ভিডিও/iframe এমবেড করতে এটা লাগবে) — কিন্তু তখন প্যারাগ্রাফ ব্রেক বা বুলেট অটো হবে না, নিজেই <code>&lt;p&gt;</code>/<code>&lt;ul&gt;</code> ট্যাগ লিখতে হবে।
  <br/>নিচে <strong>Preview</strong> বাটনে ক্লিক করে পাবলিশ করার আগেই দেখে নিতে পারবেন কেমন দেখাবে।
</div>
<textarea id="body" name="body" rows="18" style="font-family:monospace;font-size:13px" required>{{ old('body', isset($post) ? ($post->body_raw ?? $post->body) : '') }}</textarea>

<button type="button" id="previewBtn" style="margin-top:10px;width:auto;background:#fff;color:#0d6efd;border:1px solid #0d6efd;padding:10px 18px">👁️ Preview</button>

<label style="display:flex;align-items:center;gap:8px;margin-top:16px">
  <input type="checkbox" name="published" value="1" style="width:auto" {{ old('published', $post->published ?? true) ? 'checked' : '' }}/>
  Published (visible on the public blog)
</label>

<button type="submit">{{ isset($post) ? 'Save Changes' : 'Create Post' }}</button>

@include('admin.partials.content-preview-modal', ['previewUrl' => route('admin.blog.preview')])

<script>
(function () {
  var btn = document.getElementById('aiGenerateBtn');
  if (!btn) return;

  var status = document.getElementById('aiGenerateStatus');
  var generateUrl = @json(route('admin.blog.generate'));

  btn.addEventListener('click', function () {
    var topic = document.getElementById('ai_topic').value.trim();
    if (!topic) {
      alert('প্রথমে একটা বিষয় লিখুন।');
      return;
    }

    var titleField = document.getElementById('title');
    var excerptField = document.getElementById('excerpt');
    var bodyField = document.getElementById('body');

    if (
      (titleField.value.trim() || excerptField.value.trim() || bodyField.value.trim()) &&
      !confirm('এখন যা লেখা আছে তা মুছে AI-এর খসড়া বসবে। এগোতে চান?')
    ) {
      return;
    }

    var notes = document.getElementById('ai_notes').value.trim();
    var language = document.querySelector('input[name="ai_language"]:checked').value;
    var token = document.querySelector('input[name="_token"]').value;

    btn.disabled = true;
    status.style.display = 'inline';
    status.textContent = 'লেখা তৈরি হচ্ছে... (কিছুক্ষণ সময় লাগতে পারে)';

    fetch(generateUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ topic: topic, notes: notes, language: language })
    })
      .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
      .then(function (res) {
        if (!res.ok) {
          status.textContent = res.data.error || 'তৈরি করা যায়নি, আবার চেষ্টা করুন।';
          return;
        }
        titleField.value = res.data.title;
        excerptField.value = res.data.excerpt;
        bodyField.value = res.data.body;
        var textMode = document.querySelector('.body-mode-input[value="text"]');
        if (textMode) textMode.checked = true;
        status.textContent = '✅ খসড়া বসানো হয়েছে — পাবলিশ করার আগে পড়ে, ঠিক করে নিন।';
        titleField.scrollIntoView({ behavior: 'smooth', block: 'center' });
      })
      .catch(function () {
        status.textContent = 'সংযোগ সমস্যা হয়েছে, আবার চেষ্টা করুন।';
      })
      .finally(function () {
        btn.disabled = false;
      });
  });
})();
</script>
