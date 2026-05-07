<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $invoice->id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #f3f4f6;
            font-family: 'Arial', 'Inter', sans-serif;
            color: #333;
        }

        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px 50px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: white;
                -webkit-print-color-adjust: exact;
                margin: 0;
                padding: 1cm;
            }

            .no-print {
                display: none !important;
            }

            .receipt-container {
                box-shadow: none !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                border: none !important;
                padding: 0 !important;
                page-break-inside: avoid;
            }
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            font-size: 28px;
            font-weight: 900;
            color: #1a202c;
            letter-spacing: 1px;
        }

        .logo-area svg {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            fill: #1a202c;
        }

        .company-details {
            text-align: left;
            font-size: 13px;
            line-height: 1.6;
            color: #444;
        }

        .company-details h2 {
            font-size: 22px;
            font-weight: bold;
            color: #1a202c;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
        }

        .divider {
            border: 0;
            border-bottom: 2px solid #ccc;
            margin: 15px 0 25px 0;
        }

        /* Meta Info */
        .meta-area {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .bill-to {
            line-height: 1.6;
        }

        .bill-to h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: bold;
            color: #1a202c;
            text-transform: uppercase;
        }

        .invoice-details table {
            border-collapse: collapse;
        }

        .invoice-details td {
            padding: 4px 8px;
        }

        .invoice-details td:first-child {
            font-weight: bold;
            text-align: right;
            color: #1a202c;
        }

        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 12px 15px;
        }

        .items-table th {
            background: #f9f9f9;
            text-align: left;
            font-weight: bold;
            color: #1a202c;
        }

        /* Footer Box */
        .footer-box {
            border: 1px solid #ccc;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            line-height: 1.8;
            margin-top: 30px;
        }

        /* Seal */
        .seal-container {
            text-align: center;
        }

        .seal-container>strong {
            font-size: 14px;
            color: #1a202c;
            display: block;
            margin-bottom: 10px;
        }

        .seal-circle {
            width: 110px;
            height: 110px;
            border: 2px solid #1a202c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin: 0 auto;
            padding: 5px;
        }

        .seal-circle-inner {
            width: 100%;
            height: 100%;
            border: 1px dashed #1a202c;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: bold;
            color: #000;
        }
    </style>
</head>

