<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            {{ config('app.name', 'Estoque') }}
        </h1>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed">
            Controle de produtos, armazéns e níveis de estoque em um só lugar.
        </p>

        <div class="mt-8 flex flex-col gap-3">
            @if (Route::has('login'))
                <a
                    href="{{ route('login') }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Entrar
                </a>
            @endif

            @if (Route::has('register'))
                <a
                    href="{{ route('register') }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Criar conta
                </a>
            @endif
        </div>
    </div>
</x-guest-layout>
