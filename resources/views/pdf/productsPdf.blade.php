<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Price Stickers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 20px;
            text-align: center;
            vertical-align: top;
        }

        .sticker {
            padding: 5px;
            background-color: #fff;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
            height: 60px;
        }

        .sticker h2 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
        }

        .sticker .price {
            font-size: 14px;
            font-weight: bold;
        }

        .barcode {
            margin-top: 2px;
            display: inline-block;
        }

        img {
            height: 20px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <table>
        @php
            $count = 0;
        @endphp

        @foreach ($datas as $data)
            @if ($count % 5 == 0)
                <tr> 
            @endif

            <td>
                <div class="sticker">
                    <h2>Metro Men</h2>
                    <h2>{{ $data->category->name ?? '' }}</h2>
                    <h2>{{ $data->color->name ?? 'N/A'}} ({{ $data->size->size ?? ''}})</h2>
                    <div class="barcode">
                        <img src="{{ $data->barcode }}" alt="Barcode">
                    </div>
                    <div class="price">TK: {{ number_format($data->price, 2) }}</div>
                </div>
            </td>

            @php
                $count++;
            @endphp

            @if ($count % 5 == 0)
                </tr> 
            @endif
        @endforeach

        @if ($count % 5 != 0)
            @for ($i = 0; $i < (5 - $count % 5); $i++)
                <td></td>
            @endfor
            </tr>
        @endif
    </table>
</body>
</html>
