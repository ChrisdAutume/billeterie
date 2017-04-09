@extends('layouts.dashboard')

@section('title')
    Pages
@endsection

@section('smalltitle')
    Création d'une page
@endsection

@section('content')


    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Création d'une page</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name" class="col-lg-2 text-right">Titre de la page</label>
                    <div class="col-lg-10">
                        <input type="text" name="name" class="form-control" id="title" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content" class="col-lg-2 text-right">Contenu</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" rows="10" name="text" id="content">{{ old('text') }}</textarea>
                    </div>
                </div>
                <input type="submit" class="btn btn-success form-control" value="Ajouter !" />
            </form>
        </div>
    </div>
@endsection

@section('sublayout-js')
    @parent
    <script src="{{ asset('js/simplemde.min.js') }}"></script>
    <script src="{{ asset('js/codemirror-4.inline-attachment.min.js') }}"></script>
    <script>
        $(function () {
            var inlineAttachmentConfig = {
                uploadUrl: '{{ route('upload_file') }}',
                extraHeaders: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                jsonFieldName: 'url',
                progressText: '![Upload en cours...]()',
                urlText: '![Description de l\'image]({filename})'
            };

            var simplemde = new SimpleMDE({
                element: document.getElementById("content"),
                spellChecker: false
            });

            inlineAttachment.editors.codemirror4.attach(simplemde.codemirror,
                inlineAttachmentConfig);

            @if(old('text'))
                simplemde.value({{ old('text') }});
            @endif
        });
    </script>
@endsection

@section('sublayout-css')
    @parent
    <link rel="stylesheet" href="{{ asset('css/simplemde.min.css') }}">
@endsection