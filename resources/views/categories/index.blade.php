<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categorias') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="categoryManagement(@js($crudRoutes))">
        <div class="max-w-7xl mx-auto min-w-0 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6">
                    <x-validation-errors />
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">

                <div class="bg-gradient-to-r from-purple-700 to-indigo-900 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Categorias de Produtos
                    </h3>
                    <div class="flex gap-4 w-full md:w-auto">
                        <form action="{{ route('categories.index') }}" method="GET" class="relative flex-grow">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar categoria..." class="w-full bg-white/10 border-none focus:ring-2 focus:ring-purple-400 text-white placeholder-white/60 rounded-xl px-4 py-2 text-sm backdrop-blur-md">
                        </form>
                        @can('create', App\Models\Category::class)
                            <button type="button" @click="openCreateModal()" class="bg-purple-500 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-purple-400 transition transform hover:-translate-y-1 active:scale-95 whitespace-nowrap">
                                + Nova Categoria
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg flex items-center gap-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="-mx-6 w-full min-w-0 overflow-x-auto bg-gray-50 px-6 sm:mx-0 sm:px-0 rounded-xl border border-gray-200">
                        <table class="w-full min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nome / Descrição</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Notas Rápidas</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($categories as $category)
                                    @php
                                        $categoryPayload = [
                                            'id' => $category->id,
                                            'name' => $category->name,
                                            'description' => $category->description,
                                            'additional_info' => $category->additional_info,
                                        ];
                                    @endphp
                                    <tr class="hover:bg-purple-50/20 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 capitalize">{{ $category->name }}</div>
                                            <div class="text-xs text-gray-500">{{ Str::limit($category->description, 50) ?: 'Sem descrição' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-600 truncate max-w-xs italic">
                                                {{ $category->additional_info ?: '...' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                @can('update', $category)
                                                    <button type="button" @click="openInfoModal(@js($categoryPayload))" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Adicionar Info">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                    </button>
                                                    <button type="button" @click="openEditModal(@js($categoryPayload))" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                @endcan
                                                @can('delete', $category)
                                                    <form id="delete-category-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block">
                                                        @csrf @method('DELETE')
                                                        <x-hidden-return-url />
                                                        <button
                                                            type="button"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                            @click="$store.deleteConfirm.openModal('delete-category-{{ $category->id }}', @js('Excluir esta categoria? Esta ação não pode ser desfeita.'))"
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
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic text-sm">Nenhuma categoria encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <x-modal-backdrop close="closeModal()" overlay-class="bg-purple-900/60" class="z-40" />

                <x-modal-panel panel-class="max-w-lg" class="animate-scale-up z-50">
                    <form :action="formAction" method="POST">
                        @csrf
                        <x-hidden-return-url />
                        <template x-if="isEditing"><input type="hidden" name="_method" value="PUT"></template>

                        <div class="bg-purple-700 px-8 py-6 text-white">
                            <h3 class="text-xl font-bold" x-text="isEditing ? 'Editar Categoria' : 'Nova Categoria'"></h3>
                        </div>

                        <div class="px-8 pt-4">
                            <x-validation-errors />
                        </div>

                        <div class="p-8 space-y-4">
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1">Nome da Categoria</label>
                                <input type="text" name="name" x-model="form.name" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-purple-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1">Descrição</label>
                                <textarea name="description" x-model="form.description" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-purple-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1">Notas Adicionais</label>
                                <textarea name="additional_info" x-model="form.additional_info" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-purple-500"></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-8 py-6 flex justify-end gap-3">
                            <button type="button" @click="closeModal()" class="px-6 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                            <button type="submit" class="bg-purple-600 text-white px-8 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-purple-700 transition">Salvar Categoria</button>
                        </div>
                    </form>
                </x-modal-panel>
            </div>
        </div>

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
                        <input type="hidden" name="description" :value="form.description">

                        <label class="block text-xs font-black uppercase text-purple-500 mb-2 underline">Categoria: <span x-text="form.name" class="text-gray-900"></span></label>
                        <textarea name="additional_info" x-model="form.additional_info" rows="5" class="w-full bg-amber-50 border-amber-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 mb-6" placeholder="Notas sobre esta categoria..."></textarea>

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
