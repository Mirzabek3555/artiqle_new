<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $conference->title }} - Info</title>
    <style>
        @page {
            margin: 25mm 20mm 25mm 33mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
            line-height: 1.5;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .main-title {
            font-size: 16px;
            font-weight: bold;
            color: #17428f;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 16px;
            font-weight: bold;
            color: #17428f;
            margin-bottom: 5px;
        }

        .separator {
            border-top: 1.5px solid #17428f;
            margin-bottom: 5px;
        }

        .date-text {
            color: #cc0000;
            text-align: center;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .paragraph {
            text-align: justify;
            text-indent: 1.25cm;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .paragraph-bold {
            font-weight: bold;
        }

        .editor-section {
            text-align: center;
            margin-bottom: 20px;
            font-size: 15px;
            line-height: 1.3;
        }

        .editor-title {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .editor-name {
            font-weight: bold;
        }

        .email-link {
            color: #0000EE;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $confMainTitle = strtoupper($country->conference_name ?? 'THE LATEST PEDAGOGICAL AND PSYCHOLOGICAL INNOVATIONS IN EDUCATION');
        $confSubTitle = 'International online conference.';
        
        $dateObj = \Carbon\Carbon::parse($conference->conference_date);
        $dateFormatted = $dateObj->format('jS F-Y');
        $dateFormattedNumeric = $dateObj->format('d.m.Y');
        
        $quoteTitle = ucfirst(strtolower($confMainTitle));

        $alpha3map = [
            'UZB' => 'UZ', 'GBR' => 'GB', 'USA' => 'US', 'DEU' => 'DE',
            'FRA' => 'FR', 'ITA' => 'IT', 'ESP' => 'ES', 'RUS' => 'RU',
            'JPN' => 'JP', 'CHN' => 'CN', 'KOR' => 'KR', 'TUR' => 'TR',
            'POL' => 'PL', 'KAZ' => 'KZ', 'IND' => 'IN', 'BRA' => 'BR',
            'CAN' => 'CA', 'TKM' => 'TM', 'AZE' => 'AZ', 'TJK' => 'TJ', 'KGZ' => 'KG',
            'DNK' => 'DK', 'SWE' => 'SE', 'NOR' => 'NO', 'FIN' => 'FI',
            'NLD' => 'NL', 'BEL' => 'BE', 'CHE' => 'CH', 'AUT' => 'AT',
            'PRT' => 'PT', 'GRC' => 'GR', 'SAU' => 'SA', 'ARE' => 'AE',
        ];

        // Fixed Chief Editor for all conferences
        $editor = [
            'name'  => 'Theron Blackwell',
            'title' => 'Chief Editor',
            'uni'   => 'International Scientific Conferences Publishing Group',
            'email' => 't.blackwell@iscpublishing.org',
        ];
    @endphp

    <div class="text-center main-title">{{ $confMainTitle }}.</div>
    <div class="text-center sub-title">{{ $confSubTitle }}</div>
    
    <div class="separator"></div>

    <div class="date-text">Date: {{ $dateFormatted }}</div>

    <div class="paragraph">
        “{{ $quoteTitle }}”. Collection of scientific papers on materials of the international scientific-practical conference {{ $dateFormattedNumeric }}, Pub. "ISC", {{ $editor['city'] }}, {{ $totalPages ?? 163 }} p.
    </div>

    <div class="editor-section">
        <div class="editor-title">Editor:</div>
        <div class="editor-name">{{ $editor['name'] }}</div>
        <div>{{ $editor['title'] }}</div>
        <div>{{ $editor['uni'] }}</div>
        <div style="margin-top: 5px;">
            <span class="editor-name">Email: </span><a href="mailto:{{ $editor['email'] }}" class="email-link">{{ $editor['email'] }}</a>
        </div>
    </div>

    <div class="paragraph">
        The collection of published scientific papers is a scientific and practical publication, which includes scientific articles from students, teachers, candidates of sciences, doctoral students, and independent researchers. The articles contain a study that reflects the processes and changes in the structure of modern science. The collection of scientific articles is intended for students, doctoral students, teachers, researchers, practitioners, and those interested in the development trends of modern science.
    </div>

    <div class="paragraph paragraph-bold">
        All materials contained in the book, published in the author's version. The editors do not make adjustments in scientific articles. Responsibility for the information published in the materials on display, are the authors.
    </div>

    <div class="paragraph">
        The electronic version of the collection is available online scientific publishing center «ISC» Site center: internationalscientificconferences.org
    </div>
</body>
</html>
