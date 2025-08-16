<?php

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn - {{ $invoice->invoice_code ?? 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @font-face {
            font-family: 'DejaVuSans';
            src: local('DejaVuSans');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'DejaVuSans';
            src: local('DejaVuSans-Bold');
            font-weight: bold;
            font-style: normal;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 20%;
            width: 60%;
            font-size: 60px;
            color: rgba(0, 0, 0, 0.08);
            transform: rotate(-45deg);
            transform-origin: center;
            z-index: -1;
            text-align: center;
            pointer-events: none;
            font-weight: bold;
            text-transform: uppercase;
        }

        .watermark.pending {
            color: rgba(234, 179, 8, 0.1);
            /* Vàng */
        }

        .watermark.paid {
            color: rgba(22, 163, 74, 0.1);
            /* Xanh lá */
        }

        .watermark.overdue {
            color: rgba(220, 38, 38, 0.1);
            /* Đỏ */
        }

        *,
        ::before,
        ::after {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb;
        }

        ::before,
        ::after {
            --tw-content: '';
        }

        html {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -moz-tab-size: 4;
            tab-size: 4;
            font-family: 'DejaVuSans', 'DejaVu Sans', 'Liberation Sans', Arial, sans-serif;
            font-feature-settings: normal;
            font-variation-settings: normal;
        }

        body {
            margin: 0;
            line-height: inherit;
            font-family: 'DejaVuSans', 'DejaVu Sans', 'Liberation Sans', Arial, sans-serif;
        }

        hr {
            height: 0;
            color: inherit;
            border-top-width: 1px;
        }

        abbr:where([title]) {
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit;
        }

        a {
            color: inherit;
            text-decoration: inherit;
        }

        b,
        strong {
            font-weight: bolder;
        }

        code,
        kbd,
        samp,
        pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 1em;
        }

        small {
            font-size: 80%;
        }

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }

        table {
            text-indent: 0;
            border-collapse: collapse;
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            font-feature-settings: inherit;
            font-variation-settings: inherit;
            font-size: 100%;
            font-weight: inherit;
            line-height: inherit;
            color: inherit;
            margin: 0;
            padding: 0;
        }

        button,
        select {
            text-transform: none;
        }

        button,
        [type='button'],
        [type='reset'],
        [type='submit'] {
            -webkit-appearance: button;
            background-color: transparent;
            background-image: none;
        }

        :-moz-focusring {
            outline: auto;
        }

        :-moz-ui-invalid {
            box-shadow: none;
        }

        progress {
            vertical-align: baseline;
        }

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto;
        }

        [type='search'] {
            -webkit-appearance: textfield;
            outline-offset: -2px;
        }

        ::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit;
        }

        summary {
            display: list-item;
        }

        blockquote,
        dl,
        dd,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        figure,
        p,
        pre {
            margin: 0;
        }

        fieldset {
            margin: 0;
            padding: 0;
        }

        legend {
            padding: 0;
        }

        ol,
        ul,
        menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        dialog {
            padding: 0;
        }

        textarea {
            resize: vertical;
        }

        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            color: #9ca3af;
        }

        button,
        [role="button"] {
            cursor: pointer;
        }

        :disabled {
            cursor: default;
        }

        img,
        svg,
        video,
        canvas,
        audio,
        iframe,
        embed,
        object {
            display: block;
            vertical-align: middle;
        }

        img,
        video {
            max-width: 100%;
            height: auto;
        }

        [hidden] {
            display: none;
        }

        *,
        ::before,
        ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
        }

        .fixed {
            position: fixed;
        }

        .bottom-0 {
            bottom: 0px;
        }

        .left-0 {
            left: 0px;
        }

        .table {
            display: table;
        }

        .h-12 {
            height: 3rem;
        }

        .w-1\/2 {
            width: 50%;
        }

        .w-full {
            width: 100%;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .border-spacing-0 {
            --tw-border-spacing-x: 0px;
            --tw-border-spacing-y: 0px;
            border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
        }

        .whitespace-nowrap {
            white-space: nowrap;
        }

        .border-b {
            border-bottom-width: 1px;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-r {
            border-right-width: 1px;
        }

        .border-main {
            border-color: #5c6ac4;
        }

        .bg-main {
            background-color: #5c6ac4;
        }

        .bg-slate-100 {
            background-color: #f1f5f9;
        }

        .p-3 {
            padding: 0.75rem;
        }

        .px-14 {
            padding-left: 3.5rem;
            padding-right: 3.5rem;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .py-10 {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .py-6 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .pb-3 {
            padding-bottom: 0.75rem;
        }

        .pl-2 {
            padding-left: 0.5rem;
        }

        .pl-3 {
            padding-left: 0.75rem;
        }

        .pl-4 {
            padding-left: 1rem;
        }

        .pr-3 {
            padding-right: 0.75rem;
        }

        .pr-4 {
            padding-right: 1rem;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .align-top {
            vertical-align: top;
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .text-lg {
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-normal {
            font-weight: 400;
        }

        .italic {
            font-style: italic;
        }

        .text-main {
            color: #5c6ac4;
        }

        .text-neutral-600 {
            color: #525252;
        }

        .text-neutral-700 {
            color: #404040;
        }

        .text-slate-300 {
            color: #cbd5e1;
        }

        .text-slate-400 {
            color: #94a3b8;
        }

        .text-white {
            color: #fff;
        }

        .text-yellow-600 {
            color: #ca8a04;
        }

        .text-green-600 {
            color: #16a34a;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        @page {
            margin: 10mm 5mm 10mm 5mm;
            /* top, right, bottom, left */
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }

        .list-disc {
            list-style-type: disc;
        }

        .pl-6 {
            padding-left: 1.5rem;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-8 {
            gap: 2rem;
        }

        .space-y-1>*+* {
            margin-top: 0.25rem;
        }

        .space-y-6>*+* {
            margin-top: 1.5rem;
        }

        .text-justify {
            text-align: justify;
        }

        .leading-relaxed {
            line-height: 1.625;
        }

        .border-l-4 {
            border-left-width: 4px;
        }

        .border-yellow-600 {
            border-color: #ca8a04;
        }

        .bg-slate-50 {
            background-color: #f8fafc;
        }
    </style>
</head>

<body>
    <div>
        <div class="watermark {{ $invoice->status }}">
            @if ($invoice->status == 'pending')
                CHỜ THANH TOÁN
            @elseif ($invoice->status == 'paid')
                ĐÃ THANH TOÁN
            @elseif ($invoice->status == 'overdue')
                QUÁ HẠN
            @else
                {{ strtoupper($invoice->status) }}
            @endif
        </div>

        <div class="py-4">
            <div class="px-14 pb-6">
                <table class="w-full border-collapse border-spacing-0">
                    <tbody>
                        <tr>
                            <td class="w-full align-top">
                                <div>
                                    <img src="{{ asset('storage/' . $club->image) }}" alt="Logo" class="w-24 h-24">
                                </div>
                            </td>

                            <td class="align-top">
                                <div class="text-sm">
                                    <table class="border-collapse border-spacing-0">
                                        <tbody>
                                            <tr>
                                                <td class="border-r pr-4">
                                                    <div>
                                                        <p class="whitespace-nowrap text-slate-400 text-right">Ngày Tạo
                                                        </p>
                                                        <p class="whitespace-nowrap font-bold text-main text-right">
                                                            {{ $invoice->created_at ? $invoice->created_at->format('d/m/Y H:i:s') : '-' }}
                                                        </p>
                                                    </div>
                                                </td>
                                                <td class="pl-4">
                                                    <div>
                                                        <p class="whitespace-nowrap text-slate-400 text-right">Hạn Thanh
                                                            Toán</p>
                                                        <p class="whitespace-nowrap font-bold text-main text-right">
                                                            {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y H:i:s') : '-' }}
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-slate-100 px-14 py-6 text-sm">
                <h1 class="text-center text-xl font-bold text-main">HÓA ĐƠN THANH TOÁN</h1>
                <div class="text-center text-xl font-normal text-neutral-700 mb-2"
                    style="font-family: 'DejaVuSans', 'DejaVu Sans', sans-serif; font-size: 25px; font-weight: 400;">
                    Số hóa đơn: {{ $invoice->invoice_code ?? 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }} |
                    Trạng thái:
                    @if ($invoice->status == 'pending')
                        <span class="text-yellow-600">CHỜ THANH TOÁN</span>
                    @elseif($invoice->status == 'paid')
                        <span class="text-green-600">ĐÃ THANH TOÁN</span>
                    @elseif($invoice->status == 'overdue')
                        <span class="text-red-600">QUÁ HẠN</span>
                    @else
                        {{ $invoice->status }}
                    @endif
                </div>
                <table class="w-full border-collapse border-spacing-0">
                    <tbody>
                        <tr>
                            <td class="w-1/2 align-top border-r">
                                <p class="font-bold">ĐƠN VỊ BÁN HÀNG</p>
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold">{{ $club->name ?? 'Câu lạc bộ' }}</p>
                                    <p>Email: {{ $club->email ?? '-' }}</p>
                                    <p>Điện thoại: {{ $club->phone ?? '-' }}</p>
                                    <p>Địa chỉ: {{ $club->address ? $club->address->address : '-' }}</p>
                                </div>
                            </td>
                            <td class="w-1/2 align-top pl-2">
                                <p class="font-bold">ĐƠN VỊ MUA HÀNG</p>
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold">{{ $invoice->user->name ?? '-' }}
                                        ({{ $invoice->user->student_id ?? '-' }})</p>
                                    <p>Email: {{ $invoice->user->email ?? '-' }}</p>
                                    <p>Điện thoại: {{ $invoice->user->phone ?? '-' }}</p>
                                    <p>Địa chỉ: @if ($invoice->user->address)
                                            {{ $invoice->user->address->address }},
                                            {{ $invoice->user->address->commune->name }},
                                            {{ $invoice->user->address->province->name }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-14 py-10 text-sm text-neutral-700">
                <table class="w-full border-collapse border-spacing-0">
                    <thead>
                        <tr>
                            <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">#</td>
                            <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Tên Dịch Vụ</td>
                            <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main">Số Tiền</td>
                            <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main">Đã Thanh Toán
                            </td>
                            <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main">Còn Lại</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-b py-3 pl-3">1.</td>
                            <td class="border-b py-3 pl-2">{{ $invoice->name }}</td>
                            <td class="border-b py-3 pl-2 text-center">
                                {{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</td>
                            <td class="border-b py-3 pl-2 text-center">
                                {{ number_format($invoice->total_paid, 0, ',', '.') }} VNĐ</td>
                            <td class="border-b py-3 pl-2 text-center">
                                {{ number_format($invoice->remaining_amount, 0, ',', '.') }} VNĐ</td>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <table class="w-full border-collapse border-spacing-0">
                                    <tbody>
                                        <tr>
                                            <td class="w-full"></td>
                                            <td>
                                                <table class="w-full border-collapse border-spacing-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="border-b p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Tổng tiền:
                                                                </div>
                                                            </td>
                                                            <td class="border-b p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">
                                                                    {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                                                    VNĐ</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Đã thanh
                                                                    toán:
                                                                </div>
                                                            </td>
                                                            <td class="p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">
                                                                    {{ number_format($invoice->total_paid, 0, ',', '.') }}
                                                                    VNĐ</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-main p-3">
                                                                <div class="whitespace-nowrap font-bold text-white">
                                                                    Còn lại:</div>
                                                            </td>
                                                            <td class="bg-main p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-white">
                                                                    {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                                                                    VNĐ</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($invoice->transactions && $invoice->transactions->count() > 0)
                <div class="px-14 py-6 text-sm text-neutral-700">
                    <p class="text-main font-bold mb-4">LỊCH SỬ THANH TOÁN</p>
                    <table class="w-full border-collapse border-spacing-0">
                        <thead>
                            <tr>
                                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">#</td>
                                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Thời Gian</td>
                                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main text-center">Số Tiền
                                </td>
                                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main text-center">Phương Thức
                                </td>
                                <td class="border-b-2 border-main pb-3 pl-2 pr-3 font-bold text-main">Ghi Chú</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->transactions as $transaction)
                                <tr>
                                    <td class="border-b py-3 pl-3">{{ $loop->iteration }}</td>
                                    <td class="border-b py-3 pl-3">
                                        {{ $transaction->payment_date ? $transaction->payment_date->format('d/m/Y H:i:s') : '-' }}
                                    </td>
                                    <td class="border-b py-3 pl-2 text-center">
                                        {{ number_format($transaction->paid_amount, 0, ',', '.') }} VNĐ</td>
                                    <td class="border-b py-3 pl-2 text-center">{{ $transaction->payment_method }}</td>
                                    <td class="border-b py-3 pl-2 pr-3">{{ $transaction->note ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif


            @if ($invoice->note)
                <div class="px-14 py-10 text-sm text-neutral-700">
                    <p class="text-main font-bold">Ghi chú:</p>
                    <p class="italic">{{ $invoice->note }}</p>
                </div>
            @endif

            {{-- <!-- Page break for terms and conditions -->
            <div style="page-break-before: always;"></div>

            <!-- Payment Terms and Conditions -->
            <div class="px-14 text-sm text-neutral-700">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <h2 class="text-xl font-bold text-main mb-2">ĐIỀU KHOẢN THANH TOÁN HÓA ĐƠN</h2>
                    <div class="w-24 h-1 bg-main mx-auto"></div>
                </div>

                <!-- Two Column Layout for Terms -->
                <div class="grid grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Section I -->
                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">I. Phạm Vi Áp Dụng</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3">Điều khoản thanh toán này áp dụng cho các hóa đơn do
                                    <strong>{{ $club->name ?? '-' }}</strong> phát hành, phục vụ cho mục đích
                                    thu phí hoạt động, chương trình, dịch vụ, sự kiện, hoặc các khoản đóng góp tự nguyện
                                    khác thuộc phạm vi tổ chức và quản lý của Câu lạc bộ.
                                </p>
                                <p class="mb-3">Hóa đơn này là căn cứ xác nhận nghĩa vụ thanh toán của thành viên hoặc
                                    người tham gia, đồng thời phản ánh cam kết về tài chính trong việc duy trì, phát
                                    triển hoạt động chung của Câu lạc bộ.</p>
                            </div>
                        </div>

                        <!-- Section II -->
                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">II. Nội Dung Thanh Toán</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3">Các khoản mục được liệt kê trên hóa đơn bao gồm:</p>
                                <ul class="list-disc pl-6 mb-3 space-y-1">
                                    <li>Phí tham gia hoạt động hoặc sự kiện.</li>
                                    <li>Phí duy trì thành viên theo kỳ.</li>
                                    <li>Khoản đóng góp tự nguyện (nếu có).</li>
                                    <li>Các dịch vụ hoặc tài liệu học thuật, đào tạo do Câu lạc bộ cung cấp.</li>
                                </ul>
                                <p class="mb-3">Tất cả các khoản thu được xác lập trên nguyên tắc tự nguyện, minh
                                    bạch và thống nhất theo quy chế hoạt động của Câu lạc bộ, được Ban Chủ Nhiệm xét
                                    duyệt và thông báo rộng rãi đến các thành viên.</p>
                            </div>
                        </div>

                        <!-- Section III -->
                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">III. Thời Hạn Và Trách Nhiệm Thanh Toán</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3">Người nhận hóa đơn có trách nhiệm thanh toán đầy đủ và đúng hạn số
                                    tiền ghi trên hóa đơn.</p>
                                <p class="mb-3">Quá thời hạn này, Câu lạc bộ có quyền xem xét:</p>
                                <ul class="list-disc pl-6 mb-3 space-y-1">
                                    <li>Tạm ngưng quyền tham gia hoạt động.</li>
                                    <li>Không xác nhận tham gia đối với các hoạt động học thuật, thực tế, ngoại khóa.
                                    </li>
                                    <li>Tạm giữ các giấy chứng nhận/kết quả liên quan đến hoạt động của Câu lạc bộ cho
                                        đến khi hoàn tất nghĩa vụ tài chính.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">IV. Phương Thức Thanh Toán</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3"><strong>Thanh toán trực tuyến:</strong></p>
                                <p class="mb-3">Thành viên có thể thực hiện thanh toán trực tiếp qua hệ thống quản lý
                                    Câu lạc bộ (online) bằng các phương thức được hỗ trợ: ví điện tử, chuyển khoản ngân
                                    hàng, cổng thanh toán tích hợp (nếu có).</p>
                                <p class="mb-3">Hệ thống sẽ tự động cập nhật trạng thái thanh toán và gửi xác nhận
                                    qua email hoặc thông báo nội bộ.</p>
                                <p class="mb-3"><strong>Thanh toán trực tiếp:</strong></p>
                                <p class="mb-3">Trường hợp không thể thanh toán online, thành viên có thể thanh toán
                                    trực tiếp cho Ban Chủ Nhiệm tại các buổi sinh hoạt chính thức, hoặc theo hướng dẫn
                                    cụ thể qua thông báo nội bộ.</p>
                                <p class="mb-3">Thành viên cần yêu cầu và lưu giữ phiếu xác nhận thanh toán thủ công
                                    (nếu có) để đối chiếu khi cần thiết.</p>
                            </div>
                        </div>

                        <!-- Section V -->
                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">V. Hoàn Tiền Và Điều Chỉnh</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3">Câu lạc bộ không hoàn lại khoản tiền đã thanh toán, trừ các trường
                                    hợp đặc biệt như:</p>
                                <ul class="list-disc pl-6 mb-3 space-y-1">
                                    <li>Hủy sự kiện có thông báo chính thức từ Ban Tổ chức.</li>
                                    <li>Sai sót trong quá trình xử lý hoặc hệ thống ghi nhận nhầm lặp hóa đơn.</li>
                                </ul>
                                <p class="mb-3">Mọi yêu cầu điều chỉnh hóa đơn cần được gửi về Ban Tài chính hoặc Ban
                                    Chủ Nhiệm trong vòng 07 ngày làm việc kể từ ngày phát hành hóa đơn.</p>
                            </div>
                        </div>

                        <!-- Section VI -->
                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">VI. Trách Nhiệm Và Cam Kết</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3">Việc nhận hóa đơn đồng nghĩa với việc thành viên đã hiểu và đồng ý
                                    với toàn bộ điều khoản thanh toán này.</p>
                                <p class="mb-3">Câu lạc bộ cam kết sử dụng khoản phí đúng mục đích, phục vụ hoạt động
                                    chung, công khai minh bạch tài chính định kỳ theo đúng quy chế nội bộ.</p>
                                <p class="mb-3">Các khiếu nại, góp ý liên quan đến hóa đơn sẽ được tiếp nhận và xử lý
                                    nghiêm túc, minh bạch, đảm bảo quyền lợi chính đáng của thành viên.</p>
                            </div>
                        </div>

                        <div class="border-l-4 border-main pl-4">
                            <h3 class="font-bold text-main mb-3 text-base">VII. Liên Hệ Hỗ Trợ</h3>
                            <div class="text-justify leading-relaxed">
                                <p class="mb-3">Nếu có bất kỳ thắc mắc, điều chỉnh hoặc xác minh thông tin thanh
                                    toán, vui
                                    lòng liên hệ:</p>
                                <p class="mb-3"><span class="font-bold">Ban tài chính / Ban chủ nhiệm:</span>
                                    {{ $club->name ?? '-' }}
                                </p>
                                <p class="mb-3"><span class="font-bold">Email:</span> {{ $club->email ?? '-' }}</p>
                                <p class="mb-3"><span class="font-bold">Điện thoại:</span>
                                    {{ $club->phone ?? '-' }}
                                </p>
                                <p class="mb-3"><span class="font-bold">Địa chỉ:</span>
                                    {{ $club->address ? $club->address->address : '-' }}
                                </p>

                            </div>
                        </div>
                    </div>

                </div>

                <br>

                <!-- Footer Note -->
                <div class="mt-5 p-5 bg-slate-100 rounded border-l-4 border-yellow-600">
                    <p class="text-xs text-neutral-600 italic text-center">
                        Điều khoản này có thể thay đổi theo từng kỳ hoạt động. Các thay đổi (nếu có) sẽ được thông
                        báo rõ ràng đến toàn bộ thành viên thông qua hệ thống quản lý hoặc văn bản chính thức của Ban
                        Chủ Nhiệm.
                    </p>
                </div>
            </div> --}}

            <footer class="fixed bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
                {{ $club->name ?? '-' }}
                <span class="text-slate-300 px-2">|</span>
                {{ $club->email ?? '-' }}
                <span class="text-slate-300 px-2">|</span>
                {{ $club->phone ?? '-' }}
            </footer>
        </div>
    </div>
</body>

</html>
