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

<label for="thumbnail_upload">Thumbnail Image <span class="hint" style="display:inline">(optional — leave blank to keep the current one)</span></label>
@if (isset($post) && $post->thumbnail)
  <img src="{{ asset($post->thumbnail) }}" alt="" style="width:160px;height:100px;object-fit:cover;border-radius:6px;margin-bottom:8px;display:block"/>
@endif
<input id="thumbnail_upload" type="file" name="thumbnail_upload" accept="image/*"/>

<label for="body">Post Content</label>
<div class="hint" style="margin-bottom:6px">Leave a blank line between paragraphs. For more control you can also use HTML tags like &lt;h2&gt;, &lt;strong&gt;, &lt;a href="..."&gt;, &lt;ul&gt;&lt;li&gt;.</div>
<textarea id="body" name="body" rows="18" style="font-family:monospace;font-size:13px" required>{{ old('body', isset($post) ? $post->body : '') }}</textarea>

<label style="display:flex;align-items:center;gap:8px;margin-top:16px">
  <input type="checkbox" name="published" value="1" style="width:auto" {{ old('published', $post->published ?? true) ? 'checked' : '' }}/>
  Published (visible on the public blog)
</label>

<button type="submit">{{ isset($post) ? 'Save Changes' : 'Create Post' }}</button>
