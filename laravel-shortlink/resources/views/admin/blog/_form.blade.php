@csrf
@if (isset($post))
  @method('PUT')
@endif

@if ($errors->any())
  <div class="error">{{ $errors->first() }}</div>
@endif

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
