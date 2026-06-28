<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @include('partials.favicon')
    <title>{{ $pageTitle ?? 'Dashboard' }} - SIMAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Inter', sans-serif; }
      ::-webkit-scrollbar { width: 4px; height: 4px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
      ::-webkit-scrollbar-thumb:hover { background: #bfdbfe; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>

  <body class="bg-slate-50 antialiased overflow-hidden h-screen">
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
      <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-4 py-3 shadow-xl min-w-[220px]">
        <div id="toast-icon" class="w-5 h-5 shrink-0"></div>
        <p id="toast-msg" class="text-xs font-medium text-white"></p>
      </div>
    </div>

    <div class="flex h-screen w-full overflow-hidden">
