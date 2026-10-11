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

<link rel="stylesheet" href="{{ asset('vendor/quill/quill.snow.css') }}"/>
<style>
  /* This project's global button/input styles (base-style.blade.php) make
     every <button> full-width with a top margin, which would wreck
     Quill's own toolbar icons — undo that just inside the editor. */
  .ql-toolbar.ql-snow button,
  .ql-snow .ql-picker { width: auto; margin-top: 0; background: none; padding: 3px 5px; border-radius: 0; font-size: inherit; }
  .ql-toolbar.ql-snow button:disabled { background: none; }
  .ql-editor { min-height: 260px; font-size: 15px; line-height: 1.6; }
  .ql-editor img { max-width: 100%; height: auto; }
</style>

<label for="bodyEditor">Post Content</label>
<div class="hint" style="margin-bottom:6px;line-height:1.6">
  সরাসরি লিখুন, পেস্ট করুন — ওপরের টুলবার দিয়ে হেডিং, বোল্ড, বুলেট/নাম্বার লিস্ট, লিঙ্ক, ছবি আর ভিডিও বসাতে পারবেন। অন্য জায়গা থেকে লেখা কপি-পেস্ট করলে স্বাভাবিক লেখা হিসেবে বসবে, কোনো কিছু ভাঙবে না।
  ভিডিও বসাতে টুলবারের 🎬 বাটনে ক্লিক করে ইউটিউব লিঙ্ক দিন — এটা নিজে থেকেই পেজের সাথে মানানসই সাইজে বসবে।
  <br/>নিচে <strong>Preview</strong> বাটনে ক্লিক করে পাবলিশ করার আগেই দেখে নিতে পারবেন লাইভ পেজে কেমন দেখাবে।
</div>
<div id="bodyEditor" style="background:#fff;border-radius:0 0 6px 6px"></div>
<textarea id="body" name="body" required style="display:none">{{ old('body', $post->body ?? '') }}</textarea>
<input type="radio" name="body_mode" value="html" checked class="body-mode-input" style="display:none"/>

<button type="button" id="previewBtn" style="margin-top:10px;width:auto;background:#fff;color:#0d6efd;border:1px solid #0d6efd;padding:10px 18px">👁️ Preview</button>

<label style="display:flex;align-items:center;gap:8px;margin-top:16px">
  <input type="checkbox" name="published" value="1" style="width:auto" {{ old('published', $post->published ?? true) ? 'checked' : '' }}/>
  Published (visible on the public blog)
</label>

<button type="submit">{{ isset($post) ? 'Save Changes' : 'Create Post' }}</button>

@include('admin.partials.content-preview-modal', ['previewUrl' => route('admin.blog.preview')])

<script src="{{ asset('vendor/quill/quill.js') }}"></script>
<script>
(function () {
  var bodyField = document.getElementById('body');
  var toolbarOptions = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['link', 'image', 'video'],
    ['clean']
  ];

  var quill = new Quill('#bodyEditor', {
    theme: 'snow',
    placeholder: 'এখানে লেখা শুরু করুন...',
    modules: { toolbar: toolbarOptions }
  });

  if (bodyField.value.trim()) {
    quill.clipboard.dangerouslyPasteHTML(bodyField.value);
  }

  quill.on('text-change', function () {
    bodyField.value = quill.root.innerHTML;
  });

  var form = bodyField.closest('form');
  if (form) {
    form.addEventListener('submit', function () {
      bodyField.value = quill.root.innerHTML;
    });
  }

  var uploadImageUrl = @json(route('admin.blog.upload-image'));
  var csrfToken = document.querySelector('input[name="_token"]').value;

  quill.getModule('toolbar').addHandler('image', function () {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = function () {
      var file = input.files[0];
      if (!file) return;

      var formData = new FormData();
      formData.append('image', file);

      fetch(uploadImageUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      })
        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
        .then(function (res) {
          if (!res.ok || !res.data.url) {
            alert((res.data && res.data.error) || 'ছবি আপলোড করা যায়নি, আবার চেষ্টা করুন।');
            return;
          }
          var range = quill.getSelection(true);
          quill.insertEmbed(range.index, 'image', res.data.url, 'user');
          quill.setSelection(range.index + 1);
        })
        .catch(function () {
          alert('ছবি আপলোড করা যায়নি, ইন্টারনেট সংযোগ চেক করুন।');
        });
    };
  });

  window.__blogQuill = quill;
})();
</script>
<script>
(function () {
  var btn = document.getElementById('aiGenerateBtn');
  if (!btn) return;

  var status = document.getElementById('aiGenerateStatus');
  var generateUrl = @json(route('admin.blog.generate'));
  var previewUrl = @json(route('admin.blog.preview'));

  btn.addEventListener('click', function () {
    var topic = document.getElementById('ai_topic').value.trim();
    if (!topic) {
      alert('প্রথমে একটা বিষয় লিখুন।');
      return;
    }

    var titleField = document.getElementById('title');
    var excerptField = document.getElementById('excerpt');
    var quill = window.__blogQuill;

    if (
      (titleField.value.trim() || excerptField.value.trim() || quill.getText().trim()) &&
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

        // The AI returns plain-text with ##/**/- shortcuts, matching the
        // same format ContentFormatterService's "text" mode expects —
        // convert it to real HTML through the same endpoint the Preview
        // button uses, then load that into the editor.
        return fetch(previewUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
          body: JSON.stringify({ body: res.data.body, body_mode: 'text' })
        })
          .then(function (r) { return r.json(); })
          .then(function (converted) {
            var quill = window.__blogQuill;
            quill.setText('');
            quill.clipboard.dangerouslyPasteHTML(converted.html || '');
            document.getElementById('body').value = quill.root.innerHTML;
            status.textContent = '✅ খসড়া বসানো হয়েছে — পাবলিশ করার আগে পড়ে, ঠিক করে নিন।';
            titleField.scrollIntoView({ behavior: 'smooth', block: 'center' });
          });
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
