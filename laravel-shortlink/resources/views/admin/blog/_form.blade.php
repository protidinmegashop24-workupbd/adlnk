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
<div class="hint" style="margin-bottom:6px;line-height:1.6">
  Just type normally — no coding needed. Leave a blank line between paragraphs. A few optional shortcuts:
  <code>## Heading</code>,
  <code>### Smaller Heading</code>,
  <code>**bold text**</code>,
  <code>[link text](https://example.com)</code>,
  a list of lines starting with <code>-</code> for bullets or <code>1.</code> for numbers.
  <br/>If you type a real HTML tag anywhere (like <code>&lt;h2&gt;</code>), the whole post switches to raw HTML mode instead — don't mix the two styles in one post.
</div>
<textarea id="body" name="body" rows="18" style="font-family:monospace;font-size:13px" required>{{ old('body', isset($post) ? $post->body : '') }}</textarea>

<label style="display:flex;align-items:center;gap:8px;margin-top:16px">
  <input type="checkbox" name="published" value="1" style="width:auto" {{ old('published', $post->published ?? true) ? 'checked' : '' }}/>
  Published (visible on the public blog)
</label>

<button type="submit">{{ isset($post) ? 'Save Changes' : 'Create Post' }}</button>
