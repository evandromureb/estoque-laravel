<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Produtos') }}
        </h2>
    </x-slot>

    @php
        $warehousesJson = $warehouses->map(fn ($w) => ['id' => $w->id, 'name' => $w->name])->values();
    @endphp
    <div class="py-12" x-data="productManagement({ routes: @js($crudRoutes), warehouses: @js($warehousesJson) })">
        <div class="max-w-7xl mx-auto min-w-0 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6">
                    <x-validation-errors />
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-0 border border-gray-100">

                <!-- Table Header Section with Gradient -->
                <div class="bg-gradient-to-r from-indigo-700 to-purple-800 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Lista de Produtos
                    </h3>
                    <div class="flex gap-4 w-full md:w-auto">
                        <form action="{{ route('products.index') }}" method="GET" class="relative flex-grow">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar..." class="w-full bg-white/10 border-none focus:ring-2 focus:ring-white/50 text-white placeholder-white/60 rounded-xl px-4 py-2 text-sm backdrop-blur-md">
                            <button type="submit" class="absolute right-2 top-2 text-white/60 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                        @can('create', App\Models\Product::class)
                            <button type="button" @click="openCreateModal()" class="bg-white text-indigo-800 px-5 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-1 active:scale-95 whitespace-nowrap">
                                + Novo Produto
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg flex items-center gap-3 animate-fade-in-down">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="-mx-6 overflow-x-auto bg-gray-50 px-6 sm:mx-0 sm:px-0 rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Identificação</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Imagens</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produto / Categoria</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estoque</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Preço</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($products as $product)
                                    @php
                                        $productPayload = [
                                            'id' => $product->id,
                                            'category_id' => $product->category_id,
                                            'name' => $product->name,
                                            'sku' => $product->sku,
                                            'price' => $product->price,
                                            'minimum_stock' => (int) $product->minimum_stock,
                                            'description' => $product->description,
                                            'additional_info' => $product->additional_info,
                                            'images' => $product->images->map(fn ($img) => [
                                                'id' => $img->id,
                                                'url' => $img->publicUrl(),
                                                'destroyUrl' => route('products.images.destroy', ['product' => $product, 'productImage' => $img]),
                                            ])->values()->all(),
                                            'stockLocations' => $product->locations->map(fn ($loc) => [
                                                'id' => $loc->id,
                                                'warehouse_id' => $loc->warehouse_id,
                                                'warehouseName' => $loc->warehouse->name,
                                                'aisle' => $loc->aisle,
                                                'shelf' => $loc->shelf,
                                                'quantity' => (int) $loc->quantity,
                                                'updateUrl' => route('product-locations.update', $loc),
                                                'destroyUrl' => route('product-locations.destroy', $loc),
                                            ])->values()->all(),
                                        ];
                                        $viewTotalStock = (int) ($product->locations_sum_quantity ?? 0);
                                        $viewBelowMin = $product->minimum_stock > 0 && $viewTotalStock < $product->minimum_stock;
                                        $viewPayload = [
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'sku' => $product->sku,
                                            'priceFormatted' => 'R$ '.number_format((float) $product->price, 2, ',', '.'),
                                            'description' => $product->description ?? '',
                                            'additional_info' => $product->additional_info ?? '',
                                            'categoryName' => $product->category->name,
                                            'minimum_stock' => (int) $product->minimum_stock,
                                            'totalStock' => $viewTotalStock,
                                            'belowMinimum' => $viewBelowMin,
                                            'images' => $product->images->map(fn ($img) => [
                                                'url' => $img->publicUrl(),
                                            ])->values()->all(),
                                            'qrUrl' => $product->qr_code_path ? asset('storage/'.$product->qr_code_path) : null,
                                            'locations' => $product->locations->map(fn ($loc) => [
                                                'warehouseName' => $loc->warehouse->name,
                                                'aisle' => $loc->aisle,
                                                'shelf' => $loc->shelf,
                                                'quantity' => (int) $loc->quantity,
                                            ])->values()->all(),
                                            'showUrl' => route('products.show', $product),
                                            'canManage' => auth()->user()->can('update', $product),
                                            'canManageStock' => auth()->user()->can('create', App\Models\ProductLocation::class),
                                            'editPayload' => $productPayload,
                                        ];
                                    @endphp
                                    <tr class="hover:bg-indigo-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                @if($product->qr_code_path)
                                                    <div class="p-1 bg-white border rounded shadow-sm hover:shadow-md transition">
                                                        <img src="{{ asset('storage/' . $product->qr_code_path) }}" class="w-10 h-10" alt="">
                                                    </div>
                                                @endif
                                                <span class="text-xs font-mono font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $product->sku }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex -space-x-3 overflow-hidden">
                                                @foreach($product->images->take(3) as $img)
                                                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover" src="{{ $img->publicUrl() }}" alt="">
                                                @endforeach
                                                @if($product->images->count() > 3)
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500 ring-2 ring-white">+{{ $product->images->count() - 3 }}</div>
                                                @endif
                                            </div>
                                            <div class="mt-2 text-[10px] font-medium text-gray-500">{{ $product->images_count }} {{ $product->images_count === 1 ? 'imagem' : 'imagens' }}</div>
                                        </td>
                                        <td class="px-6 py-4 max-w-xs">
                                            <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                            <div class="text-xs font-medium text-gray-500 inline-flex items-center gap-1 mt-0.5">
                                                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                                {{ $product->category->name }}
                                            </div>
                                            @if(filled($product->description))
                                                <p class="mt-2 text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                                            @endif
                                            <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[10px] text-gray-400">
                                                <span class="font-mono">#{{ $product->id }}</span>
                                                <span>·</span>
                                                <span>Cadastro {{ $product->created_at->timezone(config('app.timezone'))->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-gray-900">{{ (int) ($product->locations_sum_quantity ?? 0) }}</div>
                                            <div class="text-xs text-gray-500">un. totais</div>
                                            @if($product->minimum_stock > 0)
                                                <div class="mt-0.5 text-[10px] font-bold text-gray-500">Mín. {{ $product->minimum_stock }}</div>
                                            @endif
                                            @if($product->minimum_stock > 0 && (int) ($product->locations_sum_quantity ?? 0) < $product->minimum_stock)
                                                <div class="mt-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-amber-800">Abaixo do mínimo</div>
                                            @endif
                                            <div class="mt-1 text-[10px] text-gray-400">{{ $product->locations_count }} {{ $product->locations_count === 1 ? 'posição' : 'posições' }} em estoque</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-black text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                @can('update', $product)
                                                    <button type="button" @click="openInfoModal(@js($productPayload))" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Adicionar Info">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                    </button>
                                                @endcan
                                                <button type="button" @click="openViewModal(@js($viewPayload))" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Ver produto">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                                @can('update', $product)
                                                    <button type="button" @click="openEditModal(@js($productPayload))" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                @endcan
                                                @can('delete', $product)
                                                    <form id="delete-product-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block">
                                                        @csrf @method('DELETE')
                                                        <x-hidden-return-url />
                                                        <button
                                                            type="button"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                            @click="$store.deleteConfirm.openModal('delete-product-{{ $product->id }}', @js('Excluir este produto e todos os dados vinculados? Esta ação não pode ser desfeita.'))"
                                                        >
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                <p>Nenhum produto encontrado.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Modal (Create/Edit) — abas Geral / Imagens / Estoque -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <x-modal-backdrop close="closeModal()" overlay-class="bg-indigo-900/60" class="z-40" />

                <x-modal-panel panel-class="max-w-3xl" class="animate-scale-up z-50 flex max-h-[90vh] flex-col overflow-hidden">
                    <div class="shrink-0 bg-indigo-700 px-8 py-6 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white" x-text="isEditing ? 'Editar produto' : 'Novo produto'"></h3>
                        <button type="button" @click="closeModal()" class="text-white/60 hover:text-white">&times;</button>
                    </div>

                    <div class="shrink-0 px-8 pt-4">
                        <x-validation-errors />
                    </div>

                    <div class="shrink-0 flex gap-1 border-b border-gray-200 px-8 pt-2">
                        <button type="button" @click="editTab = 'geral'" class="rounded-t-lg px-4 py-2 text-xs font-black uppercase tracking-wide transition" :class="editTab === 'geral' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-500 hover:bg-gray-50'">Geral</button>
                        <button type="button" @click="editTab = 'imagens'" class="rounded-t-lg px-4 py-2 text-xs font-black uppercase tracking-wide transition" :class="editTab === 'imagens' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-500 hover:bg-gray-50'">Imagens</button>
                        <button type="button" @click="editTab = 'estoque'" :disabled="!isEditing" class="rounded-t-lg px-4 py-2 text-xs font-black uppercase tracking-wide transition disabled:cursor-not-allowed disabled:opacity-40" :class="editTab === 'estoque' ? 'bg-teal-100 text-teal-800' : 'text-gray-500 hover:bg-gray-50'">Estoque</button>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto">
                        <form id="product-modal-form" :action="formAction" method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-hidden-return-url />
                            <template x-if="isEditing"><input type="hidden" name="_method" value="PUT"></template>

                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6" x-show="editTab === 'geral'" x-cloak>
                                <div>
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">Categoria</label>
                                    <select name="category_id" x-model="form.category_id" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Selecione...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">Nome do produto</label>
                                    <input type="text" name="name" x-model="form.name" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">SKU único</label>
                                    <input type="text" name="sku" x-model="form.sku" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">Preço (R$)</label>
                                    <input type="number" step="0.01" name="price" x-model="form.price" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">Estoque mínimo (alerta)</label>
                                    <input type="number" min="0" step="1" name="minimum_stock" x-model="form.minimum_stock" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" required>
                                    <p class="mt-1 text-[10px] text-gray-400">Quando a soma das posições for menor que este valor, o produto aparece como estoque baixo no painel.</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">Descrição</label>
                                    <textarea name="description" x-model="form.description" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500"></textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black uppercase text-gray-500 mb-2">Informações adicionais / notas</label>
                                    <textarea name="additional_info" x-model="form.additional_info" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" placeholder="Alguma observação importante?"></textarea>
                                </div>
                            </div>

                            <div class="p-8" x-show="editTab === 'imagens' || (!isEditing && editTab === 'geral')" x-cloak>
                                <p class="mb-4 text-xs text-gray-500" x-show="!isEditing">Envie pelo menos uma imagem ao criar (obrigatório). Este bloco aparece na aba Geral ou Imagens.</p>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-2">
                                    <span x-show="!isEditing">Imagens do produto</span>
                                    <span x-show="isEditing" x-cloak>Novas imagens (opcional)</span>
                                </label>
                                <div class="relative group">
                                    <input type="file" name="images[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" :required="!isEditing">
                                    <div class="border-2 border-dashed border-indigo-200 group-hover:border-indigo-500 bg-indigo-50 group-hover:bg-indigo-100/50 rounded-2xl p-8 flex flex-col items-center justify-center transition">
                                        <svg class="w-10 h-10 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-sm font-bold text-indigo-600">Clique ou arraste imagens</p>
                                        <p class="text-[10px] text-gray-400">JPG, PNG até 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="border-t border-gray-100 p-8 pt-6" x-show="editTab === 'imagens' && isEditing && form.images && form.images.length > 0" x-cloak>
                            <label class="block text-xs font-black uppercase text-gray-500 mb-2">Imagens atuais</label>
                            <p class="text-[10px] text-gray-400 mb-3">Cada remoção é confirmada e enviada num pedido separado.</p>
                            <div class="flex flex-wrap gap-4">
                                <template x-for="img in form.images" :key="img.id">
                                    <div class="relative shrink-0">
                                        <img :src="img.url" alt="" class="h-24 w-24 rounded-xl object-cover ring-2 ring-gray-200 shadow-sm">
                                        <form :id="'delete-product-image-' + img.id" :action="img.destroyUrl" method="POST" class="absolute -right-2 -top-2">
                                            @csrf
                                            @method('DELETE')
                                            <x-hidden-return-url />
                                            <button
                                                type="button"
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-sm font-bold text-white shadow-lg hover:bg-red-700"
                                                title="Remover imagem"
                                                @click="$store.deleteConfirm.openModal('delete-product-image-' + img.id, @js('Remover esta imagem do produto?'))"
                                            >×</button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-8" x-show="editTab === 'estoque' && isEditing" x-cloak>
                            @can('create', App\Models\ProductLocation::class)
                                <h4 class="mb-4 text-xs font-black uppercase tracking-widest text-teal-800">Nova posição</h4>
                                <form :action="routes.productLocationsStoreUrl" method="POST" class="mb-8 grid grid-cols-1 gap-4 rounded-2xl border border-teal-100 bg-teal-50/40 p-4 md:grid-cols-2">
                                    @csrf
                                    <x-hidden-return-url />
                                    <input type="hidden" name="product_id" :value="form.id">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black uppercase text-gray-500 mb-1">Armazém</label>
                                        <select name="warehouse_id" class="w-full rounded-xl border-gray-200 bg-white" required>
                                            <option value="">Selecione...</option>
                                            @foreach($warehouses as $w)
                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase text-gray-500 mb-1">Corredor</label>
                                        <input type="text" name="aisle" class="w-full rounded-xl border-gray-200 bg-white" placeholder="Ex.: A">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase text-gray-500 mb-1">Prateleira</label>
                                        <input type="text" name="shelf" class="w-full rounded-xl border-gray-200 bg-white" placeholder="Ex.: 12">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black uppercase text-gray-500 mb-1">Quantidade</label>
                                        <input type="number" min="1" name="quantity" class="w-full rounded-xl border-gray-200 bg-white" required>
                                    </div>
                                    <div class="md:col-span-2 flex justify-end">
                                        <button type="submit" class="rounded-xl bg-teal-600 px-6 py-2 text-sm font-bold text-white shadow hover:bg-teal-700">Alocar estoque</button>
                                    </div>
                                </form>

                                <h4 class="mb-3 text-xs font-black uppercase tracking-widest text-gray-500">Posições atuais</h4>
                                <div class="overflow-x-auto rounded-xl border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-[10px] font-black uppercase text-gray-400">Armazém</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-black uppercase text-gray-400">Pos.</th>
                                                <th class="px-3 py-2 text-right text-[10px] font-black uppercase text-gray-400">Qtd</th>
                                                <th class="px-3 py-2 text-right text-[10px] font-black uppercase text-gray-400">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 bg-white">
                                            <tr x-show="!form.stockLocations || form.stockLocations.length === 0">
                                                <td colspan="4" class="px-4 py-6 text-center text-xs text-gray-400">Nenhuma posição cadastrada.</td>
                                            </tr>
                                            <template x-for="loc in form.stockLocations" :key="loc.id">
                                                <tr>
                                                    <td class="px-3 py-2 font-semibold text-gray-900" x-text="loc.warehouseName"></td>
                                                    <td class="px-3 py-2 text-xs text-gray-600">
                                                        <span class="font-mono" x-text="(loc.aisle || '—') + ' / ' + (loc.shelf || '—')"></span>
                                                    </td>
                                                    <td class="px-3 py-2 text-right">
                                                        <form :id="'pl-upd-' + loc.id" :action="loc.updateUrl" method="POST" class="inline-flex items-center gap-1">
                                                            @csrf
                                                            @method('PUT')
                                                            <x-hidden-return-url />
                                                            <input type="hidden" name="warehouse_id" :value="loc.warehouse_id">
                                                            <input type="hidden" name="aisle" :value="loc.aisle ?? ''">
                                                            <input type="hidden" name="shelf" :value="loc.shelf ?? ''">
                                                            <input type="number" min="1" name="quantity" :value="loc.quantity" class="w-20 rounded-lg border-gray-200 py-1 text-right text-sm">
                                                        </form>
                                                    </td>
                                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                                        <button type="submit" :form="'pl-upd-' + loc.id" class="mr-1 rounded-lg bg-indigo-600 px-2 py-1 text-[10px] font-bold text-white hover:bg-indigo-700">OK</button>
                                                        <form :id="'pl-del-' + loc.id" :action="loc.destroyUrl" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-hidden-return-url />
                                                            <button type="button" class="rounded-lg bg-red-50 px-2 py-1 text-[10px] font-bold text-red-700 hover:bg-red-100" @click="$store.deleteConfirm.openModal('pl-del-' + loc.id, @js('Remover esta posição de estoque?'))">Excluir</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Sem permissão para gerir posições de estoque.</p>
                            @endcan
                        </div>

                        <div class="p-8 text-center text-sm text-gray-500" x-show="editTab === 'estoque' && !isEditing" x-cloak>
                            Guarde o produto na aba Geral para poder alocar estoque aqui.
                        </div>
                    </div>

                    <div class="shrink-0 bg-gray-50 px-8 py-6 flex flex-wrap justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="closeModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Cancelar</button>
                        <button type="submit" form="product-modal-form" x-show="editTab !== 'estoque'" class="bg-indigo-600 text-white px-8 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-indigo-700 transition">Salvar produto</button>
                        <p class="w-full text-right text-[10px] text-gray-400 md:w-auto" x-show="editTab === 'estoque'" x-cloak>Alterações de estoque usam os botões na tabela ou o formulário de nova posição.</p>
                    </div>
                </x-modal-panel>
            </div>
        </div>

        <!-- Ver produto (somente leitura) — mesmas abas que edição: Geral / Imagens / Estoque -->
        <div x-show="viewModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <x-modal-backdrop close="closeViewModal()" overlay-class="bg-indigo-900/60" class="z-40" />

                <x-modal-panel panel-class="max-w-3xl w-full" class="animate-scale-up z-50 flex max-h-[90vh] flex-col overflow-hidden border border-gray-200 shadow-2xl">
                    <div class="flex min-h-0 flex-1 flex-col" x-show="viewProduct" x-cloak>
                        <div class="shrink-0 bg-indigo-700 px-8 py-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold text-white">Ver produto</h3>
                                <p class="text-xs font-mono text-white/80" x-text="viewProduct.sku"></p>
                            </div>
                            <button type="button" @click="closeViewModal()" class="text-2xl leading-none text-white/60 hover:text-white">&times;</button>
                        </div>

                        <div class="shrink-0 flex gap-1 border-b border-gray-200 px-8 pt-2">
                            <button type="button" @click="viewTab = 'geral'" class="rounded-t-lg px-4 py-2 text-xs font-black uppercase tracking-wide transition" :class="viewTab === 'geral' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-500 hover:bg-gray-50'">Geral</button>
                            <button type="button" @click="viewTab = 'imagens'" class="rounded-t-lg px-4 py-2 text-xs font-black uppercase tracking-wide transition" :class="viewTab === 'imagens' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-500 hover:bg-gray-50'">Imagens</button>
                            <button type="button" @click="viewTab = 'estoque'" class="rounded-t-lg px-4 py-2 text-xs font-black uppercase tracking-wide transition" :class="viewTab === 'estoque' ? 'bg-teal-100 text-teal-800' : 'text-gray-500 hover:bg-gray-50'">Estoque</button>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto">
                            <!-- Aba Geral -->
                            <div class="p-8 space-y-6" x-show="viewTab === 'geral'" x-cloak>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-indigo-700" x-text="viewProduct.categoryName"></span>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 font-mono text-[10px] font-bold text-gray-600" x-text="viewProduct.sku"></span>
                                </div>
                                <h4 class="text-2xl font-black tracking-tight text-gray-900" x-text="viewProduct.name"></h4>
                                <p class="text-2xl font-black text-indigo-600" x-text="viewProduct.priceFormatted"></p>
                                <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 text-sm text-gray-700" x-show="viewProduct.minimum_stock > 0">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Alerta de estoque</p>
                                    <p>
                                        Total em posições: <span class="font-black" x-text="viewProduct.totalStock"></span> un.
                                        <span class="text-gray-400">·</span>
                                        Mínimo configurado: <span class="font-black" x-text="viewProduct.minimum_stock"></span>
                                        <span x-show="viewProduct.belowMinimum" class="ml-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black uppercase text-amber-800">Abaixo do mínimo</span>
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Descrição</h5>
                                    <p class="text-sm leading-relaxed text-gray-600" x-text="viewProduct.description || 'Sem descrição informada.'"></p>
                                </div>
                                <div class="space-y-2">
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Observações</h5>
                                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm italic text-amber-900" x-text="viewProduct.additional_info || 'Nenhuma observação extra.'"></div>
                                </div>
                                <div class="rounded-2xl border border-gray-100 bg-white p-4 text-center shadow-sm" x-show="viewProduct.qrUrl">
                                    <img :src="viewProduct.qrUrl" alt="QR Code" class="mx-auto h-28 w-28">
                                    <p class="mt-2 text-[10px] font-black uppercase tracking-widest text-gray-400">Identificador único</p>
                                    <button type="button" onclick="window.print()" class="mt-3 w-full rounded-xl bg-gray-900 py-2 text-xs font-bold text-white hover:bg-black">Imprimir QR</button>
                                </div>
                            </div>

                            <!-- Aba Imagens -->
                            <div class="p-8 space-y-6" x-show="viewTab === 'imagens'" x-cloak>
                                <div class="flex aspect-square max-h-80 items-center justify-center overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 shadow-inner mx-auto w-full max-w-md">
                                    <img x-show="activeViewImage" :src="activeViewImage" alt="" class="h-full w-full object-cover">
                                    <span x-show="!activeViewImage" class="text-sm italic text-gray-400">Sem imagem</span>
                                </div>
                                <div class="grid grid-cols-4 gap-2 sm:grid-cols-6" x-show="viewProduct.images && viewProduct.images.length > 0">
                                    <template x-for="img in viewProduct.images" :key="img.url">
                                        <button type="button" @click="activeViewImage = img.url" class="aspect-square overflow-hidden rounded-lg border-2 transition" :class="activeViewImage === img.url ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-transparent opacity-70 hover:opacity-100'">
                                            <img :src="img.url" alt="" class="h-full w-full object-cover">
                                        </button>
                                    </template>
                                </div>
                                <p class="text-center text-sm text-gray-400" x-show="!viewProduct.images || viewProduct.images.length === 0">Nenhuma imagem no catálogo.</p>
                            </div>

                            <!-- Aba Estoque -->
                            <div class="p-8 space-y-4" x-show="viewTab === 'estoque'" x-cloak>
                                <div class="rounded-xl border border-teal-100 bg-teal-50/50 p-4 text-sm text-teal-900" x-show="viewProduct.minimum_stock > 0">
                                    <span class="font-black" x-text="viewProduct.totalStock"></span> un. no total
                                    <span class="text-teal-700/70">·</span>
                                    Mínimo: <span class="font-black" x-text="viewProduct.minimum_stock"></span>
                                    <span x-show="viewProduct.belowMinimum" class="ml-2 text-xs font-black uppercase text-amber-800">Abaixo do mínimo</span>
                                </div>
                                <p class="text-xs text-gray-500" x-show="viewProduct.canManageStock">Para alterar quantidades ou posições, use <span class="font-bold text-gray-700">Editar produto</span> e a aba Estoque.</p>
                                <div class="overflow-x-auto rounded-xl border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-gray-400">Armazém</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-gray-400">Posição</th>
                                                <th class="px-4 py-3 text-right text-[10px] font-black uppercase text-gray-400">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 bg-white">
                                            <tr x-show="!viewProduct.locations || viewProduct.locations.length === 0">
                                                <td colspan="3" class="px-4 py-8 text-center text-xs italic text-gray-400">Nenhuma posição de estoque registrada.</td>
                                            </tr>
                                            <template x-for="(loc, idx) in viewProduct.locations" :key="idx">
                                                <tr>
                                                    <td class="px-4 py-3 font-bold text-gray-900" x-text="loc.warehouseName"></td>
                                                    <td class="px-4 py-3 text-xs text-gray-600">
                                                        <span x-show="loc.aisle || loc.shelf" class="inline-flex flex-wrap gap-1">
                                                            <span class="rounded bg-gray-200 px-1.5 py-0.5 text-[10px] font-bold">C <span x-text="loc.aisle || '?'"></span></span>
                                                            <span class="rounded bg-gray-200 px-1.5 py-0.5 text-[10px] font-bold">P <span x-text="loc.shelf || '?'"></span></span>
                                                        </span>
                                                        <span x-show="!loc.aisle && !loc.shelf">—</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-black text-emerald-800"><span x-text="loc.quantity"></span> un</span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-8 py-6">
                            <button type="button" @click="closeViewModal()" class="rounded-xl px-6 py-2 text-sm font-bold text-gray-600 hover:bg-gray-200/80">Fechar</button>
                            <button type="button" x-show="viewProduct.canManage" @click="openEditFromView()" class="rounded-xl bg-indigo-600 px-6 py-2 text-sm font-bold text-white shadow hover:bg-indigo-700">Editar produto</button>
                            <a x-show="viewProduct.canManage" :href="viewProduct.showUrl" class="rounded-xl border border-gray-300 bg-white px-6 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50">Página do produto</a>
                        </div>
                    </div>
                </x-modal-panel>
            </div>
        </div>

        <!-- Extra Info Modal -->
        <div x-show="infoModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <x-modal-backdrop close="infoModalOpen = false" overlay-class="bg-amber-900/40" class="z-40" />

                <x-modal-panel panel-class="max-w-md" class="animate-scale-up z-50 border-4 border-amber-400 p-8">
                    <h4 class="text-xl font-black text-amber-700 mb-4">Adicionar Informação</h4>
                    <x-validation-errors class="mb-4" />
                    <form :action="formAction" method="POST">
                        @csrf @method('PUT')
                        <x-hidden-return-url />
                        <input type="hidden" name="name" :value="form.name">
                        <input type="hidden" name="category_id" :value="form.category_id">
                        <input type="hidden" name="sku" :value="form.sku">
                        <input type="hidden" name="price" :value="form.price">
                        <input type="hidden" name="minimum_stock" :value="form.minimum_stock">
                        <input type="hidden" name="description" :value="form.description">

                        <label class="block text-xs font-black uppercase text-gray-400 mb-2 italic underline text-indigo-500">Nota para: <span x-text="form.name" class="text-gray-900"></span></label>
                        <textarea name="additional_info" x-model="form.additional_info" rows="5" class="w-full bg-amber-50 border-amber-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 mb-6" placeholder="Escreva aqui as informações extras..."></textarea>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="infoModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-400">Fechar</button>
                            <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-amber-600 transition">Salvar Nota</button>
                        </div>
                    </form>
                </x-modal-panel>
            </div>
        </div>
    </div>
</x-app-layout>
