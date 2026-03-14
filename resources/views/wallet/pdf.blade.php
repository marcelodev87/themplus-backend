<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lora:ital,wght@0,400;0,700;1,400&display=swap');

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #fdfcf7;
      font-family: 'Lora', serif;
      padding: 10mm;
    }

    .page {
        width: 100%;
        text-align: center;
    }

    .card {
      width: 85mm;
      height: 54mm;
      background: #ffffff;
      border-radius: 12px;
      display: inline-block;
      vertical-align: top;
      margin: 5px;
      text-align: left;
      border: 2px solid #202020;
      position: relative;
      overflow: hidden;
      break-inside: avoid;
  page-break-inside: avoid;
    }

    .card-side {
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 10px;
      background: background: #202020;
    }

    .card-body {
      margin-left: 10px;
      padding: 14px;
      height: 100%;
    }

    .content-table { width: 100%; border-collapse: collapse; }
    .avatar-column { width: 80px; vertical-align: middle; }

   .avatar-wrap {
        width: 80px;
        height: 80px;
        border: 2px solid #202020;
        background: #e1e1e1;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .avatar-initials {
        width: 80px;
        height: 80px;
        line-height: 60px;
        display: block;

        font-family: 'Cinzel', serif;
        font-size: 24px;
        font-weight: 700;
        color: #202020;
        background: ;
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;;
    }

    .info { padding-left: 12px; vertical-align: top; }

    .church-label {
      font-family: 'Cinzel', serif;
      font-size: 8px; color: #202020;
      text-transform: uppercase;
      margin-bottom: 2px;
      font-weight: 600;
    }

    .member-name {
      font-family: 'Cinzel', serif;
      font-size: 11px; font-weight: 700;
      color: #e1e1e1; line-height: 1.2;
      margin-bottom: 5px;
    }

    .divider {
      width: 100%; height: 1px;
      background: transparent;
      margin-bottom: 6px;
    }

    .info-row { margin-bottom: 3px; font-size: 0; }

    .info-label {
      font-family: 'Lora', serif;
      font-size: 7px; color: #202020;
      text-transform: uppercase;
      display: inline-block;
      width: 70px;
      font-weight: 500;
    }

    .info-value {
      font-family: 'Lora', serif;
      font-size: 8.5px; color: #202020;
      font-weight: 700;
      display: inline-block;
    }

    .card-footer {
      position: absolute;
      bottom: 8px; right: 12px;
      font-family: 'Lora', serif;
      font-size: 8px;
      color: #202020;
      text-align: right;
    }

    .emission-date {
      display: block;
      font-style: normal;
      font-size: 6.5px;
      margin-top: 2px;
      color: #202020;
    }

    .title-card{
        background:#202020;
        text-align:center;
        border-radius:5px;
        margin-bottom: 10px;
    }

    .title-card h4{
        padding:2px 0;
        margin:0;
        color:#e1e1e1;
        font-weight:700;
        font-size:14px;
    }
  </style>
</head>
<body>
  <div class="page">
    @foreach ($members as $member)
      @php
        $words = array_filter(explode(' ', trim($member->name)));
        $initials = strtoupper(
            implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2)))
        );
        $dateBirth = $member->date_birth ?? '-';
        $churchStart = $member->start_date ?? '-';
        $emissionDate = \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y');

        $imageData = null;
        if ($member->image_url) {
            try {
                $arrContextOptions = [
                    "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
                ];
                $content = file_get_contents($member->image_url, false, stream_context_create($arrContextOptions));
                $type = pathinfo($member->image_url, PATHINFO_EXTENSION);
                $imageData = 'data:image/' . $type . ';base64,' . base64_encode($content);
            } catch (\Exception $e) {
                $imageData = null;
            }
        }
    @endphp

      <div class="card">
        <div class="card-side"></div>
        <div class="card-body">
          <div class="title-card">
            <h4>Carteira de membro</h4>
            </div>
          <table class="content-table">
            <tr>
              <td class="avatar-column">
                <div class="avatar-wrap">
                    @if ($imageData)
                        <img src="{{ $imageData }}" alt="{{ $member->name }}">
                    @else
                        <div class="avatar-initials">{{ $initials }}</div>
                    @endif
                </div>
              </td>
              <td class="info">
                <p class="church-label">{{ $member->enterprise->name ?? 'Igreja' }}</p>
                <div class="divider"></div>

                <div class="info-row">
                  <span class="info-label">Nome</span>
                  <span class="info-value">{{ $member->name }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Nascimento</span>
                  <span class="info-value">{{ $dateBirth }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Membro desde</span>
                  <span class="info-value">{{ $churchStart }}</span>
                </div>
                @if ($member->registration)
                  <div class="info-row">
                    <span class="info-label">Matrícula</span>
                    <span class="info-value">{{ $member->registration }}</span>
                  </div>
                @endif
              </td>
            </tr>
          </table>
        </div>
        <div class="card-footer">
            <span class="quote">
                "Pois todos vós sois um em Cristo Jesus"
                <strong style="display: block; font-size: 5.5px; margin-top: 1px; color: #555;">Gálatas 3:28</strong>
            </span>
            <span class="emission-date">Emitido em: {{ $emissionDate }}</span>
        </div>
      </div>
    @endforeach
  </div>
</body>
</html>
