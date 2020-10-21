<html class="no-js" lang="en">
<head>

    <meta charset="utf-8">
    <title>エラーページ</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

</head>

<body>
<p>システムエラーが発生しました。<a href="/main">設備状況一覧</a>に戻るか、管理者にお問い合わせください。</P>
<p>{{$e ?? ""}}</p>
</body>