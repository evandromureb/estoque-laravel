<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard do Estoque') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 flex items-center gap-4 border-b-4 border-b-indigo-500">
                    <div class="bg-indigo-100 p-4 rounded-2xl text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-gray-400">Total Produtos</p>
                        <p class="text-2xl font-black text-gray-900">{{ $totalProducts }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 flex items-center gap-4 border-b-4 border-b-emerald-500">
                    <div class="bg-emerald-100 p-4 rounded-2xl text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-gray-400">Armazéns</p>
                        <p class="text-2xl font-black text-gray-900">{{ $totalWarehouses }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 flex items-center gap-4 border-b-4 border-b-amber-500">
                    <div class="bg-amber-100 p-4 rounded-2xl text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-gray-400">Estoque Baixo</p>
                        <p class="text-2xl font-black text-gray-900">{{ $lowStockProductsCount }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 flex items-center gap-4 border-b-4 border-b-purple-500">
                    <div class="bg-purple-100 p-4 rounded-2xl text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-gray-400">Usuários</p>
                        <p class="text-2xl font-black text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 lg:col-span-1">
                    <div class="p-6 bg-rose-50 border-b border-rose-100 flex justify-between items-center">
                        <div>
                            <h4 class="font-black text-rose-900 uppercase text-xs tracking-widest">Abaixo do estoque mínimo</h4>
                            <p class="mt-1 text-[10px] text-rose-700/80">Soma de todas as posições &lt; mínimo definido no produto</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="shrink-0 text-rose-700 text-xs font-bold hover:underline">Produtos</a>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-[22rem] overflow-y-auto">
                        @forelse($belowMinimumStockProducts as $p)
                            @php
                                $total = (int) ($p->locations_sum_quantity ?? 0);
                            @endphp
                            <div class="p-4 flex items-start justify-between gap-3 hover:bg-rose-50/40 transition">
                                <div class="min-w-0 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center overflow-hidden border border-rose-200 shrink-0">
                                        @if($p->images->count() > 0)
                                            <img src="{{ $p->images->first()->publicUrl() }}" class="object-cover w-full h-full" alt="">
                                        @else
                                            <span class="text-rose-500 text-[10px] font-bold">—</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $p->name }}</p>
                                        <p class="text-[10px] font-mono text-rose-600 uppercase">{{ $p->sku }}</p>
                                        <p class="mt-1 text-xs text-gray-600">
                                            <span class="font-black text-rose-700">{{ $total }}</span> un. atuais
                                            <span class="text-gray-400">·</span>
                                            mín. <span class="font-black">{{ $p->minimum_stock }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="p-8 text-sm text-gray-400 italic text-center">Nenhum produto abaixo do mínimo.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 lg:col-span-1">
                    <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h4 class="font-black text-gray-800 uppercase text-xs tracking-widest">Produtos Recentes</h4>
                        <a href="{{ route('products.index') }}" class="text-indigo-600 text-xs font-bold hover:underline">Ver tudo</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($recentProducts as $p)
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center overflow-hidden border border-indigo-100">
                                        @if($p->images->count() > 0)
                                            <img src="{{ $p->images->first()->publicUrl() }}" class="object-cover w-full h-full" alt="">
                                        @else
                                            <span class="text-indigo-400 text-[10px] font-bold">SEM FOTO</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $p->name }}</p>
                                        <p class="text-[10px] font-mono text-indigo-500 uppercase">{{ $p->sku }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-black text-gray-900">R$ {{ number_format($p->price, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 lg:col-span-1">
                    <div class="p-6 bg-amber-50 border-b border-amber-100 flex justify-between items-center text-amber-800">
                        <h4 class="font-black uppercase text-xs tracking-widest">Notas Adicionais Recentes</h4>
                        <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div class="p-4 space-y-4">
                        @forelse($recentProductNotes as $item)
                            <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100 relative overflow-hidden group">
                                <div class="absolute right-2 top-2 text-amber-200 group-hover:text-amber-400 transition">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                </div>
                                <h5 class="text-xs font-black text-amber-700 uppercase mb-1">Produto: {{ $item->name }}</h5>
                                <p class="text-sm text-gray-700 italic">"{{ Str::limit($item->additional_info, 100) }}"</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic text-center py-10 uppercase font-black opacity-30">Nenhuma nota importante encontrada.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
