{{-- filepath: resources/views/admin/settings/index.blade.php --}}
@extends('admin.layout')

@section('title', $setting ? 'Edit Settings' : 'Create Settings')
@section('page-title', $setting ? 'Chỉnh sửa cài đặt website' : 'Tạo cài đặt website')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .banner-type-toggle {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .banner-preview {
        min-height: 100px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }
    .current-file {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .current-image {
        max-width: 200px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .current-video {
        max-width: 250px;
        height: 140px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    /* Color Settings Styles */
    .color-settings-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .color-input-wrapper {
        display: flex;
        gap: 10px;
        align-items: stretch;
    }

    .color-picker {
        width: 60px !important;
        height: 40px;
        padding: 2px;
        border-radius: 6px;
        border: 2px solid #dee2e6;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .color-picker:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    .color-text-input {
        flex: 1;
        font-family: 'Courier New', monospace;
        text-transform: uppercase;
    }

    .color-preview-section {
        border-top: 1px solid #e9ecef;
        padding-top: 1rem;
    }

    #titlePreview, #subTitlePreview {
        transition: color 0.3s ease;
    }

    .color-input-wrapper input[type="color"]::-webkit-color-swatch-wrapper {
        padding: 0;
        border: none;
    }

    .color-input-wrapper input[type="color"]::-webkit-color-swatch {
        border: none;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $setting ? 'Chỉnh sửa cài đặt website' : 'Tạo cài đặt website' }}</h4>
        <p class="text-muted mb-0">{{ $setting ? 'Cập nhật cấu hình hệ thống' : 'Thiết lập cấu hình ban đầu' }}</p>
    </div>
    <a href="{{ route('admin.settings.show') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <!-- General Information -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin chung</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Logo</label>
                            @if($setting && $setting->logo_url)
                                <div class="current-file">
                                    <p class="mb-2"><strong>Logo hiện tại:</strong></p>
                                    <img width="200" src="{{ $setting->logo_url }}" alt="Current Logo" class="current-image">
                                    <p class="mt-2 mb-0 small text-muted">{{ $setting->logo_url }}</p>
                                </div>
                            @endif
                            <input type="file" name="logo_name" class="form-control @error('logo_name') is-invalid @enderror"
                                   accept="image/*">
                            @error('logo_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                @if($setting && $setting->logo_name)
                                    Để trống để giữ logo hiện tại
                                @else
                                    Upload logo website
                                @endif
                                (JPG, PNG, WebP, max 2MB)
                            </small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                      rows="3" placeholder="Nhập địa chỉ công ty...">{{ old('address', $setting->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hotline</label>
                            <input type="text" name="hotline" class="form-control @error('hotline') is-invalid @enderror"
                                   value="{{ old('hotline', $setting->hotline ?? '') }}" placeholder="0123 456 789">
                            @error('hotline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email liên hệ</label>
                            <input type="email" name="email_contact" class="form-control @error('email_contact') is-invalid @enderror"
                                   value="{{ old('email_contact', $setting->email_contact ?? '') }}" placeholder="contact@example.com">
                            @error('email_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phí vận chuyển mặc định</label>
                            <div class="input-group">
                                <input type="number" name="shipping_fee" class="form-control @error('shipping_fee') is-invalid @enderror"
                                       value="{{ old('shipping_fee', $setting->shipping_fee ?? 0) }}"
                                       placeholder="0" >
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            @error('shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Phí vận chuyển mặc định cho đơn hàng</small>
                        </div>

                         <div class="col-md-6 mb-3">
                            <label class="form-label">Giảm giá đơn hàng (global)</label>
                            <div class="input-group">
                                <input type="number" name="discount_global" class="form-control @error('discount_global') is-invalid @enderror"
                                       value="{{ old('discount_global', $setting->discount_global ?? 0) }}">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('discount_global')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Phần trăm giảm giá cho đơn hàng</small>
                        </div>


                    </div>
                </div>
            </div>
            <!-- Banner Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Cài đặt Banner</h5>
                </div>
                <div class="p-4">
                    <div id="bannerImageSection">
                      @if($setting && $setting->banner_thumb_urls && count($setting->banner_thumb_urls) > 0)
                        <div class="current-file">
                            <div class="row g-2 mb-3">
                                @foreach($setting->media as $banner)
                                  @if ($banner->collection_name == 'banner_image')
                                    <div class="col-md-6 col-lg-4">
                                        <div class="position-relative border rounded overflow-hidden">
                                            <img src="{{ $banner->getUrl() }}"
                                                class="img-fluid current-image w-100"
                                                style="height: 120px; object-fit: cover;">

                                            {{-- Action buttons --}}
                                            <div class="position-absolute top-0 end-0 m-1">
                                                <div class="btn-group-vertical">
                                                    {{-- Delete button --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger opacity-75"
                                                            onclick="deleteBanner('{{ $banner->id }}',this)"
                                                               title="Xóa banner">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  @endif
                                  @endforeach
                            </div>
                        </div>
                    @elseif($setting && $setting->banner_image_url)
                        {{-- Fallback cho trường hợp chỉ có banner_image_url --}}
                        <div class="current-file">
                            <p class="mb-2"><strong>Banner hiện tại:</strong></p>
                            <div class="text-center">
                                <img width="400" src="{{ $setting->banner_image_url }}" alt="Current Banner" class="current-image img-fluid rounded shadow">
                            </div>
                            <p class="mt-2 mb-0 small text-muted text-center">{{ $setting->banner_image_url }}</p>
                        </div>
                    @endif
                        <div class="mb-3">
                            <label class="form-label">Banner Gallery Image</label>
                            <input type="file" name="banner_image[]" multiple class="form-control @error('banner_image') is-invalid @enderror"
                                   accept="image/*">
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                @if($setting && $setting->banner_image_url)
                                    Để trống để giữ ảnh hiện tại
                                @else
                                    Upload hình ảnh banner
                                @endif
                                (JPG, PNG, WebP, max 5MB) <span class="text-danger">* Kích thước 1900 x 800</span>
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tiêu đề banner</label>
                            <input type="text" name="title_banner" class="form-control @error('title_banner') is-invalid @enderror"
                                   value="{{ old('title_banner', $setting->title_banner ?? '') }}" placeholder="Tiêu đề chính">
                            @error('title_banner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phụ đề banner</label>
                            <input type="text" name="sub_title_banner" class="form-control @error('sub_title_banner') is-invalid @enderror"
                                   value="{{ old('sub_title_banner', $setting->sub_title_banner ?? '') }}" placeholder="Phụ đề">
                            @error('sub_title_banner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Color Settings --}}
                    <div class="color-settings-section">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-palette me-2"></i>Cài đặt màu sắc văn bản
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-heading me-2"></i>Màu sắc tiêu đề banner
                                </label>
                                <div class="color-input-wrapper">
                                    <input type="color"
                                           name="color_title_banner"
                                           class="form-control color-picker @error('color_title_banner') is-invalid @enderror"
                                           value="{{ old('color_title_banner', $setting->color_title_banner ?? '#ffffff') }}"
                                           id="colorTitleBanner">
                                    <input type="text"
                                           class="form-control color-text-input"
                                           value="{{ old('color_title_banner', $setting->color_title_banner ?? '#ffffff') }}"
                                           id="colorTitleBannerText"
                                           placeholder="#ffffff">
                                </div>
                                @error('color_title_banner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Màu sắc hiển thị của tiêu đề chính trên banner</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-font me-2"></i>Màu sắc phụ đề banner
                                </label>
                                <div class="color-input-wrapper">
                                    <input type="color"
                                           name="color_subtitle_banner"
                                           class="form-control color-picker @error('color_subtitle_banner') is-invalid @enderror"
                                           value="{{ old('color_subtitle_banner', $setting->color_subtitle_banner ?? '#f8f9fa') }}"
                                           id="colorSubTitleBanner">
                                    <input type="text"
                                           class="form-control color-text-input"
                                           value="{{ old('color_subtitle_banner', $setting->color_subtitle_banner ?? '#f8f9fa') }}"
                                           id="colorSubTitleBannerText"
                                           placeholder="#f8f9fa">
                                </div>
                                @error('color_subtitle_banner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Màu sắc hiển thị của phụ đề trên banner</small>
                            </div>
                        </div>

                        {{-- Color Preview --}}
                        <div class="color-preview-section mt-3">
                            <div class="border rounded p-3" style="background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f0f0f0 75%), linear-gradient(-45deg, transparent 75%, #f0f0f0 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;">
                                <div class="text-center py-4">
                                    <h4 id="titlePreview" style="color: {{ old('color_title_banner', $setting->color_title_banner ?? '#ffffff') }}; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); margin-bottom: 10px;">
                                        {{ old('title_banner', $setting->title_banner ?? 'Tiêu đề banner mẫu') }}
                                    </h4>
                                    <p id="subTitlePreview" style="color: {{ old('color_subtitle_banner', $setting->color_subtitle_banner ?? '#f8f9fa') }}; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); margin: 0;">
                                        {{ old('sub_title_banner', $setting->sub_title_banner ?? 'Phụ đề banner mẫu') }}
                                    </p>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-eye me-1"></i>Xem trước màu sắc văn bản trên banner
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Introduction Videos Section - Thêm section mới này sau TikTok Review -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-play-circle me-2"></i>Video giới thiệu</h5>
                </div>
                <div class="p-4">
                    <!-- Introduce Video Manufacture -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-industry me-2"></i>Video giới thiệu sản xuất
                        </label>
                        @if($setting && $setting->intro_video_manufacture_url)
                            <div class="current-file">
                                <p class="mb-2"><strong>Video hiện tại:</strong></p>
                                <video width="200" controls class="current-video">
                                    <source src="{{ $setting->intro_video_manufacture_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <p class="mt-2 mb-0 small text-muted">
                                    {{ $setting->getFirstMedia('intro_video_manufacture_url')?->name ?? 'Video sản xuất' }}
                                </p>
                            </div>
                        @endif
                        <input type="file" name="introduce_video_manufacture"
                               class="form-control @error('introduce_video_manufacture') is-invalid @enderror"
                               accept="video/*" id="manufactureVideoInput">
                        @error('introduce_video_manufacture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            @if($setting && $setting->introduce_video_manufacture_url)
                                Để trống để giữ video hiện tại
                            @else
                                Upload video giới thiệu quy trình sản xuất
                            @endif
                            (MP4, AVI, MOV, max 50MB)
                        </small>

                        <!-- Preview cho manufacture video -->
                        <div id="manufactureVideoPreview" class="mt-2" style="display: none;">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted">Preview:</small>
                                <div id="manufactureVideoPreviewContent"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Introduce Video Design -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-palette me-2"></i>Video giới thiệu thiết kế
                        </label>
                        @if($setting && $setting->intro_video_design_url)
                            <div class="current-file">
                                <p class="mb-2"><strong>Video hiện tại:</strong></p>
                                <video width="200" controls class="current-video">
                                    <source src="{{ $setting->intro_video_design_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <p class="mt-2 mb-0 small text-muted">
                                    {{ $setting->getFirstMedia('intro_video_design_url')?->name ?? 'Video thiết kế' }}
                                </p>
                            </div>
                        @endif
                        <input type="file" name="introduce_video_design"
                               class="form-control @error('introduce_video_design') is-invalid @enderror"
                               accept="video/*" id="designVideoInput">
                        @error('introduce_video_design')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            @if($setting && $setting->intro_video_design_url)
                                Để trống để giữ video hiện tại
                            @else
                                Upload video giới thiệu quy trình thiết kế
                            @endif
                            (MP4, AVI, MOV, max 50MB)
                        </small>

                        <!-- Preview cho design video -->
                        <div id="designVideoPreview" class="mt-2" style="display: none;">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted">Preview:</small>
                                <div id="designVideoPreviewContent"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- TikTok Review -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fab fa-tiktok me-2"></i>TikTok Review</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label">Video TikTok Review</label>
                        <textarea name="video_tiktok_review" class="form-control @error('video_tiktok_review') is-invalid @enderror"
                                  rows="6" placeholder="Link video (nhiều sử dụng cách dấu ,)">{{ old('video_tiktok_review', $setting->video_tiktok_review ?? '') }}</textarea>
                        @error('video_tiktok_review')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nhập các link TikTok video, phân cách bằng dấu phẩy</small>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="col-lg-4">
            <!-- Social Media -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Mạng xã hội</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fab fa-facebook-f text-primary me-2"></i>Facebook
                        </label>
                        <input type="url" name="link_social_facebook" class="form-control @error('link_social_facebook') is-invalid @enderror"
                               value="{{ old('link_social_facebook', $setting->link_social_facebook ?? '') }}" placeholder="https://facebook.com/yourpage">
                        @error('link_social_facebook')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fab fa-instagram text-danger me-2"></i>Instagram
                        </label>
                        <input type="url" name="link_social_instagram" class="form-control @error('link_social_instagram') is-invalid @enderror"
                               value="{{ old('link_social_instagram', $setting->link_social_instagram ?? '') }}" placeholder="https://instagram.com/yourprofile">
                        @error('link_social_instagram')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fab fa-tiktok text-dark me-2"></i>TikTok
                        </label>
                        <input type="url" name="link_social_tiktok" class="form-control @error('link_social_tiktok') is-invalid @enderror"
                               value="{{ old('link_social_tiktok', $setting->link_social_tiktok ?? '') }}" placeholder="https://tiktok.com/@yourprofile">
                        @error('link_social_tiktok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fab fa-youtube text-danger me-2"></i>YouTube
                        </label>
                        <input type="url" name="link_social_youtube" class="form-control @error('link_social_youtube') is-invalid @enderror"
                               value="{{ old('link_social_youtube', $setting->link_social_youtube ?? '') }}" placeholder="https://youtube.com/c/yourchannel">
                        @error('link_social_youtube')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Current Values Display (for edit mode) -->
            @if($setting)
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin hiện tại</h5>
                </div>
                <div class="p-4">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-2">
                                <small class="text-muted">Tạo lúc</small>
                                <div class="fw-bold">{{ $setting->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-2">
                                <small class="text-muted">Cập nhật lần cuối</small>
                                <div class="fw-bold">{{ $setting->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.settings.show') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <button type="submit" class="btn btn-dark">
                    <i class="fas fa-save me-2"></i>{{ $setting ? 'Cập nhật' : 'Tạo mới' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Preview uploaded files
const bannerImageInput = document.querySelector('input[name="banner_image"]');
const bannerVideoInput = document.querySelector('input[name="banner_video"]');

if (bannerImageInput) {
    bannerImageInput.addEventListener('change', function() {
        previewFile(this, 'image', 'bannerPreview');
    });
}

if (bannerVideoInput) {
    bannerVideoInput.addEventListener('change', function() {
        previewFile(this, 'video', 'bannerPreview');
    });
}

// NEW: Preview for introduction videos
const manufactureVideoInput = document.getElementById('manufactureVideoInput');
const designVideoInput = document.getElementById('designVideoInput');

if (manufactureVideoInput) {
    manufactureVideoInput.addEventListener('change', function() {
        previewVideo(this, 'manufactureVideoPreview', 'manufactureVideoPreviewContent');
    });
}

if (designVideoInput) {
    designVideoInput.addEventListener('change', function() {
        previewVideo(this, 'designVideoPreview', 'designVideoPreviewContent');
    });
}

function previewFile(input, type, previewId) {
    const preview = document.getElementById(previewId);

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            if (type === 'image') {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">`;
            } else {
                preview.innerHTML = `<video controls style="max-width: 100%; max-height: 150px;"><source src="${e.target.result}" type="video/mp4"></video>`;
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function previewVideo(input, previewContainerId, previewContentId) {
    const previewContainer = document.getElementById(previewContainerId);
    const previewContent = document.getElementById(previewContentId);

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            previewContent.innerHTML = `
                <video controls style="max-width: 100%; max-height: 120px; border-radius: 4px;">
                    <source src="${e.target.result}" type="${file.type}">
                    Your browser does not support the video tag.
                </video>
                <p class="mt-1 mb-0 small text-muted">
                    <strong>File:</strong> ${file.name}
                    <strong>Size:</strong> ${(file.size / (1024 * 1024)).toFixed(2)} MB
                </p>
            `;
            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
        previewContent.innerHTML = '';
    }
}

// File size validation
function validateFileSize(input, maxSizeMB = 50) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSizeMB = file.size / (1024 * 1024);

        if (fileSizeMB > maxSizeMB) {
            alert(`File quá lớn! Kích thước tối đa cho phép: ${maxSizeMB}MB. File của bạn: ${fileSizeMB.toFixed(2)}MB`);
            input.value = '';
            return false;
        }
    }
    return true;
}

// Add file size validation to video inputs
if (manufactureVideoInput) {
    manufactureVideoInput.addEventListener('change', function() {
        if (!validateFileSize(this, 50)) {
            document.getElementById('manufactureVideoPreview').style.display = 'none';
        }
    });
}

if (designVideoInput) {
    designVideoInput.addEventListener('change', function() {
        if (!validateFileSize(this, 50)) {
            document.getElementById('designVideoPreview').style.display = 'none';
        }
    });
}

if (bannerVideoInput) {
    bannerVideoInput.addEventListener('change', function() {
        validateFileSize(this, 50);
    });
}


// Color picker functionality
function initColorPickers() {
    // Title color picker
    const titleColorPicker = document.getElementById('colorTitleBanner');
    const titleColorText = document.getElementById('colorTitleBannerText');
    const titlePreview = document.getElementById('titlePreview');

    if (titleColorPicker && titleColorText && titlePreview) {
        titleColorPicker.addEventListener('input', function() {
            titleColorText.value = this.value.toUpperCase();
            titlePreview.style.color = this.value;
        });

        titleColorText.addEventListener('input', function() {
            const color = this.value;
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                titleColorPicker.value = color;
                titlePreview.style.color = color;
            }
        });
    }

    // Subtitle color picker
    const subTitleColorPicker = document.getElementById('colorSubTitleBanner');
    const subTitleColorText = document.getElementById('colorSubTitleBannerText');
    const subTitlePreview = document.getElementById('subTitlePreview');

    if (subTitleColorPicker && subTitleColorText && subTitlePreview) {
        subTitleColorPicker.addEventListener('input', function() {
            subTitleColorText.value = this.value.toUpperCase();
            subTitlePreview.style.color = this.value;
        });

        subTitleColorText.addEventListener('input', function() {
            const color = this.value;
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                subTitleColorPicker.value = color;
                subTitlePreview.style.color = color;
            }
        });
    }

    // Update preview text when title/subtitle inputs change
    const titleInput = document.querySelector('input[name="title_banner"]');
    const subTitleInput = document.querySelector('input[name="sub_title_banner"]');

    if (titleInput && titlePreview) {
        titleInput.addEventListener('input', function() {
            titlePreview.textContent = this.value || 'Tiêu đề banner mẫu';
        });
    }

    if (subTitleInput && subTitlePreview) {
        subTitleInput.addEventListener('input', function() {
            subTitlePreview.textContent = this.value || 'Phụ đề banner mẫu';
        });
    }
}

// Initialize color pickers when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initColorPickers();
});

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const videos = [
        document.querySelector('input[name="introduce_video_manufacture"]'),
        document.querySelector('input[name="introduce_video_design"]')
    ];

    let hasValidationError = false;

    videos.forEach(input => {
        if (input && input.files && input.files[0]) {
            if (!validateFileSize(input, 50)) {
                hasValidationError = true;
            }
        }
    });

    if (hasValidationError) {
        e.preventDefault();
        alert('Vui lòng kiểm tra lại kích thước các file video!');
        return false;
    }
});

// Function to delete banner
function deleteBanner(data,btn) {
        const deleteBtn = btn.closest('button');
        const originalContent = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        deleteBtn.disabled = true;
        fetch('{{ route("admin.settings.banner.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                media_id :data
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the banner element from DOM
                const bannerElement = deleteBtn.closest('.col-md-6');
                bannerElement.style.transition = 'all 0.3s ease';
                bannerElement.style.opacity = '0';
                bannerElement.style.transform = 'scale(0.8)';
                bannerElement.remove();
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra khi xóa banner');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            deleteBtn.innerHTML = originalContent;
            deleteBtn.disabled = false;
        });
}

</script>
@endpush
