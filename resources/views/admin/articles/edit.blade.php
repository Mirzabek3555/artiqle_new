@extends('layouts.admin')

@section('page-title', 'Maqolani tahrirlash')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="bi bi-pencil me-2"></i>{{ Str::limit($article->title, 50) }} - tahrirlash
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <!-- Davlat va Konferensiya -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-globe me-1"></i>Davlat
                        </label>
                        <select class="form-select form-select-lg" id="edit_country_id" name="country_id" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $article->conference->country_id == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }} - {{ $country->conference_name ?? 'Konferensiya nomi berilmagan' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Maqola qaysi davlat konferensiyasiga tegishli ekanligini tanlang</small>
                    </div>

                    {{-- OY KIRITISH - QOLDA --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-month me-1"></i>
                            Konferensiya oyi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="month"
                            class="form-control form-select-lg @error('month_year') is-invalid @enderror"
                            id="edit_month_year"
                            name="month_year"
                            value="{{ old('month_year', $article->conference->month_year) }}"
                            required
                        >
                        @error('month_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-info">
                            <i class="bi bi-info-circle me-1"></i>
                            O'tgan yoki kelgusi oyni kiritish mumkin. Maqola avtomatik tegishli oy to'plamiga biriktiriladi.
                        </div>
                    </div>

                    {{-- ANIQ SANA - QOLDA --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-check me-1"></i>
                            Konferensiya aniq sanasi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control form-select-lg @error('conference_date') is-invalid @enderror"
                            id="edit_conference_date"
                            name="conference_date"
                            value="{{ old('conference_date', $article->conference->conference_date?->format('Y-m-d')) }}"
                            required
                        >
                        @error('conference_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-warning">
                            <i class="bi bi-calendar3 me-1"></i>
                            Masalan: <strong>12-mart</strong>, <strong>20-aprel</strong>. PDF da aniq sana chiqadi.
                        </div>
                    </div>
                </div>

                <!-- Muallif ma'lumotlari -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <i class="bi bi-person-badge me-2"></i>Muallif ma'lumotlari
                        <small class="text-muted">(Qo'lda kiritiladi)</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-person me-1"></i>Muallif ismi (to'liq) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="author_name" 
                                       value="{{ old('author_name', $article->author_name) }}" 
                                       placeholder="Masalan: Karimov Abdulla Rashidovich" required>
                                <small class="text-muted">Sertifikatda ko'rsatiladigan ism</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-building me-1"></i>Tashkilot / Universitet
                                </label>
                                <input type="text" class="form-control" name="author_affiliation" 
                                       value="{{ old('author_affiliation', $article->author_affiliation) }}"
                                       placeholder="Masalan: Tashkent University, Uzbekistan">
                                <small class="text-muted">Muallif ish joyi yoki o'quv muassasasi</small>
                            </div>
                        </div>

                        <!-- Mavjud foydalanuvchini bog'lash (ixtiyoriy) -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-link me-1"></i>Tizim foydalanuvchisiga bog'lash (ixtiyoriy)
                                </label>
                                <select class="form-select" name="author_id">
                                    <option value="">Bog'lamaslik</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ $article->author_id == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }} ({{ $author->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Foydalanuvchi o'z kabinetida maqolasini ko'rishi uchun</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maqola ma'lumotlari -->
                <div class="card mb-4">
                    <div class="card-header bg-primary bg-opacity-10">
                        <i class="bi bi-journal-text me-2"></i>Maqola ma'lumotlari
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-type me-1"></i>Maqola sarlavhasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="title" 
                                   value="{{ old('title', $article->title) }}" 
                                   placeholder="Maqolaning to'liq nomini kiriting" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-card-text me-1"></i>Annotatsiya
                            </label>
                            <textarea class="form-control" name="abstract" rows="4" 
                                      placeholder="Maqolaning qisqacha mazmuni...">{{ old('abstract', $article->abstract) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-tags me-1"></i>Kalit so'zlar
                            </label>
                            <textarea class="form-control" name="keywords" rows="3" 
                                      placeholder="Kalit so'zlar">{{ old('keywords', $article->keywords) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-book me-1"></i>Adabiyotlar (References)
                            </label>
                            <textarea class="form-control" name="references" rows="5" 
                                      placeholder="Foydalanilgan adabiyotlar...">{{ old('references', $article->references) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Fayl yuklash -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success bg-opacity-10">
                        <i class="bi bi-cloud-upload me-2"></i>Maqola faylini yangilash (ixtiyoriy)
                    </div>
                    <div class="card-body">

                        @if($article->formatted_pdf_path)
                        <div class="alert alert-info py-2 mb-3">
                            <i class="bi bi-file-earmark-pdf me-1"></i>
                            Hozirgi fayl mavjud. Yangi fayl yuklamasangiz, joriy fayl saqlanadi.
                        </div>
                        @endif

                        <!-- Yuklash turi tanlash -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Yangilash turi</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="upload_type"
                                        id="edit_upload_type_docx" value="docx" checked
                                        onchange="toggleUploadType('docx')">
                                    <label class="form-check-label" for="edit_upload_type_docx">
                                        <i class="bi bi-file-earmark-word text-primary me-1"></i>
                                        <strong>Word (DOCX)</strong>
                                        <small class="text-muted d-block">Oddiy maqolalar</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="upload_type"
                                        id="edit_upload_type_pdf" value="pdf"
                                        onchange="toggleUploadType('pdf')">
                                    <label class="form-check-label" for="edit_upload_type_pdf">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                        <strong>PDF (tayyor)</strong>
                                        <small class="text-muted d-block">Formulalar, rasmlar, murakkab layout</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- DOCX -->
                        <div id="docx_upload_section" class="mb-2">
                            <label class="form-label fw-bold">
                                <i class="bi bi-upload me-1"></i>Yangi DOCX fayl
                            </label>
                            <input type="file" class="form-control form-control-lg @error('docx_file') is-invalid @enderror"
                                id="docx_file" name="docx_file" accept=".docx,.doc">
                            @error('docx_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Yuklasangiz, tizim DOCX → PDF konvertatsiya qiladi.</small>
                        </div>

                        <!-- PDF -->
                        <div id="pdf_upload_section" class="mb-2" style="display:none;">
                            <label class="form-label fw-bold text-danger">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Tayyor PDF fayl
                            </label>
                            <input type="file" class="form-control form-control-lg @error('pdf_file') is-invalid @enderror"
                                id="pdf_file" name="pdf_file" accept=".pdf">
                            @error('pdf_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Word'dan eksport qilgan PDF — rasmlar va formulalar asl holatda saqlanadi. Maks: 50MB</small>
                        </div>
                    </div>
                </div>

                <!-- Amallar -->
                <div class="d-flex gap-2 justify-content-between">
                    <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-1"></i>Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-1"></i>Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleUploadType(type) {
    const docxSection = document.getElementById('docx_upload_section');
    const pdfSection = document.getElementById('pdf_upload_section');
    const docxInput = document.getElementById('docx_file');
    const pdfInput = document.getElementById('pdf_file');

    if (type === 'pdf') {
        docxSection.style.display = 'none';
        pdfSection.style.display = 'block';
        if (docxInput) docxInput.value = '';
    } else {
        docxSection.style.display = 'block';
        pdfSection.style.display = 'none';
        if (pdfInput) pdfInput.value = '';
    }
}
</script>
@endpush


