@php
    $photo = $getRecord()->photo;
@endphp

@if (filled($photo))
    <img
        src="{{ route('book.image', ['path' => $photo]) }}"
        alt="Book"
        style="
            width:150px;
            height:220px;
            object-fit:cover;
            border-radius:8px;
        "
    >
@else
    <div
        style="
            width:150px;
            height:220px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:1px solid #d1d5db;
            border-radius:8px;
            background:#f9fafb;
            color:#6b7280;
            text-align:center;
            padding:10px;
            font-size:14px;
        "
    >
        Image not available
    </div>
@endif