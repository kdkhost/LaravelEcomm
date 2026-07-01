<form method="POST"
      action="{{ route($bundle->exists ? 'bundles.update' : 'bundles.store', $bundle->exists ? $bundle->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($bundle->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle">@lang('partials.name') <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
               value="{{ $bundle->name ?? '' }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="description">@lang('partials.description')</label>
        <textarea class="form-control" id="description" name="description">{{ $bundle->description ?? '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="inputPrice">@lang('partials.price') <span class="text-danger">*</span></label>
        <input id="inputPrice" type="number" name="price" placeholder="Enter price" value="{{ $bundle->price ?? '' }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label for="products">@lang('partials.product')</label>
        <select class="form-control js-example-basic-multiple" id="products" name="products[]" multiple="multiple">
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->title }}</option>
            @endforeach
        </select>
    </div>

    @include('admin::components.dropzone-upload', [
        'inputId' => 'bundle-images',
        'inputName' => 'images[]',
        'label' => __('partials.image'),
        'multiple' => true,
        'accept' => 'image/*',
        'required' => !$bundle->exists,
        'existingFiles' => $bundle->getMedia('bundle')->map(fn ($media) => [
            'url' => $media->getUrl(),
            'name' => $media->name,
            'type' => $media->mime_type ?? 'image/*',
        ])->all(),
        'helpText' => 'As imagens do kit ficam padronizadas com preview integral.',
    ])

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>

<div class="row">
    @foreach($bundle->getMedia('bundle') as $media)
        <div class="col-md-3">
            <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid">
            <form action="{{ route('bundle.delete-media', ['modelId' => $bundle->id, 'mediaId' => $media->id]) }}"
                  method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </div>
    @endforeach
</div>
@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>
@endpush
