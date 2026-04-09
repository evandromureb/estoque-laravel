<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes do Produto') }}: {{ $product->sku }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm font-black text-indigo-600 hover:text-indigo-900 flex items-center gap-1 uppercase tracking-tighter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if ($errors->any())
                <x-validation-errors />
            @endif

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-0 border border-gray-100 divide-y md:divide-y-0 md:divide-x divide-gray-100 md:flex">
                
                <!-- Gallery Section -->
                <div class="md:w-1/3 p-8 bg-gray-50/50">
                    <div x-data="{ activeImage: '{{ $product->images->first()?->publicUrl() ?? '' }}' }">
                        <div class="bg-white p-4 rounded-3xl shadow-lg border border-gray-100 mb-6 aspect-square flex items-center justify-center overflow-hidden">
                            @if($product->images->count() > 0)
                                <img :src="activeImage" class="w-full h-full object-cover transition duration-300 transform hover:scale-105">
                            @else
                                <span class="text-gray-300 italic">Sem imagem</span>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-4 gap-3">
                            @foreach($product->images as $img)
                                <button type="button" @click="activeImage = '{{ $img->publicUrl() }}'" class="aspect-square rounded-xl overflow-hidden border-2 transition" :class="activeImage === '{{ $img->publicUrl() }}' ? 'border-indigo-500 shadow-md' : 'border-transparent opacity-60 hover:opacity-100'">
                                    <img src="{{ $img->publicUrl() }}" class="w-full h-full object-cover" alt="">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @can('update', $product)
                        <div class="mt-8 space-y-6 border-t border-gray-200 pt-8">
                            <div>
                                <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">Remover imagens</h4>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($product->images as $img)
                                        <div class="relative">
                                            <img src="{{ $img->publicUrl() }}" alt="" class="h-20 w-20 rounded-lg object-cover ring-2 ring-gray-200">
                                            <form id="delete-show-product-image-{{ $img->id }}" action="{{ route('products.images.destroy', [$product, $img]) }}" method="POST" class="absolute -right-2 -top-2">
                                                @csrf
                                                @method('DELETE')
                                                <x-hidden-return-url />
                                                <button
                                                    type="button"
                                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-white shadow hover:bg-red-700"
                                                    title="Remover"
                                                    @click="$store.deleteConfirm.openModal('delete-show-product-image-{{ $img->id }}', @js('Remover esta imagem do produto?'))"
                                                >×</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($product->images->isEmpty())
                                    <p class="text-xs text-gray-400 italic">Nenhuma imagem para remover.</p>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-2">Adicionar imagens</h4>
                                <form action="{{ route('products.images.store', $product) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                    @csrf
                                    <x-hidden-return-url />
                                    <div class="flex-1">
                                        <input type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/gif,image/jpg" class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>
                                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow hover:bg-indigo-700">Enviar</button>
                                </form>
                                <p class="mt-1 text-[10px] text-gray-400">JPG, PNG ou GIF, até 2 MB por arquivo.</p>
                            </div>
                        </div>
                    @endcan

                    <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                        <div class="bg-white p-6 rounded-2xl shadow-md inline-block border border-gray-100">
                            @if($product->qr_code_path)
                                <img src="{{ asset('storage/' . $product->qr_code_path) }}" class="w-32 h-32 mx-auto">
                                <p class="mt-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificador único</p>
                                <button onclick="window.print()" class="mt-4 w-full bg-gray-900 text-white py-2 rounded-xl text-xs font-bold hover:bg-black transition">IMPRIMIR QR</button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="md:flex-1 p-8 lg:p-12 space-y-8">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $product->category->name }}</span>
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[10px] font-mono font-bold">{{ $product->sku }}</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 tracking-tight">{{ $product->name }}</h3>
                        <p class="text-3xl font-black text-indigo-600 mt-2">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        @if ($product->minimum_stock > 0)
                            @php
                                $totalStockShow = (int) $product->locations->sum('quantity');
                            @endphp
                            <p class="mt-3 text-sm text-gray-600">
                                Estoque total: <span class="font-black text-gray-900">{{ $totalStockShow }}</span> un.
                                <span class="text-gray-400">·</span>
                                Mínimo para alerta: <span class="font-black text-gray-900">{{ $product->minimum_stock }}</span>
                                @if ($totalStockShow < $product->minimum_stock)
                                    <span class="ml-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black uppercase text-amber-800">Abaixo do mínimo</span>
                                @endif
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Descrição detalhada</h4>
                            <p class="text-gray-600 leading-relaxed">{{ $product->description ?: 'Sem descrição informada.' }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Observações Administrativas</h4>
                            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100 text-amber-900 text-sm italic">
                                {{ $product->additional_info ?: 'Nenhuma observação extra.' }}
                            </div>
                        </div>
                    </div>

                    <div id="product-estoque-localizacao" class="scroll-mt-24 pt-8 border-t border-gray-100" @if (auth()->user()->can('create', App\Models\ProductLocation::class)) x-data="{ showAlocateForm: false, editingLocationId: {{ json_encode(old('product_location_id')) }} }" @else x-data="{ editingLocationId: {{ json_encode(old('product_location_id')) }} }" @endif>
                        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-widest text-gray-400">Estoque e localização</h4>
                                <p class="mt-1 max-w-xl text-xs text-gray-500">Defina em qual armazém o produto está, o corredor e a prateleira. Você pode adicionar várias posições e editar cada linha abaixo.</p>
                            </div>
                            @can('create', App\Models\ProductLocation::class)
                                <button type="button" @click="showAlocateForm = !showAlocateForm" class="shrink-0 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-lg transition hover:bg-indigo-700 active:scale-95">
                                    + Nova posição de estoque
                                </button>
                            @endcan
                        </div>

                        @if(session('success'))
                            <div class="mb-4 bg-emerald-50 text-emerald-700 p-3 rounded-xl border border-emerald-100 text-sm font-bold flex gap-2 items-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                {{ session('success') }}
                            </div>
                        @endif

                        @can('create', App\Models\ProductLocation::class)
                            <div x-show="showAlocateForm" class="mb-8 p-6 bg-indigo-50 border border-indigo-100 rounded-3xl" x-transition style="display: none;">
                                <form action="{{ route('product-locations.store') }}" method="POST">
                                    @csrf
                                    <x-hidden-return-url />
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <p class="mb-4 text-xs font-bold text-indigo-800">Alocar em um armazém (combinação armazém + corredor + prateleira soma quantidade se já existir).</p>
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                        <div class="md:col-span-1">
                                            <label class="mb-1 block text-[10px] font-black uppercase text-indigo-400">Armazém</label>
                                            <select name="warehouse_id" class="w-full bg-white border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                                                <option value="">Selecione...</option>
                                                @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black uppercase text-indigo-400 mb-1">Corredor</label>
                                            <input type="text" name="aisle" class="w-full bg-white border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black uppercase text-indigo-400 mb-1">Prateleira</label>
                                            <input type="text" name="shelf" class="w-full bg-white border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black uppercase text-indigo-400 mb-1">Quantidade</label>
                                            <div class="flex gap-2">
                                                <input type="number" name="quantity" min="1" value="1" class="w-full bg-white border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-indigo-700 shadow-md">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endcan

                        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-gray-50/30">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-white/80 backdrop-blur-md">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Armazém</th>
                                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Posição</th>
                                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Saldo</th>
                                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($product->locations as $loc)
                                        <tr class="transition hover:bg-white" x-show="editingLocationId !== {{ $loc->id }}">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $loc->warehouse->name }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-[10px] font-bold text-gray-500">
                                                    @if($loc->aisle || $loc->shelf)
                                                        <span class="rounded bg-gray-200 px-2 py-1">CORREDOR {{ $loc->aisle ?: '?' }}</span>
                                                        <span class="ml-1 rounded bg-gray-200 px-2 py-1">PRATELEIRA {{ $loc->shelf ?: '?' }}</span>
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">{{ $loc->quantity }} UN</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    @can('update', $loc)
                                                        <button type="button" class="rounded-lg p-2 text-indigo-600 transition hover:bg-indigo-50" title="Alterar localização" @click="editingLocationId = {{ $loc->id }}">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </button>
                                                    @endcan
                                                    @can('delete', $loc)
                                                        <form id="delete-product-location-{{ $loc->id }}" action="{{ route('product-locations.destroy', $loc) }}" method="POST" class="inline">
                                                            @csrf @method('DELETE')
                                                            <x-hidden-return-url />
                                                            <button
                                                                type="button"
                                                                class="p-2 text-red-300 transition hover:text-red-600"
                                                                @click="$store.deleteConfirm.openModal('delete-product-location-{{ $loc->id }}', @js('Remover este registro de estoque?'))"
                                                            >
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @can('update', $loc)
                                            <tr class="bg-indigo-50/40" x-show="editingLocationId === {{ $loc->id }}" x-cloak style="display: none;">
                                                <td colspan="4" class="px-6 py-5">
                                                    <form action="{{ route('product-locations.update', $loc) }}" method="POST" class="space-y-4">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-hidden-return-url />
                                                        <input type="hidden" name="product_location_id" value="{{ $loc->id }}">
                                                        <p class="text-xs font-bold text-indigo-800">Alterar armazém, corredor, prateleira ou quantidade desta posição.</p>
                                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                                            <div>
                                                                <label class="mb-1 block text-[10px] font-black uppercase text-indigo-500">Armazém</label>
                                                                <select name="warehouse_id" class="w-full rounded-xl border-none bg-white text-sm shadow-sm focus:ring-2 focus:ring-indigo-500" required>
                                                                    @foreach ($warehouses as $w)
                                                                        <option value="{{ $w->id }}" @selected($w->id === $loc->warehouse_id)>{{ $w->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-[10px] font-black uppercase text-indigo-500">Corredor</label>
                                                                <input type="text" name="aisle" value="{{ old('aisle', $loc->aisle) }}" class="w-full rounded-xl border-none bg-white text-sm shadow-sm focus:ring-2 focus:ring-indigo-500">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-[10px] font-black uppercase text-indigo-500">Prateleira</label>
                                                                <input type="text" name="shelf" value="{{ old('shelf', $loc->shelf) }}" class="w-full rounded-xl border-none bg-white text-sm shadow-sm focus:ring-2 focus:ring-indigo-500">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-[10px] font-black uppercase text-indigo-500">Quantidade</label>
                                                                <input type="number" name="quantity" min="1" value="{{ old('quantity', $loc->quantity) }}" class="w-full rounded-xl border-none bg-white text-sm shadow-sm focus:ring-2 focus:ring-indigo-500" required>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-wrap justify-end gap-2">
                                                            <button type="button" class="rounded-xl px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-200/80" @click="editingLocationId = null">Cancelar</button>
                                                            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow hover:bg-indigo-700">Salvar alterações</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endcan
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-xs italic text-gray-400">Nenhum estoque alocado. Use &quot;Nova posição de estoque&quot; acima para registrar armazém e localização.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