<body class="antialiased">

    @php
        function convertNumberToWords($number)
        {
            $words = [
                0 => 'ZERO',
                1 => 'ONE',
                2 => 'TWO',
                3 => 'THREE',
                4 => 'FOUR',
                5 => 'FIVE',
                6 => 'SIX',
                7 => 'SEVEN',
                8 => 'EIGHT',
                9 => 'NINE',
                10 => 'TEN',
                11 => 'ELEVEN',
                12 => 'TWELVE',
                13 => 'THIRTEEN',
                14 => 'FOURTEEN',
                15 => 'FIFTEEN',
                16 => 'SIXTEEN',
                17 => 'SEVENTEEN',
                18 => 'EIGHTEEN',
                19 => 'NINETEEN',
                20 => 'TWENTY',
                30 => 'THIRTY',
                40 => 'FORTY',
                50 => 'FIFTY',
                60 => 'SIXTY',
                70 => 'SEVENTY',
                80 => 'EIGHTY',
                90 => 'NINETY'
            ];
            if ($number < 20)
                return $words[$number];
            if ($number < 100)
                return $words[$number - ($number % 10)] . ($number % 10 ? ' ' . $words[$number % 10] : '');
            if ($number < 1000)
                return $words[floor($number / 100)] . ' HUNDRED' . ($number % 100 ? ' AND ' . convertNumberToWords($number % 100) : '');
            if ($number < 100000)
                return convertNumberToWords(floor($number / 1000)) . ' THOUSAND' . ($number % 1000 ? ' ' . convertNumberToWords($number % 1000) : '');
            if ($number < 10000000)
                return convertNumberToWords(floor($number / 100000)) . ' LAKH' . ($number % 100000 ? ' ' . convertNumberToWords($number % 100000) : '');
            return convertNumberToWords(floor($number / 10000000)) . ' CRORE' . ($number % 10000000 ? ' ' . convertNumberToWords($number % 10000000) : '');
        }

        $amountInWords = convertNumberToWords((int) $invoice->price) . ' ONLY';
    @endphp

    <div class="text-center no-print mt-8 mb-4">
        <button onclick="window.print()"
            class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold shadow">
            Print Invoice (A4)
        </button>
        <a href="{{ route('invoices.index') }}"
            class="ml-4 px-6 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-bold shadow">
            Back to Invoices
        </a>
    </div>

    <div class="receipt-container">

        <!-- Header -->
        <div class="header">
            <div class="logo-area">
                <img src="{{asset('build/assets/img/alphahalogo.png')}}" alt="" style="width: 300px;">
            </div>

            <div class="company-details">
                <h2>Alpha Academy</h2>
                Noble Arcade Tower, Sub Registrar Office Rd,<br>
                Caltex Junction, Kannur, Kerala 670002<br>
                <strong>Mob :</strong> +91 6238140310<br>
                <strong>Email :</strong> alphahsad@gmail.com
            </div>
        </div>

        <hr class="divider">

        <!-- Meta Area -->
        <div class="meta-area">
            <div class="bill-to">
                <strong>Bill To:</strong><br><br>
                <h3>{{ $invoice->student_name }}</h3>
                <span style="color: #666;">Student</span><br>
                @if($invoice->batch)
                    <span
                        style="color: #666; font-size: 12px; margin-top: 5px; display: inline-block;"><strong>Batch:</strong>
                        {{ $invoice->batch }}</span>
                @endif
            </div>

            <div class="invoice-details">
                <table>
                    <tr>
                        <td>Invoice #:</td>
                        <td>{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td>Date:</td>
                        <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Payment Mode:</td>
                        <td>{{ $invoice->payment_type }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 70%">Service</th>
                    <th style="width: 25%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>
                        {{ $invoice->service }}<br>
                        <span style="font-size: 12px; color: #666;">Payment for {{ strtolower($invoice->service) }}
                            services.</span>
                    </td>
                    <td style="text-align: right;">{{ number_format($invoice->price, 2) }}</td>
                </tr>

                <!-- Blank row for spacing -->
                <tr>
                    <td style="padding: 25px;"></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Totals Section -->
                <tr>
                    <td colspan="3" style="padding: 0; border: none;">
                        <div style="display: flex; border: 1px solid #ccc; border-top: none;">
                            <div style="flex: 1; padding: 20px 15px; border-right: 1px solid #ccc;">
                                <span style="color: #555;">Total Amount (in words) :</span><br><br>
                                <strong>INR {{ $amountInWords }}</strong>
                            </div>
                            <div style="width: 350px;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px;">
                                            <strong>Sub Total :</strong></td>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px;">
                                            {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px;">
                                            Rounding off:</td>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px;">
                                            0.00</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px; background: #f9f9f9;">
                                            <strong>Total:</strong></td>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px; background: #f9f9f9;">
                                            <strong>{{ number_format($invoice->price, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px;">
                                            Paid Amount:</td>
                                        <td
                                            style="text-align: right; border-bottom: 1px solid #eee; padding: 8px 15px;">
                                            {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding: 8px 15px;"><strong>Balance:</strong></td>
                                        <td style="text-align: right; padding: 8px 15px;"><strong>0.00</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Footer / Bank Details -->
        <div class="footer-box">
            <div>
                <strong>For the amount yet to be paid:</strong><br><br>
                <img src="{{asset('build/assets/img/qrgpay.png')}}" alt="" style="width: 125px;">

            </div>

            <div class="seal-container">
                <strong>Authorized Seal</strong>
                <!-- Replace this with actual seal image if needed -->
                <img src="{{asset('build/assets/img/seal.png')}}" alt="" style="width: 125px;">
            </div>
        </div>

        <div style="text-align: center; margin-top: 25px; font-size: 12px; color: #777; font-style: italic;">
            This is a system generated invoice.
        </div>
    </div>

    </div>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>