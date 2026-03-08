@php($appName = config('app.name', 'Expense Tracker'))
@php($appDescription = config('app.description', 'Track trips and expenses in one place.'))

<title>{{ $appName }}</title>
<meta name="description" content="{{ $appDescription }}">
<meta property="og:title" content="{{ $appName }}">
<meta property="og:description" content="{{ $appDescription }}">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $appName }}">
<meta name="twitter:description" content="{{ $appDescription }}">
