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
            padding: 5px; 
            text-align: center;
            vertical-align: top;
        }

        .sticker {
            padding: 5px;
            background-color: #fff;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
            height: 68px;
            /* border: 1px solid #000; */
            margin: 0; 
        }

        .sticker h2 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            display: inline; 
        }
        
        .sticker h1 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            display: inline; 
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
            height: 20px; /* Adjust this value to make the barcode smaller */
            display: block;
            margin: 0 auto;
        }

        .inline-headers h2 {
            display: inline; 
            margin-right: 5px; 
        }
    </style>
</head>
<body>
    <table>
        @php
            $count = 0;
        @endphp

        @foreach ($datas as $data)
            @if ($count % 4 == 0)
                <tr> 
            @endif

            <td>
                <div class="sticker">
                    <h1>Metro Men</h1>
                    <div class="inline-headers">
                        <h2>{{ $data->category->name ?? '' }}</h2>
                        <h2>{{ $data->color->name ?? 'N/A'}} ({{ $data->size->size ?? ''}})</h2>
                    </div>
                    <div class="barcode">
                        <img src="{{ $data->barcode }}" alt="Barcode">
                    </div>
                    <div class="price">TK: {{ number_format($data->price, 2) }}</div>
                </div>
            </td>

            @php
                $count++;
            @endphp

            @if ($count % 4 == 0)
                </tr> 
            @endif
        @endforeach

        @if ($count % 3 != 0)
            @for ($i = 0; $i < (4 - $count % 4); $i++)
                <td></td>
            @endfor
            </tr>
        @endif
    </table>
</body>
</html>