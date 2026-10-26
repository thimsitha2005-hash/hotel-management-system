<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hotel Management') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800">
    <div class="min-h-screen grid lg:grid-cols-2">

        <!-- ផ្នែកខាងឆ្វេង៖ ម៉ាកសណ្ឋាគារ -->
        <div class="relative hidden lg:flex flex-col justify-between overflow-hidden p-12 text-white
                    bg-gradient-to-br from-slate-900 via-slate-800 to-amber-700">

            <!-- រង្វង់តុបតែង -->
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-amber-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-amber-400/20 blur-3xl"></div>

            <!-- Logo -->
            <div class="relative flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500 text-2xl shadow-lg">🏨</div>
                <span class="text-xl font-bold tracking-wide">Hotel Management</span>
            </div>

            <!-- អត្ថបទស្វាគមន៍ -->
            <div class="relative max-w-md">
                <h2 class="text-4xl font-bold leading-tight">
                    ស្វាគមន៍មកកាន់<br>ប្រព័ន្ធគ្រប់គ្រងសណ្ឋាគារ
                </h2>
                <p class="mt-4 text-slate-300 leading-relaxed">
                    កក់បន្ទប់ គ្រប់គ្រងការស្នាក់នៅ និងទទួលបានសេវាកម្មដ៏ល្អបំផុត គ្រប់ពេលវេលា។
                </p>

                <div class="mt-8 flex gap-8 text-sm">
                    <div>
                        <p class="text-2xl font-bold text-amber-400">24/7</p>
                        <p class="text-slate-300">សេវាកម្ម</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-400">100+</p>
                        <p class="text-slate-300">បន្ទប់</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-400">5★</p>
                        <p class="text-slate-300">គុណភាព</p>
                    </div>
                </div>
            </div>

            <p class="relative text-sm text-slate-400">© {{ date('Y') }} Hotel Management System</p>
        </div>

        <!-- ផ្នែកខាងស្តាំ៖ ទម្រង់ -->
        <div class="flex items-center justify-center bg-slate-50 px-6 py-12">
            <div class="w-full max-w-md">

                <!-- Logo សម្រាប់ទូរស័ព្ទ -->
                <div class="mb-8 flex items-center justify-center gap-2 lg:hidden">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-xl">🏨</div>
                    <span class="text-lg font-bold text-slate-800">Hotel Management</span>
                </div>

                <div class="rounded-2xl bg-white p-8 shadow-xl ring-1 ring-slate-100">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>