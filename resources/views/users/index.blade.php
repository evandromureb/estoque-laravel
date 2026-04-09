<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="userManagement(@js($crudRoutes))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6">
                    <x-validation-errors />
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">

                <div class="bg-gradient-to-r from-slate-700 to-slate-900 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Gestão de Usuários
                    </h3>
                    <div class="flex gap-4 w-full md:w-auto">
                        <form action="{{ route('users.index') }}" method="GET" class="relative flex-grow">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou email..." class="w-full bg-white/10 border-none focus:ring-2 focus:ring-indigo-400 text-white placeholder-white/60 rounded-xl px-4 py-2 text-sm backdrop-blur-md">
                        </form>
                        @can('create', App\Models\User::class)
                            <button type="button" @click="openCreateModal()" class="bg-indigo-500 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-indigo-400 transition transform hover:-translate-y-1 active:scale-95 whitespace-nowrap">
                                + Novo Usuário
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

                    <div class="overflow-hidden bg-gray-50 rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nome / Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Perfil</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Conta</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Notas</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($users as $user)
                                    @php
                                        $userPayload = [
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'email' => $user->email,
                                            'additional_info' => $user->additional_info,
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            <div class="mt-1 text-[10px] font-mono text-gray-400">ID #{{ $user->id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->is_admin)
                                                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-bold text-violet-800">Administrador</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Membro</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->email_verified_at)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    E-mail verificado
                                                </span>
                                                <div class="mt-1 text-[10px] text-gray-500">{{ $user->email_verified_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</div>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    Verificação pendente
                                                </span>
                                            @endif
                                            <div class="mt-2 text-[10px] uppercase tracking-wide text-gray-400">Cadastro</div>
                                            <div class="text-xs text-gray-600">{{ $user->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-600 line-clamp-2 max-w-xs italic">
                                                {{ $user->additional_info ?: '—' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                @can('update', $user)
                                                    <button type="button" @click="openInfoModal(@js($userPayload))" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Adicionar Info">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                    </button>
                                                    <button type="button" @click="openEditModal(@js($userPayload))" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                @endcan
                                                @can('delete', $user)
                                                    <form id="delete-user-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                                        @csrf @method('DELETE')
                                                        <x-hidden-return-url />
                                                        <button
                                                            type="button"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                            @click="$store.deleteConfirm.openModal('delete-user-{{ $user->id }}', @js('Excluir este usuário? Esta ação não pode ser desfeita.'))"
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
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic text-sm">Nenhum usuário encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <x-modal-backdrop close="closeModal()" overlay-class="bg-slate-900/60" class="z-40" />

                <x-modal-panel panel-class="max-w-lg" class="animate-scale-up z-50">
                    <form :action="formAction" method="POST">
                        @csrf
                        <x-hidden-return-url />
                        <template x-if="isEditing"><input type="hidden" name="_method" value="PUT"></template>

                        <div class="bg-indigo-600 px-8 py-6">
                            <h3 class="text-xl font-bold text-white" x-text="isEditing ? 'Editar Usuário' : 'Novo Usuário'"></h3>
                        </div>

                        <div class="px-8 pt-4">
                            <x-validation-errors />
                        </div>

                        <div class="p-8 space-y-4">
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1">Nome Completo</label>
                                <input type="text" name="name" x-model="form.name" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1" x-text="isEditing ? 'Nova Senha (opcional)' : 'Senha'"></label>
                                <input type="password" name="password" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" :required="!isEditing">
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-1">Notas / Informações Adicionais</label>
                                <textarea name="additional_info" x-model="form.additional_info" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500" placeholder="Histórico, observações..."></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-8 py-6 flex justify-end gap-3">
                            <button type="button" @click="closeModal()" class="px-6 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                            <button type="submit" class="bg-indigo-600 text-white px-8 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-indigo-700">Salvar</button>
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
                        <input type="hidden" name="email" :value="form.email">

                        <label class="block text-xs font-black uppercase text-indigo-500 mb-2 underline">Usuário: <span x-text="form.name" class="text-gray-900"></span></label>
                        <textarea name="additional_info" x-model="form.additional_info" rows="5" class="w-full bg-amber-50 border-amber-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 mb-6" placeholder="Notas sobre este usuário..."></textarea>

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
