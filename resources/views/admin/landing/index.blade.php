@extends('layouts.app')

@section('title', 'Kontrol Landing Page')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">Kontrol Landing Page</h3>
            <p class="text-muted small mb-0">Ubah konten yang tampil di halaman depan.</p>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.landing.update') }}" method="POST">
                @csrf
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-hero-tab" data-bs-toggle="tab" data-bs-target="#nav-hero" type="button" role="tab" aria-controls="nav-hero" aria-selected="true">Hero Section</button>
                        <button class="nav-link" id="nav-features-tab" data-bs-toggle="tab" data-bs-target="#nav-features" type="button" role="tab" aria-controls="nav-features" aria-selected="false">Features Section</button>
                    </div>
                </nav>
                <div class="tab-content py-4" id="nav-tabContent">
                    {{-- Hero Section --}}
                    <div class="tab-pane fade show active" id="nav-hero" role="tabpanel" aria-labelledby="nav-hero-tab">
                        <h5>Hero Section</h5>
                        <div class="mb-3">
                            <label for="landing_hero_title" class="form-label">Judul Utama</label>
                            <input type="text" class="form-control" id="landing_hero_title" name="landing_hero_title" value="{{ $settings['landing_hero_title']->value ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="landing_hero_subtitle" class="form-label">Sub Judul</label>
                            <textarea class="form-control" id="landing_hero_subtitle" name="landing_hero_subtitle" rows="3">{{ $settings['landing_hero_subtitle']->value ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="landing_hero_cta_text" class="form-label">Teks Tombol CTA</label>
                            <input type="text" class="form-control" id="landing_hero_cta_text" name="landing_hero_cta_text" value="{{ $settings['landing_hero_cta_text']->value ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="landing_hero_image" class="form-label">URL Gambar Preview</label>
                            <input type="url" class="form-control" id="landing_hero_image" name="landing_hero_image" value="{{ $settings['landing_hero_image']->value ?? '' }}">
                            <small class="text-muted">Gunakan URL gambar dari internet. Contoh: https://placehold.co/800x400</small>
                        </div>
                    </div>

                    {{-- Features Section --}}
                    <div class="tab-pane fade" id="nav-features" role="tabpanel" aria-labelledby="nav-features-tab">
                        <h5>Features Section</h5>
                        <div class="mb-3">
                            <label for="landing_features_title" class="form-label">Judul Fitur</label>
                            <input type="text" class="form-control" id="landing_features_title" name="landing_features_title" value="{{ $settings['landing_features_title']->value ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="landing_features_subtitle" class="form-label">Sub Judul Fitur</label>
                            <input type="text" class="form-control" id="landing_features_subtitle" name="landing_features_subtitle" value="{{ $settings['landing_features_subtitle']->value ?? '' }}">
                        </div>

                        <hr class="my-4">

                        {{-- Feature 1 --}}
                        <h6><i class="bi bi-1-circle"></i> Fitur 1</h6>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="landing_feature_1_icon" class="form-label">Ikon</label>
                                <input type="text" class="form-control" id="landing_feature_1_icon" name="landing_feature_1_icon" value="{{ $settings['landing_feature_1_icon']->value ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="landing_feature_1_title" class="form-label">Judul</label>
                                <input type="text" class="form-control" id="landing_feature_1_title" name="landing_feature_1_title" value="{{ $settings['landing_feature_1_title']->value ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="landing_feature_1_description" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="landing_feature_1_description" name="landing_feature_1_description" value="{{ $settings['landing_feature_1_description']->value ?? '' }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Feature 2 --}}
                        <h6><i class="bi bi-2-circle"></i> Fitur 2</h6>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="landing_feature_2_icon" class="form-label">Ikon</label>
                                <input type="text" class="form-control" id="landing_feature_2_icon" name="landing_feature_2_icon" value="{{ $settings['landing_feature_2_icon']->value ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="landing_feature_2_title" class="form-label">Judul</label>
                                <input type="text" class="form-control" id="landing_feature_2_title" name="landing_feature_2_title" value="{{ $settings['landing_feature_2_title']->value ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="landing_feature_2_description" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="landing_feature_2_description" name="landing_feature_2_description" value="{{ $settings['landing_feature_2_description']->value ?? '' }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Feature 3 --}}
                        <h6><i class="bi bi-3-circle"></i> Fitur 3</h6>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="landing_feature_3_icon" class="form-label">Ikon</label>
                                <input type="text" class="form-control" id="landing_feature_3_icon" name="landing_feature_3_icon" value="{{ $settings['landing_feature_3_icon']->value ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="landing_feature_3_title" class="form-label">Judul</label>
                                <input type="text" class="form-control" id="landing_feature_3_title" name="landing_feature_3_title" value="{{ $settings['landing_feature_3_title']->value ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="landing_feature_3_description" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="landing_feature_3_description" name="landing_feature_3_description" value="{{ $settings['landing_feature_3_description']->value ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
